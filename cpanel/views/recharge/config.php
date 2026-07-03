<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_POST['SaveSettings']) && $data_user['level'] == 'admin') {
    foreach ($_POST as $key => $value) {
        $db->update("options", array(
            'value' => $value
        ), " `key` = '$key' ");
    }
    die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
}

if (isset($_POST['ThemNganHang']) && $data_user['level'] == 'admin') {
    $isInsert = $db->insert("bank", [
        'short_name'    => strtoupper(Anti_xss($_POST['short_name'])),
        'accountNumber' => Anti_xss($_POST['accountNumber']),
        'accountName'   => Anti_xss($_POST['accountName']),
        'url_api'   => Anti_xss($_POST['urlApi'])
    ]);
    if ($isInsert) {
        insetLog($data_user['id'], "Thêm ngân hàng (" . $config_listbank[Anti_xss($_POST['short_name'])] . " - " . $_POST['accountNumber'] . ") vào hệ thống.");
        die('<script type="text/javascript">if(!alert("Thêm thành công !")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Thêm thất bại !")){window.history.back().location.reload();}</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Cấu hình ngân hàng</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Nạp tiền</a></li>
                        <li class="breadcrumb-item"><a href="/cpanel/recharge">Ngân hàng</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cấu hình</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <a class="btn btn-danger label-btn mb-3" href="/cpanel/recharge">
                        <i class="ri-arrow-go-back-line label-btn-icon me-2"></i> QUAY LẠI
                    </a>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH NGÂN HÀNG
                        </div>
                        <div class="d-flex">
                            <button data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2" class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light"><i class="ri-add-line fw-semibold align-middle"></i> Thêm ngân hàng</button>
                        </div>
                    </div>
                    <div class="table-responsive table-wrapper mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th>Ngân hàng</th>
                                    <th>Số tài khoản</th>
                                    <th>Chủ tài khoản</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($db->get_list("SELECT * FROM `bank` ORDER BY `id` DESC") as $row) : ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $row['short_name'] ?></td>
                                        <td><?= $row['accountNumber'] ?></td>
                                        <td><?= $row['accountName'] ?></td>
                                        <td><?= $row['status'] == 1 ? '<span class="badge bg-success">Hiển thị</span>':'<span class="badge bg-danger">Ẩn</span>' ?></td>
                                        <td><a aria-label="" href="/cpanel/recharge/bank/edit/<?= $row['id'] ?>" style="color:white;" class="btn btn-info btn-sm btn-icon-left m-b-10" type="button">
                                                <i class="fas fa-edit mr-1"></i><span class=""> Edit</span>
                                            </a>
                                            <button style="color:white;" onclick="confirmAction(<?= $row['id'] ?>)" class="btn btn-danger btn-sm btn-icon-left m-b-10" type="button">
                                                <i class="fas fa-trash mr-1"></i><span class=""> Delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CẤU HÌNH
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="example-hf-email">Trạng
                                            thái</label>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" name="bank_status">
                                                <option <?=$db->site('bank_status') == 0 ? 'selected':''?> value="0">OFF
                                                </option>
                                                <option <?=$db->site('bank_status') == 1 ? 'selected':''?> value="1">ON
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="example-hf-email">Prefix</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm" value="<?=$db->site('prefix_autobank')?>" name="prefix_autobank" placeholder="VD: NAPTIEN">
                                                <span class="input-group-text">
                                                    1 </span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Số tiền
                                            nạp tối thiểu</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control form-control-sm" value="<?=$db->site('bank_min')?>" name="bank_min">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Số tiền
                                            nạp tối đa</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control form-control-sm" value="<?=$db->site('bank_max')?>" name="bank_max">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Lưu ý nạp tiền</label>
                                        <div class="col-sm-12">
                                            <textarea id="bank_notice" name="bank_notice"><?=$db->site('bank_notice')?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a type="button" class="btn btn-danger" href=""><i class="fa fa-fw fa-undo me-1"></i>
                                Reload</a>
                            <button type="submit" name="SaveSettings" class="btn btn-success">
                                <i class="fa fa-fw fa-save me-1"></i> Save </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2">Thêm ngân hàng mới</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label">Ngân hàng <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" list="options" name="short_name" placeholder="Nhập tên ngân hàng" required>
                            <datalist id="options">
                                <option value="">Chọn ngân hàng</option>
                                <?php foreach ($config_listbank as $key => $value) { ?>
                                    <option value="<?= $key; ?>"><?= $value; ?></option>
                                <?php } ?>
                            </datalist>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label">Số tài khoản <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="accountNumber" placeholder="Nhập số tài khoản" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label">Chủ tài khoản <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="accountName" placeholder="Nhập tên chủ tài khoản" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Link API LSGD</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="urlApi" placeholder="Áp dụng khi cấu hình nạp tiền tự động.">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="ThemNganHang" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const confirmAction = (id) => {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện xóa bank " + id,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await Item(id);
            }
        });
    }

    const Item = async (id) => {
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
            url: '/model/admin/delete',
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'removeBank',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.reload();
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
<script>CKEDITOR.replace("bank_notice");</script>