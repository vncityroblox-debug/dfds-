<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
$array = array();
$listCloudVps = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE `status` = 'on' AND `notification_expired` = 'no'");
foreach ($listCloudVps as $data) {
    array_push($array, (int)$data['vps_id']);
}

if (!empty($array)) {
    $jsonString = json_encode($array);
    $result = infoListVps($jsonString);

    if (isset($result['error']) && $result['error'] == 0) {
        foreach ($result['data'] as $infovps) {
            $data = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `vps_id` = '{$infovps['vps-id']}'");
            if ($infovps['vps-status'] == 'expire') {
                $htmlTemplate = file_get_contents(DOMAIN . '/libs/mails/cloudvps/expired', false, stream_context_create($arrContextOptions));
                $htmlContent = str_replace(
                    ['{{user_id}}', '{{service_name}}', '{{ip}}', '{{amount}}', '{{purchase_date}}', '{{expiry_date}}', '{{service_url}}', '{{support_phone}}', '{{support_email}}'],
                    [
                        htmlspecialchars(getRowUser($data['user_id'], 'username')),
                        htmlspecialchars($infovps['text-config']),
                        htmlspecialchars($infovps['ip']),
                        number_format($data['price'], 0, ',', '.') . 'đ',
                        htmlspecialchars($infovps['date_create']),
                        htmlspecialchars($infovps['next_due_date']),
                        DOMAIN . '/history/vps/dashboard/' . $data['id'],
                        $db->site('hotline'),
                        $db->site('email')
                    ],
                    $htmlTemplate
                );
                $mail_nhan = getRowUser($data['user_id'], 'email');
                $ten_nhan = 'Nguyễn Văn A';
                $chu_de = 'Thông báo: Dịch vụ của bạn đã hết hạn';

                $result = sendCSM($mail_nhan, $ten_nhan, $chu_de, $htmlContent);

                $db->query("UPDATE tbl_purchased_cloudvps SET `status` = 'expire', notification_expired = 'yes' WHERE vps_id = '{$infovps['vps-id']}'");
            }
           
        }
    }
}
