<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $category = $db->get_row("SELECT * FROM `categories` WHERE `category_id` = '" . $id . "'");
    if (!$category) {
        new Redirect("/cpanel/product/categories");
    }
} else {
    new Redirect("/cpanel/product/categories");
}
if (isset($_POST['submit']) && $data_user['level'] == 'admin') {
    $category_id = (int)$_POST['category_id']; // ID danh mục cần chỉnh sửa
    $category_name = trim($_POST['category_name']);
    $parent_category_id = !empty($_POST['parent_category']) ? $_POST['parent_category'] : null; // Nếu danh mục cha được chọn

    // Kiểm tra nếu tên danh mục rỗng
    if (empty($category_name)) {
        die('<script type="text/javascript">alert("Vui lòng nhập tên danh mục"); window.history.back();</script>');
    }

    // Chống XSS
    $category_name = Anti_xss($category_name);
    $parent_category_id = $parent_category_id !== null ? Anti_xss($parent_category_id) : 0; // Xử lý trường hợp null

    // Lấy thông tin danh mục cũ để xóa ảnh cũ nếu cần
    $old_category = $db->query("SELECT * FROM categories WHERE category_id = $category_id")->fetch_assoc();

    $data = [
        'name' => $category_name,
        'slug' => create_slug($category_name),
        'parent_category_id' => $parent_category_id
    ];

    // Kiểm tra upload ảnh mới
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        // Upload thumbnail mới
        $thumbnail_path = uploadThumbnail($_FILES['thumbnail']);
        if ($thumbnail_path) {
            // Xóa ảnh cũ nếu có
            if (!empty($old_category['thumbnail'])) {
                unlink(realpath($_SERVER["DOCUMENT_ROOT"]).$old_category['thumbnail']);
            }
            $data['thumbnail'] = $thumbnail_path;
        } else {
            die('<script type="text/javascript">alert("Định dạng ảnh đại diện không hợp lệ hoặc upload thất bại!"); window.history.back();</script>');
        }
    }

    // Cập nhật danh mục trong bảng categories
    if ($db->update('categories', $data, "category_id = $category_id")) {
        die('<script type="text/javascript">alert("Danh mục đã được chỉnh sửa thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Có lỗi khi chỉnh sửa danh mục"); window.history.back();</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Chỉnh sửa danh mục #[<?= $category['name'] ?>]</h1>
        </div>
        <div class="row">

            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA DANH MỤC #[<?= $category['name'] ?>]
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">

                            <div class="mb-3">
                                <label for="category_name" class="form-label">Tên danh mục:</label>
                                <input type="text" name="category_name" class="form-control" id="category_name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                            </div>


                            <div class="mb-3">
                                <label for="parent_category" class="form-label">Danh mục cha:</label>
                                <select name="parent_category" class="form-select" id="parent_category">
                                    <option value="">Chọn danh mục cha</option>
                                    <!-- Danh sách danh mục cha -->
                                    <?php foreach ($db->get_list("SELECT * FROM categories") as $parent_category): ?>
                                        <option value="<?php echo $parent_category['category_id']; ?>" <?php if ($parent_category['category_id'] == $category['parent_category_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($parent_category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail:</label>
                                <input type="file" name="thumbnail" class="form-control" id="thumbnail" accept="image/*">
                                <small class="form-text text-muted">Chọn một ảnh đại diện cho danh mục (không bắt buộc).</small>
                            </div>

                            <div class="card-footer">
                                <a href="/cpanel/product/categories" class="btn btn-danger waves-effect">QUAY
                                    LẠI</a>
                                <button type="submit" name="submit" class="btn btn-success">LƯU NGAY</button>

                            </div>
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