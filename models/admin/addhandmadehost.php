<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        try {
            if ($data_user['level'] != 'admin') {
                die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
            }
            if (empty($_POST["username"])) {
                die(JsonMsg('error', 'Vui lòng nhập tên người dùng'));
            }
            if (empty($_POST["package"])) {
                die(JsonMsg('error', 'Vui lòng chọn gói hosting'));
            }
            if (empty($_POST["domain"])) {
                die(JsonMsg('error', 'Vui lòng nhập tên miền'));
            }
            if (empty($_POST["month"])) {
                die(JsonMsg('error', 'Vui lòng chọn thời gian đăng ký'));
            }
            if (empty($_POST["ip"])) {
                die(JsonMsg('error', 'Vui lòng nhập ip'));
            }
            if (empty($_POST["account"])) {
                die(JsonMsg('error', 'Vui lòng nhập tài khoản hosting người dùng'));
            }
            if (empty($_POST["password"])) {
                die(JsonMsg('error', 'Vui lòng nhập mật khẩu hosting người dùng'));
            }
            if (empty($_POST["account_server"])) {
                die(JsonMsg('error', 'Vui lòng nhập tài khoản của máy chủ'));
            }
            if (!($product = $db->get_row("SELECT * FROM `hosting_packages` WHERE `id` = '" . Anti_xss($_POST["package"]) . "'"))) {
                die(JsonMsg('error', 'Gói hosting không tồn tại'));
            }
            if (!$whm = $db->get_row("SELECT * FROM `whm_info` WHERE `id`='" . $product['whm_id'] . "' AND `status`=1")) {
                die(JsonMsg('error', 'Máy chủ đang bảo trì, vui lòng quay lại sau'));
            }
            $username = Anti_xss($_POST["username"]);

            if (!$getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $username . "' AND `banned` = 0 ")) {
                die(JsonMsg('error', 'Người dùng không tồn tại'));
            }

            $month = Anti_xss($_POST["month"]);
            $domain = Anti_xss($_POST["domain"]);
            $ip = Anti_xss($_POST["ip"]);
            $username = Anti_xss($_POST["username"]);
            $account = Anti_xss($_POST["account"]);
            $password = Anti_xss($_POST["password"]);
            $account_server = Anti_xss($_POST["account_server"]);
            $total = $month * $product["price"];

            $info_whm = array();
            $info_whm['ip'] = $ip;
            $info_whm['username'] = $account_server;
            $db->insert("purchased_hosting", array(
                'user_id' => $getUser['id'],
                'package_id' => $product['id'],
                'ip' => $ip,
                'start_date' => time(),
                'end_date' => time() + (2592000 * $month),
                'username' => $account,
                'password' => $password,
                'month' => $month,
                'email' => $getUser['email'],
                'domain_name' => $domain,
                'server_whm' => json_encode($info_whm),
                'info_package' => json_encode($product),
                'price' => $total,
                'total' => $total,
                'status' => 'active',
                'created_at' => gettime(),
            ));
            insert_log($data_user['id'], 'Thực hiện tạo thủ công hosting tên miền: ' . $domain);
            die(JsonMsg('success', 'Đã tạo hosting thành công'));
        } catch (Exception $e) {

            die(JsonMsg('error', 'Đã xảy ra lỗi ngoại lệ'));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
