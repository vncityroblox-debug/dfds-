<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Access Denied']));
}

if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid CSRF Token']));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if($db->site('cron_status') == 0){
 die(JsonMsg('error', 'Cron đang bảo trì'));
}
if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}
$getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . ($data_user['username'] ?? '') . "' AND `banned`=0");

            $url = isset($_POST['url']) ? Anti_xss($_POST['url']) : null;
            $cron_expression = isset($_POST['cron_expression']) ? Anti_xss($_POST['cron_expression']) : null;
            $method = isset($_POST['method']) ? Anti_xss($_POST['method']) : null;
            $servers = isset($_POST['server']) ? Anti_xss($_POST['server']) : null;
            $months = isset($_POST['months']) ? Anti_xss($_POST['months']) : null;
            
            $body = isset($_POST['body']) ? Anti_xss($_POST['body']) : null;
            $headers = isset($_POST['headers']) ? Anti_xss($_POST['headers']) : null;
            
            
            if (!$url || !$cron_expression || !$method || !$servers || !$months) {
               die(JsonMsg('error', 'Vui lòng điền đủ nội dung'));
            }
            $server = $db->get_row("SELECT * FROM server_cronjobs WHERE id = $servers");

            if (!$server) {
                die(JsonMsg('error', 'Máy chủ không tồn tại!'));
            }

            $price_per_month = $server['price'];
            $discount = $server['discount_percent'];
            $discount_valid_until = $server['discount_valid_until'];

            // Kiểm tra xem giảm giá có hợp lệ không
            if ($discount > 0 && strtotime($discount_valid_until) > time()) {
                $final_price_per_month = $price_per_month - ($price_per_month * $discount / 100);
            } else {
                $final_price_per_month = $price_per_month;
            }

           
            $ck = $getUser['chietkhau'];
            $total_price = $final_price_per_month * $months;

            $total = $total_price - $total_price * $ck / 100;

            $expires_at = date('Y-m-d H:i:s', strtotime("+$months months"));

            if ($total < 0) {
                die(JsonMsg('error', 'Dữ liệu không hợp lệ'));
            }
            if (0 >= $server['usage_limit']) {
                die(JsonMsg('error', 'Máy chủ đã đầy, vui lòng liên hệ admin để xử lý!'));
            }
            if ($total > $getUser['money']) {
                die(JsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ', vui lòng nạp thêm để thực hiện'));
            }
            $isMoney = RemoveCredits($getUser['id'], $total, "Thuê dịch vụ cronjob với link " . $url . " giá " . format_cash($total));
            if ($isMoney) {
                $data = [
                    'trans_id' => random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 11)
,
                    'user_id' => $getUser['id'],
                    'url' => $url,
                    'method' => $method,
                    'cron_expression' => $cron_expression,
                    'server_id' => $servers,
                    'expires_at' => $expires_at,
                    'body' => $body,
                    'headers' => $headers,
                    'payment' => $total
                ];
                if ($db->insert('cronjobs', $data)) {
                $db->tru("server_cronjobs", 'usage_limit', 1, " `id` = '".$server['id']."' ");
                    die(JsonMsg('success', 'Thêm cronjob thành công!'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi khi thêm dữ liệu đơn hàng'));
                }
            }