<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_POST['AddBlog']) && $data_user['level'] == 'admin') {
    if ($db->site("status_demo") != 0) {
        exit('<script type="text/javascript">if(!alert("Đây là trang web demo bạn không thể thực hiện chức năng này !")){window.history.back().location.reload();}</script>');
    }
    $url_icon = null;
    if (check_img('image') == true) {
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 4);
        $uploads_dir = '/upload/blog/blog' . $rand . '.png';
        $tmp_name = $_FILES['image']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, realpath($_SERVER["DOCUMENT_ROOT"]) . $uploads_dir);
        if ($addlogo) {
            $url_icon = $uploads_dir;
        }
    }
    $isInsert = $db->insert("posts", ["user_id" => $data_user["id"], "image" => $url_icon, "title" => Anti_xss($_POST["title"]), "slug" => create_slug(Anti_xss($_POST["title"])), "category_id" => Anti_xss($_POST["category_id"]), "content" => isset($_POST["content"]) ? base64_encode($_POST["content"]) : NULL, "status" => Anti_xss($_POST["status"]), "created_at" => gettime()]);
    if ($isInsert) {
        insetLog($data_user['id'], "Thêm bài viết " . Anti_xss($_POST['title']) . " vào hệ thống.");
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
            <h1 class="page-title fw-semibold fs-18 mb-0"> Viết bài mới</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Bài viết</a></li>
                        <li class="breadcrumb-item active" aria-current="page"> Viết bài mới</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            THÊM BÀI VIẾT MỚI
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row mb-4">
                                <div class="row mb-4">
                                    <label class="col-sm-4 col-form-label" for="example-hf-email">Tiêu đề bài viết:
                                        <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <input name="title" type="text" class="form-control" required="">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-sm-4 col-form-label" for="example-hf-email">Ảnh nổi bật:
                                        <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <img width="200px" class="active mb-1" id="img_1" src="/assets/back-end/img/image-size.png">

                                        <div class="custom-file text-left">
                                            <input type="file" name="image" class="form-control image-preview-before-upload" data-preview="#viewer" required="" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" onchange="document.getElementById('img_1').src = window.URL.createObjectURL(this.files[0])">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-sm-4 col-form-label" for="example-hf-email">Chuyên mục <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-select" name="category_id" required="">
                                            <option value="">-- Chọn chuyên mục --</option>
                                            <?php foreach ($db->get_list("SELECT * FROM `post_category` WHERE `status`  = 1") as $category) : ?>
                                                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-sm-4 col-form-label" for="example-hf-email">Nội dung chi tiết:</label>
                                    <div class="col-sm-12">
                                        <textarea class="content" id="content" name="content"></textarea>
                                        <script>
                                            CKEDITOR.replace('content');
                                        </script>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-sm-4 col-form-label" for="example-hf-email">Trạng thái: <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="status" required="">
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <a type="button" class="btn btn-danger" href="/cpanel/blog/list"><i class="fa fa-fw fa-undo me-1"></i> Back</a>
                            <button type="submit" name="AddBlog" class="btn btn-primary"><i class="fa fa-fw fa-save me-1"></i> Submit</button>
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