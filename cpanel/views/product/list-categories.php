<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_POST["submit"]) && $data_user['level'] == "admin") {
    $category_name = trim($_POST['category_name']);
    $parent_category_id = !empty($_POST['parent_category']) ? $_POST['parent_category'] : 0;

    if (empty($category_name)) {
        die('<script type="text/javascript">alert("Vui lòng nhập tên danh mục"); window.history.back();</script>');
    }

    $category_name = Anti_xss($category_name);
    $parent_category_id = $parent_category_id !== null ? Anti_xss($parent_category_id) : 0;

    $thumbnail_path = null;
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbnail_path = uploadThumbnail($_FILES['thumbnail']);
        if (!$thumbnail_path) {
            die('<script type="text/javascript">alert("Upload thumbnail thất bại hoặc định dạng không hợp lệ"); window.history.back();</script>');
        }
    }

    $data = [
        'name' => $category_name,
        'slug' => create_slug($category_name),
        'parent_category_id' => $parent_category_id,
        'thumbnail' => $thumbnail_path
    ];

    if ($db->insert('categories', $data)) {
        die('<script type="text/javascript">alert("Danh mục đã được thêm thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Thêm danh mục thất bại"); window.history.back();</script>');
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
$where = " `category_id` > 0 ";
$name = "";
$create_gettime = "";
$shortByDate = "";

if (!empty($_GET["name"])) {
    $name = Anti_xss($_GET["name"]);
    $where .= " AND `name` LIKE \"%" . $name . "%\" ";
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

$listDatatable = $db->get_list(" SELECT * FROM `categories` WHERE " . $where . " ORDER BY `category_id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `categories` WHERE " . $where . " ORDER BY `category_id` DESC ");
$urlDatatable = pagination(("categories?limit=" . $limit . "&shortByDate=" . $shortByDate . "&name=" . $name . "&create_gettime=" . $create_gettime . "&"), $from, $totalDatatable, $limit);
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Danh mục</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" id="open-card-hide" class="btn btn-primary btn-sm mb-3">
                        <i class="fa-solid fa-plus"></i> Thêm danh mục mới
                    </button>
                </div>
            </div>
            <div class="col-xl-12" id="card-hide" style="display: none;">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Tên Danh Mục</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Nhập tên danh mục" required>
                            </div>
                            <div class="mb-3">
                                <label for="parent_category" class="form-label">Danh Mục Cha (Nếu có)</label>
                                <select class="form-select" id="parent_category" name="parent_category">
                                    <option value="">Chọn danh mục cha (nếu có)</option>
                                    <?php
                                    $categories = $db->query("SELECT category_id, name FROM categories WHERE parent_category_id = 0");
                                    while ($row = $categories->fetch_assoc()) {
                                        echo "<option value='{$row['category_id']}'>{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail:</label>
                                <input type="file" name="thumbnail" class="form-control" id="thumbnail" accept="image/*">
                                <small class="form-text text-muted">Chọn một ảnh đại diện cho danh mục (không bắt buộc).</small>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Thêm Danh Mục</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH DANG MỤC
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">

                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $name; ?>" name="name" placeholder="Tên danh mục">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control form-control-sm" id="daterange" value="<?php echo $create_gettime; ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/product/categories"><i class="fa fa-trash"></i>
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
                                        <th>Tên danh mục</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) { ?>
                                        <tr onchange="updateForm('<?= $row['category_id']; ?>')">
                                            <td><img src="<?= $row['thumbnail'] ?>" width="40px"/></td>
                                            <td><?= $row['name'] ?></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?= $row["category_id"]; ?>" value="1" <?= $row["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <a type="button" href="/cpanel/product/categories/edit/<?= $row["category_id"] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                    <i class="fa fa-pencil-alt"></i> Edit
                                                </a>
                                                <a type="button" onclick="RemoveRow('<?= $row['category_id']; ?>')" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
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
                action: 'updateTableCategory',
                id: id,
                status: $('#status' + id + ':checked').val()
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
                action: 'removeCategory',
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
            text: "Bạn có đồng ý xóa mục ID " + id + " này không ?",
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