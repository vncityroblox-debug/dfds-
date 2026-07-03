<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        if ($data_user['level'] != 'admin') {
            die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
        }
        $param = Anti_xss($_POST['id']);
        $action = Anti_xss($_POST['action']);
        try {
            if (empty($param)) {
                die(JsonMsg('error', 'Tham số không hợp lệ'));
            }
            if (empty($action)) {
                die(JsonMsg('error', 'Vui lòng chọn chức năng'));
            }
            $check_history = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `id` = '$param'");
            if (!$check_history) {
                die(JsonMsg('error', 'Đơn hàng của bạn không tồn tại hoặc đã bị hủy'));
            }

            $detail = json_decode(decryptAES($check_history['info']), true);
            if ($check_history['site'] == 'VNCLOUD') {
                switch ($action) {
                    case '1':
                        //start
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        $data = actionCloudVps('on', $check_history['vps_id']);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg('success', $data['message']));
                        } else {
                            die(JsonMsg('error', 'Không thể start vps. Vui lòng liên hệ ADMIN'));
                        }
                        break;
                    case '2':
                        //stop
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        $data = actionCloudVps('off', $check_history['vps_id']);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg('success', $data['message']));
                        } else {
                            die(JsonMsg('error', 'Không thể start vps. Vui lòng liên hệ ADMIN'));
                        }
                        break;
                    case '3':
                        //restart
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        $data = actionCloudVps('restart', $check_history['vps_id']);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg('success', $data['message']));
                        } else {
                            die(JsonMsg('error', 'Không thể restart vps. Vui lòng liên hệ ADMIN'));
                        }
                        break;
                    case '4':
                        //rebuild
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        if (!isset($_POST['osid']) || empty($_POST['osid'])) {
                            die(JsonMsg('error', "Quý khách cần chọn hệ điều hành server đang đăng ký"));
                        }
                        $osid = Anti_xss($_POST['osid']);
                        $data = rebuildCloudVps($check_history['vps_id'], $osid);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg("success", $data['message']));
                        } else {
                            die(JsonMsg('error', $data['message']));
                        }
                        break;
                    case '5':
                        //gia hạn
                        $extend = extendCloudVps($check_history['vps_id'], $check_history['billingcycle']);
                        if (isset($extend['error']) && $extend['error'] == 0) {
                            insert_log($data_user['id'], "Admin gia hạn vps IP:" . $detail['ip'] . "");
                            /* GHI LOG DÒNG TIỀN */
                            die(JsonMsg('success', "Bạn đã gia hạn thành công"));
                        } else {
                            die(JsonMsg('error', $extend['message']));
                        }
                        break;


                    default:
                        // code...
                        break;
                }
            } else {
                switch ($action) {
                    case '1':
                        //start
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        $data = actionCloudNest('on', $check_history['vps_id']);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg('success', $data['message']));
                        } else {
                            die(JsonMsg('error', 'Không thể start vps. Vui lòng liên hệ ADMIN'));
                        }
                        break;
                    case '2':
                        //stop
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        $data = actionCloudNest('off', $check_history['vps_id']);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg('success', $data['message']));
                        } else {
                            die(JsonMsg('error', 'Không thể start vps. Vui lòng liên hệ ADMIN'));
                        }
                        break;
                    case '3':
                        //restart
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        $data = actionCloudNest('restart', $check_history['vps_id']);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg('success', $data['message']));
                        } else {
                            die(JsonMsg('error', 'Không thể restart vps. Vui lòng liên hệ ADMIN'));
                        }
                        break;
                    case '4':
                        //rebuild
                        if ($detail['vps-status'] == 'expire') {
                            die(JsonMsg('success', 'Vui lòng gia hạn để tiếp tục thực hiện'));
                        }
                        if (!isset($_POST['osid']) || empty($_POST['osid'])) {
                            die(JsonMsg('error', "Quý khách cần chọn hệ điều hành server đang đăng ký"));
                        }
                        $osid = Anti_xss($_POST['osid']);
                        $data = rebuildCloudNest($check_history['vps_id'], $osid);
                        if (isset($data['error']) && $data['error'] == 0) {
                            die(JsonMsg("success", $data['message']));
                        } else {
                            die(JsonMsg('error', $data['message']));
                        }
                        break;
                    case '5':
                        //gia hạn
                        $extend = extendCloudCloudNest($check_history['vps_id'], $check_history['billingcycle']);
                        if (isset($extend['error']) && $extend['error'] == 0) {
                            insert_log($data_user['id'], "Admin gia hạn vps IP:" . $detail['ip'] . "");
                            /* GHI LOG DÒNG TIỀN */
                            die(JsonMsg('success', "Bạn đã gia hạn thành công"));
                        } else {
                            die(JsonMsg('error', $extend['message']));
                        }
                        break;


                    default:
                        // code...
                        break;
                }
            }
        } catch (Exception $e) {
            //http_response_code(401);
            die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
