<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(JsonMsg('error', 'Access Denied'));
}

if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != ($_SESSION["csrf_token"] ?? '')) {
    die(JsonMsg('error', 'Invalid CSRF Protection Token'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if($db->site('card_status') == 0){
 die(JsonMsg('error', 'Nạp thẻ đang bảo trì'));
}
if ($db->site('status_demo') == 1) {
    die(JsonMsg('error', 'Đây là trang website demo, bạn không thể thực hiện chức năng này'));
}

if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}

$getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . ($data_user['username'] ?? '') . "' AND `banned`=0");

if (!$getUser) {
    die(JsonMsg('error', 'Tài khoản không tồn tại hoặc bị khóa.'));
}

if (empty($_POST["telco"])) {
    die(JsonMsg('error', 'Vui lòng chọn nhà mạng'));
}
if (empty($_POST["amount"]) || $_POST["amount"] < 0) {
    die(JsonMsg('error', 'Vui lòng chọn mệnh giá cần nạp'));
}
if (empty($_POST["serial"])) {
    die(JsonMsg('error', 'Vui lòng nhập serial thẻ'));
}
if (empty($_POST["pin"])) {
    die(JsonMsg('error', 'Vui lòng nhập mã thẻ'));
}

$telco = Anti_xss($_POST["telco"]);
$amount = Anti_xss($_POST["amount"]);
$serial = Anti_xss($_POST["serial"]);
$pin = Anti_xss($_POST["pin"]);

$checkFormat = checkFormatCard($telco, $serial, $pin);
if (!$checkFormat["status"]) {
    die(JsonMsg('error', $checkFormat["msg"]));
}

$pendingCount = $db->num_rows("SELECT * FROM `cards` WHERE `user_id` = '" . $getUser["id"] . "' AND `status` = 'pending'");
if ($pendingCount > 5) {
    die(JsonMsg('error', 'Vui lòng không spam!'));
}

$errorCount = $db->num_rows("SELECT * FROM `cards` WHERE `status` = 'error' AND `user_id` = '" . $getUser["id"] . "' AND `create_date` >= CURDATE() AND `create_date` < CURDATE() + INTERVAL 1 DAY");
$successCount = $db->num_rows("SELECT * FROM `cards` WHERE `status` = 'completed' AND `user_id` = '" . $getUser["id"] . "' AND `create_date` >= CURDATE() AND `create_date` < CURDATE() + INTERVAL 1 DAY");

if ($errorCount > 5 && $successCount == 0) {
    die(JsonMsg('error', 'Bạn đã bị chặn sử dụng chức năng nạp thẻ trong 1 ngày'));
}

$trans_id = random("QWERTYUIOPASDFGHJKLZXCVBNM", 6) . time();
$data = napthe($telco, $amount, $serial, $pin, $trans_id);

if (!isset($data["status"])) {
    die(JsonMsg('error', 'Lỗi không xác định từ hệ thống nạp thẻ.'));
}

if ($data["status"] == 99) {
    $isInsert = $db->insert("cards", [
        "trans_id" => $trans_id,
        "telco" => $telco,
        "amount" => $amount,
        "serial" => $serial,
        "pin" => $pin,
        "price" => 0,
        "user_id" => $getUser["id"],
        "status" => "pending",
        "reason" => "",
        "create_date" => gettime(),
        "update_date" => gettime()
    ]);

    if ($isInsert) {
        $db->update("users", ["time_request" => time()], " `id` = '" . $getUser["id"] . "' ");
        insert_log($getUser['id'], "Thực hiện nạp thẻ Serial: " . $serial . " - Pin: " . $pin);
        die(JsonMsg('success', 'Nạp thẻ thành công'));
    }

    die(JsonMsg('error', 'Nạp thẻ thất bại, vui lòng liên hệ Admin'));
}

die(JsonMsg('error', $data["message"] ?? 'Lỗi không xác định.'));
