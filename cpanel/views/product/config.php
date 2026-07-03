<?php
$title = "Cấu hình bán mã nguồn";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if(isset($_POST['btnSaveOption']) && $data_user['level'] == 'admin')
{
    foreach ($_POST as $key => $value)
    {
        $db->update("options", array(
            'value' => $value
        ), " `key` = '$key' ");
    }
    die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Cấu hình bán mã nguồn</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Product Program</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cấu hình bán mã nguồn</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CẤU HÌNH BÁN MÃ NGUỒN
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-3">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Hoa hồng bán code</label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="<?= $db->site('ck_product') ?>" name="ck_product" placeholder="VD 10 = 10%">
                                                <span class="input-group-text">
                                                    %
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-3">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Số tiền rút tối thiểu</label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="<?= $db->site('minrut_product') ?>" name="minrut_product" placeholder="VD 100000 = 100.000đ">
                                                <span class="input-group-text">
                                                    VNĐ </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-3">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Phương thức rút tiền</label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <textarea class="form-control" rows="4" placeholder="Mỗi dòng 1 ngân hàng" name="listbank_product"><?= $db->site('listbank_product') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Lưu ý</label>
                                        <div class="col-sm-12">
                                            <textarea id="notice_product" name="notice_product"><?= $db->site('notice_product') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">

                                </div>
                            </div>
                            <a type="button" class="btn btn-danger" href=""><i class="fa fa-fw fa-undo me-1"></i>
                                Reload</a>
                            <button type="submit" name="btnSaveOption" class="btn btn-primary">
                                <i class="fa fa-fw fa-save me-1"></i> Lưu Ngay </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    CKEDITOR.replace("notice_product");
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>