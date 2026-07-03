<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `id` = '" . $id . "'");
    if (!$row) {
        new Redirect("/cpanel/vps/plan");
    }
    $pricing = json_decode($row['pricing'], true);
    $price = json_decode($row['price'], true);
} else {
    new Redirect("/cpanel/vps/plan");
}
if (isset($_POST['updatePlan']) && $data_user['level'] == 'admin') {
    $updated_price = [];

    foreach ($price as $key => $value) {
        if (isset($_POST[$key])) {
            $new_amount = Anti_xss($_POST[$key]);

            if ($new_amount !== $value['amount']) {
                $updated_price[$key] = $value;
                $updated_price[$key]['amount'] = $new_amount;
            }
        }
    }

    if (!empty($updated_price)) {
        foreach ($updated_price as $key => $value) {
            $price[$key] = $value;
        }
    }

    $isUpdate = $db->update("tbl_cloudvps", array(
        'price'         => json_encode($price),
        'status'        => Anti_xss($_POST['status']),
        'updated_at'    => gettime()
    ), " `id` = '" . $row['id'] . "' ");

    if ($isUpdate) {
        die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Lưu thất bại!")){window.history.back().location.reload();}</script>');
    }
}
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Chỉnh sửa gói VPS</h1>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">CHỈNH SỬA GÓI VPS - #<?= $row['id'] ?></h5>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row clearfix">
                                <b class="text-danger">Giá gốc</b>
                                <?php foreach ($pricing as $key => $datapricing) : ?>
                                    <div class="col-sm-3 mb-2">
                                        <label for="taikhoan"><?= $datapricing['billing_cycle'] ?></label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" value="<?= $datapricing['amount'] ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                                <b class="text-primary">Giá bán</b>
                                <?php foreach ($price as $key => $dataprice) : ?>
                                    <div class="col-sm-3 mb-2">
                                        <label for="taikhoan"><?= $dataprice['billing_cycle'] ?></label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="<?= $key ?>" value="<?= $dataprice['amount'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>

                            </div>
                            <div class="row">
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Trạng thái:</label>
                                    <select name="status" class="form-control" data-toggle="select2" required>

                                        <option <?= $row['status'] == '1' ? 'selected' : ''; ?> value="1">Hoạt
                                            động
                                        </option>
                                        <option <?= $row['status'] == '0' ? 'selected' : ''; ?> value="0">
                                            Tạm dừng</option>

                                    </select>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Ngày tạo:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="start_date" type="text" class="form-control" value="<?= $row['created_at'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Cập nhật:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="end_date" type="text" class="form-control" value="<?= $row['updated_at'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/cpanel/vps/plan" class="btn btn-danger waves-effect">QUAY LẠI</a>
                            <button type="submit" name="updatePlan" class="btn btn-success">LƯU NGAY</button>
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