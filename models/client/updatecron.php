<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Correct the condition here
    $id = Anti_xss($_POST['id']);
    $action = Anti_xss($_POST['action']);
    
    if (empty($id) || empty($action)) {
        die(JsonMsg('error', 'Vui lòng chọn dữ liệu'));
    }
    if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
    if($db->site('cron_status') == 0){
       die(JsonMsg('error', 'Cron đang bảo trì'));
    }
    switch ($action) {
        case 'pause':
            if (!$user) {
                die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
            }
            $cronjob = $db->get_row("SELECT * FROM `cronjobs` WHERE `id` = {$id}");
            if (!$cronjob) {
                die(JsonMsg('error', 'Đơn hàng không tồn tại'));
            }
            
            $isUpdate = $db->update("cronjobs", array(
                'status' => 'paused'
            ), " `id` = '" . $id . "' ");
            
            if ($isUpdate) {
                die(JsonMsg('success', 'Đã tạm dừng thành công'));
            } else {
                die(JsonMsg('error', 'Đã xảy ra lỗi cập nhập dữ liệu'));
            }
            break;
        case 'play':
                if (!$user) {
                    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
                }
                $cronjob = $db->get_row("SELECT * FROM `cronjobs` WHERE `id` = {$id}");
                if (!$cronjob) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                $isUpdate = $db->update("cronjobs", array(
                    'status' => 'active'
                ), " `id` = '" . $id . "' ");
                if ($isUpdate) {
                    die(JsonMsg('success', 'Đưa vào hàng đợi chạy thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi cập nhập dữ liệu'));
                }
                break;
        case 'edit':
                if (!$user) {
                    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
                }
                $isUpdate = $db->update("cronjobs", ["url" => !empty($_POST["url"]) ? Anti_xss($_POST["url"]) : 0, "cron_expression" => !empty($_POST["cron_expression"]) ? Anti_xss($_POST["cron_expression"]) : 0, "method" => !empty($_POST["method"]) ? Anti_xss($_POST["method"]) : 0, "headers" => !empty($_POST["headers"]) ? Anti_xss($_POST["headers"]) : 0, "body" => !empty($_POST["body"]) ? Anti_xss($_POST["body"]) : 0, "server_id" => !empty($_POST["server"]) ? Anti_xss($_POST["server"]) : 0], " `id` = '" . $id . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Thay đổi dữ liệu đơn cronjob");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
                case 'extend':
            $months = Anti_xss($_POST['month']);
            if (!$user) {
                die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
            }
            if (!$months) {
                die(JsonMsg('error', 'Vui lòng chọn thời gian gia hạn'));
            }
            $getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $data_user['username'] . "' AND `banned`=0");
            $cronjob = $db->get_row("SELECT * FROM `cronjobs` WHERE `id` = {$id} AND `user_id` = '{$getUser['id']}'");
            if (!$cronjob) {
                die(JsonMsg('error', 'Đơn hàng không tồn tại'));
            }
            
            $month = $months;
            $current_expiry = strtotime($cronjob['expires_at']);
            $now = time();

            if ($current_expiry > $now) {
            $new_expiry = strtotime("+{$month} months", $current_expiry);
            } else {
            $new_expiry = strtotime("+{$month} months", $now);
           }

           $timeto = date('Y-m-d H:i:s', $new_expiry);
           $total = $month * $cronjob['payment'];
           if ($total > $getUser['money']) {
               die(JsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ', vui lòng nạp thêm để thực hiện'));
           }
       
 
        $isMoney = RemoveCredits($getUser['id'], $total, 'Gia hạn cron #' . $cronjob['url']);
        if ($isMoney) {
            // Cập nhật thời gian hết hạn mới
            $isUpdate = $db->update("cronjobs", ['expires_at' => $timeto, 'status' => 'active'], " `id` = '{$id}' ");
            if ($isUpdate) {
                die(JsonMsg('success', 'Gia hạn thành công'));
            } else {
                die(JsonMsg('error', 'Đã xảy ra lỗi cập nhật dữ liệu'));
            }
        }
        break;
        default:
    }
} else {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}
?>
