<?php
ob_start();
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        echo JsonMsg('error', 'Lỗi hệ thống khi thanh toán: ' . $error['message']);
    }
});

function product_response($status, $msg)
{
    if (ob_get_length()) {
        ob_clean();
    }
    die(JsonMsg($status, $msg));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    product_response('error', 'Access Denied');
}

if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != ($_SESSION["csrf_token"] ?? null)) {
    product_response('error', 'Invalid CSRF Token');
}

if (check_license($db->site('license'))['status'] == 'error') {
    product_response('error', 'Website này chưa kích hoạt bản quyền');
}

if (!$user) {
    product_response('error', 'Vui lòng đăng nhập để thực hiện');
}

$product_id = Anti_xss($_POST['product_id'] ?? '');
$coupon_code = Anti_xss($_POST['coupon'] ?? '');

if (empty($product_id)) {
    product_response('error', 'Vui lòng nhập đủ thông tin');
}

$product = $db->get_row("SELECT * FROM `products` WHERE `product_id` = '" . $product_id . "' AND `status` = 1");
if (!$product) {
    product_response('error', 'Sản phẩm không tồn tại');
}

$getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $data_user['username'] . "' AND `banned` = 0");
if (!$getUser) {
    product_response('error', 'Tài khoản không tồn tại hoặc đã bị khóa');
}

if ($db->get_row("SELECT * FROM `orders` WHERE `product_id` = '" . $product_id . "' AND `user_id` = '" . $getUser['id'] . "'")) {
    product_response('error', 'Bạn đã mua sản phẩm này rồi');
}

if (!canPurchase($getUser['id'], $product['product_id'])) {
    $requiredRank = $db->get_row("SELECT name FROM ranks WHERE id = '{$product['rank_id']}'");
    $requiredRankName = $requiredRank ? $requiredRank['name'] : 'Chưa xác định';

    $currentRank = $db->get_row("SELECT name FROM ranks WHERE id = '{$getUser['rank_id']}'");
    $currentRankName = $currentRank ? $currentRank['name'] : 'Chưa xác định';

    product_response('error', 'Cấp bậc hiện tại của bạn là ' . $currentRankName . ', nhưng cần cấp bậc ' . $requiredRankName . ' để mua sản phẩm này.');
}

$total = (float)$product['sale_price'];
$price = $total;
$discount = 0;

if ($coupon_code !== '') {
    $discount = $getUser ? (float)checkCoupon($coupon_code, $getUser['id'], $total) : 0;
}

if ($discount > 0) {
    $total = $total - ($total * $discount / 100);
}

if (!is_numeric($total) || $total < 0) {
    product_response('error', 'Dữ liệu không hợp lệ');
}

if ($total > $getUser['money']) {
    product_response('error', 'Số dư của bạn không đủ ' . format_cash($total) . 'đ, vui lòng nạp thêm để thực hiện');
}

$seller_earning = $price - ($price * (float)$db->site('ck_product') / 100);
$isMoney = RemoveCredits($getUser['id'], $total, "Thanh toán đơn hàng mua code <b>" . $product['title'] . "</b>");

if (!$isMoney) {
    product_response('error', 'Không thể trừ số dư, vui lòng thử lại');
}

if ($discount > 0 && $coupon_code !== '') {
    $coupon = $db->get_row("SELECT `used` FROM tbl_coupons WHERE `code` = '" . $coupon_code . "' ");
    if ($coupon) {
        $db->update("tbl_coupons", [
            'used' => $coupon['used'] + 1
        ], " `code` = '" . $coupon_code . "' ");
    }
}

$db->update("products", [
    'sales_count' => $product['sales_count'] + 1
], " `product_id` = '" . $product['product_id'] . "' ");

$product_data = [
    'user_id' => $getUser['id'],
    'product_id' => $product['product_id'],
    'total_price' => $total
];

$db->cong('users', 'product_amount', $seller_earning, " `id` = '" . $product['seller_id'] . "' ");
$db->cong('users', 'product_total_amount', $seller_earning, " `id` = '" . $product['seller_id'] . "' ");

if (!$db->insert('orders', $product_data)) {
    product_response('error', 'Đã xảy ra lỗi khi thêm dữ liệu đơn hàng');
}

$order_id = $db->get_id_insert();
$order_item_data = [
    'order_id' => $order_id,
    'product_id' => $product['product_id'],
    'purchase_code' => getPurchaseCode(),
    'user_id' => $getUser['id'],
    'seller_id' => $product['seller_id'],
    'product_price' => $price,
    'quantity' => 1,
    'seller_earning' => $seller_earning,
    'created_at' => gettime(),
    'updated_at' => gettime()
];

if ($db->site("noti_action") != "") {
    $my_text = $db->site("noti_action");
    $replacements = [
        '{domain}' => $domain,
        '{username}' => $data_user["username"],
        '{action}' => "Thanh toán đơn hàng mua code : " . $product["title"],
        '{ip}' => myip(),
        '{time}' => gettime()
    ];
    $my_text = str_replace(array_keys($replacements), array_values($replacements), $my_text);
    sendMessAdmin($my_text);
}

if (!$db->insert('order_items', $order_item_data)) {
    product_response('error', 'Đã xảy ra lỗi khi thêm chi tiết đơn hàng');
}

product_response('success', 'Mua sản phẩm thành công');