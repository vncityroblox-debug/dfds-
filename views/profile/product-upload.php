<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
if (!@$user || $data_user['ctv'] != '1') {
    new Redirect('/');
    exit;
}
$title = "Upload sản phẩm";
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

if (isset($_POST["submit"]) && $data_user['ctv'] == "1") {
    // Lấy dữ liệu từ form và bảo vệ chống XSS
    $title = Anti_xss($_POST['title']);
    $description = Anti_xss($_POST['description']);
    $detail = base64_encode($_POST['detail']);
    $price = Anti_xss($_POST['price']);
    $category_ids = Anti_xss($_POST['category_id']);
    $seller_id = Anti_xss($data_user['id']);
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
        'slug' => create_slug($title),  // Tạo slug từ tiêu đề
        'description' => $description,
        'detail' => $detail,
        'price' => $price,
        'seller_id' => $seller_id,
        'file_url' => $file_url,
        'category_id' => $category_ids,
        'sale_price' => $sale_price,
        'approved' => 1,
        'discount_percentage' => $discount_percentage,
        'rank_id' => isset($_POST['rank_id']) ? Anti_xss($_POST['rank_id']) : 0,
        'is_new' => isset($_POST['is_new']) && Anti_xss($_POST['is_new']) == 'on' ? 1 : 0,
        'is_cheap' => isset($_POST['is_cheap']) && Anti_xss($_POST['is_cheap']) == 'on' ? 1 : 0,
        'is_free' => isset($_POST['is_free']) && Anti_xss($_POST['is_free']) == 'on' ? 1 : 0,
        'is_pinned' => isset($_POST['is_pinned']) && Anti_xss($_POST['is_pinned']) == 'on' ? 1 : 0,
    ];

    // Upload ảnh đại diện
    $thumbnail_path = uploadThumbnail($_FILES['thumbnail']);
    if ($thumbnail_path) {
        $product_data['thumbnail'] = $thumbnail_path;
    } else {
        die('<script type="text/javascript">alert("Định dạng ảnh đại diện không hợp lệ hoặc upload thất bại!"); window.history.back();</script>');
    }

    // Chèn sản phẩm vào cơ sở dữ liệu
    if ($db->insert('products', $product_data)) {
        $product_id = $db->get_id_insert();
        
        // Lưu các hashtag từ tiêu đề
        $newHashtags = generateHashtags($title);
        saveHashtags($product_id, $newHashtags);

        // Upload các ảnh demo nếu có
        $demo_image_paths = uploadDemoImages($_FILES['demo_images']);
        if (!empty($demo_image_paths)) {
            foreach ($demo_image_paths as $demo_image) {
                $db->insert('product_images', ['product_id' => $product_id, 'image_url' => $demo_image]);
            }
        }

        // Thông báo thành công
        die('<script type="text/javascript">alert("Sản phẩm đã được thêm thành công, vui lòng chờ admin duyệt;"); window.history.back();</script>');
    } else {
        // Thông báo lỗi
        die('<script type="text/javascript">alert("Có lỗi khi thêm sản phẩm!"); window.history.back();</script>');
    }
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
                                <input type="text" class="form-control" placeholder="Tiêu đề sản phẩm *" id="title" name="title" required>
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
        echo "<option value='{$parent_category['category_id']}'>{$parent_category['name']}</option>";

        // Lấy danh mục con
        $subcategories = $db->get_list("SELECT * FROM categories WHERE parent_category_id = {$parent_category['category_id']}");
        foreach ($subcategories as $subcategory) {
            echo "<option value='{$subcategory['category_id']}'>-- {$subcategory['name']}</option>";
        }
    }
    ?>
</select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Giá gốc *</label>
                                <input type="text" class="form-control" placeholder="Giá gốc *" id="price" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Phần trăm giảm giá (%) *</label>
                                <input type="text" class="form-control" placeholder="Phần trăm giảm giá (%) *" id="discount_percentage" name="discount_percentage">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap gig-option">
                                <h6>Phân loại sản phẩm</h6>
                                <label class="custom_check">
                                    <input type="checkbox" name="is_new">
                                    <span class="checkmark"></span>Sản phẩm mới
                                </label>
                                <label class="custom_check">
                                    <input type="checkbox" name="is_cheap">
                                    <span class="checkmark"></span>Giá rẻ
                                </label>
                                <label class="custom_check">
                                    <input type="checkbox" name="is_free">
                                    <span class="checkmark"></span>Miễn phí
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" required>
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
                                <input type="text" class="form-control" placeholder="Link tải *" id="file_url" name="file_url" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="col-form-label">Mô tả ngắn</label>
                                <textarea class="form-control" rows="6" placeholder="Mỗi dòng một mô tả *" id="description" name="description" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="col-form-label">Chi tiết</label>
                                <textarea class="form-control" id="detail" name="detail" rows="3" required></textarea>
                                <script>
                                    CKEDITOR.replace('detail');
                                </script>
                            </div>

                        </div>
                        <div class="btn-item">
                            <button class="btn btn-primary" type="submit" name="submit">Tải lên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>
