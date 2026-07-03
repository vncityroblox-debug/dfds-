<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($user) {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        if ($data_user['level'] != 'admin') {
            die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
        }
        
        if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
        $id = Anti_xss($_POST['id']);
        $status = Anti_xss($_POST['status']);
        $note = Anti_xss($_POST['note']);
        if (!$row = $db->get_row("SELECT * FROM `withdraw_ref` WHERE `id` = '$id' ")) {
            die(JsonMsg('error', 'ID lịch sử không tồn tại trong hệ thống!'));
        }

        $isUpdate = $db->update("withdraw_ref", [
            'status' => $status,
            'reason' => $note,
            'update_gettime' => gettime(),
        ], " `id` = '" . $row['id'] . "' ");

        if ($isUpdate) {
            $db->insert("logs", [
                'user_id'       => $data_user['id'],
                'ip'            => myip(),
                'device'        => $_SERVER['HTTP_USER_AGENT'],
                'create_date'    => gettime(),
                'action'        => 'Chỉnh sửa trạng thái lịch sử rút tiền CTV (ID ' . $row['id'] . ')'
            ]);
            die(JsonMsg('success', 'Cập nhật thành công'));
        } else {
            die(JsonMsg('error', 'Cập nhật thất bại'));
        }
    } else {
        die(JsonMsg('error', 'Dữ liệu không hợp lệ!'));
    }
} else {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}
case 'withdrawProduct':
if ($user) {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        if ($data_user['level'] != 'admin') {
            die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
        }
        $id = Anti_xss($_POST['id']);
        $status = Anti_xss($_POST['status']);
        $note = Anti_xss($_POST['note']);
        if (!$row = $db->get_row("SELECT * FROM `withdraw_ref` WHERE `id` = '$id' ")) {
            die(JsonMsg('error', 'ID lịch sử không tồn tại trong hệ thống!'));
        }

        $isUpdate = $db->update("product_withdraw", [
            'status' => $status,
            'reason' => $note,
            'update_gettime' => gettime(),
        ], " `id` = '" . $row['id'] . "' ");

        if ($isUpdate) {
            $db->insert("logs", [
                'user_id'       => $data_user['id'],
                'ip'            => myip(),
                'device'        => $_SERVER['HTTP_USER_AGENT'],
                'create_date'    => gettime(),
                'action'        => 'Chỉnh sửa trạng thái lịch sử rút tiền CTV (ID ' . $row['id'] . ')'
            ]);
            die(JsonMsg('success', 'Cập nhật thành công'));
        } else {
            die(JsonMsg('error', 'Cập nhật thất bại'));
        }
    } else {
        die(JsonMsg('error', 'Dữ liệu không hợp lệ!'));
    }
} else {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}
break;