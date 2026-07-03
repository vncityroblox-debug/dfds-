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
            if (!($product = $db->get_row("SELECT * FROM `hosting_packages` WHERE `id` = '" . Anti_xss($_POST["package"]) . "' AND `status` = 1 "))) {
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

            $total = $month * $product["price"];

            if ($total < 0) {
                die(JsonMsg('error', 'Dữ liệu không hợp lệ'));
            }

            $username = strtolower(extractDomain($domain));
            $password = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM@#%mnbvcxzlkjhgfdsapoiuytrewq', rand(15, 16));
            if ($whm['username'] == 'root') {
                $package = $row['package_name'];
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
                        'user_id' => $getUser['id'],
                        'package_id' => $product['id'],
                        'ip' => $result['data']['ip'],
                        'start_date' => time(),
                        'end_date' => time() + (2592000 * $month),
                        'username' => $username,
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
                    insert_log($data_user['id'],'Thực hiện tạo mới hosting tên miền: '.$domain);
                    die(JsonMsg('success', 'Đã tạo hosting thành công'));
                } else {
                    exit(json_encode(array('status' => 'error', 'msg' => $result['metadata']['reason'])));
                }
            } else {

                exit(json_encode(array('status' => 'error', 'msg' => 'Đã xảy ra lỗi khi tạo hosting')));
            }
        } catch (Exception $e) {

            die(JsonMsg('error', 'Đã xảy ra lỗi ngoại lệ'));
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
