<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET["limit"]) && $data_user['level'] == 'admin') {
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
$title = "";
$shortByDate = "";
if (!empty($_GET["title"])) {
    $title = Anti_xss($_GET["title"]);
    $where .= " AND `title` LIKE \"%" . $title . "%\" ";
}
if (!empty($_GET["category"])) {
    $category = Anti_xss($_GET["category"]);
    $where .= " AND `category_id` = " . $category . " ";
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
$listDatatable = $db->get_list(" SELECT * FROM `posts` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `posts` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("blogs&limit=" . $limit . "&shortByDate=" . $shortByDate . "&title=" . $title . "&category=" . $category . "&create_gettime=" . $create_gettime . "&"), $from, $totalDatatable, $limit);
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Blogs</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Blogs</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH BÀI VIẾT
                        </div>
                        <div class="d-flex">
                            <a type="button" href="/cpanel/blog/add" class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light"><i class="ri-add-line fw-semibold align-middle"></i> Viết bài mới</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $title ?>" name="title" placeholder="Title">
                                </div>
                                <div class="col-md-3 col-6">
                                    <select class="form-control mb-1" name="category">
                                        <option value="">-- Chuyên mục --</option>
                                        <?php foreach ($db->get_list(" SELECT * FROM `post_category` ") as $listcategory) : ?>
                                            <option <?= $listcategory["id"] == $category ? "selected" : "" ?> value="<?= $listcategory["id"] ?>    "><?= $listcategory["name"] ?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control" id="daterange" value="<?= $create_gettime ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i> Tìm kiếm </button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/blog/list"><i class="fa fa-trash"></i>
                                        Loại bỏ </a>
                                </div>
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
                                <div class="filter-short">
                                    <label class="filter-label">ShortbyDate:</label> <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tấtcả</option>
                                        <option <?= $shortByDate == 1 ? "selected" : "" ?> value="1">Hôm nay
                                        </option>
                                        <option <?= $shortByDate == 2 ? "selected" : "" ?> value="2">Tuầ này
                                        </option>
                                        <option <?= $shortByDate == 3 ? "selected" : "" ?> value="3"> Tháng này
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Vị trí</th>
                                        <th>Tiêu đề bài viết</th>
                                        <th>Ảnh</th>
                                        <th>Chuyên mục</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th>Lượt xem</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) : ?>
                                        <tr onchange="updateForm('<?= $row['id']; ?>')">
                                            <td class="text-center"><input id="stt<?= $row["id"]; ?>" class="form-control" type="number" value="<?= $row["stt"]; ?>"></td>
                                            <td><?= $row["title"] ?> </td>
                                            <td><img src="<?= $row["image"] ?>" width="100px"> </td>
                                            <td><a class="text-primary" href="/cpanel/blog/category/update/<?= $row["category_id"] ?>" target="_blank"><i class="fa fa-pencil-alt"></i>
                                                    <?= getRowRealtime("post_category", $row["category_id"], "name") ?> </a> </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?php echo $row["id"]; ?>" value="1" <?php echo $row["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td><?= $row["view"] ?> lượt xem </td>
                                            <td>
                                                <a type="button" target="_blank" href="/blog/<?= $row["slug"] ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Xem"> <i class="fa fa-eye"></i> </a>
                                                <a type="button" href="/cpanel/blog/list/update/<?= $row["id"] ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Chỉnh sửa"> <i class="fa fa-pencil-alt"></i> </a>
                                                <a type="button" onclick="confirmAction('<?= $row['id'] ?>')" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Xoá"> <i class="fas fa-trash"></i> </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?= $limit ?> of <?= format_cash($totalDatatable) ?> Results
                                </p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3"> <?= $limit < $totalDatatable ? $urlDatatable : "" ?> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function updateForm(id) {
        $.ajax({
            url: "/model/admin/update",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'updateTablePost',
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
            text: "Bạn đồng ý thực hiện xóa bài viết " + id,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await Item(id);
            }
        });
    }

    const Item = async (id) => {
        Swal.fire({
            icon: "info",
            title: "Đang xử lý!",
            html: "Không được tắt trang này, vui lòng đợi trong giây lát!",
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
            willClose: () => {},
        });

        $.ajax({
            url: '/model/admin/delete',
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'removePost',
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
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>