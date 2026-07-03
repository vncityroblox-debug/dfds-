<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_POST["submit"]) && $data_user['level'] == "admin") {

    $title = Anti_xss($_POST['title']);
    $description = Anti_xss($_POST['description']);
    $detail = base64_encode($_POST['detail']);
    $price = Anti_xss($_POST['price']);
    $category_ids = Anti_xss($_POST['category_id']);
    $seller_id = Anti_xss($data_user['id']);
    $file_url = Anti_xss($_POST['file_url']);
    $discount_percentage = Anti_xss($_POST['discount_percentage']);

    $sale_price =  $price;

    if (isset($discount_percentage) && is_numeric($discount_percentage)) {
        $sale_price = $price - ($price * $discount_percentage / 100);
    }

    $product_data = [
        'title' => $title,
        'slug' => create_slug($title),
        'description' => $description,
        'detail' => $detail,
        'price' => $price,
        'seller_id' => $seller_id,
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

    $thumbnail_path = uploadThumbnail($_FILES['thumbnail']);
    if ($thumbnail_path) {
        $product_data['thumbnail'] = $thumbnail_path;
    } else {
        die('<script type="text/javascript">alert("Định dạng ảnh đại diện không hợp lệ hoặc upload thất bại!"); window.history.back();</script>');
    }

    if ($db->insert('products', $product_data)) {
        $product_id = $db->get_id_insert();
        $newHashtags = generateHashtags($title);
        saveHashtags($product_id, $newHashtags);

        $demo_image_paths = uploadDemoImages($_FILES['demo_images']);
        if (!empty($demo_image_paths)) {
            foreach ($demo_image_paths as $demo_image) {
                $db->insert('product_images', ['product_id' => $product_id, 'image_url' => $demo_image]);
            }
        }

        die('<script type="text/javascript">alert("Sản phẩm đã được thêm thành công!"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Có lỗi khi thêm sản phẩm!"); window.history.back();</script>');
    }
}

if (isset($_GET["limit"])) {
    $limit = (int) Anti_xss($_GET["limit"]);
} else {
    $limit = 10;
}

if (isset($_GET["page"])) {
    $page = Anti_xss((int) $_GET["page"]);
} else {
    $page = 1;
}

$from = ($page - 1) * $limit;
$where = " `product_id` > 0 ";
$name = "";
$create_gettime = "";
$shortByDate = "";

if (!empty($_GET["name"])) {
    $name = Anti_xss($_GET["name"]);
    $where .= " AND `title` LIKE \"%" . $name . "%\" ";
}

if (!empty($_GET["create_gettime"])) {
    $create_date = Anti_xss($_GET["create_gettime"]);
    $create_gettime = $create_date;
    $create_date_1 = str_replace("-", "/", $create_date);
    $create_date_1 = explode(" to ", $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . " 00:00:01", $create_date_1[1] . " 23:59:59"];
        $where .= " AND `created_at` >= '" . $create_date_1[0] . "' AND `created_at` <= '" . $create_date_1[1] . "' ";
    }
}

if (isset($_GET["shortByDate"])) {
    $shortByDate = Anti_xss($_GET["shortByDate"]);
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date("m");
    $currentYear = date("Y");
    $currentDate = date("Y-m-d");
    if ($shortByDate == 1) {
        $where .= " AND `created_at` LIKE '%" . $currentDate . "%' ";
    }
    if ($shortByDate == 2) {
        $where .= " AND YEAR(created_at) = " . $currentYear . " AND WEEK(created_at, 1) = " . $currentWeek . " ";
    }
    if ($shortByDate == 3) {
        $where .= " AND MONTH(created_at) = '" . $currentMonth . "' AND YEAR(created_at) = '" . $currentYear . "' ";
    }
}

$listDatatable = $db->get_list(" SELECT * FROM `products` WHERE " . $where . " ORDER BY `product_id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `products` WHERE " . $where . " ORDER BY `product_id` DESC ");
$urlDatatable = pagination(("list?limit=" . $limit . "&shortByDate=" . $shortByDate . "&title=" . $name . "&create_gettime=" . $create_gettime . "&"), $from, $totalDatatable, $limit);
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Sản phẩm</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" id="open-card-hide" class="btn btn-primary btn-sm mb-3">
                        <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
                    </button>
                </div>
            </div>
            <div class="col-xl-12" id="card-hide" style="display: none;">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <!-- Tiêu đề sản phẩm -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề sản phẩm</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>


                            <div class="mb-3">
                                <label for="price" class="form-label">Giá gốc</label>
                                <input type="number" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="discount_percentage" class="form-label">Phần trăm giảm giá (%)</label>
                                <input type="number" class="form-control" id="discount_percentage" name="discount_percentage">
                            </div>

                            <!-- Danh mục sản phẩm -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php
                                    $categories = $db->get_list("SELECT * FROM categories WHERE parent_category_id = 0");
                                    foreach ($categories as $parent_category) {
                                        echo "<option value='{$parent_category['category_id']}'>{$parent_category['name']}</option>";
                                        $subcategories = $db->get_list("SELECT * FROM categories WHERE parent_category_id = {$parent_category['category_id']}");
                                        foreach ($subcategories as $subcategory) {
                                            echo "<option value='{$subcategory['category_id']}'>-- {$subcategory['name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Yêu cầu mức rank</label>
                                <select class="form-control" id="rank_id" name="rank_id" required>
                                    <option value="0">Không yêu cầu</option>
                                    <?php foreach ($db->get_list("SELECT * FROM ranks") as $rank) : ?>
                                        <option value="<?= $rank['id']; ?>">
                                            <?= $rank['name']; ?> - Đạt móc nạp từ <?= format_cash($rank['required_amount']); ?>đ
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchPrimary" name="is_new" type="checkbox"> <label for="toggleswitchPrimary" class="label-secondary"></label><span class="ms-3">Sản phẩm mới</span> </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchSecondary" name="is_cheap" type="checkbox"> <label for="toggleswitchSecondary" class="label-secondary"></label><span class="ms-3">Giá rẻ</span> </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchSuccess" name="is_free" type="checkbox"> <label for="toggleswitchSuccess" class="label-secondary"></label><span class="ms-3">Miễn phí</span> </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="custom-toggle-switch d-flex align-items-center mb-4"> <input id="toggleswitchDanger" name="is_pinned" type="checkbox"> <label for="toggleswitchDanger" class="label-secondary"></label><span class="ms-3">Ghim đầu trang</span> </div>
                                </div>
                            </div>


                            <!-- Ảnh đại diện (thumbnail) -->
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" required>
                            </div>

                            <!-- Ảnh demo -->
                            <div class="mb-3">
                                <label for="demo_images" class="form-label">Ảnh demo</label>
                                <input type="file" class="form-control" id="demo_images" name="demo_images[]" accept="image/*" multiple>
                            </div>
                            <!-- Link tải -->
                            <div class="mb-3">
                                <label for="price" class="form-label">Link tải</label>
                                <input type="text" class="form-control" id="file_url" name="file_url" required>
                            </div>
                            <!-- Mô tả sản phẩm -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả ngắn sản phẩm</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Mỗi dòng một mô tả" required></textarea>
                            </div>


                            <!-- Mô tả sản phẩm -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Chi tiết sản phẩm</label>
                                <textarea class="form-control" id="detail" name="detail" rows="3" required></textarea>
                                <script>
                                    CKEDITOR.replace('detail');
                                </script>
                            </div>


                            <!-- Nút submit -->
                            <button type="submit" name="submit" class="btn btn-primary">Thêm sản phẩm</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH SẢN PHẨM
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">

                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $name; ?>" name="name" placeholder="Tên sản phẩm">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control form-control-sm" id="daterange" value="<?php echo $create_gettime; ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/product/list"><i class="fa fa-trash"></i>
                                        Bỏ lọc</a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option <?php echo $limit == 5 ? "selected" : ""; ?> value="5">5</option>
                                        <option <?php echo $limit == 10 ? "selected" : ""; ?> value="10">10</option>
                                        <option <?php echo $limit == 20 ? "selected" : ""; ?> value="20">20</option>
                                        <option <?php echo $limit == 50 ? "selected" : ""; ?> value="50">50</option>
                                        <option <?php echo $limit == 100 ? "selected" : ""; ?> value="100">100</option>
                                        <option <?php echo $limit == 500 ? "selected" : ""; ?> value="500">500</option>
                                        <option <?php echo $limit == 1000 ? "selected" : ""; ?> value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label">Short by Date:</label>
                                    <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option <?php echo $shortByDate == 1 ? "selected" : ""; ?> value="1">Hôm nay</option>
                                        <option <?php echo $shortByDate == 2 ? "selected" : ""; ?> value="2">Tuần này</option>
                                        <option <?php echo $shortByDate == 3 ? "selected" : ""; ?> value="3">Tháng này</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá bán</th>
                                        <th>Lượt xem</th>
                                        <th>Xét duyệt</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) { ?>
                                        <tr onchange="updateForm('<?= $row['product_id']; ?>')">
                                            <td><img src="<?= $row['thumbnail'] ?>" width="40px" /></td>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= format_cash($row['price']) ?>đ</td>
                                            <td><?= format_cash($row['view']) ?></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="approved<?= $row["product_id"]; ?>" value="1" <?= $row["approved"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?= $row["product_id"]; ?>" value="1" <?= $row["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <a type="button" href="/cpanel/product/list/edit/<?= $row["product_id"] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                    <i class="fa fa-pencil-alt"></i> Edit
                                                </a>
                                                <a type="button" onclick="RemoveRow('<?= $row['product_id']; ?>')" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?php echo $limit; ?> of <?php echo format_cash($totalDatatable); ?> Results</p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3">
                                <?php echo $limit < $totalDatatable ? $urlDatatable : ""; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateForm(id) {
        $.ajax({
            url: "/model/admin/update",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'updateTableProduct',
                id: id,
                status: $('#status' + id + ':checked').val(),
                approved: $('#approved' + id + ':checked').val()
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function() {
                alert(html(result));
                location.reload();
            }
        });
    }
</script>

<script type="text/javascript">
    function postRemove(id) {
        $.ajax({
            url: "/model/admin/delete",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'removeProduct',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: result.msg
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: result.msg
                    })
                }
            }
        });
    }



    function RemoveRow(id) {

        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn có đồng ý xóa sản phẩm ID " + id + " này không ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                postRemove(id);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var button = document.getElementById('open-card-hide');
        var card = document.getElementById('card-hide');

        // Thêm sự kiện click cho nút button
        button.addEventListener('click', function() {
            // Kiểm tra nếu card đang hiển thị thì ẩn đi, ngược lại hiển thị
            if (card.style.display === 'none' || card.style.display === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>