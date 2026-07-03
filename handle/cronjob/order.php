<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
header('Content-Type: application/json');
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
$update_query = "UPDATE cronjobs 
                 SET status = 'expired' 
                 WHERE expires_at <= NOW() AND status = 'active'";
$db->query($update_query); // Execute the update query
// Lấy tất cả cronjobs chưa hết hạn hoặc đang hoạt động với trạng thái 'active'
$query = "SELECT c.*, s.name as server_name, s.price as server_price
          FROM cronjobs c
          JOIN server_cronjobs s ON c.server_id = s.id
          WHERE c.expires_at > NOW() AND c.status = 'active'"; // Lấy các cronjob còn hạn sử dụng và có trạng thái 'active'

$cronjobs = $db->get_list($query); // Sử dụng phương thức get_list

// Chuyển đổi dữ liệu để phù hợp với định dạng mong muốn
foreach ($cronjobs as &$cronjob) {
    $cronjob = [
        'id' => $cronjob['id'],
        'url' => $cronjob['url'],
        'method' => $cronjob['method'],
        'cron_expression' => $cronjob['cron_expression'],
        'server' => [
            'id' => $cronjob['server_id'],
            'name' => $cronjob['server_name'],
            'price' => $cronjob['server_price'],
        ],
        'headers' => $cronjob['headers'],
        'body' => $cronjob['body'],
        'last_run' => $cronjob['last_run'],
        'expires_at' => $cronjob['expires_at']
    ];
}

// Trả về kết quả dưới dạng JSON
echo json_encode([
    'status' => 'success',
    'data' => $cronjobs
]);
