<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        if ($data_user['level'] != 'admin') {
            die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
        }
        if(check_license($db->site('license'))['status'] == 'error'){
            die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
        }
        $id = Anti_xss($_POST['id']);
        $action = Anti_xss($_POST['action']);
        if (empty($id) || empty($action)) {
            die(JsonMsg('error', 'Vui lòng chọn dữ liệu'));
        }
        switch ($action) {
            case 'removeUser':
                $check_user = $db->get_row("SELECT * FROM `users` WHERE `id` = {$id}");
                if (!$check_user) {
                    die(JsonMsg('error', 'Người dùng không tồn tại'));
                }
                $isRemove = $db->remove("users", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa thành viên [' . $check_user['username'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa người dùng thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa người dùng'));
                break;
            case 'removeBank':
                $check_bank = $db->get_row("SELECT * FROM `bank` WHERE `id` = {$id}");
                if (!$check_bank) {
                    die(JsonMsg('error', 'Ngân hàng không tồn tại'));
                }
                $isRemove = $db->remove("bank", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa ngân hàng [' . $check_bank['short_name'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa ngân hàng thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa ngân hàng'));
                break;
            case 'removePromotion':
                $check_promotions = $db->get_row("SELECT * FROM `promotions` WHERE `id` = {$id}");
                if (!$check_promotions) {
                    die(JsonMsg('error', 'Mốc khuyến mãi không tồn tại'));
                }
                $isRemove = $db->remove("promotions", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa mốc khuyến mãi [' . format_cash($check_promotions['amount']) . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa mốc khuyến mãi thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa mốc khuyến mãi'));
                break;
            case 'deleteCron':
                $cronjob = $db->get_row("SELECT * FROM `cronjobs` WHERE `id` = {$id}");
                if (!$cronjob) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                $isRemove = $db->remove("cronjobs", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa link cron [' . $cronjob['url'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa cron thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa cron'));
                break;
            case 'removeServerCron':
                $server = $db->get_row("SELECT * FROM `server_cronjobs` WHERE `id` = {$id}");
                if (!$server) {
                    die(JsonMsg('error', 'Máy chủ không tồn tại'));
                }
                $isRemove = $db->remove("server_cronjobs", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa máy chủ [' . $server['name'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa máy chủ thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa máy chủ'));
                break;
            case 'removeCoupon':
                $check_coupon = $db->get_row("SELECT * FROM `tbl_coupons` WHERE `id` = {$id}");
                if (!$check_coupon) {
                    die(JsonMsg('error', 'Mã giảm giá không tồn tại'));
                }
                $isRemove = $db->remove("tbl_coupons", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa mã giảm giá [' . $check_coupon['code'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa mã giảm giá thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa mã giảm giá'));
                break;
            case 'removeCategory':
                $categories = $db->get_row("SELECT * FROM categories WHERE category_id = {$id}");
                if (!$categories) {
                    die(JsonMsg('error', 'Danh mục không tồn tại'));
                }
                if ($db->remove('categories', "category_id = " . (int)$id)) {
                    insert_log($data_user['id'], "Thực hiện xóa danh mục " . $categories['name']);
                    die(JsonMsg('success', 'Xóa danh mục thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa danh mục'));
                break;
            case 'removeProduct':
                $product = $db->get_row("SELECT * FROM `products` WHERE `product_id` = {$id}");
                if (!$product) {
                    die(JsonMsg('error', 'Sản phẩm không tồn tại'));
                }
                if (!empty($product['thumbnail'])) {
                    $thumb_path = realpath($_SERVER["DOCUMENT_ROOT"]) . $product['thumbnail'];
                    if (file_exists($thumb_path)) {
                        unlink($thumb_path); // Xóa ảnh thumb từ thư mục
                    }
                }
                $query_demo_images = $db->get_list("SELECT * FROM `product_images` WHERE `product_id` = {$id}");
                foreach ($query_demo_images as $image) {
                    $image_path = realpath($_SERVER["DOCUMENT_ROOT"]) . $image['image_url'];
                    if (file_exists($image_path)) {
                        unlink($image_path); // Xóa ảnh demo từ thư mục
                    }
                }
                $isRemove = $db->remove("products", " `product_id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa sản phẩm [' . $product['title'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa sản phẩm thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa sản phẩm'));
                break;
            case 'deleteOrderProduct':
                $order = $db->get_row("SELECT * FROM `orders` WHERE `order_id` = {$id}");
                if (!$order) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                $isRemove = $db->remove("orders", " `order_id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa đơn hàng [' . $order['order_id'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa đơn hàng thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa đơn hàng'));
                break;
            case 'removeListVps':
                $order = $db->get_row("SELECT * FROM `list_vps` WHERE `id` = {$id}");
                if (!$order) {
                    die(JsonMsg('error', 'Gói VPS không tồn tại'));
                }
                $isRemove = $db->remove("list_vps", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa gói vps [' . $order['package_name'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa đơn hàng thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa đơn hàng'));
                break;
            case 'deleteTablePlan':
                $plan = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `id` = {$id}");
                if (!$plan) {
                    die(JsonMsg('error', 'Gói VPS không tồn tại'));
                }
                $isRemove = $db->remove("tbl_cloudvps", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa gói VPS [' . $plan['name'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa gói VPS thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa gói VPS'));
                break;
            default:
                die(JsonMsg('error', 'Hành động không hợp lệ'));
                break;
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}