<?php
$title = "Cấu hình Cronjobs";
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
            <h1 class="page-title fw-semibold fs-18 mb-0">Cấu hình Cronjobs</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cronjobs Program</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cấu hình Cronjobs</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CẤU HÌNH CRONJOB
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-3">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Trạng thái</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="cron_status" required>
                                                <option value="1" <?= $db->site('cron_status') == 1 ? 'selected' : '' ?>>Bật
                                                </option>
                                                <option value="0" <?= $db->site('cron_status') == 0 ? 'selected' : '' ?>>Tắt
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Lưu ý</label>
                                        <div class="col-sm-12">
                                            <textarea id="cron_notice" name="cron_notice"><?= $db->site('cron_notice') ?></textarea>
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
    CKEDITOR.replace("cron_notice");
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>