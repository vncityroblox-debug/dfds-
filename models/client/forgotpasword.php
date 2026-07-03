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
if (isset($_POST['action']) && $_POST['action'] == 'forgot') {
$email = Anti_xss($_POST['email'] ?? '');

if ($db->site('status_captcha') == 1) {
    $captcha = Anti_xss($_POST['captcha'] ?? '');
    if (empty($captcha)) {
        die(JsonMsg('error', 'Vui lòng xác thực captcha'));
    }
    $captchaUrl = "https://www.google.com/recaptcha/api/siteverify?secret=" . $db->site('secret_key') . "&response=$captcha&remoteip=" . $_SERVER['REMOTE_ADDR'];
    $captchaResponse = json_decode(curl_get($captchaUrl));
    if (!$captchaResponse->success) {
        die(JsonMsg('error', 'Vui lòng xác thực captcha'));
    }
}

if (!$email) {
    die(JsonMsg('error', 'Vui lòng nhập email'));
}

if (!check_email($email)) {
    die(JsonMsg('error', 'Email không đúng định dạng'));
}

$getUser = $db->get_row("SELECT * FROM `users` WHERE `email` = '$email'");
if (!$getUser) {
    die(JsonMsg('error', 'Địa chỉ Email này không tồn tại trong hệ thống'));
}

if (time() - $getUser['time_session'] < 300) {
    die(JsonMsg('error', 'Bạn thao tác quá nhanh vui lòng thử lại sau 5 phút'));
}

if (!$db->site('pass_email_smtp') || !$db->site('email_smtp')) {
    die(JsonMsg('error', 'SMTP chưa được cấu hình'));
}

$token = md5(random('QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 6) . time());
$body = "Nếu bạn yêu cầu đặt lại mật khẩu, vui lòng nhấp vào liên kết bên dưới để xác minh.<br>
         <a target='_blank' href='$base_url/reset-password/$token'>$base_url/reset-password/$token</a><br>
         <p>Nếu không phải là bạn, vui lòng liên hệ ngay với Quản trị viên của bạn để được hỗ trợ về bảo mật.</p>";

$chu_de = 'Khôi phục lại mật khẩu - ' . $db->site('title');
$content = file_get_contents(DOMAIN . '/libs/mails/notification');
$content = str_replace(['{title}', '{content}'], ['Xác nhận khôi phục mật khẩu', $body], $content);

sendCSM($getUser['email'], $getUser['username'], $chu_de, $content, $db->site('title'));

if ($db->update('users', ['otp' => $token, 'time_session' => time()], "id = '" . $getUser['id'] . "'")) {
    die(JsonMsg('success', 'Vui lòng kiểm tra Email của bạn để hoàn tất quá trình đặt lại mật khẩu'));
}
}
if (isset($_POST['action']) && $_POST['action'] == 'change') {
$token = Anti_xss($_POST['otp'] ?? '');
$renewpassword = Anti_xss($_POST['renewpassword'] ?? '');
$newpassword = Anti_xss($_POST['newpassword'] ?? '');
if (empty($token)) {
    die(JsonMsg('error', 'Otp không hợp lệ'));
}

if (!$getUser = $db->get_row("SELECT * FROM `users` WHERE `otp` = '" . $token . "' ")) {
    die(JsonMsg('error', 'Liên kết không tồn tại'));
}
if ($getUser['otp'] == null) {
    die(JsonMsg('error', 'Liên kết không tồn tại'));
}
if (!$newpassword) {
    die(JsonMsg('error', 'Vui lòng nhập mật khẩu mới'));
}
if (!$renewpassword) {
    die(JsonMsg('error', 'Vui lòng xác nhận mật khẩu mới'));
}
if ($newpassword != $renewpassword) {
    die(JsonMsg('error', 'Xác nhận 2 mật khẩu không khớp nhau'));
}
$isUpdate = $db->update("users", [
    'otp' => null,
    'password' => isset($newpassword) ? sha1(Anti_xss($newpassword)) : null,
], " `id` = '" . Anti_xss($getUser['id']) . "' ");
if ($isUpdate) {
    insert_log($getUser['id'], 'Thực hiện đặt lại mật khẩu mới');
    die(JsonMsg('success', 'Thay đổi mật khẩu thành công'));
}
die(JsonMsg('error', 'Thay đổi mật khẩu thất bại'));
}