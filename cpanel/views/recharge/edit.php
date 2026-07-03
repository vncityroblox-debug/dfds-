<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id =  Anti_xss($_GET['id']);
    $row = $db->get_row(" SELECT * FROM `bank` WHERE `id` = '" . $id . "'  ");
    if (!$row) {
        new Redirect('/cpanel/recharge/bank/config');
    }
} else {
    new Redirect('/cpanel/recharge/bank/config');
}

if (isset($_POST['LuuNganHang']) && $data_user['level'] == 'admin') {
    $isUpdate = $db->update("bank", [
        'short_name'    => strtoupper(Anti_xss($_POST['short_name'])),
        'accountNumber' => Anti_xss($_POST['accountNumber']),
        'accountName'   => Anti_xss($_POST['accountName']),
        'url_api'   => Anti_xss($_POST['url_api']),
        'status'   => Anti_xss($_POST['status'])
    ], " `id` = '$id' ");
    if ($isUpdate) {
        insetLog($data_user['id'], "Cập nhật thông tin ngân hàng (" . $config_listbank[Anti_xss($_POST['short_name'])] . " - " . $_POST['accountNumber'] . ") vào hệ thống.");
        die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Lưu thất bại !")){window.history.back().location.reload();}</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa ngân hàng <?= $row['short_name'] ?></h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Nạp tiền</a></li>
                        <li class="breadcrumb-item"><a href="/cpanel/recharge/bank/config">Ngân hàng</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa ngân hàng
                            VCB</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA NGÂN HÀNG
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="exampleInputEmail1">Ngân hàng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="<?= $row['short_name'] ?>" list="options" name="short_name" placeholder="Nhập tên ngân hàng" required>
                                <datalist id="options">
                                    <?php foreach ($config_listbank as $key => $value) { ?>
                                        <option <?= $row['short_name'] == $key ? 'selected' : ''; ?> value="<?= $key; ?>">
                                            <?= $value; ?></option>
                                    <?php } ?>
                                </datalist>
                            </div>

                            <div class="mb-4">
                                <label for="exampleInputEmail1">Account number</label>
                                <input type="text" class="form-control" name="accountNumber" value="<?= $row['accountNumber'] ?>" placeholder="Nhập số tài khoản" required>
                            </div>
                            <div class="mb-4">
                                <label for="exampleInputEmail1">Account name</label>
                                <input type="text" class="form-control" name="accountName" value="<?= $row['accountName'] ?>" placeholder="Nhập tên chủ tài khoản" required>
                            </div>
                            <div class="mb-4">
                                <label for="exampleInputEmail1">Trạng thái</label>
                                <select class="form-control" name="status">
                                    <option <?= $row['status'] == 1 ? 'selected' : '' ?> value="1">ON</option>
                                    <option <?= $row['status'] == 0 ? 'selected' : '' ?> value="0">OFF</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="exampleInputEmail1">Link API LSGD</label>
                                <input type="text" class="form-control" name="url_api" value="<?= $row['url_api'] ?>" placeholder="Áp dụng khi cấu hình nạp tiền tự động.">
                            </div>
                            <a type="button" class="btn btn-hero btn-danger" href="/cpanel/recharge/bank/config"><i class="fa fa-fw fa-undo me-1"></i>
                                Back</a>
                            <button type="submit" name="LuuNganHang" class="btn btn-hero btn-success"><i class="fa fa-fw fa-save me-1"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>