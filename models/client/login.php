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
        echo JsonMsg('error', 'Lỗi hệ thống khi đăng nhập: ' . $error['message']);
    }
});
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    die(JsonMsg('error', 'Access Denied'));
}
if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    ob_clean();
    die(JsonMsg('error', 'Invalid CSRF Protection Token'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    ob_clean();
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
$username = Anti_xss($_POST['username'] ?? '');
$passwordInput = Anti_xss($_POST['password'] ?? '');
$password = sha1($passwordInput);
if ($db->site('status_captcha') == 1) {
    if (empty($_POST['captcha'])) {
        ob_clean();
        die(JsonMsg('error', 'Vui lòng xác thực captcha'));
    }
    $secret = $db->site('secret_key');
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = Anti_xss($_POST['captcha']);
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip";
    $fire = curl_get($url);
    $data = json_decode($fire);
    if (!$data || empty($data->success)) {
        ob_clean();
        die(JsonMsg('error', 'Vui lòng xác thực captcha'));
    }
}
if (empty($username)) {
    ob_clean();
    die(JsonMsg('error', 'Vui lòng nhập tên đăng nhập'));
} elseif (empty($passwordInput)) {
    ob_clean();
    die(JsonMsg('error', 'Vui lòng nhập mật khẩu'));
} elseif (!$getUser = $db->get_row(" SELECT * FROM `users` WHERE `username` = '$username' ")) {
    ob_clean();
    die(JsonMsg('error', 'Tài khoản hoặc mật khẩu không chính xác'));
} elseif (!$users = $db->get_row("SELECT * FROM `users` WHERE `username` = '{$username}' AND `password` = '{$password}'")) {
    if ($getUser['login_attempts'] >= 5) {
        $db->update("users", array(
            'banned' => 1,
            'device' => $_SERVER['HTTP_USER_AGENT']
        ), " `id` = '" . $getUser['id'] . "' ");
        $db->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => $_SERVER['HTTP_USER_AGENT'],
            'create_date' => gettime(),
            'action' => "Tài khoản của bạn đã bị tạm khoá do đang nhập sai nhiều lần",
        ]);
        ob_clean();
        die(JsonMsg('error', 'Tài khoản của bạn đã bị tạm khoá do đang nhập sai nhiều lần'));
    }
    $db->cong('users', 'login_attempts', 1, " `id` = '" . $getUser['id'] . "' ");
    ob_clean();
    die(JsonMsg('error', 'Tài khoản hoặc mật khẩu không chính xác'));
} else {
    if ($users['banned'] == 1) {
        ob_clean();
        die(JsonMsg('error', 'Tài khoản của bạn đã bị tạm khoá, liên hệ admin để hỗ trợ'));
    }
    if ($users['status_2fa'] == 1) {
        ob_clean();
        die(json_encode([
            'status'    => 'verify',
            'url'       => '/verify/' . ($users['token']),
            'msg'       => 'Vui lòng xác minh 2FA để hoàn thành đăng nhập'
        ]));
    }
    $db->update("users", array(
        'login_attempts' => 0,
        'device' => $_SERVER['HTTP_USER_AGENT'],
        'time_session' => time()
    ), " `id` = '" . $getUser['id'] . "' ");
    $session->send($username);
    insert_log($users['id'], "Đăng nhập vào hệ thống bằng phương thức tài khoản");
    ob_clean();
    die(JsonMsg('success', 'Đăng nhập thành công!'));
    
}
