<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        if ($data_user['level'] != 'admin') {
            die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
        }
        if (check_license($db->site('license'))['status'] == 'error') {
            die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
        }
        $id = Anti_xss($_POST['id']);
        $action = Anti_xss($_POST['action']);
        if (empty($id) || empty($action)) {
            die(JsonMsg('error', 'Vui lòng chọn dữ liệu'));
        }
        switch ($action) {
            case 'updateTableWhm':
                if (Anti_xss((int)$_POST['status']) > 0) {
                    if ($db->num_rows("SELECT * FROM `whm_info` WHERE `status` = 1 AND `id` != '" . Anti_xss($_POST["id"]) . "'") > 0) {
                        die(JsonMsg('error', 'Bạn đang có một máy chủ đang hoạt động, vui lòng tắt máy chủ đó trước khi chỉnh sửa máy chủ mới!'));
                    }
                }
                $isUpdate = $db->update("whm_info", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái whm (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableService':
                $isUpdate = $db->update("services", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái dịch vụ (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableDomainPricing':
                $isUpdate = $db->update("domain_pricing", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái tên miền (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTablePost':
                $isUpdate = $db->update("posts", ["stt" => !empty($_POST["stt"]) ? Anti_xss($_POST["stt"]) : 0, "status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái bài viết (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTablePlan':
                $isUpdate = $db->update("tbl_cloudvps", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái gói VPS (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableCategoryBlog':
                $isUpdate = $db->update("post_category", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái chuyên mục bài viết (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableCategoryLogo':
                $isUpdate = $db->update("category_logo", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái chuyên mục logo (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableCategory':
                $isUpdate = $db->update("categories", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `category_id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Update Table Category (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableCategoryBoosting':
                $isUpdate = $db->update("boostings", ["stt" => !empty($_POST["stt"]) ? Anti_xss($_POST["stt"]) : 0, "status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Update Table Category (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableSubCategory':
                $isUpdate = $db->update("subcategory", ["stt" => !empty($_POST["stt"]) ? Anti_xss($_POST["stt"]) : 0, "status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Update Table SubCategory (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableSubboosting':
                $isUpdate = $db->update("subboostings", ["stt" => !empty($_POST["stt"]) ? Anti_xss($_POST["stt"]) : 0, "status" => !empty($_POST["status"]) ? Anti_xss($_POST["stt"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Update Table SubCategory (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableBanner':
                $isUpdate = $db->update("banner", ["stt" => !empty($_POST["stt"]) ? Anti_xss($_POST["stt"]) : 0, "status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Update Table banner (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableFeedback':
                $isUpdate = $db->update("feedbacks", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật đánh giá (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
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
            case 'removeCategoryCaptcha':
                $check_bank = $db->get_row("SELECT * FROM `category_captcha` WHERE `id` = {$id}");
                if (!$check_bank) {
                    die(JsonMsg('error', 'Dịch vụ không tồn tại'));
                }
                $isRemove = $db->remove("category_captcha", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa dịch vụ [' . $check_bank['name'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa dịch vụ thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa dịch vụ'));
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
                case 'addServerHost':
                $data = [
                    'package_name' => Anti_xss($_POST['package_name'] ?? ''),
                    'storage' => Anti_xss($_POST['storage'] ?? ''),
                    'bandwidth' => Anti_xss($_POST['bandwidth'] ?? ''),
                    'ssl' => Anti_xss($_POST['ssl'] ?? ''),
                    'domains' => Anti_xss($_POST['domains'] ?? ''),
                    'aliases' => Anti_xss($_POST['aliases'] ?? ''),
                    'other_params' => Anti_xss($_POST['other_params'] ?? ''),
                    'location' => Anti_xss($_POST['location'] ?? ''),
                    'price' => Anti_xss($_POST['price'] ?? ''),
                    'period' => Anti_xss($_POST['period'] ?? ''),
                    'status' => 1 // Mặc định là active
                ];

                // Kiểm tra các trường bắt buộc
                foreach ($data as $key => $value) {
                    if (empty($value) && $key != 'status') {
                        die(JsonMsg('error', 'Vui lòng điền đầy đủ thông tin'));
                    }
                }

                if ($db->insert('server_host', $data)) {
                    insert_log($data_user['id'], "Thêm gói hosting [" . $data['package_name'] . "] vào hệ thống");
                    die(JsonMsg('success', 'Thêm gói hosting thành công'));
                } else {
                    die(JsonMsg('error', 'Thêm gói hosting thất bại'));
                }
                break;

            default:
                die(JsonMsg('error', 'Hành động không hợp lệ'));
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
            case 'removeServerHost':
                $check_host = $db->get_row("SELECT * FROM `server_host` WHERE `id` = {$id}");
                if (!$check_host) {
                    die(JsonMsg('error', 'Gói hosting không tồn tại'));
                }
                $isRemove = $db->remove("server_host", " `id` = '$id' ");
                if ($isRemove) {
                    insert_log($data_user['id'], 'Thực hiện xóa gói hosting [' . $check_host['package_name'] . '] ra khỏi hệ thống');
                    die(JsonMsg('success', 'Xóa gói hosting thành công'));
                }
                die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa gói hosting'));
                break;
            case 'updateOrderDomain':
                $status = Anti_xss($_POST["status"]);
                $admin_note = !empty($_POST["admin_note"]) ? Anti_xss($_POST["admin_note"]) : '';
                if (!$check_order = $db->get_row("SELECT * FROM `order_domains` WHERE `id` = '{$id}'")) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                if ($status == "error_refund" || $status == "cancelled_refund" || $status == "cancelled") {
                    if ($check_order['status'] == "error_refund" || $check_order['status'] == "cancelled_refund" || $check_order['status'] == "cancelled") {
                        die(JsonMsg('error', 'Đơn hàng này đã hủy hoặc hoàn tiền rồi'));
                    }
                    PlusCredits($check_order['user_id'], $check_order['total_amount'], $admin_note);
                    $db->update("order_domains", ["status" => $status, "admin_note" => $admin_note], " `id` = '{$id}' ");
                    insert_log($data_user['id'], "Cập nhật đơn hàng mua tên miền (ID {$id}) với trạng thái {$status} và ghi chú {$admin_note}");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                } else {
                    $isUpdate = $db->update("order_domains", ["status" => $status, "admin_note" => $admin_note], " `id` = '{$id}' ");
                    if ($isUpdate) {
                        insert_log($data_user['id'], "Cập nhật đơn hàng mua tên miền (ID {$id}) với trạng thái {$status} và ghi chú {$admin_note}");
                        die(JsonMsg('success', 'Cập nhật thành công'));
                    } else {
                        die(JsonMsg('error', 'Cập nhật không thành công'));
                    }
                }
                break;
            case 'updateOrderService':
                $status = Anti_xss($_POST["status"]);
                $admin_note = !empty($_POST["admin_note"]) ? Anti_xss($_POST["admin_note"]) : '';
                if (!$check_order = $db->get_row("SELECT * FROM `order_services` WHERE `id` = '{$id}'")) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                if ($status == "error_refund" || $status == "cancelled_refund" || $status == "cancelled") {
                    if ($check_order['status'] == "error_refund" || $check_order['status'] == "cancelled_refund" || $check_order['status'] == "cancelled") {
                        die(JsonMsg('error', 'Đơn hàng này đã hủy hoặc hoàn tiền rồi'));
                    }
                    PlusCredits($check_order['user_id'], $check_order['total'], $admin_note);
                    $db->update("order_services", ["status" => $status, "admin_note" => $admin_note], " `id` = '{$id}' ");
                    insert_log($data_user['id'], "Cập nhật đơn hàng tạo shop (ID {$id}) với trạng thái {$status} và ghi chú {$admin_note}");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                } else {
                    $isUpdate = $db->update("order_services", ["status" => $status, "admin_note" => $admin_note], " `id` = '{$id}' ");
                    if ($isUpdate) {
                        insert_log($data_user['id'], "Cập nhật đơn hàng tạo shop (ID {$id}) với trạng thái {$status} và ghi chú {$admin_note}");
                        die(JsonMsg('success', 'Cập nhật thành công'));
                    } else {
                        die(JsonMsg('error', 'Cập nhật không thành công'));
                    }
                }
                break;
            case 'updateOrderLogo':
                $status = Anti_xss($_POST["status"]);
                $admin_note = !empty($_POST["note"]) ? Anti_xss($_POST["note"]) : '';
                $download = !empty($_POST["download"]) ? Anti_xss($_POST["download"]) : '';
                if (!$check_order = $db->get_row("SELECT * FROM `order_logos` WHERE `id` = '{$id}'")) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                if ($status == "error_refund" || $status == "cancelled_refund" || $status == "cancelled") {
                    if ($check_order['status'] == "error_refund" || $check_order['status'] == "cancelled_refund" || $check_order['status'] == "cancelled") {
                        die(JsonMsg('error', 'Đơn hàng này đã hủy hoặc hoàn tiền rồi'));
                    }
                    PlusCredits($check_order['user_id'], $check_order['payment'], $admin_note);
                    $db->update("order_logos", ["status" => $status, "note" => $admin_note, "download" => $download], " `id` = '{$id}' ");
                    insert_log($data_user['id'], "Cập nhật đơn hàng tạo logo (ID {$id}) với trạng thái {$status} và ghi chú {$admin_note}");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                } else {
                    $isUpdate = $db->update("order_logos", ["status" => $status, "note" => $admin_note, "download" => $download], " `id` = '{$id}' ");
                    if ($isUpdate) {
                        insert_log($data_user['id'], "Cập nhật đơn hàng tạo logo (ID {$id}) với trạng thái {$status} và ghi chú {$admin_note}");
                        die(JsonMsg('success', 'Cập nhật thành công'));
                    } else {
                        die(JsonMsg('error', 'Cập nhật không thành công'));
                    }
                }
                break;
            case 'pauseCron':
                $cronjob = $db->get_row("SELECT * FROM `cronjobs` WHERE `id` = {$id}");
                if (!$cronjob) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                $isUpdate = $db->update("cronjobs", array('status' => 'paused'), " `id` = '" . $id . "' ");
                if ($isUpdate) {
                    die(JsonMsg('success', 'Đã tạm dừng thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi cập nhập dữ liệu'));
                }
                break;
            case 'activeCron':
                $cronjob = $db->get_row("SELECT * FROM `cronjobs` WHERE `id` = {$id}");
                if (!$cronjob) {
                    die(JsonMsg('error', 'Đơn hàng không tồn tại'));
                }
                $isUpdate = $db->update("cronjobs", array('status' => 'active'), " `id` = '" . $id . "' ");
                if ($isUpdate) {
                    die(JsonMsg('success', 'Đưa vào hàng đợi chạy thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi cập nhập dữ liệu'));
                }
                break;
            case 'updateTableServerCron':
                $isUpdate = $db->update("server_cronjobs", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật máy chủ (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableServerHost':
                $isUpdate = $db->update("server_host", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái gói hosting (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableLogo':
                $isUpdate = $db->update("logos", ["stt" => !empty($_POST["stt"]) ? Anti_xss($_POST["stt"]) : 0, "status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0], " `id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Update Table Logo (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'updateTableProduct':
                $isUpdate = $db->update("products", ["status" => !empty($_POST["status"]) ? Anti_xss($_POST["status"]) : 0, "approved" => !empty($_POST["approved"]) ? Anti_xss($_POST["approved"]) : 0], " `product_id` = '" . Anti_xss($_POST["id"]) . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cập nhật trạng thái sản phẩm (ID " . Anti_xss($_POST["id"]) . ")");
                    die(JsonMsg('success', 'Cập nhật thành công'));
                }
                die(JsonMsg('error', 'Cập nhật thất bại'));
                break;
            case 'turnOnVps':
                $vps = $db->get_row("SELECT * FROM `order_vps` WHERE `id` = {$id}");
                if (!$vps) {
                    die(JsonMsg('error', 'VPS không tồn tại'));
                }
                $isUpdate = $db->update("order_vps", array('status' => 'on'), " `id` = '" . $id . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Bật VPS (ID " . $id . ")");
                    die(JsonMsg('success', 'Đã bật VPS thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi khi cập nhật dữ liệu'));
                }
                break;
            case 'turnOffVps':
                $vps = $db->get_row("SELECT * FROM `order_vps` WHERE `id` = {$id}");
                if (!$vps) {
                    die(JsonMsg('error', 'VPS không tồn tại'));
                }
                $isUpdate = $db->update("order_vps", array('status' => 'off'), " `id` = '" . $id . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Tắt VPS (ID " . $id . ")");
                    die(JsonMsg('success', 'Đã tắt VPS thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi khi cập nhật dữ liệu'));
                }
                break;
            case 'extendVps':
                $vps = $db->get_row("SELECT * FROM `order_vps` WHERE `id` = {$id}");
                if (!$vps) {
                    die(JsonMsg('error', 'VPS không tồn tại'));
                }
                $new_expired_at = date('Y-m-d H:i:s', strtotime($vps['expired_at'] . ' +1 month'));
                $isUpdate = $db->update("order_vps", array('expired_at' => $new_expired_at), " `id` = '" . $id . "' ");
                if ($isUpdate) {
                    insert_log($data_user['id'], "Cộng thêm 1 tháng cho VPS (ID " . $id . ")");
                    die(JsonMsg('success', 'Đã cộng thêm 1 tháng thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi khi cập nhật dữ liệu'));
                }
                break;
                
            case 'deleteVps':
                $vps = $db->get_row("SELECT * FROM `order_vps` WHERE `id` = {$id}");
                if (!$vps) {
                    die(JsonMsg('error', 'VPS không tồn tại'));
                }
                $isRemove = $db->remove("order_vps", " `id` = '" . $id . "' ");
                if ($isRemove) {
                    insert_log($data_user['id'], "Xóa VPS (ID " . $id . ")");
                    die(JsonMsg('success', 'Đã xóa VPS thành công'));
                } else {
                    die(JsonMsg('error', 'Đã xảy ra lỗi khi xóa VPS'));
                }
                break;
                
        }
    } else {
        die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
    }
}
