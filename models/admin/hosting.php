<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        try {
            if ($data_user['level'] != 'admin') {
                die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
            }
            $param = Anti_xss($_POST['param']);
            $action = Anti_xss($_POST['action']);
            if (empty($param)) {
                die(JsonMsg('error', 'Tham số không hợp lệ'));
            }
            if (empty($action)) {
                die(JsonMsg('error', 'Vui lòng chọn chức năng'));
            }
            $check_history = $db->get_row("SELECT * FROM `purchased_hosting` WHERE `id` = '$param'");
            if (!$check_history) {
                die(JsonMsg('error', 'Đơn hàng của bạn không tồn tại hoặc đã bị hủy'));
            }
            $whm = json_decode($check_history['server_whm'], true);

            if (!$server_whm = $db->get_row("SELECT * FROM `whm_info` WHERE `username` = '" . $whm['username'] . "' AND `status` = 1")) {
                die(JsonMsg('error', 'Máy chủ không tồn tại'));
            }

            $info_package = json_decode($check_history['info_package'], true);
            switch ($action) {
                case 'changepassword':
                    //đặt lại mk
                    $password = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM@#%mnbvcxzlkjhgfdsapoiuytrewq', rand(15, 16));
                    $data = changePasswordHostingViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username'], $password);
                    if (isset($data['metadata']['result'])) {
                        if ($data['metadata']['result'] == 1) {
                            $db->update("purchased_hosting", array(
                                'password' => $password,
                            ), " `id` = '$param' ");
                            die(JsonMsg('success', "Hệ thống đã đổi mật khẩu hosting lại thành: $password"));
                        } else {
                            die(JsonMsg('error', 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu'));
                        }
                    } else {
                        die(JsonMsg('error', 'Không thể đặt lại. Vui lòng liên hệ ADMIN'));
                    }
                    break;
                case 'remove':
                    //xóa hosting
                    $data = removeHostingViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username'], $check_history['domain_name']);
                    if (isset($data['metadata']['result'])) {
                        if ($data['metadata']['result'] == 1) {
                            $isRemove = $db->remove("purchased_hosting", " `id` = '$param' ");
                            if ($isRemove) {
                                insert_log($data_user['id'], "Admin Thực hiện xóa hosting có tên miền: " . $check_history['domain_name']);
                                die(JsonMsg('success', 'Xóa hosting thành công'));
                            }
                        } else {
                            die(JsonMsg('error', $data['metadata']['reason']));
                        }
                    } else {
                        die(JsonMsg('error', 'Không thể xóa hosting'));
                    }
                case '4':
                    $price = $check_history['price'];
                    $time = time();
                    if ($check_history['end_date'] < $time) {
                        $timeto = $time + 2592000 * $check_history['month'];
                        $total = $price * 1;
                        // exit(json_encode(array('status' => '1', 'msg' => 'hết hạn!')));
                    } else {
                        $timeto = $check_history['end_date'] + 2592000 * $check_history['month'];
                        $total = $price * 1;
                        // exit(json_encode(array('status' => '2', 'msg' => 'còn hạn!')));
                    }
                    if ($total > $data_user['money']) {
                        die(JsonMsg('error', "Bạn không đủ tiền để gia hạn. Bạn cần nạp thêm " . format_cash($total - $data_user['money']) . "đ"));
                    }
                    $trans_id = random("QWERTYUOPASDFGHJKZXCVBNM123456789", 3) . uniqid();
                    if ($check_history['status'] == 'active') //đang hoạt động
                    {
                        $isMoney = RemoveCredits($data_user['id'], $total, "Gia hạn hosting " . $check_history['domain_name'] . " thêm " . $check_history['month'] . " tháng");
                        if ($isMoney) {
                            $isTime = $db->update("purchased_hosting", [
                                'total' => $check_history['total'] + $total,
                                'end_date' => $timeto,
                            ], " `id` = '$param' ");
                            insetLog($data_user["id"], "Gia hạn hosting " . $check_history['domain_name'] . " thêm " . $check_history['month'] . " tháng");
                            die(JsonMsg('success', "Bạn đã gia hạn thành công thêm " . $check_history['month'] . " tháng nữa"));
                        }
                    }
                    if ($check_history['status'] == 'expired') {
                        $unsub = unsuspendacctHostingViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username']);
                        if (isset($unsub['metadata']['result'])) {
                            if ($unsub['metadata']['result'] == 1) {
                                $db->update("purchased_hosting", array(
                                    'status' => 'active',
                                ), " `id` = '" . $check_history['id'] . "' ");
                                $isMoney = RemoveCredits($data_user['id'], $total, "Gia hạn hosting " . $check_history['domain_name'] . " thêm " . $check_history['month'] . " tháng");
                                if ($isMoney) {
                                    $isTime = $db->update("purchased_hosting", [
                                        'total' => $check_history['total'] + $total,
                                        'end_date' => $timeto,
                                    ], " `id` = '$param' ");
                                    insetLog($data_user["id"], "Gia hạn hosting " . $check_history['domain_name'] . " thêm " . $check_history['month'] . " tháng");
                                    die(JsonMsg('success', "Bạn đã gia hạn thành công thêm 1 tháng nữa"));
                                }
                            } else {
                                exit(json_encode(array('status' => 'error', 'msg' => $unsub['metadata']['reason'])));
                            }
                        } else {
                            exit(json_encode(array('status' => 'error', 'msg' => "Đã xảy ra lỗi")));
                        }
                    }
                    break;
                case '5':
                    if (!isset($_POST['package']) || empty($_POST['package'])) {
                        die(JsonMsg('error', 'Vui lòng chọn gói nâng cấp'));
                    }
                    $package = Anti_xss($_POST['package']);
                    $check_package = $db->get_row("SELECT * FROM `hosting_packages` WHERE `id` = '$package'");
                    if (!$check_package) {
                        die(JsonMsg('error', 'Gói không hợp lệ'));
                    }
                    if ($check_package['id'] == $check_history['package_id']) {
                        die(JsonMsg('error', 'Bạn đang sử dụng gói này rồi'));
                    }
                    $price_package_new = $check_package['price'] * $check_history['month'];
                    $money = $price_package_new - $check_history['price'];
                    if ($money > $data_user['money']) {
                        die(JsonMsg('error', 'Số dư của bạn không đủ ' . $money . ', vui lòng nạp thêm để thực hiện'));
                    }
                    if ($server_whm['username'] == 'root') {
                        $package = $check_package['package_name'];
                    } else {
                        $package = $server_whm['username'] . '_' . $check_package['package_name'];
                    }

                    $isMoney = RemoveCredits($data_user['id'], $money, 'Thực hiện nâng cấp hosting từ gói ' . $info_package['package_name'] . ' sang gói ' . $check_package['package_name']);
                    if ($isMoney) {
                        $data = changePackageHostingViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username'], $package);

                        if (isset($data['metadata']['result'])) {
                            if ($data['metadata']['result'] == 1) {
                                $db->update("purchased_hosting", array(
                                    'total' => $check_history['total'] + $money,
                                    'package_id' => $check_package['id'],
                                    'info_package' => json_encode($check_package),
                                    'price' => $price_package_new,
                                ), " `id` = '$param' ");
                                die(JsonMsg('success', 'Hệ thống nâng cấp gói thành công. Cảm ơn bạn đã sử dụng dịch vụ'));
                            } else {
                                die(JsonMsg('error', $data['metadata']['reason']));
                            }
                        } else {
                            die(JsonMsg('error', 'Không thể nâng cấp hosting. Vui lòng liên hệ ADMIN'));
                        }
                    }
                    break;
                case '6':
                    if (!isset($_POST['email']) || empty($_POST['email'])) {
                        die(JsonMsg('error', "Nhập email người dùng"));
                    }
                    $email = Anti_xss($_POST['email']);
                    if (check_email($email) == false) {
                        die(JsonMsg('error', "Định dạng email không hợp lệ"));
                    }
                    $check_user = $db->get_row("SELECT * FROM `users` WHERE `email` = '$email' AND `banned` = 0");
                    if (!$check_user) {
                        die(JsonMsg('error', "Người dùng không tồn tại"));
                    }
                    if ($check_user['username'] == $data_user['username']) {
                        die(JsonMsg('error', "Không thể chuyển quyền cho bản thân"));
                    }
                    insert_log($data_user["id"], "Đã chuyển quyền quản trị hosting có tên miền " . $check_history['domain_name'] . " cho tài khoản " . $check_user['email']);
                    $db->update("purchased_hosting", array(
                        'user_id' => $check_user['id'],
                    ), " `id` = '$param' ");
                    die(JsonMsg('success', "Đã chuyển quyền quản trị hosting có tên miền " . $check_history['domain_name'] . " cho tài khoản " . $check_user['email']));
                    break;

                case 'suspended':
                    $banned = suspendacctHostingViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username'], 'Tạm khóa'); // khóa hosting
                    if (isset($banned['metadata']['result'])) {
                        if ($banned['metadata']['result'] == 1) {
                            $db->update("purchased_hosting", array(
                                'status' => 'suspended',
                            ), " `id` = '" . $check_history['id'] . "' ");
                            insert_log($check_history['user_id'], "Admin đã tạm khóa hosting đang sử dụng tên miền " . $check_history['domain_name'] . "");
                            insert_log($data_user['id'], "Admin đã tạm khóa hosting đang sử dụng tên miền " . $check_history['domain_name'] . "");
                            exit(json_encode(array('status' => 'success', 'msg' => "Đã khóa hosting thành công")));
                        } else {
                            exit(json_encode(array('status' => 'error', 'msg' => $banned['metadata']['reason'])));
                        }
                    } else {
                        exit(json_encode(array('status' => 'error', 'msg' => "Đã xảy ra lỗi")));
                    }
                    break;
                case 'active':
                    $unsub = unsuspendacctHostingViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username']);
                    if (isset($unsub['metadata']['result'])) {
                        if ($unsub['metadata']['result'] == 1) {
                            $db->update("purchased_hosting", array(
                                'status' => 'active',
                            ), " `id` = '" . $check_history['id'] . "' ");
                            insert_log($check_history['user_id'], "Admin đã mở khóa hosting đang sử dụng tên miền " . $check_history['domain_name'] . "");
                            insert_log($data_user['id'], "Admin đã mở khóa hosting đang sử dụng tên miền " . $check_history['domain_name'] . "");
                            exit(json_encode(array('status' => 'success', 'msg' => "Đã mở khóa hosting thành công")));
                        } else {
                            exit(json_encode(array('status' => 'error', 'msg' => $unsub['metadata']['reason'])));
                        }
                    } else {
                        exit(json_encode(array('status' => 'error', 'msg' => "Đã xảy ra lỗi")));
                    }
                    break;
                case '9':
                    if (!isset($_POST['subdomain']) || empty($_POST['subdomain'])) {
                        die(JsonMsg('error', "Nhập subdomain cần thêm"));
                    }
                    if (!isset($_POST['rootdomain']) || empty($_POST['rootdomain'])) {
                        die(JsonMsg('error', "Nhập root domain cần thêm"));
                    }
                    $subdomain = Anti_xss($_POST['subdomain']);
                    $rootdomain = Anti_xss($_POST['rootdomain']);
                    $pattern = "/^(?:(?:https?|ftp):\/\/)?(?:www\.)?([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,4})$/";
                    if (!preg_match($pattern, $rootdomain)) {
                        die(JsonMsg('error', "Root domain nhập không hợp lệ"));
                    }
                    $data = addSubDomainViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username'], $subdomain, $rootdomain);
                    if (isset($data['cpanelresult']['data'][0]['result'])) {
                        if ($data['cpanelresult']['data'][0]['result'] == 1) {
                            die(JsonMsg('success', $data['cpanelresult']['data'][0]['reason']));
                        } else {
                            die(JsonMsg('error', $data['cpanelresult']['data'][0]['reason']));
                        }
                    } else {
                        die(JsonMsg('error', 'Không thể thêm addon domain. Vui lòng thử lại'));
                    }
                    break;
                case '10':
                    if (!isset($_POST['cronLink']) || empty($_POST['cronLink'])) {
                        die(JsonMsg('error', "Vui lòng nhập link cron"));
                    }
                    if (!isset($_POST['cronTime']) || empty($_POST['cronTime'])) {
                        die(JsonMsg('error', "Vui lòng nhập thời gian chạy"));
                    }
                    $urlcron = Anti_xss($_POST['cronLink']);
                    $minute = Anti_xss($_POST['cronTime']);
                    if ($minute <= 0 || $minute > 60) {
                        die(JsonMsg('error', "Thời gian không hợp lệ"));
                    }
                    $command = "wget -q -O - $urlcron?cron >/dev/null 2>&1";
                    $data = addCronJobViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username'], $minute, $command);
                    if (isset($data['cpanelresult']['data'][0]['status'])) {
                        if ($data['cpanelresult']['data'][0]['status'] == 1) {
                            die(JsonMsg('success', $data['cpanelresult']['data'][0]['statusmsg']));
                        } else {
                            die(JsonMsg('error', $data['cpanelresult']['data'][0]['statusmsg']));
                        }
                    } else {
                        die(JsonMsg('error', 'Không thể thêm cronjob. Vui lòng thử lại'));
                    }
                    break;
                case '11':
                    if (!isset($_POST['changedomain']) || empty($_POST['changedomain'])) {
                        die(JsonMsg('error', "Vui lòng nhập tên miền"));
                    }
                    $domain = Anti_xss($_POST['changedomain']);
                    $pattern = "/^(?:(?:https?|ftp):\/\/)?(?:www\.)?([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,4})$/";
                    if (!preg_match($pattern, $domain)) {
                        die(JsonMsg('error', "Tên miền nhập không hợp lệ"));
                    }
                    $data = changeDomainViaAPI($server_whm['ip'], $server_whm['username'], $server_whm['password'], $check_history['username'], $domain);
                    if (isset($data['metadata']['result'])) {
                        if ($data['metadata']['result'] == 1) {
                            $db->update("purchased_hosting", array(
                                'domain_name' => $domain,
                            ), " `id` = '$param' ");
                            die(JsonMsg('success', $data['metadata']['reason']));
                        } else {
                            die(JsonMsg('error', $data['metadata']['reason']));
                        }
                    } else {
                        die(JsonMsg('error', 'Không thể đổi tên miền. Vui lòng thử lại'));
                    }
                    break;
                case '12':
                    if (time() > $check_history['end_date']) {
                        die(JsonMsg('error', 'Gói hosting đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $bandwidth = getBandWidthViaAPI($server_whm['ip'], $check_history['username'], $check_history['password']);
                    $result = getDiskViaAPI($server_whm['ip'], $check_history['username'], $check_history['password']);
                    if ($result['status'] == 1 && isset($result['data'])) {
                        echo json_encode(array(
                            'status' => 'success',
                            'msg' => 'Thành công',
                            'disk' => $result['data'],
                            'bandwidth' => $bandwidth['data']
                        ));
                    } else {
                        die(JsonMsg('error', 'Có lỗi xảy ra trong quá trình xử lý'));
                    }
                    break;
                case '13':
                    if (!isset($_POST['ip']) || empty($_POST['ip'])) {
                        die(JsonMsg('error', "Vui lòng nhập IP"));
                    }
                    $ip = Anti_xss($_POST['ip']);
                    $data = blockIP($server_whm['ip'], $check_history['username'], $check_history['password'], $ip);
                    if (isset($data['status'])) {
                        if ($data['status'] == 1) {
                            die(JsonMsg('success', "Thêm vào danh sách chặn thành công"));
                        } else {
                            die(JsonMsg('error', $data['errors'][0]));
                        }
                    } else {
                        die(JsonMsg('error', 'Không thể thêm vào danh sách chặn. Vui lòng thử lại'));
                    }
                    break;
                case '14':
                    if (!isset($_POST['ip']) || empty($_POST['ip'])) {
                        die(JsonMsg('error', "Vui lòng nhập IP"));
                    }
                    $ip = Anti_xss($_POST['ip']);
                    $data = unBlockIP($server_whm['ip'], $check_history['username'], $check_history['password'], $ip);
                    if (isset($data['status'])) {
                        if ($data['status'] == 1) {
                            die(JsonMsg('success', "Đã bỏ IP khỏi danh sách chặn thành công"));
                        } else {
                            die(JsonMsg('error', $data['errors'][0]));
                        }
                    } else {
                        die(JsonMsg('error', 'Không thể bỏ ip khỏi danh sách chặn. Vui lòng thử lại'));
                    }
                    break;
                default:
                    // code...
                    break;
            }
        } catch (Exception $e) {

            die(JsonMsg('error', 'Đã xảy ra lỗi ngoại lệ'));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
