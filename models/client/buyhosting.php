<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
        die(jsonMsg('error', 'Invalid CSRF Protection Token'));
    }
    if ($user) {
        try {
            
           
          
            if (empty($_POST["id"])) {
                die(JsonMsg('error', 'Vui lòng chọn gói hosting'));
            }
            if (empty($_POST["domain"])) {
                die(JsonMsg('error', 'Vui lòng nhập tên miền'));
            }
            if (empty($_POST["selectedMonths"])) {
                die(JsonMsg('error', 'Vui lòng chọn thời gian đăng ký'));
            }
            if (!($product = $db->get_row("SELECT * FROM `hosting_packages` WHERE `id` = '" . Anti_xss($_POST["id"]) . "' AND `status` = 1 "))) {
                die(JsonMsg('error', 'Gói hosting không tồn tại'));
            }
            if (!$whm = $db->get_row("SELECT * FROM `whm_info` WHERE `id`='" . $product['whm_id'] . "' AND `status`=1")) {
                die(JsonMsg('error', 'Máy chủ đang bảo trì, vui lòng quay lại sau'));
            }

            $ck = 0;
            if ($user) {
                if ($getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $data_user['id'] . "' AND `banned` = 0 ")) {
                    $ck = $getUser['chietkhau'];
                }
            }
            $month = Anti_xss($_POST["selectedMonths"]);
            $domain = Anti_xss($_POST["domain"]);
            $data = checkDomainHostingPackageViaAPI($whm['ip'], $whm['username'], $whm['password']);
            foreach ($data['data']['domains'] as $demo) {
                if ($demo['domain'] == $domain) {
                    $checkdo = true;
                }
            }
            if (isset($checkdo)) {
                die(JsonMsg('error', 'Tên miền này đã tồn tại trong hệ thống'));
            }
            $trans_id = random("QWERTYUOPASDFGHJKZXCVBNM123456789", 3) . uniqid();

            $money = $month * $product["price"];
            $money = $money - $money * $ck / 100;
            $total = $money;
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


            $isMoney = RemoveCredits($data_user['id'], $total, "Thanh toán đơn hàng mua hosting" . " <b>" . $product["name"] . "</b> - #" . $trans_id, "ORDER_" . $trans_id);
            if ($isMoney) {
                $username = 't' . substr(str_replace(['-', '.'], '', strtolower(extractDomain($domain))), 0, 11);
                $password = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM@#%mnbvcxzlkjhgfdsapoiuytrewq', rand(15, 16));
                 if ($whm['username'] == 'root') {
                    $package = $product['package_name'];
                } else {
                    $package = $whm['username'] . '_' . $product['package_name'];
                }
                $result = createHostingViaAPI($whm['ip'], $whm['username'], $whm['password'], $username, $domain, $getUser['email'], $package, $password);
                if (isset($result['metadata']['result'])) {
                    if ($result['metadata']['result'] == 1) {
                        $info_whm = array();
                        $info_whm['ip'] = $whm['ip'];
                        $info_whm['username'] = $whm['username'];
                        $db->insert("purchased_hosting", array(
                            'user_id' => $data_user['id'],
                            'package_id' => $product['id'],
                            'ip' => $result['data']['ip'],
                            'start_date' => time(),
                            'end_date' => time() + (2592000 * $month),
                            'username' => $username,
                            'password' => $password,
                            'month' => $month,
                            'email' => $data_user['email'],
                            'domain_name' => $domain,
                            'server_whm' => json_encode($info_whm),
                            'info_package' => json_encode($product),
                            'price' => $money,
                            'total' => $money,
                            'status' => 'active',
                            'created_at' => gettime(),
                        ));
                        if ($db->site("noti_action") != "") {
                            $my_text = $db->site("noti_action");
                            $replacements = [
                                '{domain}' => $domain,
                                '{username}' => $data_user["username"],
                                '{action}' => "Thanh toán đơn hàng mua hosting gói : ". $product["name"],
                                '{ip}' => myip(),
                                '{time}' => gettime()
                            ];
                            $my_text = str_replace(array_keys($replacements), array_values($replacements), $my_text);
                            sendMessAdmin($my_text);
                        }
                        die(JsonMsg('success', 'Đã tạo hosting thành công, cảm ơn bạn đã sử dụng dịch vụ'));
                    } else {
                        PlusCredits($data_user['id'],$total,"Hoàn tiền khi đăng ký lỗi hosting ".$product["name"]);
                        exit(json_encode(array('status' => 'error', 'msg' => $result['metadata']['reason'])));
                    }
                } else {
                    PlusCredits($data_user['id'],$total,"Hoàn tiền khi đăng ký lỗi hosting ".$product["name"]);
                    exit(json_encode(array('status' => 'error', 'msg' => 'Đã xảy ra lỗi khi tạo hosting')));
                }
            } else {
                die(JsonMsg('error', 'Đã xảy ra lỗi, vui lòng liên hệ admin'));
            }
        } catch (Exception $e) {

            die(JsonMsg('error', 'Đã xảy ra lỗi ngoại lệ'));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
