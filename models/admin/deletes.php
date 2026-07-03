<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        if ($data_user['level'] != 'admin') {
            die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
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
            case 'removePost':
                $post = $db->get_row("SELECT * FROM `posts` WHERE `id` = {$id}");
                if (!$post) {
                    die(JsonMsg('error', 'Bài viết không tồn tại'));
                }
                $isRemove = $db->remove("posts", " `id` = '$id' ");
                if ($isRemove) {
                    unlink('../..' . $post['image']);
                    insert_log($data_user['id'], 'Thực hiện xóa bài viết [' . $post['title'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa bài viết thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa bài viết'));
                break;
            case 'removeCategoryBlog':
                $check_category = $db->get_row("SELECT * FROM `post_category` WHERE `id` = {$id}");
                if (!$check_category) {
                    die(JsonMsg('error', 'Chuyên mục không tồn tại'));
                }
                $isRemove = $db->remove("post_category", " `id` = '$id' ");
                if ($isRemove) {
                    unlink('../..' . $check_category['icon']);
                    insert_log($data_user['id'], 'Thực hiện xóa chuyên mục bài viết [' . $check_category['name'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa chuyên mục bài viết thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa chuyên mục bài viết'));
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
            case 'removeCoupon':
                $check_coupon = $db->get_row("SELECT * FROM `tbl_coupons` WHERE `id` = {$id}");
                if (!$check_coupon) {
                    die(JsonMsg('error', 'Mã giảm giá không tồn tại'));
                }
                $isRemove = $db->remove("tbl_coupons", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện mã giảm giá [' . $check_coupon['code'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa mã giảm giá thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa mã giảm giá'));
                break;
            case 'removeWHM':
                $check_server = $db->get_row("SELECT * FROM `whm_info` WHERE `id` = {$id}");
                if (!$check_server) {
                    die(JsonMsg('error', 'Máy chủ không tồn tại'));
                }
                $isRemove = $db->remove("whm_info", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa máy chủ [' . $check_server['ip'] . ' - ' . $check_server['username'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa máy chủ thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa máy chủ'));
                break;
            case 'removePackage':
                $package = $db->get_row("SELECT * FROM hosting_packages WHERE id = {$id}");
                if (!$package) {
                    die(JsonMsg('error', 'Gói không tồn tại'));
                }
                if ($db->num_rows("SELECT * FROM `purchased_hosting` WHERE `package_id` = '{$package['id']}' ") > 0) {
                    die(JsonMsg('error', 'Không thể xóa gói hosting khi đang có khách hàng sử dụng'));
                }

                $isRemove = $db->remove("hosting_packages", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], "Thực hiện xóa gói hosting " . $package['name']);
                    die(JsonMsg('success', 'Xóa gói thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa gói'));
                break;
            default:
                // code...
                break;
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
