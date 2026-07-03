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
    if (!$getUser = $db->get_row("SELECT * FROM `users` WHERE `token` = '".Anti_xss($_GET['token'])."'  ")) {
        die(json_encode(['status' => 'error', 'msg' => 'Token không hợp lệ']));
    }
    // Query all active cronjob servers
    $getData = $db->get_list("SELECT * FROM `server_cronjobs` WHERE `status` = 1");
    
    if ($getData) {
        $data = [];
        foreach ($getData as $row) {
            $data[] = [
                'id' => (string)$row['id'], // Cast to string to match JSON example
                'name' => $row['name'],
                'price' => (float)$row['price'], // Ensure numeric format
                'quantity' => (int)($row['usage_limit']),
                'limit_second' => 1
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Thành công',
            'data' => $data
        ]);
    } else {
        exit(json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy dữ liệu'
        ]));
    }
} else {
    exit(json_encode([
        'status' => 'error',
        'message' => 'Phương thức yêu cầu không hợp lệ'
    ]));
}