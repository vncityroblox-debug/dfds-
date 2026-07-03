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
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Cấu hình nạp thẻ cào</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Nạp tiền</a></li>
                        <li class="breadcrumb-item"><a href="/cpanel/recharge/card">Thẻ cào</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cấu hình</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <a class="btn btn-danger label-btn mb-3" href="/cpanel/recharge/card">
                        <i class="ri-arrow-go-back-line label-btn-icon me-2"></i> QUAY LẠI
                    </a>
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
                            <div class="row mb-3">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="example-hf-email">Trạng
                                            thái</label>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" name="card_status">
                                                <option <?= $db->site('card_status') == 0 ? 'selected' : '' ?> value="0">OFF
                                                </option>
                                                <option <?= $db->site('card_status') == 1 ? 'selected' : '' ?> value="1">ON
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="example-hf-email">Partner ID</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" value="<?= $db->site('card_partner_id') ?>" name="card_partner_id">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Phí nạp thẻ</label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm" value="<?= $db->site('card_ck') ?>" name="card_ck">
                                                <span class="input-group-text">
                                                    % </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Partner Key</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control form-control-sm" value="<?= $db->site('card_partner_key') ?>" name="card_partner_key">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">URL API CARD</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="card_url_api" class="form-control form-control-sm" value="<?= $db->site('card_url_api') ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">URL CALLBACK CARD</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="card_url_api" class="form-control form-control-sm" value="<?= DOMAIN ?>/api/deposit/card" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label" for="example-hf-email">Lưu ý nạp thẻ</label>
                                        <div class="col-sm-12">
                                            <textarea id="card_notice" name="card_notice"><?= $db->site('card_notice') ?></textarea>
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
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>
<script>
    CKEDITOR.replace("card_notice");
</script>