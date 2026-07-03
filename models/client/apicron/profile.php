<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // If no token parameter is provided, return empty response
    if (!isset($_GET["token"])) {
        exit();
    }
    
    // If token is empty or only whitespace, return "Thiếu token"
    if (empty(trim($_GET["token"]))) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => 'Thiếu token'
        )));
    }
    
    // Query user with token
    $getData = $db->get_row("SELECT * FROM `users` WHERE `token` = '" . Anti_xss($_GET["token"]) . "' LIMIT 1");
    
    if ($getData) {
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Thành công',
            'data' => array(
                'username' => $getData['username'],
                'email' => $getData['email'],
                'coin' => $getData['money'],
                'chietkhau' => $getData['chietkhau']
            )
        ));
    } else {
        exit(json_encode(array(
            'status' => 'error',
            'message' => 'Token không hợp lệ'
        )));
    }
}