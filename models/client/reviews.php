<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(JsonMsg('error', 'Access Denied'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
$product_id = Anti_xss($_POST['product_id']);
$rating = Anti_xss($_POST['rating']);
$review = Anti_xss($_POST['review']);

if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}

$getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $data_user['username'] . "' AND `banned`=0");

if (empty($product_id)) {
    die(JsonMsg('error', 'Vui lòng nhập đủ thông tin'));
}

if (empty($rating)) {
    die(JsonMsg('error', 'Vui lòng chọn sao đánh giá'));
}

if ($rating < 1 || $rating > 5) {
    die(JsonMsg('error', 'Bạn chỉ được phép chọn từ 1 - 5 sao'));
}

if (empty($review)) {
    die(JsonMsg('error', 'Vui lòng nhập nội dung cần đánh giá'));
}

$existingRecord = $db->get_row("
    SELECT * FROM order_items
    WHERE product_id = {$product_id} AND user_id = '{$getUser['id']}'
");

if (!$existingRecord) {
    die(JsonMsg('error', 'Sản phẩm không tồn tại trong đơn hàng của bạn'));
}

if ($db->get_row("SELECT * FROM `reviews` WHERE `product_id` = '$product_id' AND `user_id` = '{$getUser['id']}'")) {
    die(JsonMsg('error', 'Sản phẩm này bạn đã đánh giá rồi'));
}

$data = [
    'user_id' => $getUser['id'],
    'seller_id' => $existingRecord['seller_id'],
    'product_id' => $existingRecord['product_id'],
    'rating' => $rating,
    'review' => $review,
    'created_at' => gettime(),
    'updated_at' => gettime()
];

if (!$db->insert('reviews', $data)) {
    die(JsonMsg('error', 'Đánh giá thất bại'));
}

die(JsonMsg('success', 'Đánh giá thành công'));
?>
