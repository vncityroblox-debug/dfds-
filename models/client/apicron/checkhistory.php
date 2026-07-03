<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
header('Content-Type: application/json; charset=utf-8');


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // If no token parameter is provided, return empty response
    if (!isset($_GET["token"]) || empty(trim($_GET["id"]))) {
        exit();
    }
    
    // If token is empty or only whitespace, return "Thiếu token"
    if (empty(trim($_GET["token"]))) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => 'Thiếu token'
        )));
    }
    if (empty(trim($_GET["trans_id"]))) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => 'Thiếu trans_id'
        )));
    }
    if (!$getUser = $db->get_row("SELECT * FROM `users` WHERE `token` = '".Anti_xss($_GET['token'])."'  ")) {
        die(json_encode(['status' => 'error', 'msg' => 'Token không hợp lệ']));
    }
    $trans_id = isset($_GET['trans_id']) ; 0;
    // Query all active cronjob servers
    $getData = $db->get_list("SELECT * FROM `cronjobs` 
    WHERE `user_id` = '". $getUser['id'] ."' AND `id` = '{$id}'");
    
    if ($getData) {
        $data = [];
        foreach ($getData as $row) {
            $data[] = [
                'trans_id' => $row['trans_id'], // Cast to string to match JSON example
                'url' => $row['url'],
                'server' => $row['server_id'], // Ensure numeric format
                'second' => preg_match('/^\*\/(\d+)/', trim($row['cron_expression']), $m) ? (string)$m[1] : null,
                'status' => $row['status'],
                'response' => $row['status_code'],
                'created_at' => $row['created_at'],
                'expired_date' => $row['expires_at'],
                'expired_timestamp' => $row['expires_at'] ? strtotime($row['expires_at']) : 0,
                'last_run' => $row['last_run'],
                'lastrun_timestamp' => $row['last_run'] ? strtotime($row['last_run']) : 0
               
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Thành công',
            'data' => $data
        ]);
    }
}