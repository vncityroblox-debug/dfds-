<?php
$title = "Cấu hình Giao Diện";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_POST['btnSaveOption']) && $data_user['level'] == 'admin') {
    uploadAndSaveOption('logo', 'logo');
    uploadAndSaveOption('favicon', 'favicon');
    uploadAndSaveOption('anhbia', 'anhbia');
    die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Cấu hình giao diện</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Theme Program</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cấu hình giao diện</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            THAY ĐỔI GIAO DIỆN WEBSITE
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-group">
                                        <label for="formFile" class="form-label">Logo</label>
                                        <input type="file" class="form-control" name="logo">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <img width="300px" src="<?= $db->site('logo') ?>" />
                                    <hr>
                                </div>
                               
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-group">
                                        <label for="formFile" class="form-label">Favicon</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" name="favicon">

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <img width="100px" src="<?= $db->site('favicon') ?>" />
                                    <hr>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-group">
                                        <label for="formFile" class="form-label">Image</label>
                                        <input type="file" class="form-control" name="anhbia">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <img width="400px" src="<?= $db->site('anhbia') ?>" />
                                    <hr>
                                </div>
                             
                               
                            </div>
                            <button name="btnSaveOption" class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Lưu Ngay</button>
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