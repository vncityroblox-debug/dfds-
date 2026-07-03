<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

foreach ($db->get_list("SELECT * FROM `purchased_hosting` WHERE `status` = 'expired'") as $data) {
    $end_date = $data['end_date'];
    // Calculate the timestamp for 3 days after the end date
    $three_days_after_end = $end_date + (3 * 24 * 60 * 60);

    if (time() > $three_days_after_end) {
        $whm = json_decode($data['server_whm'], true);
        if (!$check_server = $db->get_row("SELECT * FROM `whm_info` WHERE `username` = '" . $whm['username'] . "'")) {
            die(JsonMsg('error', 'Máy chủ không tồn tại'));
        }

        $remove = removeHostingViaAPI($check_server['ip'], $check_server['username'], $check_server['password'], $data['username'], $data['domain_name']);
        if (isset($remove['metadata']['result'])) {
            if ($remove['metadata']['result'] == 1) {
                $isRemove = $db->remove("purchased_hosting", " `id` = '".$data['id']."' ");
                if ($isRemove) {
                    insert_log($data_user['id'], "Hệ thống tự động hực hiện xóa hosting có tên miền: " . $data['domain_name']." khi đã quá 3 ngày không gia hạn");
                }
            } else {
                continue;
            }
        }
    }
}
