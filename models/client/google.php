<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
use PragmaRX\Google2FA\Google2FA;
$client = new Google_Client();
$client->setClientId(GOOGLE_APP_ID);
$client->setClientSecret(GOOGLE_APP_SECRET);
$client->setRedirectUri(GOOGLE_APP_CALLBACK_URL);
$client->addScope("email");
$client->addScope("profile");
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!$client->isAccessTokenExpired()) {
        $client->setAccessToken($token);
        $google_oauth = new Google\Service\Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        $userEmail =  Anti_xss($google_account_info->email);
        $userName =  Anti_xss($google_account_info->name);
        $userId =  Anti_xss($google_account_info->id);
        $user = $db->get_row("SELECT * FROM `users` WHERE `email`='$userEmail' AND `provider`='google' AND `provider_id`='$userId'");
        if (!$user) {
            $google2fa = new Google2FA();
            $create = $db->insert("users", [
                'username' => $userId,
                'name' => $userName,
                'email' => $userEmail,
                'password' => sha1(random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 10)),
                'provider'      => 'google',
                'level' => 'member',
                'provider_id'      => $userId,
                'device'=>$_SERVER['HTTP_USER_AGENT'],
                'ip' => myip(),
                'time_session' => time(),
                'ref_id'        => !empty($_SESSION['ref']) ? $_SESSION['ref'] : 0,
                'token' => md5(random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 6).time()),
                'secretkey' => $google2fa->generateSecretKey(),
                'create_date' => gettime()
            ]);
            if ($create) {
                $user_id = $db->get_id_insert();
                insert_log($user_id, "Tham gia hệ thống bằng phương thức google");
               
                $session->send(getRowRealtime('users',$user_id,'username'));
                new Redirect('/');
            } else {
                die(JsonMsg('error', 'Hệ thống lỗi rồi, inbox admin đi nào'));
            }
        } else {
            insert_log($user['id'], "Đăng nhập vào hệ thống bằng phương thức google");
            $db->update("users", array(
                'login_attempts' => 0,
                'time_session' => time(),
                'ip' => myip(),
            ), " `id` = '" . $user['id'] . "' ");
           
            $session->send($user['username']);
            new Redirect('/');
        }
    } else {
        new Redirect($client->createAuthUrl());
    }
} else {
    new Redirect($client->createAuthUrl());
}
