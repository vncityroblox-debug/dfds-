<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(JsonMsg('error', 'Access Denied'));
}
if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    die(jsonMsg('error', 'Invalid CSRF Protection Token'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
$current_password = sha1(Anti_xss($_POST['current_password']));
$new_password = Anti_xss($_POST['new_password']);
$confirm_password = Anti_xss($_POST['confirm_password']);

if (!$user) {
                die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
            }
            $getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $data_user['username'] . "' AND `banned`=0");
            if (empty($current_password) || empty($new_password) || empty($new_password)) {
                die(JsonMsg('error', 'Vui lòng nhập đủ thông tin'));
            }
if (time() - $getUser['time_session'] < 30) {
                die(JsonMsg('error', 'Vui lòng thử lại sau 30 giây'));
            }
            if ($db->num_rows("SELECT * FROM `users` WHERE `id` = '{$getUser['id']}' AND `password` = '{$current_password}'") == 0) {
                die(JsonMsg('error', 'Mật khẩu hiện tại không chính xác'));
            }
            if ($_POST['confirm_password'] != $_POST['new_password']) {
            die(json_encode(['status' => 'error', 'msg' => 'Nhập lại mật khẩu không đúng']));
        }
            $isUpdate = $db->update("users", array(
                'password' => sha1($new_password),
                'time_session' => time()
            ), " `id` = '" . $getUser['id'] . "' ");
            if ($isUpdate) {
                insert_log($getUser['id'], 'Cập nhật mật khẩu cá nhân');
                die(JsonMsg('success', 'Cập nhật thành công'));
            } else {
                die(JsonMsg('error', 'Đã xảy ra lỗi cập nhập dữ liệu'));
            }