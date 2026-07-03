<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

// Cập nhật os_vps
$osvps = osCloudVps();
if (isset($osvps['error']) && $osvps['error'] == 0 && !empty($osvps['data'])) {
    $update1 = $db->update("options", array(
        'value' => json_encode($osvps)
    ), " `key` = 'os_vps' ");
    if ($update1) {
        echo '[<b style="color:blue">-</b>] Cập nhật os_vps thành công.' . PHP_EOL;
    } else {
        echo '[<b style="color:red">x</b>] Cập nhật os_vps thất bại.' . PHP_EOL;
    }
} else {
    echo '[<b style="color:red">x</b>] Lỗi khi lấy danh sách OS từ osCloudVps: ' . ($osvps['message'] ?? 'Không có dữ liệu') . PHP_EOL;
    error_log("osCloudVps error: " . json_encode($osvps));
}

// Cập nhật os_vps_cloudnest
$osvps_cloudnest = osCloudVpsCloudNest();
if (isset($osvps_cloudnest['error']) && $osvps_cloudnest['error'] == 0 && !empty($osvps_cloudnest['data'])) {
    $update2 = $db->update("options", array(
        'value' => json_encode($osvps_cloudnest)
    ), " `key` = 'os_vps_cloudnest' ");
    if ($update2) {
        echo '[<b style="color:blue">-</b>] Cập nhật os_vps_cloudnest thành công.' . PHP_EOL;
    } else {
        echo '[<b style="color:red">x</b>] Cập nhật os_vps_cloudnest thất bại.' . PHP_EOL;
    }
} else {
    echo '[<b style="color:red">x</b>] Lỗi khi lấy danh sách OS từ osCloudVpsCloudNest: ' . ($osvps_cloudnest['message'] ?? 'Không có dữ liệu') . PHP_EOL;
    error_log("osCloudVpsCloudNest error: " . json_encode($osvps_cloudnest));
}

// Cập nhật os_vps_h2cloud
$osvps_h2cloud = osCloudVpsH2();
if (isset($osvps_h2cloud['error']) && $osvps_h2cloud['error'] == 0 && !empty($osvps_h2cloud['data'])) {
    $update3 = $db->update("options", array(
        'value' => json_encode($osvps_h2cloud)
    ), " `key` = 'os_vps_h2cloud' ");
    if ($update3) {
        echo '[<b style="color:blue">-</b>] Cập nhật os_vps_h2cloud thành công.' . PHP_EOL;
    } else {
        echo '[<b style="color:red">x</b>] Cập nhật os_vps_h2cloud thất bại.' . PHP_EOL;
    }
} else {
    echo '[<b style="color:red">x</b>] Lỗi khi lấy danh sách OS từ osCloudVpsH2: ' . ($osvps_h2cloud['message'] ?? 'Không có dữ liệu') . PHP_EOL;
    error_log("osCloudVpsH2 error: " . json_encode($osvps_h2cloud));
}