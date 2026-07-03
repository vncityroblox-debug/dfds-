<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `purchased_hosting` WHERE `id` = '" . $id . "'");
    if (!$row) {
        new Redirect("/cpanel/hosting/history");
    }
} else {
    new Redirect("/cpanel/hosting/history");
}
if (isset($_POST['updateOrder']) && $data_user['level'] == 'admin') {
    $isUpdate = $db->update("purchased_hosting", [
        'username'     => Anti_xss($_POST['username']),
        'password'     => Anti_xss($_POST['password']),
        'domain_name'     => Anti_xss($_POST['domain_name']),
        'end_date' => strtotime(Anti_xss($_POST['end_date'])),
        'start_date' => strtotime(Anti_xss($_POST['start_date']))
    ], " `id` = '" . $row['id'] . "' ");
    if ($isUpdate) {
        insert_log($data_user['id'], 'Cập nhật thông tin đơn hàng hosting [' . $row['id'] . '].');
        die('<script type="text/javascript">if(!alert("Cập nhật thông tin thành công")){window.history.back().location.reload();}</script>');
    }
}

?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Chỉnh sửa đơn hàng #[<?= $row['id'] ?>]</h1>
        </div>
        <div class="row">

            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA ĐƠN HÀNG #[<?= $row['id'] ?>]
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Tài khoản:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="username" value="<?= ($row['username']) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Mật khẩu:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="password" type="text" class="form-control" value="<?= ($row['password']) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Tên miền:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="domain_name" type="text" class="form-control" value="<?= $row['domain_name'] ?>" required>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Ngày mua:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="start_date" type="text" class="form-control" value="<?= date('Y-m-d h:i:s', $row['start_date']) ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="taikhoan">Ngày hết hạn:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input name="end_date" type="text" class="form-control" value="<?= date('Y-m-d h:i:s', $row['end_date']) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="/cpanel/hosting/history" class="btn btn-danger waves-effect">QUAY
                                    LẠI</a>
                                <button type="submit" name="updateOrder" class="btn btn-success">LƯU NGAY</button>
                                <button type="button" class="btn btn-warning" onclick="confirmAction(<?= $row['id'] ?>,`suspended`)">Khóa
                                    Hosting</button>
                                <button type="button" onclick="confirmAction(<?= $row['id'] ?>,`active`)" class="btn btn-info">Mở
                                    khóa
                                    Hosting</button>
                                <button type="button" onclick="confirmAction(<?= $row['id'] ?>,`changepassword`)" class="btn btn-dark">Đổi mật khẩu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const confirmAction = (param, action) => {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện chức năng này chứ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await Item(param, action);
            }
        });
    }

    const Item = async (param, action) => {
        Swal.fire({
            icon: "info",
            title: "Đang xử lý!",
            html: "Không được tắt trang này, vui lòng đợi trong giây lát!",
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
            willClose: () => {},
        });

        $.ajax({
            url: '/model/admin/hosting',
            method: "POST",
            dataType: "JSON",
            data: {
                param: param,
                action: action
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.href = '';
                    });
                } else {
                    Swal.fire('Thất Bại', result.msg, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Thất Bại', xhr.responseText, 'error');
            }
        });
    }
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>