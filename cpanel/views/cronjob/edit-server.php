<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $server = $db->get_row("SELECT * FROM `server_cronjobs` WHERE `id` = '" . $id . "'");
    if (!$server) {
        new Redirect("/cpanel/cron/server");
    }
} else {
    new Redirect("/cpanel/cron/server");
}
if (isset($_POST['submit']) && $data_user['level'] == 'admin') {


    $data = [
        'name' => Anti_xss($_POST['name']),
        'price' => Anti_xss($_POST['price']),
        'usage_limit' => Anti_xss($_POST['usage_limit']),
        'current_usage' => Anti_xss($_POST['current_usage']),
        'discount_percent' => Anti_xss($_POST['discount_percent']),
        'discount_valid_until' => Anti_xss($_POST['discount_valid_until'])
    ];


    if ($db->update('server_cronjobs', $data, "id = $id")) {
        die('<script type="text/javascript">alert("Danh mục đã được chỉnh sửa thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Có lỗi khi chỉnh sửa danh mục"); window.history.back();</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Chỉnh sửa Server Cron Job #[<?= $server['name'] ?>]</h1>
        </div>
        <div class="row">

            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA SERVER CRON
                        </div>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="card-body">

                            <div class="mb-3">
                                <label for="name" class="form-label">Tên máy chủ</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?=$server['name']?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Giá</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?=$server['price']?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">Giới hạn sử dụng</label>
                                <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="<?=$server['usage_limit']?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="current_usage" class="form-label">Current Usage</label>
                                <input type="number" class="form-control" id="current_usage" name="current_usage" value="<?=$server['current_usage']?>">
                            </div>

                            <div class="mb-3">
                                <label for="discount_percent" class="form-label">Phần trăm chiết khấu giảm giá</label>
                                <input type="number" class="form-control" id="discount_percent" name="discount_percent" value="<?=$server['discount_percent']?>">
                            </div>

                            <div class="mb-3">
                                <label for="discount_valid_until" class="form-label">Giảm giá có giá trị đến</label>
                                <input type="datetime-local" class="form-control" id="discount_valid_until" name="discount_valid_until" value="<?=$server['discount_valid_until']?>">
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/cpanel/cron/server" class="btn btn-danger waves-effect">QUAY
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