<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        if ($data_user['level'] != 'admin') {
            die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
        }
        if (check_license($db->site('license'))['status'] == 'error') {
            die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
        }

        $data = [
            'package_name' => Anti_xss($_POST['package_name']),
            'storage' => Anti_xss($_POST['storage']),
            'bandwidth' => Anti_xss($_POST['bandwidth']),
            'ssl' => Anti_xss($_POST['ssl']),
            'domains' => Anti_xss($_POST['domains']),
            'aliases' => Anti_xss($_POST['aliases']),
            'other_params' => Anti_xss($_POST['other_params']),
            'location' => Anti_xss($_POST['location']),
            'price' => Anti_xss($_POST['price']),
            'period' => Anti_xss($_POST['period']),
            'status' => 1 // Mặc định là active
        ];

        // Kiểm tra các trường bắt buộc
        foreach ($data as $key => $value) {
            if (empty($value) && $key != 'status') {
                die(JsonMsg('error', 'Vui lòng điền đầy đủ thông tin'));
            }
        }

        if ($db->insert('server_host', $data)) {
            insert_log($data_user['id'], "Thêm gói hosting [" . $data['package_name'] . "] vào hệ thống");
            die(JsonMsg('success', 'Thêm gói hosting thành công'));
        } else {
            die(JsonMsg('error', 'Thêm gói hosting thất bại'));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
error_reporting(E_ALL);
ini_set('display_errors', 1);