<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

foreach ($db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE `status` = 'on' AND notified = 'no'") as $data) {
    $info = json_decode(decryptAES($data['info']),true);
   
    $nextDueDate = DateTime::createFromFormat('d-m-Y', $info['next_due_date']);
    $currentDate = new DateTime();
    $threeDaysBefore = (clone $nextDueDate)->modify('-3 days');

    if ($currentDate >= $threeDaysBefore && $currentDate < $nextDueDate) {
        $htmlTemplate = file_get_contents(DOMAIN . '/libs/mails/cloudvps/noti', false, stream_context_create($arrContextOptions));
        $htmlContent = str_replace(
            ['{{user_id}}', '{{service_name}}', '{{ip}}', '{{amount}}', '{{purchase_date}}', '{{expiry_date}}', '{{service_url}}', '{{support_phone}}', '{{support_email}}'],
            [
                htmlspecialchars(getRowUser($data['user_id'],'username')),
                htmlspecialchars($info['text-config']),
                htmlspecialchars($info['ip']),
                number_format($data['price'], 0, ',', '.') . 'đ',
                htmlspecialchars($info['date_create']),
                htmlspecialchars($info['next_due_date']),
                DOMAIN . '/history/vps/dashboard/' . $data['id'],
                $db->site('hotline'),
                $db->site('email')
            ],
            $htmlTemplate
        );
        $mail_nhan = getRowUser($data['user_id'],'email');
        $ten_nhan = 'Nguyễn Văn A';
        $chu_de = 'Thông báo: Dịch vụ của bạn sắp hết hạn';

        $result = sendCSM($mail_nhan, $ten_nhan, $chu_de, $htmlContent);

        $db->query("UPDATE tbl_purchased_cloudvps SET notified = 'yes' WHERE id = '{$data['id']}'");
    }
}
