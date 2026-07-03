<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if ($db->site("card_status") != 1) {
    exit("status_card_off");
}
if (isset($_GET["request_id"]) && isset($_GET["callback_sign"])) {
    $status = Anti_xss($_GET["status"]);
    $message = Anti_xss($_GET["message"]);
    $request_id = Anti_xss($_GET["request_id"]);
    $declared_value = Anti_xss($_GET["declared_value"]);
    $value = Anti_xss($_GET["value"]);
    $amount = Anti_xss($_GET["amount"]);
    $code = Anti_xss($_GET["code"]);
    $serial = Anti_xss($_GET["serial"]);
    $telco = Anti_xss($_GET["telco"]);
    $trans_id = Anti_xss($_GET["trans_id"]);
    $callback_sign = Anti_xss($_GET["callback_sign"]);
    if ($callback_sign != md5($db->site("card_partner_key") . $code . $serial)) {
        exit("callback_sign_error");
    }
    if (!($row = $db->get_row(" SELECT * FROM `cards` WHERE `trans_id` = '" . $request_id . "' AND `status` = 'pending' "))) {
        exit("request_id_error");
    }
    if (!($getUser = $db->get_row(" SELECT * FROM `users` WHERE `id` = '" . $row["user_id"] . "' AND `banned` = 0 "))) {
        exit("user không hợp lệ");
    }
    if ($status == 1) {
        if ($db->site("card_ck") == 0) {
            $price = $amount;
        } else {
            $price = $value - $value * $db->site("card_ck") / 100;
        }
        $db->update("cards", ["status" => "completed", "price" => $price, "update_date" => gettime()], " `id` = '" . $row["id"] . "' ");
        $isCong = PlusCredits($row["user_id"], $price, "Nạp thẻ cào Seri " . $row["serial"] . " - Pin " . $row["pin"], "TOPUP_CARD_" . $row["pin"]);
        if ($isCong) {
            if ($db->site("status_ref") == 1 && $getUser["ref_id"] != 0) {
                addRef($getUser['id'], $price, 'Hoa hồng thành viên');
            }
            $my_text = $db->site("noti_recharge");
            $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
            $my_text = str_replace("{username}", $getUser["username"], $my_text);
            $my_text = str_replace("{method}", $telco, $my_text);
            $my_text = str_replace("{amount}", format_cash($amount), $my_text);
            $my_text = str_replace("{price}", format_cash($price), $my_text);
            $my_text = str_replace("{time}", gettime(), $my_text);
            sendMessAdmin($my_text);
            exit("payment.success");
        }
        exit("thẻ này đã được cộng tiền rồi");
    }
    $db->update("cards", ["status" => "error", "price" => 0, "update_date" => gettime(), "reason" => "Thẻ cào không hợp lệ hoặc đã được sử dụng"], " `id` = '" . $row["id"] . "' ");
    exit("payment.error");
}

?>