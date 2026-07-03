<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

// Kiểm tra phương thức yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy các tham số từ POST
    $action = Anti_xss($_POST['action']);
    $trans_id = Anti_xss($_POST['trans_id']);
    $token = Anti_xss($_POST['token']);
    
    // Kiểm tra nếu thiếu tham số
    if (empty($trans_id) || empty($action) || empty($token)) {
        die(JsonMsg('error', 'Vui lòng cung cấp đầy đủ tham số'));
    }

    // Kiểm tra tính hợp lệ của token
    if (!$getUser = $db->get_row("SELECT * FROM `users` WHERE `token` = '{$token}'")) {
        die(JsonMsg('error', 'Token không hợp lệ'));
    }

    // Kiểm tra sự tồn tại của cron job theo trans_id và user_id
    $cronjob = $db->get_row("SELECT * FROM `cronjobs` WHERE `trans_id` = '{$trans_id}' AND `user_id` = '{$getUser['id']}'");
    if (!$cronjob) {
        die(JsonMsg('error', 'Đơn hàng không tồn tại'));
    }

    // Xử lý các hành động API dựa trên tham số action
    switch ($action) {
        // API chỉnh sửa cron job
        case 'edit':
            $url = Anti_xss($_POST['url']);
            $second = (int)$_POST['second'];
            $cron_expression = $second ? "*/{$second}* * * * *" : $cronjob['cron_expression']; // Giả sử cron_expression theo chu kỳ giây

            // Cập nhật cronjob với url và cron_expression mới
            $isUpdate = $db->update("cronjobs", [
                'url' => $url,
                'cron_expression' => $cron_expression
            ], "trans_id = '{$trans_id}'");

            if ($isUpdate) {
                die(JsonMsg('success', 'Cập nhật thành công'));
            } else {
                die(JsonMsg('error', 'Đã xảy ra lỗi cập nhật dữ liệu'));
            }
            break;

        // API kích hoạt cron job
        case 'active':
            if ($cronjob['status'] == 'paused') {
                $isUpdate = $db->update("cronjobs", ['status' => 'active'], "trans_id = '{$trans_id}'");

                if ($isUpdate) {
                    die(JsonMsg('success', 'Đã kích hoạt thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi cập nhật dữ liệu'));
                }
            } else {
                die(JsonMsg('error', 'Cronjob đã đang hoạt động'));
            }
            break;

        // API tạm dừng cron job
        case 'stop':
            $isUpdate = $db->update("cronjobs", ['status' => 'paused'], "trans_id = '{$trans_id}'");

            if ($isUpdate) {
                die(JsonMsg('success', 'Đã dừng cron thành công'));
            } else {
                die(JsonMsg('error', 'Đã xảy ra lỗi cập nhật dữ liệu'));
            }
            break;

        // API gia hạn cron job
        case 'giahan':
            $months = Anti_xss($_POST['month']);
            if (empty($months)) {
                die(JsonMsg('error', 'Vui lòng cung cấp số tháng cần gia hạn'));
            }

            $current_expiry = strtotime($cronjob['expires_at']);
            $now = time();
            $new_expiry = strtotime("+{$months} months", max($current_expiry, $now));
            $timeto = date('Y-m-d H:i:s', $new_expiry);

            $total = $months * $cronjob['payment'];
            if ($total > $getUser['money']) {
                die(JsonMsg('error', 'Số dư của bạn không đủ'));
            }

            // Trừ tiền trong tài khoản người dùng
            $isMoney = RemoveCredits($getUser['id'], $total, 'Gia hạn cronjob #' . $cronjob['trans_id']);
            if ($isMoney) {
                $isUpdate = $db->update("cronjobs", ['expires_at' => $timeto, 'status' => 'active'], "trans_id = '{$trans_id}'");
                if ($isUpdate) {
                    die(json_encode([
    'status' => 'success',
    'message' => 'Gia hạn thành công',
    'data' => [
        'expired_date' => $timeto,
        'expired_timestamp' => $new_expiry
    ]
]));

                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi cập nhật dữ liệu'));
                }
            } else {
                die(JsonMsg('error', 'Không đủ tiền'));
            }
            break;

        default:
    }
}
?>
