<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_GET["id"]) && $data_user['level'] == "admin") {
    $id = Anti_xss($_GET["id"]);
    if (!($row = $db->get_row("SELECT * FROM `whm_info` WHERE `id` = '" . $id . "' "))) {
        new Redirect('/cpanel/hosting/server');
    }
} else {
    new Redirect('/cpanel/hosting/server');
}

if (isset($_POST["SaveCategory"]) && $data_user['level'] == "admin") {

    if (Anti_xss((int)$_POST['status']) > 0) {
        if ($db->num_rows("SELECT * FROM `whm_info` WHERE `status` = 1 AND `id` != '".$row['id']."'") > 0) {
            die('<script type="text/javascript">if(!alert("Bạn đang có một máy chủ đang hoạt động, vui lòng tắt máy chủ đó trước khi chỉnh sửa máy chủ mới!")){window.history.back().location.reload();}</script>');
        }
    }
    $isUpdate = $db->update("whm_info", array(
        'ip'         => Anti_xss($_POST['ip']),
        'username'         => Anti_xss($_POST['username']),
        'password'         => Anti_xss($_POST['password']),
        'status'        => Anti_xss($_POST['status'])
    ), " `id` = '" . $row['id'] . "' ");
    if ($isUpdate) {
        insert_log($data_user['id'], "Chỉnh sửa máy chủ máy chủ WHM (" . Anti_xss($_POST["ip"]) . ").");
        exit("<script type=\"text/javascript\">if(!alert(\"Lưu thành công!\")){window.history.back().location.reload();}</script>");
    }
    exit("<script type=\"text/javascript\">if(!alert(\"Lưu thất bại!\")){window.history.back().location.reload();}</script>");
}
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><a type="button" class="btn btn-dark btn-raised-shadow btn-wave btn-sm me-1"
                    href="/cpanel/hosting/server"><i class="fa-solid fa-arrow-left"></i></a> Chỉnh sửa máy chủ <?php echo $row["ip"]; ?></h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA MÁY CHỦ
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row mb-4">
                                <div class="col-sm-12">
                                    <div class="mb-4">
                                        <label class="form-label" for="name">IP máy chủ:</label>
                                        <input type="text" class="form-control" value="<?= $row["ip"]; ?>" name="ip"
                                            placeholder="Nhập IP máy chủ" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label" for="name">Tài khoản:</label>
                                        <input type="text" class="form-control" value="<?= $row["username"]; ?>" name="username"
                                            placeholder="Nhập tài khoản" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label" for="name">Mật khẩu:</label>
                                        <input type="password" class="form-control" value="<?= $row["password"]; ?>" name="password"
                                            placeholder="Nhập mật khẩu" required>
                                    </div>
                                   
                                    <div class="mb-4">
                                        <label class="form-label" for="symbol_right">Status:</label>
                                        <select class="form-control" name="status" required>
                                            <option <?= $row["status"] == 1 ? "selected" : ""; ?> value="1">ON</option>
                                            <option <?= $row["status"] == 0 ? "selected" : ""; ?> value="0">OFF</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <a type="button" class="btn btn-danger" href="/cpanel/hosting/server"><i
                                    class="fa fa-fw fa-undo me-1"></i> Quay lại</a>
                            <button type="submit" name="SaveCategory" class="btn btn-primary"><i
                                    class="fa fa-fw fa-save me-1"></i> Lưu ngay</button>
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
