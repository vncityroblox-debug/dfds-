<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

// Kiểm tra quyền admin và lấy dữ liệu gói VPS
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $vps = $db->get_row("SELECT * FROM `list_vps` WHERE `id` = '" . $id . "'");
    if (!$vps) {
        new Redirect("/cpanel/vps");
    }
} else {
    new Redirect("/cpanel/vps");
}

// Xử lý khi submit form
if (isset($_POST['submit']) && $data_user['level'] == 'admin') {
    $data = [
        'package_name' => Anti_xss($_POST['package_name']),
        'cpu' => Anti_xss($_POST['cpu']),
        'ram' => Anti_xss($_POST['ram']),
        'disk' => Anti_xss($_POST['disk']),
        'ip' => Anti_xss($_POST['ip']),
        'bandwidth' => Anti_xss($_POST['bandwidth']),
        'os' => Anti_xss($_POST['os']),
        'price' => Anti_xss($_POST['price']),
        'period' => Anti_xss($_POST['period'])
    ];

    if ($db->update('list_vps', $data, "id = $id")) {
        die('<script type="text/javascript">alert("Gói VPS đã được chỉnh sửa thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Có lỗi khi chỉnh sửa gói VPS"); window.history.back();</script>');
    }
}

active_license();
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-server"></i> Chỉnh sửa Gói VPS #[<?= $vps['package_name'] ?>]</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA GÓI VPS
                        </div>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="package_name" class="form-label">Tên gói VPS</label>
                                <input type="text" class="form-control" id="package_name" name="package_name" value="<?= $vps['package_name'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="cpu" class="form-label">CPU</label>
                                <input type="text" class="form-control" id="cpu" name="cpu" value="<?= $vps['cpu'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="ram" class="form-label">RAM</label>
                                <input type="text" class="form-control" id="ram" name="ram" value="<?= $vps['ram'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="disk" class="form-label">Disk</label>
                                <input type="text" class="form-control" id="disk" name="disk" value="<?= $vps['disk'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="ip" class="form-label">IP</label>
                                <input type="text" class="form-control" id="ip" name="ip" value="<?= $vps['ip'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="bandwidth" class="form-label">Bandwidth</label>
                                <input type="text" class="form-control" id="bandwidth" name="bandwidth" value="<?= $vps['bandwidth'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="os" class="form-label">Hệ điều hành (OS)</label>
                                <input type="text" class="form-control" id="os" name="os" value="<?= $vps['os'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Giá</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $vps['price'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="period" class="form-label">Chu kỳ</label>
                                <input type="text" class="form-control" id="period" name="period" value="<?= $vps['period'] ?>" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/cpanel/vps" class="btn btn-danger waves-effect">QUAY LẠI</a>
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