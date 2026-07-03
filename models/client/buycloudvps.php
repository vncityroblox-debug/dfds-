<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
        die(jsonMsg('error', 'Invalid CSRF Protection Token'));
    }
    
if($db->site('vps_status') == 0){
 die(JsonMsg('error', 'VPSCLOUD đang bảo trì'));
}
    
    if ($user) {
        $vpsid = Anti_xss($_POST['vpsId']);
        $os = Anti_xss($_POST['os']);
        $billingcycle = Anti_xss($_POST['billingcycle']);
        try {
            if (empty($vpsid)) {
                die(JsonMsg('error', 'Vui lòng chọn gói vps cần mua'));
            }
            if (empty($os)) {
                die(JsonMsg('error', 'Quý khách cần điền hệ điều hành cho server đang đăng ký'));
            }
            if (empty($billingcycle)) {
                die(JsonMsg('error', 'Quý khách cần chọn thời gian cho server đang đăng ký'));
            }
            if (!$row = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `id`='" . $vpsid . "' AND `status` = 1")) {
                die(JsonMsg('error', 'Gói vps không tồn tại'));
            }
            $ck = 0;
            if ($user) {
                if ($getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $data_user['id'] . "' AND `banned` = 0 ")) {
                    $ck = $getUser['chietkhau'];
                }
            }
            $detail = json_decode($row['detail'], true);
            $pricing = json_decode($row['price'], true);

            if (!isset($pricing[$billingcycle])) {
                die(JsonMsg('error', 'Thời gian không hợp lệ'));
            }

            if ($row['site'] == 'VNCLOUD') {
                $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu']));
                $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram']));
                $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk']));

                $cash_cpu = getAddonPrice('VNCLOUD','addon_cpu', $cpu, $billingcycle);
                $cash_ram = getAddonPrice('VNCLOUD','addon_ram', $ram, $billingcycle);
                $cash_disk = getAddonPrice('VNCLOUD','addon_disk', $disk, $billingcycle);

                $total = $pricing[$billingcycle]['amount'] + $cash_cpu + $cash_ram + $cash_disk;
                $total = $total - $total * $ck / 100;
                $price = $total;
                if (isset($_POST['coupon'])) {
                    $discount = checkCoupon(Anti_xss($_POST['coupon']), $data_user['id'], $total);
                }
                if (isset($discount)) {
                    $total = $total - $total * $discount / 100;
                }
                if ($total < 0) {
                    die(JsonMsg('error', 'Dữ liệu không hợp lệ'));
                }
                if ($total > $data_user['money']) {
                    die(JsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ', vui lòng nạp thêm để thực hiện'));
                }
                $isMoney = RemoveCredits($data_user['id'], $total, "Mua gói vps " . $row['name'] . " giá " . format_cash($total));
                if ($isMoney) {
                    $productid = $detail['product_id'];
                    $osid = $os;
                    $data = createOrderCloud($productid, $billingcycle, $osid, $cpu, $ram, $disk * 10);

                    if (isset($data['error']) && $data['error'] == 0) {
                        insert_log($data_user['id'], "Mua gói vps " . $row['name'] . " giá " . format_cash($total));

                        $result = infoListVps($data['data'][0]['vps-id']);
                        $billingcycleday = 0;
                        if (isset($result['error']) && $result['error'] == 0) {
                            $billingcycleday = preg_replace('/\D/', '', $result['data'][0]['day-left']);
                        }
                        $db->insert("tbl_purchased_cloudvps", array(
                            'user_id' => $data_user['id'],
                            'billingcycle' => $billingcycle,
                            'billingcycleday' => $billingcycleday,
                            'vps_id' => $data['data'][0]['vps-id'],
                            'price' =>  $price,
                            'cost' => $data['total'],
                            'total_price' => $price,
                            'total_cost' => $data['total'],
                            'notified' => 'no',
                            'notification_expired' => 'no',
                            'notification_delete' => 'no',
                            'created_at' => gettime(),
                            'updated_at' => gettime(),
                            'site' => 'VNCLOUD'
                        ));
                        exit(json_encode(array('status' => 'success', 'msg' => $data['message'],'url' => '/user/history/vps')));
                    } else {
                        PlusCredits($data_user['id'], $total, "Hoàn tiền vps " . $row['name']);
                        exit(json_encode(array('status' => 'error', 'msg' => $data['message'])));
                    }
                }
            } else if ($row['site'] == 'CLOUDNEST') {
                $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu']));
                $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram']));
                $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk']));

                $cash_cpu = getAddonPrice('CLOUDNEST','addon_cpu', $cpu, $billingcycle);
                $cash_ram = getAddonPrice('CLOUDNEST','addon_ram', $ram, $billingcycle);
                $cash_disk = getAddonPrice('CLOUDNEST','addon_disk', $disk, $billingcycle);

                $total = $pricing[$billingcycle]['amount'] + $cash_cpu + $cash_ram + $cash_disk;
                $total = $total - $total * $ck / 100;
                $price = $total;
                if (isset($_POST['coupon'])) {
                    $discount = checkCoupon(Anti_xss($_POST['coupon']), $data_user['id'], $total);
                }
                if (isset($discount)) {
                    $total = $total - $total * $discount / 100;
                }
                if ($total < 0) {
                    die(JsonMsg('error', 'Dữ liệu không hợp lệ'));
                }
                if ($total > $data_user['money']) {
                    die(JsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ', vui lòng nạp thêm để thực hiện'));
                }
                $isMoney = RemoveCredits($data_user['id'], $total, "Mua gói vps " . $row['name'] . " giá " . format_cash($total));
                if ($isMoney) {
                    $productid = $detail['product_id'];
                    $osid = $os;
                    $data = createOrderCloudNest($productid, $billingcycle, $osid, $cpu, $ram, $disk * 10);

                    if (isset($data['error']) && $data['error'] == 0) {
                        insert_log($data_user['id'], "Mua gói vps platinum " . $row['name'] . " giá " . format_cash($total));

                        $result = infoListVpsCloudNest($data['data'][0]['vps-id']);
                        $billingcycleday = 0;
                        if (isset($result['error']) && $result['error'] == 0) {
                            $billingcycleday = preg_replace('/\D/', '', $result['data'][0]['day-left']);
                        }
                        $db->insert("tbl_purchased_cloudvps", array(
                            'user_id' => $data_user['id'],
                            'billingcycle' => $billingcycle,
                            'billingcycleday' => $billingcycleday,
                            'vps_id' => $data['data'][0]['vps-id'],
                            'price' =>  $price,
                            'cost' => $data['total'],
                            'total_price' => $price,
                            'total_cost' => $data['total'],
                            'notified' => 'no',
                            'notification_expired' => 'no',
                            'notification_delete' => 'no',
                            'created_at' => gettime(),
                            'updated_at' => gettime(),
                            'site' => 'CLOUDNEST'
                        ));
                        exit(json_encode(array('status' => 'success', 'msg' => $data['message'],'url' => '/user/history/platinum')));
                    } else {
                        PlusCredits($data_user['id'], $total, "Hoàn tiền vps " . $row['name']);
                        exit(json_encode(array('status' => 'error', 'msg' => $data['message'])));
                    }
                }
            } else if ($row['site'] == 'H2CLOUD') {
                $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu']));
                $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram']));
                $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk']));

                $cash_cpu = getAddonPrice('H2CLOUD','addon_cpu', $cpu, $billingcycle);
                $cash_ram = getAddonPrice('H2CLOUD','addon_ram', $ram, $billingcycle);
                $cash_disk = getAddonPrice('H2CLOUD','addon_disk', $disk, $billingcycle);

                $total = $pricing[$billingcycle]['amount'] + $cash_cpu + $cash_ram + $cash_disk;
                $total = $total - $total * $ck / 100;
                $price = $total;
                if (isset($_POST['coupon'])) {
                    $discount = checkCoupon(Anti_xss($_POST['coupon']), $data_user['id'], $total);
                }
                if (isset($discount)) {
                    $total = $total - $total * $discount / 100;
                }
                if ($total < 0) {
                    die(JsonMsg('error', 'Dữ liệu không hợp lệ'));
                }
                if ($total > $data_user['money']) {
                    die(JsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ', vui lòng nạp thêm để thực hiện'));
                }
                $isMoney = RemoveCredits($data_user['id'], $total, "Mua gói vps " . $row['name'] . " giá " . format_cash($total));
                if ($isMoney) {
                    $productid = $detail['product_id'];
                    $osid = $os;
                    $data = createOrderCloudH2($productid, $billingcycle, $osid, $cpu, $ram, $disk * 10);

                    if (isset($data['error']) && $data['error'] == 0) {
                        insert_log($data_user['id'], "Mua gói vps cheap " . $row['name'] . " giá " . format_cash($total));

                        $result = infoListVpsH2($data['data'][0]['vps-id']);
                        $billingcycleday = 0;
                         if (isset($result['error']) && $result['error'] == 0 && isset($result['list-service'][0]['day-left'])) {
                            $billingcycleday = preg_replace('/\D/', '', $result['list-service'][0]['day-left']);
                          }
                      $insert_result =  $db->insert("tbl_purchased_cloudvps", array(
                          'user_id' => $data_user['id'],
                          'billingcycle' => $billingcycle,
                          'billingcycleday' => !empty($billingcycleday) ? $billingcycleday : 0,
                          'vps_id' => $data['data'][0]['vps-id'],
                          'price' => $price,
                          'cost' => $data['total'],
                          'total_price' => $price,
                          'total_cost' => $data['total'],
                          'notified' => 'no',
                          'notification_expired' => 'no',
                          'notification_delete' => 'no',
                          'created_at' => gettime(),
                          'updated_at' => gettime(),
                          'site' => 'H2CLOUD',
                           ));
                           
                        exit(json_encode(array('status' => 'success', 'msg' => $data['message'],'url' => '/user/history/vps')));
                    } else {
                        PlusCredits($data_user['id'], $total, "Hoàn tiền vps " . $row['name']);
                        exit(json_encode(array('status' => 'error', 'msg' => $data['message'])));
                    }
                }
            }
        } catch (Exception $e) {

            die(JsonMsg('error', $e));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
