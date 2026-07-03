<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(JsonMsg('error', 'Access Denied'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    die(jsonMsg('error', 'Invalid CSRF Protection Token'));
}

if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}

$getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $data_user['username'] . "' AND `banned`=0");

if ($db->site('status_withdraw_product') != 1) {
    die(JsonMsg('error', 'Chức năng rút tiền đang bảo trì'));
}

if (empty($_POST['bank'])) {
    die(JsonMsg('error', 'Vui lòng chọn ngân hàng cần rút'));
}

if (empty($_POST['stk'])) {
    die(JsonMsg('error', 'Vui lòng nhập số tài khoản cần rút'));
}

if (empty($_POST['name'])) {
    die(JsonMsg('error', 'Vui lòng nhập tên chủ tài khoản'));
}

if (empty($_POST['amount'])) {
    die(JsonMsg('error', 'Vui lòng nhập số tiền cần rút'));
}

if ($_POST['amount'] < $db->site('minrut_product')) {
    die(JsonMsg('error', 'Số tiền rút tối thiểu phải là' . ' ' . format_cash($db->site('minrut_product'))));
}

if ($getUser['product_amount'] < $_POST['amount']) {
    die(JsonMsg('error', 'Số dư hoa hồng khả dụng của bạn không đủ'));
}

$amount = Anti_xss(preg_replace('/\D/', '', $_POST['amount']));
$trans_id = random('123456789QWERTYUIOPASDFGHJKLZXCVBNM', 6);
$isTru = $db->tru('users', 'product_amount', $amount, " `id` = '" . $getUser['id'] . "' ");

if (getRowUser($getUser['id'], 'product_amount') < 0) {
    Banned($getUser['id'], 'Gian lận khi rút số dư bán hàng');
    die(JsonMsg('error', 'Bạn đã bị khoá tài khoản vì gian lận'));
}

$isInsert = $db->insert('product_withdraw', [
    'trans_id'  => $trans_id,
    'user_id'   => $getUser['id'],
    'bank'      => Anti_xss($_POST['bank']),
    'stk'       => Anti_xss($_POST['stk']),
    'name'      => Anti_xss($_POST['name']),
    'amount'    => Anti_xss($_POST['amount']),
    'status'    => 0,
    'create_gettime'    => gettime(),
    'update_gettime'    => gettime()
]);

if ($isInsert) {
    die(JsonMsg('success', 'Tạo yêu cầu rút tiền thành công, vui lòng đợi ADMIN xử lý'));
} else {
    die(JsonMsg('error', 'ERROR 1 - Phát hiện lỗi khi rút tiền, vui lòng liên hệ ADMIN'));
}