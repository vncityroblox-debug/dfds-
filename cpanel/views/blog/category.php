<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_POST['AddCategory']) && $data_user['level'] == 'admin') {
    if ($db->site("status_demo") != 0) {
        exit('<script type="text/javascript">if(!alert("Đây là trang web demo bạn không thể thực hiện chức năng này !")){window.history.back().location.reload();}</script>');
    }
    $url_icon = null;
    if (check_img('image') == true) {
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 4);
        $uploads_dir = '/upload/blog/icon' . $rand . '.png';
        $tmp_name = $_FILES['image']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, realpath($_SERVER["DOCUMENT_ROOT"]) . $uploads_dir);
        if ($addlogo) {
            $url_icon = $uploads_dir;
        }
    }
    $isInsert = $db->insert("post_category", [
        'name'          => Anti_xss($_POST['name']),
        'slug'          => create_slug(Anti_xss($_POST['name'])),
        'icon'          => $url_icon,
        'content' => base64_encode($_POST['content']),
        'created_at' => gettime()
    ]);
    if ($isInsert) {
        insetLog($data_user['id'], "Thêm chuyên mục bài viết " . Anti_xss($_POST['name']) . " vào hệ thống.");
        die('<script type="text/javascript">if(!alert("Thêm thành công !")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Thêm thất bại !")){window.history.back().location.reload();}</script>');
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
$where = " `id` > 0 ";
$category = "";
$create_gettime = "";
$name = "";
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
        $create_date_1 = [$create_date_1[0] . " 00:00:00", $create_date_1[1] . " 23:59:59"];
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
$listDatatable = $db->get_list(" SELECT * FROM `post_category` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `post_category` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("/cpanel/blog/category?limit=" . $limit . "&shortByDate=" . $shortByDate . "&name=" . $name . "&create_gettime=" . $create_gettime . "&"), $from, $totalDatatable, $limit);
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0">Chuyên mục bài viết</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Chuyên mục bài viết</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title"> DANH SÁCH CHUYÊN MỤC BÀI VIẾT </div>
                        <div class="d-flex"> <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2" class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light"><i class="ri-add-line fw-semibold align-middle"></i> Thêm chuyên mục</button> </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6"> <input class="form-control form-control-sm" value="<?= $name ?>" name="name" placeholder="Tên chuyên mục"> </div>
                                <div class="col-lg col-md-4 col-6"> <input type="text" name="create_gettime" class="form-control form-control-sm" id="daterange" value="<?= $create_gettime ?>" placeholder="Chọn thời gian"> </div>
                                <div class="col-12"> <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i> Tìm kiếm </button> <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/blog/category"><i class="fa fa-trash"></i> Bỏ lọc </a> </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show"> <label class="filter-label">Show :</label> <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option <?= $limit == 5 ? "selected" : "" ?> value="5">5</option>
                                        <option <?= $limit == 10 ? "selected" : "" ?> value="10">10</option>
                                        <option <?= $limit == 20 ? "selected" : "" ?> value="20">20</option>
                                        <option <?= $limit == 50 ? "selected" : "" ?> value="50">50</option>
                                        <option <?= $limit == 100 ? "selected" : "" ?> value="100">100</option>
                                        <option <?= $limit == 500 ? "selected" : "" ?> value="500">500</option>
                                        <option <?= $limit == 1000 ? "selected" : "" ?> value="1000">1000</option>
                                    </select> </div>
                                <div class="filter-short"> <label class="filter-label">ShortbyDate:</label> <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option <?= $shortByDate == 1 ? "selected" : "" ?> value="1">Hôm nay </option>
                                        <option <?= $shortByDate == 2 ? "selected" : "" ?> value="2">Tuần này </option>
                                        <option <?= $shortByDate == 3 ? "selected" : "" ?> value="3"> Tháng này </option>
                                    </select> </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tên chuyên mục</th>
                                        <th>Ảnh</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) : ?>
                                        <tr onchange="updateForm('<?= $row['id']; ?>')">
                                            <td><?= $row['name'] ?></td>
                                            <td><img width="100px" src="<?= $row['icon'] ?>" /></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?php echo $row["id"]; ?>" value="1" <?php echo $row["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="/cpanel/blog/category/update/<?= $row['id'] ?>">
                                                    <i class="fa fa-fw fa-edit"></i>
                                                </a>
                                                <a class="btn btn-sm btn-danger" href="javascript:void(0)" onclick="confirmAction(<?= $row['id'] ?>)">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?= $limit ?> of <?= format_cash($totalDatatable) ?> Results</p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3"> <?= $limit < $totalDatatable ? $urlDatatable : "" ?> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2" data-bs-keyboard="false" style="display: none;" aria-hidden="true">
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-centered modal-xl dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2">Thêm chuyên mục bài viết mới</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" fdprocessedid="naqsz6"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Tên chuyên mục: <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="name" placeholder="Nhập tên chuyên mục" required="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Icon:</label>
                        <div class="col-sm-8">
                            <input type="file" class="custom-file-input" name="image">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Mô tả chi tiết:</label>
                        <div class="col-sm-8"> <textarea name="content" id="content"></textarea></div>
                        <script>
                            CKEDITOR.replace('content');
                        </script>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Status: <span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="status" required="">
                                <option value="1">ON</option>
                                <option value="0">OFF</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="AddCategory" class="btn btn-primary"><i class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
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
                action: 'updateTableCategoryBlog',
                id: id,
                stt: $('#stt' + id).val(),
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

    const confirmAction = (id) => {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện xóa chuyên mục bài viết " + id,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/model/admin/delete',
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'removeCategoryBlog',
                        id: id
                    },
                    success: function(result) {
                        if (result.status == 'success') {
                            Swal.fire('Thành công',
                                `${result.msg}`,
                                'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Thất Bại', result.msg, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Thất Bại', xhr.responseText, 'error');
                    }
                });
            }
        });
    }
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>