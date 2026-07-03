<?php
$title = "Cấu hình Giao Diện";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_POST['btnSaveOption']) && $data_user['level'] == 'admin') {
    foreach ($_POST as $key => $value) {
        if ($key == 'btnSaveOption') continue;
        if ($key == 'design_status') {
            $db->update("options", ['value' => $value], " `key` = '$key' ");
        } elseif ($key == 'page_design_title' || $key == 'page_design_description') {
            $db->update("options", ['value' => base64_encode($value)], " `key` = '$key' ");
        }
    }

    die('<script type="text/javascript">if(!alert("Lưu thành công!")){window.location.reload();}</script>');
}
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Cấu hình Giao Diện</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">CẤU HÌNH GIAO DIỆN</div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">

                                <!-- Trạng thái -->
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-6 col-form-label" for="design_status">Trạng thái thiết kế</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="design_status" required>
                                                <option value="1" <?= $db->site('design_status') == 1 ? 'selected' : '' ?>>Bật</option>
                                                <option value="0" <?= $db->site('design_status') == 0 ? 'selected' : '' ?>>Tắt</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tiêu đề trang -->
                                <div class="col-lg-12 col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">Tiêu đề trang:</label>
                                        <textarea name="page_design_title" id="title_editor"><?= base64_decode($db->site('page_design_title')) ?></textarea>
                                    </div>
                                </div>

                                <!-- Mô tả trang -->
                                <div class="col-lg-12 col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả trang:</label>
                                        <textarea name="page_design_description" id="desc_editor"><?= base64_decode($db->site('page_design_description')) ?></textarea>
                                    </div>
                                </div>

                            </div>
                            <a class="btn btn-danger" href=""><i class="fa fa-fw fa-undo me-1"></i> Reload</a>
                            <button type="submit" name="btnSaveOption" class="btn btn-primary"><i class="fa fa-fw fa-save me-1"></i> Lưu Ngay</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('title_editor');
    CKEDITOR.replace('desc_editor');
</script>

<?php require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php'); ?>
