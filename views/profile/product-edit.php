<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
if (!@$user || $data_user['ctv'] != '1') {
    new Redirect('/');
    exit;
}
$title = "Chỉnh sửa sản phẩm";
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

// Lấy ID sản phẩm từ URL (GET)
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id == 0) {
    die('<script type="text/javascript">alert("Sản phẩm không tồn tại!"); window.history.back();</script>');
}

// Lấy thông tin sản phẩm từ cơ sở dữ liệu
$product = $db->get_row("SELECT * FROM products WHERE product_id = $product_id");

if (!$product) {
    die('<script type="text/javascript">alert("Sản phẩm không tồn tại!"); window.history.back();</script>');
}

if (isset($_POST["submit"]) && $data_user['ctv'] == "1") {
    // Lấy dữ liệu từ form và bảo vệ chống XSS
    $title = Anti_xss($_POST['title']);
    $description = Anti_xss($_POST['description']);
    $detail = base64_encode($_POST['detail']);
    $price = Anti_xss($_POST['price']);
    $category_ids = Anti_xss($_POST['category_id']);
    $file_url = Anti_xss($_POST['file_url']);
    $discount_percentage = Anti_xss($_POST['discount_percentage']);

    // Tính giá bán sau giảm giá
    $sale_price =  $price;
    if (isset($discount_percentage) && is_numeric($discount_percentage)) {
        $sale_price = $price - ($price * $discount_percentage / 100);
    }

    // Chuẩn bị mảng dữ liệu sản phẩm
    $product_data = [
        'title' => $title,
        'slug' => create_slug($title),
        'description' => $description,
        'detail' => $detail,
        'price' => $price,
        'file_url' => $file_url,
        'category_id' => $category_ids,
        'sale_price' => $sale_price,
        'discount_percentage' => $discount_percentage,
        'rank_id' => isset($_POST['rank_id']) ? Anti_xss($_POST['rank_id']) : 0,
        'is_new' => isset($_POST['is_new']) && Anti_xss($_POST['is_new']) == 'on' ? 1 : 0,
        'is_cheap' => isset($_POST['is_cheap']) && Anti_xss($_POST['is_cheap']) == 'on' ? 1 : 0,
        'is_free' => isset($_POST['is_free']) && Anti_xss($_POST['is_free']) == 'on' ? 1 : 0,
        'is_pinned' => isset($_POST['is_pinned']) && Anti_xss($_POST['is_pinned']) == 'on' ? 1 : 0,
    ];

    // Upload ảnh đại diện nếu có
    if ($_FILES['thumbnail']['error'] == 0) {
        $thumbnail_path = uploadThumbnail($_FILES['thumbnail']);
        if ($thumbnail_path) {
            $product_data['thumbnail'] = $thumbnail_path;
        } else {
            die('<script type="text/javascript">alert("Định dạng ảnh đại diện không hợp lệ hoặc upload thất bại!"); window.history.back();</script>');
        }
    }

    // Cập nhật sản phẩm vào cơ sở dữ liệu
    if ($db->update('products', $product_data, "product_id = $product_id")) {
        // Lưu các hashtag từ tiêu đề
        $newHashtags = generateHashtags($title);
        deleteHashtags($product_id);
        saveHashtags($product_id, $newHashtags);
        // Upload các ảnh demo nếu có
        if ($_FILES['demo_images']['error'][0] == 0) {
            $demo_image_paths = uploadDemoImages($_FILES['demo_images']);
            if (!empty($demo_image_paths)) {
                // Xóa ảnh demo cũ trước khi thêm ảnh mới
                $db->delete('product_images', "product_id = $product_id");
                foreach ($demo_image_paths as $demo_image) {
                    $db->insert('product_images', ['product_id' => $product_id, 'image_url' => $demo_image]);
                }
            }
        }

        // Thông báo thành công
        die('<script type="text/javascript">alert("Sản phẩm đã được cập nhật thành công!"); window.history.back();</script>');
    } else {
        // Thông báo lỗi
        die('<script type="text/javascript">alert("Có lỗi khi cập nhật sản phẩm!"); window.history.back();</script>');
    }
}
if (isset($_POST['changeimage']) && $data_user['ctv'] == '1') {
    $image_id = $_POST['image_id'];

    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {

        $old_image = $db->get_row("SELECT image_url FROM product_images WHERE image_id = $image_id");

        $new_image_path = uploadDemoImage($_FILES['new_image']); // Tạo hàm upload tương tự như trước
        if ($new_image_path) {

            unlink(realpath($_SERVER["DOCUMENT_ROOT"]) . $old_image['image_url']);


            $db->query("UPDATE product_images SET image_url = '$new_image_path' WHERE image_id = $image_id");
            die('<script type="text/javascript">alert("Ảnh đã được cập nhật thành công"); window.history.back();</script>');
        } else {
            die('<script type="text/javascript">alert("Có lỗi khi upload ảnh mới"); window.history.back();</script>');
        }
    }
}
if (isset($_POST['deleteimage']) && $data_user['ctv'] == '1') {
    $image_id = Anti_xss($_POST['image_id']);
    $product_id = Anti_xss($_POST['product_id']);

    $old_image = $db->get_row("SELECT image_url FROM product_images WHERE image_id = $image_id");

    if (!empty($old_image['image_url'])) {
        unlink(realpath($_SERVER["DOCUMENT_ROOT"]) . $old_image['image_url']);
    }

    $db->query("DELETE FROM product_images WHERE image_id = $image_id");
    die('<script type="text/javascript">alert("Xóa ảnh demo thành công!"); window.history.back();</script>');
}
?>

<script src="/assets/ckeditor/ckeditor.js?v=<?= time(); ?>"></script>

<section class="py-110">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="add-property-wrap">
                    <form action="" method="POST" class="row" enctype="multipart/form-data">
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Tiêu đề sản phẩm *</label>
                                <input type="text" class="form-control" placeholder="Tiêu đề sản phẩm *" id="title" name="title" value="<?= Anti_xss($product['title']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Danh mục</label>
                                <select class="custom-style-select nice-select select-dropdown" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php
                                    // Lấy danh mục cha và con từ database
                                    $categories = $db->get_list("SELECT * FROM categories WHERE parent_category_id = 0");
                                    foreach ($categories as $parent_category) {
                                        $selected = $parent_category['category_id'] == $product['category_id'] ? 'selected' : '';
                                        echo "<option value='{$parent_category['category_id']}' $selected>{$parent_category['name']}</option>";

                                        // Lấy danh mục con
                                        $subcategories = $db->get_list("SELECT * FROM categories WHERE parent_category_id = {$parent_category['category_id']}");
                                        foreach ($subcategories as $subcategory) {
                                            $selected = $subcategory['category_id'] == $product['category_id'] ? 'selected' : '';
                                            echo "<option value='{$subcategory['category_id']}' $selected>-- {$subcategory['name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Giá gốc *</label>
                                <input type="text" class="form-control" placeholder="Giá gốc *" id="price" name="price" value="<?= Anti_xss($product['price']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Phần trăm giảm giá (%) *</label>
                                <input type="text" class="form-control" placeholder="Phần trăm giảm giá (%) *" id="discount_percentage" name="discount_percentage" value="<?= Anti_xss($product['discount_percentage']) ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap gig-option">
                                <h6>Phân loại sản phẩm</h6>
                                <label class="custom_check">
                                    <input type="checkbox" name="is_new" <?= $product['is_new'] ? 'checked' : '' ?>>
                                    <span class="checkmark"></span>Sản phẩm mới
                                </label>
                                <label class="custom_check">
                                    <input type="checkbox" name="is_cheap" <?= $product['is_cheap'] ? 'checked' : '' ?>>
                                    <span class="checkmark"></span>Giá rẻ
                                </label>
                                <label class="custom_check">
                                    <input type="checkbox" name="is_free" <?= $product['is_free'] ? 'checked' : '' ?>>
                                    <span class="checkmark"></span>Miễn phí
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="demo_images" class="form-label">Ảnh demo</label>
                                <input type="file" class="form-control" id="demo_images" name="demo_images[]" accept="image/*" multiple>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="col-form-label">Link tải *</label>
                                <input type="text" class="form-control" placeholder="Link tải *" id="file_url" name="file_url" value="<?= Anti_xss($product['file_url']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="col-form-label">Mô tả ngắn</label>
                                <textarea class="form-control" rows="6" placeholder="Mỗi dòng một mô tả *" id="description" name="description" required><?= Anti_xss($product['description']) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="col-form-label">Chi tiết</label>
                                <textarea class="form-control" id="detail" name="detail" rows="3" required><?= Anti_xss(base64_decode($product['detail'])) ?></textarea>
                                <script>
                                    CKEDITOR.replace('detail');
                                </script>
                            </div>

                        </div>
                        <div class="btn-item">
                            <button class="btn btn-primary" type="submit" name="submit">Cập nhật</button>
                        </div>
                    </form>
                </div>
            <div class="col-lg-12">
                <div class="add-property-wrap">
                
                <?php
                            $demo_images = $db->get_list("SELECT * FROM product_images WHERE product_id = " . $product['product_id']);
                            if (!empty($demo_images)): ?>
                                <div class="mt-3">
                                    <p class="fw-semibold">Các ảnh demo hiện tại:</p>
                                    <div class="row">
                                        <?php foreach ($demo_images as $demo_image): ?>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="card">
                                                    <img src="<?php echo $demo_image['image_url']; ?>" alt="Demo Image" class="card-img-top m-auto" style="width:80px">
                                                    <div class="card-body text-center">
                                                        <form action="" method="POST" enctype="multipart/form-data" style="display: inline;">
                                                            <input type="hidden" name="image_id" value="<?php echo $demo_image['image_id']; ?>">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                            <input type="file" name="new_image" accept="image/*" class="form-control mb-2">
                                                            <button type="submit" name="changeimage" class="btn btn-warning btn-sm">Cập nhật</button>
                                                        </form>

                                                        <form action="" method="POST" style="display: inline;" onsubmit="return confirmDelete();">
                                                            <input type="hidden" name="image_id" value="<?php echo $demo_image['image_id']; ?>">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                            <button type="submit" name="deleteimage" class="btn btn-danger btn-sm">Xóa</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                </div>
        </div>
                
            </div>
        </div>
    </div>
</section>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>
