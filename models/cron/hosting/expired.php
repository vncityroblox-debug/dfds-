<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

foreach ($db->get_list("SELECT * FROM `purchased_hosting` WHERE `status` = 'active'") as $data) {
    if (time() > $data['end_date']) {
        $whm = json_decode($data['server_whm'], true);
        if (!$check_server = $db->get_row("SELECT * FROM `whm_info` WHERE `username` = '" . $whm['username'] . "'")) {
            die(JsonMsg('error', 'Máy chủ không tồn tại'));
        }
        $banned = suspendacctHostingViaAPI($check_server['ip'], $check_server['username'], $check_server['password'], $data['username'], 'Tạm khóa do hết hạn'); // khóa hosting
        if (isset($banned['metadata']['result'])) {
            if ($banned['metadata']['result'] == 1) {
                $db->update("purchased_hosting", array(
                    'status' => 'expired',
                ), " `id` = '" . $data['id'] . "' ");
                insert_log($data['user_id'], "Hệ thống đã tạm khóa hosting đang sử dụng tên miền " . $data['domain_name'] . " do hết hạn sử dụng");
            } else {
                continue;
            }
        }
    }
}
