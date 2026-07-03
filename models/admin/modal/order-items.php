<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}
if ($data_user['level'] != 'admin') {
    die(JsonMsg('error', 'Bạn không có quyền truy cập vào trang này'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    die(jsonMsg('error', 'Invalid CSRF Protection Token'));
}
if (!isset($_POST["id"]) || empty($_POST["id"])) {
    die(jsonMsg('error', 'Vui lòng chọn đơn'));
}
$id = Anti_xss($_POST["id"]);
if (!($row = $db->get_row(" SELECT * FROM `order_items` WHERE `id` = '" . Anti_xss($_POST["id"]) . "'  "))) {
}
?>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Nhóm</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= $row['name_package'] ?>" disabled>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Vật phẩm</label>
        <input type="text" class="form-control" value="<?= $row['value'] ?> <?= $row['unit'] ?>" disabled>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="code" class="form-label">Mã đơn</label>
            <input type="text" id="code" name="code" class="form-control" value="<?= $row['code'] ?>" disabled>
        </div>
        <div class="col-md-6">
            <label for="payment" class="form-label">Thanh toán</label>
            <input type="text" id="payment" name="payment" class="form-control" value="<?= format_cash($row['payment']) ?> ₫" disabled>
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-md-4">
            <label for="input_user" class="form-label">Tài khoản</label>
            <input type="text" id="input_user" name="input_user" class="form-control" value="<?= decodecryptData($row['input_user']) ?>" disabled>
        </div>
        <div class="col-md-4">
            <label for="input_pass" class="form-label">Mật khẩu</label>
            <input type="text" id="input_pass" name="input_pass" class="form-control" value="<?= decodecryptData($row['input_pass']) ?>" disabled>
        </div>
        <div class="col-md-4">
            <label for="input_auth" class="form-label">Đăng nhập</label>
            <input type="text" id="input_auth" name="input_auth" class="form-control" value="<?= $row['input_extra'] ?>" disabled>
        </div>
    </div>
    <div class="mb-3">
        <label for="admin_note" class="form-label">Ghi chú admin</label>
        <textarea class="form-control" id="admin_note" name="admin_note" rows="3"><?= $row['admin_note'] ?></textarea>
    </div>
    <div class="mb-3">
        <label for="order_note" class="form-label">Ghi chú khách</label>
        <textarea class="form-control" id="order_note" name="order_note" rows="3"><?= $row['order_note'] ?></textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Trạng thái</label>
        <select class="form-select" id="status" name="status" required>
            <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
            <option value="processing" <?= $row['status'] == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
            <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
            <option value="error_refund" <?= $row['status'] == 'error_refund' ? 'selected' : '' ?>>Lỗi Đơn / hoàn tiền</option>
            <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Hủy đơn</option>
            <option value="cancelled_refund" <?= $row['status'] == 'cancelled_refund' ? 'selected' : '' ?>>Hủy đơn hoàn tiền</option>
        </select>
    </div>
    <div class="mb-3">
        <button class="btn btn-primary" type="button" id="change" onclick="updateOrder(<?= $row['id'] ?>)">Cập nhật</button>
    </div>
</form>