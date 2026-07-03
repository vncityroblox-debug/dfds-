<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $rows = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `id` = '" . $id . "' AND `site` = 'CLOUDNEST'");
    if (!$rows) {
        new Redirect("/cpanel/vps/platinum/history");
    }
    $result = infoListVpsCloudNest($rows['vps_id']);
    if (isset($result['error']) && $result['error'] == 0) {
        foreach ($result['data'] as $infovps) {
            $db->update("tbl_purchased_cloudvps", array(
                'info' => encryptAES(json_encode($infovps)),
            ), " `vps_id` = '" . $infovps['vps-id'] . "' ");
        }
    }
    $row = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `id` = '" . $id . "' AND `site` = 'CLOUDNEST'");
    $detail = json_decode(decryptAES($row['info']), true);
    $os = json_decode($db->site('os_vps_cloudnest'), true);
} else {
    new Redirect("/cpanel/vps/platinum/history");
}
if (isset($_POST['updateOrder']) && $data_user['level'] == 'admin') {
    $isUpdate = $db->update("tbl_purchased_cloudvps", array(
        'vps_id'         => Anti_xss($_POST['vpsid'])
    ), " `id` = '" . $row['id'] . "' ");
    if ($isUpdate) {
        die('<script type="text/javascript">if(!alert("Lưu thành công !")){location.href = "";}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Lưu thất bại!")){window.history.back().location.reload();}</script>');
    }
}
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Chỉnh sửa đơn VPS</h1>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">CHỈNH SỬA ĐƠN VPS - #<?= $row['id'] ?> <?= $detail['html-vps-status'] ?></h5>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row clearfix">
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">IP:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" value="<?= $detail['ip'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">VPS ID:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="vpsid" value="<?= $row['vps_id'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Tài khoản:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="username" value="<?= $detail['username'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Mật khẩu:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="password" type="text" class="form-control" value="<?= $detail['password'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Ngày mua:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="start_date" type="text" class="form-control" value="<?= $detail['date_create'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Ngày hết hạn:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="end_date" type="text" class="form-control" value="<?= $detail['next_due_date'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/cpanel/vps/platinum/history" class="btn btn-danger waves-effect">QUAY LẠI</a>
                            <button type="submit" name="updateOrder" class="btn btn-success">LƯU NGAY</button>
                            <button type="button" class="btn btn-warning" onclick="showPopup(<?= $row['id'] ?>,1)">Start VPS</button>
                            <button type="button" onclick="showPopup(<?= $row['id'] ?>,3)" class="btn btn-info">Reboot VPS</button>
                            <button type="button" onclick="showPopup(<?= $row['id'] ?>,2)" class="btn btn-dark">Off VPS</button>
                            <button type="button" onclick="showPopup(<?= $row['id'] ?>,5)" class="btn btn-dark">Gia hạn VPS</button>
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModalgrid">Rebuild VPS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModalgrid" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">Rebuild VPS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row g-3">

                    <div class="col-xxl-12">
                        <div>
                            <label for="emailInput" class="form-label">CHU KỲ</label>
                            <select id="osid" class="form-control">
                                <option value="">--Chọn hệ điều hành--</option>
                                <?php foreach ($os['os-vps'] as $osEntry) :
                                ?>
                                    <option value="<?= $osEntry['os-id'] ?>"><?= $osEntry['os-name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div><!--end col-->

                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-primary" onclick="showPopup(<?= $row['id'] ?>,4)">Xác Nhận</button>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->

            </div>
        </div>
    </div>
</div>
<script>
    function showPopup(id, action) {
        Swal.fire({
            title: 'Xác Nhận Thao Tác?',
            text: "Bạn có chắc chắn muốn thao tác chức năng này chứ!",
            icon: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: "Không",

        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Đang xử lý',
                    text: 'Vui lòng đợi trong giây lát',
                    imageUrl: '/assets/images/loading.gif',
                    imageWidth: 400,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                })
                $.post('/model/admin/vps', {
                    id: id,
                    osid: $('#osid').val(),
                    action: action
                }, function(data) {
                    if (data.status == 'success') {
                        Swal.fire('Thành công',
                            `${data.msg}`,
                            'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Có lỗi',
                            text: data.msg
                        })
                    }
                }, 'json');
            }
        })


    }
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');
?>