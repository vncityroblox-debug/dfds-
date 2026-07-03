<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
foreach ($_POST as $key => $value) {
    $encrypted_value = in_array($key, ['ck_vncloud', 'ck_cloudnest', 'ck_h2cloud']) ? $value : encryptAES($value);
    $db->update("options", array(
        'value' => $encrypted_value
    ), " `key` = '$key' ");
    die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
}
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Cấu hình thông tin API Cloud</h1>
        </div>
        <div class="row">

            <div class="col-xl-12" id="card-hide">
                <div class="card custom-card">
                    <form action="" method="post">
                        <div class="card-body">
        <div class="alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-warning" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none"></path>
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
            </svg>Cron update gói vps qua api
            <a class="text-primary" href="<?= DOMAIN ?>/api/v1/cloudvps/plan" target="_blank">Ấn vào đây để cron</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
        <div class="alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-warning" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none"></path>
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
            </svg>Cron update os vps qua api
            <a class="text-primary" href="<?= DOMAIN ?>/api/v1/cloudvps/os" target="_blank">Ấn vào đây để cron</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
        <div class="alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-warning" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none"></path>
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
            </svg>Cron update addon vps qua api
            <a class="text-primary" href="<?= DOMAIN ?>/api/v1/cloudvps/addon" target="_blank">Ấn vào đây để cron</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
                            <div class="row mt-3">
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API USERNAME VNCLOUD (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_username" type="text" class="form-control" value="<?= decryptAES($db->site('api_username')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API APP VNCLOUD (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_app" type="text" class="form-control" value="<?= decryptAES($db->site('api_app')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API SECRET VNCLOUD (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_secret" type="text" class="form-control" value="<?= decryptAES($db->site('api_secret')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API USERNAME CLOUDNEST (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_username_cloudnest" type="text" class="form-control" value="<?= decryptAES($db->site('api_username_cloudnest')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API APP CLOUDNEST (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_app_cloudnest" type="text" class="form-control" value="<?= decryptAES($db->site('api_app_cloudnest')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API SECRET CLOUDNEST (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_secret_cloudnest" type="text" class="form-control" value="<?= decryptAES($db->site('api_secret_cloudnest')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API USERNAME H2CLOUD (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_username_h2cloud" type="text" class="form-control" value="<?= decryptAES($db->site('api_username_h2cloud')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API APP H2CLOUD (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_app_h2cloud" type="text" class="form-control" value="<?= decryptAES($db->site('api_app_h2cloud')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>API SECRET H2CLOUD (Được cấp):</label>
                                    <div class="form-group">
                                        <input name="api_secret_h2cloud" type="text" class="form-control" value="<?= decryptAES($db->site('api_secret_h2cloud')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Lãi VNCLOUD ( % ):</label>
                                    <div class="form-group">
                                        <input name="ck_vncloud" type="text" class="form-control" value="<?= $db->site('ck_vncloud') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Lãi CLOUDNEST ( % ):</label>
                                    <div class="form-group">
                                        <input name="ck_cloudnest" type="text" class="form-control" value="<?= $db->site('ck_cloudnest') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Lãi H2CLOUD ( % ):</label>
                                    <div class="form-group">
                                        <input name="ck_h2cloud" type="text" class="form-control" value="<?= $db->site('ck_h2cloud') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="SaveSettings" class="btn btn-primary">Cập Nhật</button>
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