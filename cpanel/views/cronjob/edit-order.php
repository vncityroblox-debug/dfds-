<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $cron = $db->get_row("SELECT * FROM `cronjobs` WHERE `id` = '" . $id . "'");
    if (!$cron) {
        new Redirect("/cpanel/cron/order");
    }
} else {
    new Redirect("/cpanel/cron/order");
}
if (isset($_POST['submit']) && $data_user['level'] == 'admin') {


    $data = [
        'url' => Anti_xss($_POST['url']),
        'cron_expression' => Anti_xss($_POST['cron_expression']),
        'method' => Anti_xss($_POST['method']),
        'server_id' => Anti_xss($_POST['server_id']),
        'expires_at' => Anti_xss($_POST['expires_at']),
        'headers' => Anti_xss($_POST['headers']),
        'body' => Anti_xss($_POST['body']),
        'status' => Anti_xss($_POST['status'])
    ];


    if ($db->update('cronjobs', $data, "id = $id")) {
        die('<script type="text/javascript">alert("Đơn hàng đã được chỉnh sửa thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Có lỗi khi chỉnh sửa đơn hàng"); window.history.back();</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Chỉnh sửa Link Cron Job #[<?= $cron['id'] ?>]</h1>
        </div>
        <div class="row">

            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA ĐƠN HÀNG CRON
                        </div>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="card-body">

                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="example-hf-email">Liên kết cần CRON (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="<?= $cron['url'] ?>"
                                        name="url" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Vòng lặp (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="<?= $cron['cron_expression'] ?>"
                                        name="cron_expression" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Method</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="method" required>

                                        <option value="GET" <?= $cron['method'] == "GET" ? 'selected' : '' ?>>GET</option>
                                        <option value="POST" <?= $cron['method'] == "POST" ? 'selected' : '' ?>>POST
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Máy chủ</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="server_id" required>
                                        <?php foreach ($db->get_list("SELECT * FROM `server_cronjobs` WHERE `status` = 1") as $server): ?>
                                            <option value="<?= $server['id'] ?>" <?= $cron['server_id'] == $server['id'] ? 'selected' : '' ?>>
                                                <?= $server['name'] ?> - <?= format_cash($server['price']) ?>đ</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Headers (Tùy chọn)</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control shadow-none" id="headers" name="headers" rows="3" placeholder="{&quot;Content-Type&quot;: &quot;application/json&quot;}"><?= $cron['headers'] ?></textarea>
                                    <div class="form-text">Headers phải ở định dạng JSON (ví dụ: {"Content-Type": "application/json"})</div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Body (Tùy chọn)</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control shadow-none" id="body" name="body" rows="3" placeholder="{&quot;key&quot;: &quot;value&quot;}"><?= $cron['body'] ?></textarea>
                                    <div class="form-text">Body cần nhập khi chọn phương thức POST</div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="example-hf-email">Thời gian hết hạn (<span
                                        class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-text text-muted"> <i class="ri-time-line"></i>
                                        </div>
                                        <input type="text" class="form-control" id="limitdatetime" name="expires_at"
                                            value="<?= $cron['expires_at'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Trạng thái</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="status" required>
                                        <option value="active" <?= $cron['status'] == "active" ? 'selected' : '' ?>>Hoạt
                                            động</option>
                                        <option value="paused" <?= $cron['status'] == "paused" ? 'selected' : '' ?>>Tạm dừng
                                        </option>
                                        <option value="expired" <?= $cron['status'] == "expired" ? 'selected' : '' ?>>Hết
                                            hạn</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="example-hf-email">Tổng số tiền đã thanh toán (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="<?= format_cash($cron['payment']) ?>đ"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/cpanel/cron/order" class="btn btn-danger waves-effect">QUAY
                                LẠI</a>
                            <button type="submit" name="submit" class="btn btn-success">LƯU NGAY</button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>