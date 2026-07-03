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
if (!($row = $db->get_row(" SELECT * FROM `withdraw_logs` WHERE `id` = '" . Anti_xss($_POST["id"]) . "'  "))) {
}
?>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Khách hàng</label>
        <input type="text" id="username" name="username" class="form-control" value="<?= $row['username'] ?>" disabled>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="code" class="form-label">Mã đơn</label>
            <input type="text" id="code" name="code" class="form-control" value="#<?= $row['id'] ?>" disabled>
        </div>
        <div class="col-md-6">
            <label for="payment" class="form-label">Thanh toán</label>
            <input type="text" id="payment" name="payment" class="form-control" value="<?= format_cash($row['value']) ?> <?=$row['unit']?>" disabled>
        </div>
    </div>
    <div class="mb-3">
        <label for="order_note" class="form-label">Ghi chú khách</label>
        <textarea class="form-control" id="user_note" name="user_note" rows="3"><?= $row['user_note'] ?></textarea>
    </div>
    <div class="mb-3">
        <label for="admin_note" class="form-label">Ghi chú admin</label>
        <textarea class="form-control" id="admin_note" name="admin_note" rows="3"><?= $row['admin_note'] ?></textarea>
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