<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

$purchased_hostings = $db->get_list("SELECT * FROM `purchased_hosting` WHERE `status` = 'expired'");

foreach ($purchased_hostings as $data) {
    if ($data['extend'] != 1) {
        continue;
    }

    $whm = json_decode($data['server_whm'], true);
    $check_server = $db->get_row("SELECT * FROM `whm_info` WHERE `username` = '" . $whm['username'] . "'");
    if (!$check_server) {
        continue;
    }

    $current_time = time();
    $end_date = max($data['end_date'], $current_time);
    $timeto = $end_date + 2592000 * $data['month'];
    $total = $data['price'];

    $user = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $data['user_id'] . "' AND `banned` = '0'");
    if (!$user || $total > $user['money']) {
        continue;
    }

    if (RemoveCredits($user['id'], $total, "Hệ thống tự động gia hạn hosting " . $data['domain_name'] . " thêm " . $data['month'] . " tháng")) {
        $unsub = unsuspendacctHostingViaAPI($check_server['ip'], $check_server['username'], $check_server['password'], $data['username']);
        if (isset($unsub['metadata']['result']) && $unsub['metadata']['result'] == 1) {
            $db->update("purchased_hosting", [
                'total' => $data['total'] + $total,
                'end_date' => $timeto,
                'status' => 'active',
            ], " `id` = '" . $data['id'] . "' ");
        }
    }
}
?>
