<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(JsonMsg('error', 'Access Denied'));
}
if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    die(jsonMsg('error', 'Invalid CSRF Protection Token'));
}
$product_id = Anti_xss($_POST['product_id']);
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if (!$user) {
                die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
            }
            $getUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '" . $data_user['username'] . "' AND `banned`=0");
            if (empty($product_id)) {
                die(JsonMsg('error', 'Vui lòng chọn sản phẩm yêu thích'));
            }
            $user_id = $getUser['id'];
            $check_fav_query = $db->get_row("SELECT * FROM favorites WHERE user_id = '{$user_id}' AND product_id = '{$product_id}'");

            if ($check_fav_query) {
                $db->query("DELETE FROM favorites WHERE user_id = '{$user_id}' AND product_id = '{$product_id}'");
                $status = 'removed';
                $msg = 'Đã xóa sản phẩm khỏi danh sách yêu thích';
            } else {
                $db->query("INSERT INTO favorites (user_id, product_id) VALUES ('{$user_id}', '{$product_id}')");
                $status = 'added';
                $msg = 'Đã thêm sản phẩm vào danh sách yêu thích';
            }

            $fav_count = $db->get_row("SELECT COUNT(*) FROM favorites WHERE user_id = '{$user_id}'");

            die(json_encode(['status' => $status, 'msg' => $msg, 'fav_count' => $fav_count['COUNT(*)']]));