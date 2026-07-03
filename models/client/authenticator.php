<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
use PragmaRX\Google2FA\Google2FA;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(JsonMsg('error', 'Access Denied'));
}
if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    die(jsonMsg('error', 'Invalid CSRF Protection Token'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if ($_POST['type'] == 'ChangeGoogle2FA') {
$secret = Anti_xss($_POST['secret'] ?? '');

if (empty($secret)) {
    die(JsonMsg('error', 'Vui lòng nhập mã xác minh 2FA!'));
}
$getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $data_user['username'] . "' AND `banned`=0");
if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}

    $google2fa = new Google2FA();
    if ($google2fa->verifyKey($getUser['secretkey'], $secret) != true) {
        die(JsonMsg('error', 'Mã xác minh không chính xác!'));
    }
    $isUpdate = $db->update("users", [
        'status_2fa' => $getUser['status_2fa'] == 1 ? 0 : 1,
    ], " `id` = '" . Anti_xss($getUser['id']) . "' ");
    
    insert_log($getUser['id'], 'Cập nhật trạng thái 2FA');
    die(JsonMsg('success', 'Lưu thành công'));
}
if ($_POST['type'] == 'VerifyGoogle2FA') {
if ($db->site('status_captcha') == 1) {
    if (empty($_POST['captcha'])) {
        die(JsonMsg('error', 'Vui lòng xác thực captcha'));
    }
    $secret = $db->site('secret_key');
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = Anti_xss($_POST['captcha']);
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip";
    $fire = curl_get($url);
    $data = json_decode($fire);
    if ($data->success == false) {
        die(JsonMsg('error', 'Vui lòng xác thực captcha'));
    }
}
    if (empty($_POST['token'])) {
        die(JsonMsg('error', 'Vui lòng đăng nhập !'));
    }

    // Lấy thông tin người dùng từ cơ sở dữ liệu
    if (!$getUser = $db->get_row("SELECT * FROM `users` WHERE `token` = '".Anti_xss($_POST['token'])."' AND `banned` = '0'")) {
        die(JsonMsg('error', 'Vui lòng đăng nhập!'));
    }
    // Kiểm tra mã xác minh
    if (empty($_POST['secret'])) {
        die(JsonMsg('error', 'Vui lòng nhập mã xác minh!'));
    }
    // Khởi tạo Google2FA và kiểm tra mã OTP
    $google2fa = new Google2FA();

    if ($google2fa->verifyKey($getUser['secretkey'], Anti_xss($_POST['secret'])) !== true) {
        die(JsonMsg('error', 'Mã xác minh không chính xác!'));
    }

    // Nếu xác minh thành công
    insert_log($getUser['id'], "Đăng nhập vào hệ thống bằng phương thức tài khoản");
    $session->send($getUser['username']);
    die(JsonMsg('success', 'Đăng nhập thành công'));
}