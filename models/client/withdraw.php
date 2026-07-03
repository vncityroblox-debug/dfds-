<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        if ($db->site('status_ref') != 1) {
            die(JsonMsg('error', 'Chức năng rút tiền đang bảo trì'));
        }
        if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
            die(jsonMsg('error', 'Invalid CSRF Protection Token'));
        }
        if(check_license($db->site('license'))['status'] == 'error'){
           die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
        }
        if ($db->site('status_demo') == 1) {
            die(JsonMsg('error', 'Đây là trang web demo, bạn không thể thực hiện chức năng này'));
        }
        if (empty($_POST['bank'])) {
            die(JsonMsg('error', 'Vui lòng chọn ngân hàng cần rút'));
        }
        if (empty($_POST['stk'])) {
            die(JsonMsg('error', 'Vui lòng nhập số tài khoản cần rút'));
        }
        if (empty($_POST['name'])) {
            die(JsonMsg('error', 'Vui lòng nhập tên chủ tài khoản'));
        }
        if (empty($_POST['amount'])) {
            die(JsonMsg('error', 'Vui lòng nhập số tiền cần rút'));
        }
        if($_POST['amount'] < $db->site('minrut_ref')){
            die(JsonMsg('error', 'Số tiền rút tối thiểu phải là'.' '.format_cash($db->site('minrut_ref'))));
        }
        if($data_user['ref_money'] < $_POST['amount']){
            die(JsonMsg('error', 'Số dư hoa hồng khả dụng của bạn không đủ'));
        }
        $amount =  Anti_xss(preg_replace('/\D/', '',$_POST['amount']));
        $trans_id = random('123456789QWERTYUIOPASDFGHJKLZXCVBNM', 6);
        $isTru = $db->tru('users', 'ref_money', $amount, " `id` = '".$data_user['id']."' ");
        if($isTru){
            $db->insert('log_ref', [
                'user_id'       => $data_user['id'],
                'reason'        => 'Rút số dư hoa hồng #'.$trans_id,
                'sotientruoc'   => $data_user['ref_money'],
                'sotienthaydoi' => $amount,
                'sotienhientai' => $data_user['ref_money'] - $amount,
                'created_at'    => gettime()
            ]);
            if(getRowUser($data_user['id'], 'ref_money') < 0){
                Banned($data_user['id'], 'Gian lận khi rút số dư hoa hồng');
                die(JsonMsg('error', 'Bạn đã bị khoá tài khoản vì gian lận'));
            }
            $isInsert = $db->insert('withdraw_ref', [
                'trans_id'  => $trans_id,
                'user_id'   => $data_user['id'],
                'bank'      => Anti_xss($_POST['bank']),
                'stk'       => Anti_xss($_POST['stk']),
                'name'      => Anti_xss($_POST['name']),
                'amount'    => Anti_xss($_POST['amount']),
                'status'    => 0,
                'create_gettime'    => gettime(),
                'update_gettime'    => gettime(),
                'reason'    => NULL
            ]);
            if($isInsert){  
            $my_text = $db->site("noti_affiliate_withdraw");
                    $replacements = [
                        '{domain}' => $_SERVER["SERVER_NAME"],
                        '{username}' => $data_user['username'],
                        '{bank}' => $_POST['bank'],
                        '{account_number}' => $_POST['stk'],
                        '{account_name}' => $_POST['name'],
                        '{amount}' => format_cash($_POST['amount']),
                        '{ip}' => myip(),
                        '{time}' => gettime()
                    ];
                    $my_text = str_replace(array_keys($replacements), array_values($replacements), $my_text);
                    sendMessAdmin($my_text);
                die(JsonMsg('success', 'Tạo yêu cầu rút tiền thành công, vui lòng đợi ADMIN xử lý'));
            }
            die(JsonMsg('error', 'ERROR 1 - Phát hiện lỗi khi rút tiền, vui lòng liên hệ ADMIN'));
        }else{
            die(JsonMsg('error', 'ERROR 2 - Phát hiện lỗi khi rút tiền, vui lòng liên hệ ADMIN'));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
