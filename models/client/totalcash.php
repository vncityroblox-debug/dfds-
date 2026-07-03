<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
        die(jsonMsg('error', 'Invalid CSRF Protection Token'));
    }
    if (isset($_POST['action']) && $_POST['action'] == 'hosting') {
        if (empty($_POST["id"])) {
            die(JsonMsg('error', 'Vui lòng chọn gói hosting'));
        }
        if (empty($_POST["selectedMonths"])) {
            die(JsonMsg('error', 'Vui lòng chọn thời gian đăng ký'));
        }
        if (!($product = $db->get_row("SELECT * FROM `hosting_packages` WHERE `id` = '" . Anti_xss($_POST["id"]) . "' AND `status` = 1 "))) {
            die(JsonMsg('error', 'Gói hosting không tồn tại'));
        }
        $ck = 0;
        if ($user) {
            if ($getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $data_user['id'] . "' AND `banned` = 0 ")) {
                $ck = $getUser['chietkhau'];
            }
        }
        $month = Anti_xss($_POST["selectedMonths"]);
        $money = $month * $product["price"];

        $money = $money - $money * $ck / 100;
        $total = $money;
        if (isset($_POST['coupon'])) {
            $discount = $user ? checkCoupon(Anti_xss($_POST['coupon']), $data_user['id'], $total) : 0;
        }
        if (isset($discount)) {
            $total = $money - $money * $discount / 100;
        }

        exit(json_encode(["status" => "success", "money" => format_cash($money), "discount" => format_cash($money - $total), "discount_number" => $discount, "pay" => format_cash($total)]));
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'cloudvps') {
        if (empty($_POST['vpsid'])) {
            die(json_encode([
                'status' => 'success',
                'type'  => '',
                'total' => format_cash(0),
                'msg'   => ''
            ]));
        }
        $vpsid = Anti_xss($_POST['vpsid']);
        if ($row = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `id` = '" . $vpsid . "' ")) {
            $detail = json_decode($row['price'], true);
            $ck = 0;
            if ($user) {
                if ($getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $data_user['id'] . "' AND `banned` = 0 ")) {
                    $ck = $getUser['chietkhau'];
                }
            }

            $billingcycle = Anti_xss($_POST['billingcycle']);

            $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu']));
            $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram']));
            $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk']));
            if ($row['site'] == 'VNCLOUD') {
                $cash_cpu = getAddonPrice('VNCLOUD', 'addon_cpu', $cpu, $billingcycle);
                $cash_ram = getAddonPrice('VNCLOUD', 'addon_ram', $ram, $billingcycle);
                $cash_disk = getAddonPrice('VNCLOUD', 'addon_disk', $disk, $billingcycle);
            } else if ($row['site'] == 'CLOUDNEST') {
                $cash_cpu = getAddonPrice('CLOUDNEST', 'addon_cpu', $cpu, $billingcycle);
                $cash_ram = getAddonPrice('CLOUDNEST', 'addon_ram', $ram, $billingcycle);
                $cash_disk = getAddonPrice('CLOUDNEST', 'addon_disk', $disk, $billingcycle);
            } else if ($row['site'] == 'H2CLOUD') {
                $cash_cpu = getAddonPrice('H2CLOUD', 'addon_cpu', $cpu, $billingcycle);
                $cash_ram = getAddonPrice('H2CLOUD', 'addon_ram', $ram, $billingcycle);
                $cash_disk = getAddonPrice('H2CLOUD', 'addon_disk', $disk, $billingcycle);
            }


            $total = $detail[$billingcycle]['amount'] + $cash_cpu + $cash_ram + $cash_disk;
            $total -= $total * $ck / 100;

            if (isset($_POST['coupon'])) {
                $discount = $user ? checkCoupon(Anti_xss($_POST['coupon']), $data_user['id'], $total) : 0;
            }
            if (isset($discount)) {
                $total = $total - $total * $discount / 100;
            }

            die(json_encode([
                'status'        => 'success',
                'total'         => format_cash($total)
            ]));
        }
    }
    if (isset($_POST['action']) && $_POST['action'] == 'upgrade') {
        if ($user) {
            if (empty($_POST['id'])) {
                die(json_encode([
                    'status' => 'success',
                    'type'  => '',
                    'total' => format_cash(0),
                    'msg'   => ''
                ]));
            }
            $id = Anti_xss($_POST['id']);
            if ($row = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `id` = '" . $id . "' AND `user_id` = '{$data_user['id']}'")) {
                $ck = 0;
                if ($user) {
                    if ($getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $data_user['id'] . "' AND `banned` = 0 ")) {
                        $ck = $getUser['chietkhau'];
                    }
                }
                $detail = json_decode(decryptAES($row['info']), true);
                $billingcycle = $row['billingcycle'];

                $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu']));
                $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram']));
                $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk']));

                if ($row['site'] == 'VNCLOUD') {
                    $cash_cpu = getAddonPrice('VNCLOUD', 'addon_cpu', $cpu, $billingcycle);
                    $cash_ram = getAddonPrice('VNCLOUD', 'addon_ram', $ram, $billingcycle);
                    $cash_disk = getAddonPrice('VNCLOUD', 'addon_disk', $disk, $billingcycle);
                } else if ($row['site'] == 'CLOUDNEST') {
                    $cash_cpu = getAddonPrice('CLOUDNEST', 'addon_cpu', $cpu, $billingcycle);
                    $cash_ram = getAddonPrice('CLOUDNEST', 'addon_ram', $ram, $billingcycle);
                    $cash_disk = getAddonPrice('CLOUDNEST', 'addon_disk', $disk, $billingcycle);
                } else if ($row['site'] == 'H2CLOUD') {
                    $cash_cpu = getAddonPrice('H2CLOUD', 'addon_cpu', $cpu, $billingcycle);
                    $cash_ram = getAddonPrice('H2CLOUD', 'addon_ram', $ram, $billingcycle);
                    $cash_disk = getAddonPrice('H2CLOUD', 'addon_disk', $disk, $billingcycle);
                }

                $total = $cash_cpu + $cash_ram + $cash_disk;
                $total = $total - $total * $ck / 100;

                $billingcycleday = $row['billingcycleday'];

                $dayleft = preg_replace('/\D/', '', $detail['day-left']);

                $hesochia = $dayleft / $billingcycleday;

                $hesonhan = custom_round($hesochia, floor($hesochia));
                $total_giahan = $total * $hesonhan;

                die(json_encode([
                    'status'        => 'success',
                    'total'         => format_cash($total_giahan)
                ]));
            }
        } else {
            die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
        }
    }
}
