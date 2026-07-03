<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET["id"]) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET["id"]);
    if (!($row = $db->get_row("SELECT * FROM `post_category` WHERE `id` = '" . $id . "' "))) {
        new Redirect('/cpanel/blog/category');
    }
} else {
    new Redirect('/cpanel/blog/category');
}
if (isset($_POST['UpdateCategory']) && $data_user['level'] == 'admin') {
    if ($db->site("status_demo") != 0) {
        exit('<script type="text/javascript">if(!alert("Đây là trang web demo bạn không thể thực hiện chức năng này !")){window.history.back().location.reload();}</script>');
    }
    if ($db->get_row("SELECT * FROM `post_category` WHERE `name` = '" . Anti_xss($_POST["name"]) . "' AND `id` != " . $row["id"] . " ")) {
        exit('<script type="text/javascript">if(!alert("Chuyên mục này đã tồn tại trong hệ thống")){window.history.back().location.reload();}</script>');
    }
    if (check_img("icon")) {
        unlink('../../..'.$row["icon"]);
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 4);
        $uploads_dir = '/upload/blog/icon' . $rand . '.png';
        $tmp_name = $_FILES['icon']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, realpath($_SERVER["DOCUMENT_ROOT"]) . $uploads_dir);
        if ($addlogo) {
            $db->update("post_category", ["icon" => $uploads_dir], " `id` = '" . $row["id"] . "' ");
        }
    }
    $isInsert = $db->update("post_category", ["name" => Anti_xss($_POST["name"]), "slug" => create_slug(Anti_xss($_POST["name"])), "content" => isset($_POST["content"]) ? base64_encode($_POST["content"]) : NULL, "status" => Anti_xss($_POST["status"])], " `id` = '" . $row["id"] . "' ");
    if ($isInsert) {
        insetLog($data_user['id'], "Cập nhật chuyên mục bài viết (" . $row["name"] . " ID " . $row["id"] . ").");
        die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Lưu thất bại !")){window.history.back().location.reload();}</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0">Chỉnh sửa chuyên mục bài viết [<?=$row['name']?>]</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page"><?=$row['name']?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title"> CHỈNH SỬA CHUYÊN MỤC BÀI VIẾT </div>
                    </div>
                    <div class="card-body">
                    <form id="form-data" action="" method="post" enctype="multipart/form-data" class="mb-2">
                    <div class="row mb-2">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">Tên chuyên mục</label>
                                <input class="form-control" name="name" type="text" placeholder="Nhập tên chuyên mục" value="<?= $row['name'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="form-group">
                                <label class="form-label">Icon</label>
                                <img class="w-100 active lazyLoad" id="img_1" src="<?= $row['icon'] ?>">
                                <center>
                                    <span class="btn btn-default btn-file">
                                        <input name="icon" type="file" class="form-control" onchange="document.getElementById('img_1').src = window.URL.createObjectURL(this.files[0])">
                                    </span>
                                </center>
                            </div>
                        </div>
                    </div>
                    

                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <label class="form-label">Mô tả</label>
                                <textarea name="content" id="content" cols="50" rows="5"><?=base64_decode($row['content']) ?></textarea>
                                <script>
                                    CKEDITOR.replace('content');
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="form-label">Hiển thị</label>
                        <div class="col-sm-12">
                            <select class="form-control show-tick select2bs4" name="status" required>
                                <option <?= $row['status'] == 1 ? 'selected' : ''; ?> value="1">Hiển thị
                                </option>
                                <option <?= $row['status'] == 0 ? 'selected' : ''; ?> value="0">Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="UpdateCategory" class="btn btn-primary btn-block">
                        <span>LƯU NGAY</span></button>
                    <a type="button" href="/cpanel/blog/category" class="btn btn-danger btn-block waves-effect">
                        <span>TRỞ LẠI</span>
                    </a>
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