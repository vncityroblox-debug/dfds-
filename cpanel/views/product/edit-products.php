<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $product = $db->get_row("SELECT * FROM `products` WHERE `product_id` = '" . $id . "'");
    if (!$product) {
        new Redirect("/cpanel/product/list");
    }
    $selected_category_ids = is_array($product['category_id'])
        ? $product['category_id']
        : explode(',', $product['category_id']);
} else {
    new Redirect("/cpanel/product/list");
}
if (isset($_POST['update']) && $data_user['level'] == 'admin') {

    $product_id = Anti_xss($_POST['product_id']);
    $title = Anti_xss($_POST['title']);
    $description = Anti_xss($_POST['description']);
    $detail = base64_encode($_POST['detail']);
    $price = Anti_xss($_POST['price']);
    $category_ids = Anti_xss($_POST['category_id']);
    $seller_id = Anti_xss($data_user['id']);
    $file_url = Anti_xss($_POST['file_url']);
    $discount_percentage = Anti_xss($_POST['discount_percentage']);

    $sale_price = $product['price'];
    if (isset($discount_percentage) && is_numeric($discount_percentage)) {
        $sale_price = $price - ($price * $discount_percentage / 100);
    }

    $old_product = $db->query("SELECT * FROM products WHERE `product_id` = $product_id")->fetch_assoc();

    $product_data = [
        'title' => $title,
        'slug' => create_slug($title),
        'description' => $description,
        'detail' => $detail,
        'price' => $price,
        'seller_id' => $seller_id,
        'file_url' => $file_url,
        'sale_price' => $sale_price,
        'discount_percentage' => $discount_percentage,
        'category_id' => $category_ids,
        'rank_id' => isset($_POST['rank_id']) ? Anti_xss($_POST['rank_id']) : 0,
        'is_new' => isset($_POST['is_new']) && Anti_xss($_POST['is_new']) == 'on' ? 1 : 0,
        'is_cheap' => isset($_POST['is_cheap']) && Anti_xss($_POST['is_cheap']) == 'on' ? 1 : 0,
        'is_free' => isset($_POST['is_free']) && Anti_xss($_POST['is_free']) == 'on' ? 1 : 0,
        'is_pinned' => isset($_POST['is_pinned']) && Anti_xss($_POST['is_pinned']) == 'on' ? 1 : 0,
    ];


    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $thumbnail_path = uploadThumbnail($_FILES['thumbnail']);
        if ($thumbnail_path) {

            if (!empty($old_product['thumbnail'])) {
                unlink(realpath($_SERVER["DOCUMENT_ROOT"]) . $old_product['thumbnail']);
            }
            $product_data['thumbnail'] = $thumbnail_path;
        } else {
            die('<script type="text/javascript">alert("Định dạng ảnh đại diện không hợp lệ hoặc upload thất bại!"); window.history.back();</script>');
        }
    }


    if ($db->update('products', $product_data, "product_id = $product_id")) {
        $newHashtags = generateHashtags($title);
        deleteHashtags($product_id);
        saveHashtags($product_id, $newHashtags);
        if (isset($_FILES['demo_images']) && $_FILES['demo_images']['error'][0] != UPLOAD_ERR_NO_FILE) {

            $old_demo_images = $db->query("SELECT * FROM product_images WHERE product_id = $product_id");
            while ($old_image = $old_demo_images->fetch_assoc()) {
                unlink(realpath($_SERVER["DOCUMENT_ROOT"]) . $old_image['image_url']); // Xóa ảnh cũ
            }

            $db->query("DELETE FROM product_images WHERE product_id = $product_id");


            $demo_image_paths = uploadDemoImages($_FILES['demo_images']);
            if (!empty($demo_image_paths)) {
                foreach ($demo_image_paths as $demo_image) {
                    $db->insert('product_images', ['product_id' => $product_id, 'image_url' => $demo_image]);
                }
            }
        }

        die('<script type="text/javascript">alert("Sản phẩm đã được chỉnh sửa thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Có lỗi khi chỉnh sửa sản phẩm!"); window.history.back();</script>');
    }
}
if (isset($_POST['changeimage']) && $data_user['level'] == 'admin') {
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
if (isset($_POST['deleteimage']) && $data_user['level'] == 'admin') {
    $image_id = Anti_xss($_POST['image_id']);
    $product_id = Anti_xss($_POST['product_id']);

    $old_image = $db->get_row("SELECT image_url FROM product_images WHERE image_id = $image_id");

    if (!empty($old_image['image_url'])) {
        unlink(realpath($_SERVER["DOCUMENT_ROOT"]) . $old_image['image_url']);
    }

    $db->query("DELETE FROM product_images WHERE image_id = $image_id");
    die('<script type="text/javascript">alert("Xóa ảnh demo thành công!"); window.history.back();</script>');
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Chỉnh sửa sản phẩm #[<?= $product['title'] ?>]</h1>
        </div>
        <div class="row">

            <div class="col-xl-8">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA SẢN PHẨM #[<?= $product['title'] ?>]
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">

                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                            <div class="mb-3">
                                <label for="title" class="form-label">Tên sản phẩm</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo $product['title']; ?>" required>
                            </div>



                            <div class="mb-3">
                                <label for="price" class="form-label">Giá</label>
                                <input type="number" class="form-control" id="price" name="price" value="<?php echo $product['price']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <?php foreach ($db->get_list("SELECT * FROM categories") as $category) : ?>
                                        <option value="<?php echo $category['category_id']; ?>"
                                            <?php echo in_array($category['category_id'], $selected_category_ids) ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Yêu cầu mức rank</label>
                                <select class="form-control" id="rank_id" name="rank_id" required>
                                    <option value="0">Không yêu cầu</option>
                                    <?php foreach ($db->get_list("SELECT * FROM ranks") as $rank) : ?>
                                        <option value="<?= $rank['id']; ?>" <?= $product['rank_id'] == $rank['id'] ? 'selected' : '' ?>>
                                            <?= $rank['name']; ?> - Đạt móc nạp từ <?= format_cash($rank['required_amount']); ?>đ
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchPrimary" <?= $product['is_new'] == 1 ? 'checked' : '' ?> name="is_new" type="checkbox"> <label for="toggleswitchPrimary" class="label-secondary"></label><span class="ms-3">Sản phẩm mới</span> </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchSecondary" <?= $product['is_cheap'] == 1 ? 'checked' : '' ?> name="is_cheap" type="checkbox"> <label for="toggleswitchSecondary" class="label-secondary"></label><span class="ms-3">Giá rẻ</span> </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchSuccess" <?= $product['is_free'] == 1 ? 'checked' : '' ?> name="is_free" type="checkbox"> <label for="toggleswitchSuccess" class="label-secondary"></label><span class="ms-3">Miễn phí</span> </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchDanger" <?= $product['is_pinned'] == 1 ? 'checked' : '' ?> name="is_pinned" type="checkbox"> <label for="toggleswitchDanger" class="label-secondary"></label><span class="ms-3">Ghim đầu trang</span> </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                                <?php if (!empty($product['thumbnail'])): ?>
                                    <div class="mt-3">
                                        <p>Ảnh hiện tại:</p>
                                        <img src="<?php echo $product['thumbnail']; ?>" alt="Thumbnail" width="150">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="demo_images" class="form-label">Ảnh demo</label>
                                <input type="file" class="form-control" id="demo_images" name="demo_images[]" multiple>

                            </div>

                            <div class="mb-3">
                                <label for="discount_percentage" class="form-label">Phần trăm giảm giá</label>
                                <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?php echo $product['discount_percentage']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="file_url" class="form-label">Link tải</label>
                                <input type="text" class="form-control" id="file_url" name="file_url" value="<?php echo $product['file_url']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $product['description']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Chi tiết sản phẩm</label>
                                <textarea class="form-control" id="detail" name="detail" rows="3" required><?= base64_decode($product['detail']); ?></textarea>
                                <script>
                                    CKEDITOR.replace('detail');
                                </script>
                            </div>

                            <button type="submit" name="update" class="btn btn-primary">Cập nhật sản phẩm</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            ẢNH DEMO
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
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
    </div>
</div>
<script>
    function confirmDelete() {
        return confirm("Bạn có chắc chắn muốn xóa ảnh này không?");
    }
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>