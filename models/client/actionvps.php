<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
        die(jsonMsg('error', 'Invalid CSRF Protection Token'));
    }

    if (!$user) {
        die(jsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }

    $param = Anti_xss($_POST['param'] ?? '');
    $action = Anti_xss($_POST['action'] ?? '');

    try {
        // Kiểm tra đầu vào
        if (empty($param)) {
            die(jsonMsg('error', 'Tham số không hợp lệ'));
        }
        if (empty($action)) {
            die(jsonMsg('error', 'Vui lòng chọn chức năng'));
        }

        // Kiểm tra đơn hàng VPS
        $check_history = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `id` = ? AND `user_id` = ?", [$param, $data_user['id']]);
        if (!$check_history) {
            die(jsonMsg('error', 'Đơn hàng của bạn không tồn tại hoặc đã bị hủy'));
        }

        $detail = json_decode(decryptAES($check_history['info'] ?? ''), true) ?: [];
        if (empty($detail)) {
            error_log("Invalid VPS info for ID: $param");
            die(jsonMsg('error', 'Thông tin VPS không hợp lệ'));
        }

        // Xử lý theo nhà cung cấp
        if ($check_history['site'] == 'VNCLOUD') {
            switch ($action) {
                case '1': // Start
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudVps('on', $check_history['vps_id']);
                    error_log("VNCLOUD actionCloudVps(on) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể khởi động VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '2': // Stop
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudVps('off', $check_history['vps_id']);
                    error_log("VNCLOUD actionCloudVps(off) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể dừng VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '3': // Restart
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudVps('restart', $check_history['vps_id']);
                    error_log("VNCLOUD actionCloudVps(restart) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể khởi động lại VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '4': // Rebuild
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $osid = Anti_xss($_POST['osid'] ?? '');
                    if (empty($osid)) {
                        die(jsonMsg('error', 'Quý khách cần chọn hệ điều hành cho server'));
                    }
                    $data = rebuildCloudVps($check_history['vps_id'], $osid);
                    error_log("VNCLOUD rebuildCloudVps response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Lỗi khi rebuild VPS'));
                    }
                    break;

                case '5': // Gia hạn
                    $total = $check_history['price'];
                    if ($data_user['money'] < $total) {
                        die(jsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ' để gia hạn, vui lòng nạp thêm'));
                    }
                    $isMoney = RemoveCredits($data_user['id'], $total, "Gia hạn VPS " . ($detail['ip'] ?? 'unknown') . " với số tiền " . format_cash($total));
                    if ($isMoney) {
                        $extend = extendCloudVps($check_history['vps_id'], $check_history['billingcycle']);
                        error_log("VNCLOUD extendCloudVps response: " . json_encode($extend));
                        if (isset($extend['error']) && $extend['error'] == 0) {
                            $db->update("tbl_purchased_cloudvps", [
                                'total_price' => $check_history['total_price'] + $total,
                                'total_cost' => $check_history['total_cost'] + ($extend['total'] ?? 0),
                                'updated_at' => gettime()
                            ], " `id` = ?", [$param]);
                            die(jsonMsg('success', 'Gia hạn VPS thành công'));
                        } else {
                            PlusCredits($data_user['id'], $total, "Hoàn tiền gia hạn VPS " . ($detail['ip'] ?? 'unknown'));
                            die(jsonMsg('error', $extend['message'] ?? 'Lỗi khi gia hạn VPS'));
                        }
                    }
                    break;

                case '7': // Chuyển quyền
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $email = Anti_xss($_POST['email'] ?? '');
                    if (empty($email)) {
                        die(jsonMsg('error', 'Vui lòng nhập email'));
                    }
                    if (!check_email($email)) {
                        die(jsonMsg('error', 'Địa chỉ email không hợp lệ'));
                    }
                    $check_user = $db->get_row("SELECT * FROM `users` WHERE `email` = ? AND `banned` = 0", [$email]);
                    if (!$check_user) {
                        die(jsonMsg('error', 'Người dùng không tồn tại'));
                    }
                    if ($check_user['email'] == $data_user['email']) {
                        die(jsonMsg('error', 'Không thể chuyển quyền cho chính bạn'));
                    }
                    insert_log($data_user['id'], "Chuyển quyền quản trị VPS IP: " . ($detail['ip'] ?? 'unknown') . " cho email " . $check_user['email']);
                    $db->update("tbl_purchased_cloudvps", ['user_id' => $check_user['id'], 'updated_at' => gettime()], " `id` = ?", [$param]);
                    die(jsonMsg('success', "Chuyển quyền quản trị cho email: " . $check_user['email'] . " thành công"));
                    break;

                case '10': // Nâng cấp
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu'] ?? '0'));
                    $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram'] ?? '0'));
                    $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk'] ?? '0'));

                    $ck = 0;
                    $getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = ? AND `banned` = 0", [$data_user['id']]);
                    if ($getUser) {
                        $ck = $getUser['chietkhau'];
                    }

                    $billingcycle = $check_history['billingcycle'];
                    $cash_cpu = getAddonPrice('VNCLOUD', 'addon_cpu', $cpu, $billingcycle);
                    $cash_ram = getAddonPrice('VNCLOUD', 'addon_ram', $ram, $billingcycle);
                    $cash_disk = getAddonPrice('VNCLOUD', 'addon_disk', $disk, $billingcycle);

                    $total = $cash_cpu + $cash_ram + $cash_disk;
                    $total = $total - $total * $ck / 100;

                    $billingcycleday = $check_history['billingcycleday'];
                    $dayleft = preg_replace('/\D/', '', $detail['day-left'] ?? '0');
                    $hesochia = $billingcycleday ? $dayleft / $billingcycleday : 1;
                    $hesonhan = custom_round($hesochia, floor($hesochia));
                    $total_giahan = $total * $hesonhan;

                    if ($total_giahan < 0) {
                        die(jsonMsg('error', 'Dữ liệu không hợp lệ'));
                    }
                    if ($total_giahan > $data_user['money']) {
                        die(jsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total_giahan) . ' để nâng cấp, vui lòng nạp thêm'));
                    }

                    $isMoney = RemoveCredits($data_user['id'], $total_giahan, "Nâng cấp VPS " . ($detail['ip'] ?? 'unknown') . " với số tiền " . format_cash($total_giahan));
                    if ($isMoney) {
                        $result = upgradeCloudVps($check_history['vps_id'], $cpu, $ram, $disk * 10);
                        error_log("VNCLOUD upgradeCloudVps response: " . json_encode($result));
                        if (isset($result['error']) && $result['error'] == 0) {
                            $db->update("tbl_purchased_cloudvps", [
                                'price' => $check_history['price'] + $total,
                                'cost' => $check_history['cost'] + ($result['total'] ?? 0),
                                'total_price' => $check_history['total_price'] + $total_giahan,
                                'total_cost' => $check_history['total_cost'] + ($result['total'] ?? 0),
                                'updated_at' => gettime()
                            ], " `id` = ?", [$param]);
                            die(jsonMsg('success', $result['message'] ?? 'Nâng cấp VPS thành công'));
                        } else {
                            PlusCredits($data_user['id'], $total_giahan, "Hoàn tiền nâng cấp VPS " . ($detail['ip'] ?? 'unknown'));
                            die(jsonMsg('error', $result['message'] ?? 'Lỗi khi nâng cấp VPS'));
                        }
                    }
                    break;

                default:
                    die(jsonMsg('error', 'Hành động không được hỗ trợ'));
            }
        } else if ($check_history['site'] == 'CLOUDNEST') {
            switch ($action) {
                case '1': // Start
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudNest('on', $check_history['vps_id']);
                    error_log("CLOUDNEST actionCloudNest(on) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể khởi động VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '2': // Stop
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudNest('off', $check_history['vps_id']);
                    error_log("CLOUDNEST actionCloudNest(off) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể dừng VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '3': // Restart
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudNest('restart', $check_history['vps_id']);
                    error_log("CLOUDNEST actionCloudNest(restart) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể khởi động lại VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '4': // Rebuild
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $osid = Anti_xss($_POST['osid'] ?? '');
                    if (empty($osid)) {
                        die(jsonMsg('error', 'Quý khách cần chọn hệ điều hành cho server'));
                    }
                    $data = rebuildCloudNest($check_history['vps_id'], $osid);
                    error_log("CLOUDNEST rebuildCloudNest response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Lỗi khi rebuild VPS'));
                    }
                    break;

                case '5': // Gia hạn
                    $total = $check_history['price'];
                    if ($data_user['money'] < $total) {
                        die(jsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ' để gia hạn, vui lòng nạp thêm'));
                    }
                    $isMoney = RemoveCredits($data_user['id'], $total, "Gia hạn VPS " . ($detail['ip'] ?? 'unknown') . " với số tiền " . format_cash($total));
                    if ($isMoney) {
                        $extend = extendCloudNest($check_history['vps_id'], $check_history['billingcycle']); // Sửa lỗi từ extendCloudCloudNest
                        error_log("CLOUDNEST extendCloudNest response: " . json_encode($extend));
                        if (isset($extend['error']) && $extend['error'] == 0) {
                            $db->update("tbl_purchased_cloudvps", [
                                'total_price' => $check_history['total_price'] + $total,
                                'total_cost' => $check_history['total_cost'] + ($extend['total'] ?? 0),
                                'updated_at' => gettime()
                            ], " `id` = ?", [$param]);
                            die(jsonMsg('success', 'Gia hạn VPS thành công'));
                        } else {
                            PlusCredits($data_user['id'], $total, "Hoàn tiền gia hạn VPS " . ($detail['ip'] ?? 'unknown'));
                            die(jsonMsg('error', $extend['message'] ?? 'Lỗi khi gia hạn VPS'));
                        }
                    }
                    break;

                case '7': // Chuyển quyền
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $email = Anti_xss($_POST['email'] ?? '');
                    if (empty($email)) {
                        die(jsonMsg('error', 'Vui lòng nhập email'));
                    }
                    if (!check_email($email)) {
                        die(jsonMsg('error', 'Địa chỉ email không hợp lệ'));
                    }
                    $check_user = $db->get_row("SELECT * FROM `users` WHERE `email` = ? AND `banned` = 0", [$email]);
                    if (!$check_user) {
                        die(jsonMsg('error', 'Người dùng không tồn tại'));
                    }
                    if ($check_user['email'] == $data_user['email']) {
                        die(jsonMsg('error', 'Không thể chuyển quyền cho chính bạn'));
                    }
                    insert_log($data_user['id'], "Chuyển quyền quản trị VPS IP: " . ($detail['ip'] ?? 'unknown') . " cho email " . $check_user['email']);
                    $db->update("tbl_purchased_cloudvps", ['user_id' => $check_user['id'], 'updated_at' => gettime()], " `id` = ?", [$param]);
                    die(jsonMsg('success', "Chuyển quyền quản trị cho email: " . $check_user['email'] . " thành công"));
                    break;

                case '10': // Nâng cấp
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu'] ?? '0'));
                    $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram'] ?? '0'));
                    $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk'] ?? '0'));

                    $ck = 0;
                    $getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = ? AND `banned` = 0", [$data_user['id']]);
                    if ($getUser) {
                        $ck = $getUser['chietkhau'];
                    }

                    $billingcycle = $check_history['billingcycle'];
                    $cash_cpu = getAddonPrice('CLOUDNEST', 'addon_cpu', $cpu, $billingcycle);
                    $cash_ram = getAddonPrice('CLOUDNEST', 'addon_ram', $ram, $billingcycle);
                    $cash_disk = getAddonPrice('CLOUDNEST', 'addon_disk', $disk, $billingcycle);

                    $total = $cash_cpu + $cash_ram + $cash_disk;
                    $total = $total - $total * $ck / 100;

                    $billingcycleday = $check_history['billingcycleday'];
                    $dayleft = preg_replace('/\D/', '', $detail['day-left'] ?? '0');
                    $hesochia = $billingcycleday ? $dayleft / $billingcycleday : 1;
                    $hesonhan = custom_round($hesochia, floor($hesochia));
                    $total_giahan = $total * $hesonhan;

                    if ($total_giahan < 0) {
                        die(jsonMsg('error', 'Dữ liệu không hợp lệ'));
                    }
                    if ($total_giahan > $data_user['money']) {
                        die(jsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total_giahan) . ' để nâng cấp, vui lòng nạp thêm'));
                    }

                    $isMoney = RemoveCredits($data_user['id'], $total_giahan, "Nâng cấp VPS " . ($detail['ip'] ?? 'unknown') . " với số tiền " . format_cash($total_giahan));
                    if ($isMoney) {
                        $result = upgradeCloudNest($check_history['vps_id'], $cpu, $ram, $disk * 10);
                        error_log("CLOUDNEST upgradeCloudNest response: " . json_encode($result));
                        if (isset($result['error']) && $result['error'] == 0) {
                            $db->update("tbl_purchased_cloudvps", [
                                'price' => $check_history['price'] + $total,
                                'cost' => $check_history['cost'] + ($result['total'] ?? 0),
                                'total_price' => $check_history['total_price'] + $total_giahan,
                                'total_cost' => $check_history['total_cost'] + ($result['total'] ?? 0),
                                'updated_at' => gettime()
                            ], " `id` = ?", [$param]);
                            die(jsonMsg('success', $result['message'] ?? 'Nâng cấp VPS thành công'));
                        } else {
                            PlusCredits($data_user['id'], $total_giahan, "Hoàn tiền nâng cấp VPS " . ($detail['ip'] ?? 'unknown'));
                            die(jsonMsg('error', $result['message'] ?? 'Lỗi khi nâng cấp VPS'));
                        }
                    }
                    break;

                default:
                    die(jsonMsg('error', 'Hành động không được hỗ trợ'));
            }
        } else if ($check_history['site'] == 'H2CLOUD') {
            switch ($action) {
                case '1': // Start
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudVpsH2('on', $check_history['vps_id']);
                    error_log("H2CLOUD actionCloudVpsH2(on) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể khởi động VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '2': // Stop
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudVpsH2('off', $check_history['vps_id']);
                    error_log("H2CLOUD actionCloudVpsH2(off) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể dừng VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '3': // Restart
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $data = actionCloudVpsH2('restart', $check_history['vps_id']);
                    error_log("H2CLOUD actionCloudVpsH2(restart) response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Không thể khởi động lại VPS. Vui lòng liên hệ ADMIN'));
                    }
                    break;

                case '4': // Rebuild
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $osid = Anti_xss($_POST['osid'] ?? '');
                    if (empty($osid)) {
                        die(jsonMsg('error', 'Quý khách cần chọn hệ điều hành cho server'));
                    }
                    $data = rebuildCloudVpsH2($check_history['vps_id'], $osid);
                    error_log("H2CLOUD rebuildCloudVpsH2 response: " . json_encode($data));
                    if (isset($data['error']) && $data['error'] == 0) {
                        die(jsonMsg('success', $data['message']));
                    } else {
                        die(jsonMsg('error', $data['message'] ?? 'Lỗi khi rebuild VPS'));
                    }
                    break;

                case '5': // Gia hạn
                    $total = $check_history['price'];
                    if ($data_user['money'] < $total) {
                        die(jsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total) . ' để gia hạn, vui lòng nạp thêm'));
                    }
                    $isMoney = RemoveCredits($data_user['id'], $total, "Gia hạn VPS " . ($detail['ip'] ?? 'unknown') . " với số tiền " . format_cash($total));
                    if ($isMoney) {
                        $extend = extendCloudVpsH2($check_history['vps_id'], $check_history['billingcycle']);
                        error_log("H2CLOUD extendCloudVpsH2 response: " . json_encode($extend));
                        if (isset($extend['error']) && $extend['error'] == 0) {
                            $db->update("tbl_purchased_cloudvps", [
                                'total_price' => $check_history['total_price'] + $total,
                                'total_cost' => $check_history['total_cost'] + ($extend['total'] ?? 0),
                                'updated_at' => gettime()
                            ], " `id` = ?", [$param]);
                            die(jsonMsg('success', 'Gia hạn VPS thành công'));
                        } else {
                            PlusCredits($data_user['id'], $total, "Hoàn tiền gia hạn VPS " . ($detail['ip'] ?? 'unknown'));
                            die(jsonMsg('error', $extend['message'] ?? 'Lỗi khi gia hạn VPS'));
                        }
                    }
                    break;

                case '7': // Chuyển quyền
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $email = Anti_xss($_POST['email'] ?? '');
                    if (empty($email)) {
                        die(jsonMsg('error', 'Vui lòng nhập email'));
                    }
                    if (!check_email($email)) {
                        die(jsonMsg('error', 'Địa chỉ email không hợp lệ'));
                    }
                    $check_user = $db->get_row("SELECT * FROM `users` WHERE `email` = ? AND `banned` = 0", [$email]);
                    if (!$check_user) {
                        die(jsonMsg('error', 'Người dùng không tồn tại'));
                    }
                    if ($check_user['email'] == $data_user['email']) {
                        die(jsonMsg('error', 'Không thể chuyển quyền cho chính bạn'));
                    }
                    insert_log($data_user['id'], "Chuyển quyền quản trị VPS IP: " . ($detail['ip'] ?? 'unknown') . " cho email " . $check_user['email']);
                    $db->update("tbl_purchased_cloudvps", ['user_id' => $check_user['id'], 'updated_at' => gettime()], " `id` = ?", [$param]);
                    die(jsonMsg('success', "Chuyển quyền quản trị cho email: " . $check_user['email'] . " thành công"));
                    break;

                case '10': // Nâng cấp
                    if ($detail['vps-status'] == 'expire') {
                        die(jsonMsg('error', 'VPS đã hết hạn, vui lòng gia hạn để tiếp tục'));
                    }
                    $cpu = Anti_xss(preg_replace('/\D/', '', $_POST['cpu'] ?? '0'));
                    $ram = Anti_xss(preg_replace('/\D/', '', $_POST['ram'] ?? '0'));
                    $disk = Anti_xss(preg_replace('/\D/', '', $_POST['disk'] ?? '0'));

                    $ck = 0;
                    $getUser = $db->get_row("SELECT * FROM `users` WHERE `id` = ? AND `banned` = 0", [$data_user['id']]);
                    if ($getUser) {
                        $ck = $getUser['chietkhau'];
                    }

                    $billingcycle = $check_history['billingcycle'];
                    $cash_cpu = getAddonPrice('H2CLOUD', 'addon_cpu', $cpu, $billingcycle);
                    $cash_ram = getAddonPrice('H2CLOUD', 'addon_ram', $ram, $billingcycle);
                    $cash_disk = getAddonPrice('H2CLOUD', 'addon_disk', $disk, $billingcycle);

                    error_log("H2CLOUD addon prices: cpu=$cash_cpu, ram=$cash_ram, disk=$cash_disk");

                    $total = $cash_cpu + $cash_ram + $cash_disk;
                    $total = $total - $total * $ck / 100;

                    $billingcycleday = $check_history['billingcycleday'];
                    $dayleft = preg_replace('/\D/', '', $detail['day-left'] ?? '0');
                    $hesochia = $billingcycleday ? $dayleft / $billingcycleday : 1;
                    $hesonhan = custom_round($hesochia, floor($hesochia));
                    $total_giahan = $total * $hesonhan;

                    if ($total_giahan < 0) {
                        die(jsonMsg('error', 'Dữ liệu không hợp lệ'));
                    }
                    if ($total_giahan > $data_user['money']) {
                        die(jsonMsg('error', 'Số dư của bạn không đủ ' . format_cash($total_giahan) . ' để nâng cấp, vui lòng nạp thêm'));
                    }

                    $isMoney = RemoveCredits($data_user['id'], $total_giahan, "Nâng cấp VPS " . ($detail['ip'] ?? 'unknown') . " với số tiền " . format_cash($total_giahan));
                    if ($isMoney) {
                        $result = upgradeCloudVpsH2($check_history['vps_id'], $cpu, $ram, $disk * 10);
                        error_log("H2CLOUD upgradeCloudVpsH2 response: " . json_encode($result));
                        if (isset($result['error']) && $result['error'] == 0) {
                            $db->update("tbl_purchased_cloudvps", [
                                'price' => $check_history['price'] + $total,
                                'cost' => $check_history['cost'] + ($result['total'] ?? 0),
                                'total_price' => $check_history['total_price'] + $total_giahan,
                                'total_cost' => $check_history['total_cost'] + ($result['total'] ?? 0),
                                'updated_at' => gettime()
                            ], " `id` = ?", [$param]);
                            die(jsonMsg('success', $result['message'] ?? 'Nâng cấp VPS thành công'));
                        } else {
                            PlusCredits($data_user['id'], $total_giahan, "Hoàn tiền nâng cấp VPS " . ($detail['ip'] ?? 'unknown'));
                            die(jsonMsg('error', $result['message'] ?? 'Lỗi khi nâng cấp VPS'));
                        }
                    }
                    break;

                default:
                    die(jsonMsg('error', 'Hành động không được hỗ trợ'));
            }
        } else {
            die(jsonMsg('error', 'Nhà cung cấp không được hỗ trợ: ' . $check_history['site']));
        }
    } catch (Exception $e) {
        error_log("VPS action error: " . $e->getMessage());
        die(jsonMsg('error', 'Lỗi hệ thống: ' . $e->getMessage()));
    }
}