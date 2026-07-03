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
        echo JsonMsg('error', 'Lỗi hệ thống khi đăng ký: ' . $error['message']);
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
if($db->site('status_demo') == 1){
        ob_clean();
        die(JsonMsg('error', 'Đây là trang website demo, bạn không thể thực hiện chức năng này'));
    }

try {
    $username = Anti_xss($_POST['username'] ?? '');
    $password = Anti_xss($_POST['password'] ?? '');
    $email = Anti_xss($_POST['email'] ?? '');

    // Regex patterns
    $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    $usernameRegex = '/^[a-zA-Z][a-zA-Z0-9_]{6,15}$/';
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/';

    $ipRegister = myip();
   
    // Validate email
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
    if (empty($email)) {
        ob_clean();
        die(JsonMsg('error', 'Vui lòng điền email'));
    } elseif (!preg_match($emailRegex, $email)) {
        ob_clean();
        die(JsonMsg('error', 'Email không đúng định dạng'));
    } elseif ($db->num_rows("SELECT * FROM `users` WHERE `email` = '{$email}'") > 0) {
        ob_clean();
        die(JsonMsg('error', 'Email này đã tồn tại trên hệ thống'));
    } elseif (empty($username)) {
        ob_clean();
        die(JsonMsg('error', 'Vui lòng điền tên đăng nhập'));
    } elseif (!preg_match($usernameRegex, $username)) {
        ob_clean();
        die(JsonMsg('error', 'Tên người dùng phải dài từ 6-15 ký tự, bắt đầu bằng chữ cái, và có thể chứa chữ cái, số và dấu gạch dưới.'));
    } elseif (strlen($username) <= 4) {
        ob_clean();
        die(JsonMsg('error', 'Tên đăng nhập có nhiều hơn 6 ký tự trở lên'));
    } elseif ($db->num_rows("SELECT * FROM `users` WHERE `username` = '{$username}'") > 0) {
        ob_clean();
        die(JsonMsg('error', 'Tên đăng nhập này đã tồn tại trên hệ thống'));
    } elseif (empty($password)) {
        ob_clean();
        die(JsonMsg('error', 'Vui lòng nhập mật khẩu'));
    } elseif (!preg_match($passwordRegex, $password)) {
        ob_clean();
        die(JsonMsg('error', 'Mật khẩu phải dài 8-20 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.'));
    } else {
        $realPass = sha1($password);
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $isInsert = $db->insert("users", array(
            'username' => $username,
            'password' => $realPass,
            'email' => $email,
            'device' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => myip(),
            'time_session' => time(),
            'ref_id' => !empty($_SESSION['ref']) ? $_SESSION['ref'] : 0,
            'token' => md5(random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 6) . time()),
            'secretkey' => $google2fa->generateSecretKey(),
            'provider' => 'account',
            'level' => 'member',
            'create_date' => gettime()
        ));
        if($isInsert){
        insert_log($db->get_row("SELECT * FROM `users` WHERE `username` = '$username' ")['id'], "Tham gia hệ thống bằng phương thức tài khoản");
            ob_clean();
            die(JsonMsg('success', 'Đăng ký thành công'));
        }
        $errorDetail = !empty($db->last_error) ? ': ' . $db->last_error : '';
        ob_clean();
        die(JsonMsg('error', 'Đã xảy ra lỗi khi đăng ký tài khoản' . $errorDetail));
    }
} catch (Throwable $e) {
    ob_clean();
    die(JsonMsg('error', 'Lỗi hệ thống khi đăng ký: ' . $e->getMessage()));
}
    