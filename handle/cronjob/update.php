<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(check_license($db->site('license'))['status'] == 'error'){
        die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
    }
    $jsonString = file_get_contents("php://input");
    $data = json_decode($jsonString, true);
    if ($data !== null) {
        $cronjob_id = Anti_xss($data['id']); // ID của cron job
        $status_code = Anti_xss($data['status_code']); // Mã trạng thái
        $last_run = date('Y-m-d H:i:s'); // Thời gian chạy cuối cùng

        // Kiểm tra xem ID cron job có tồn tại không
        $query = "SELECT * FROM cronjobs WHERE id = '$cronjob_id'";
        $result = $db->get_row($query);

        if ($result) {
            // Cập nhật last_run và status_code
            $update_data = [
                'last_run' => $last_run,
                'status_code' => $status_code
            ];
            $where = "id = '$cronjob_id'"; // Điều kiện để cập nhật
            if ($db->update('cronjobs', $update_data, $where)) {
                echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Cập nhật thất bại!']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cron job không tồn tại!']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ!']);
}
