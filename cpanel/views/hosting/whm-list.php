<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_POST["submit"]) && $data_user['level'] == "admin") {

    $isInsert = $db->insert("whm_info", [
        "ip" => Anti_xss($_POST["ip"]),
        "username" => Anti_xss($_POST["username"]),
        "password" => Anti_xss($_POST["password"]),
        "created_at" => gettime()
    ]);
    if ($isInsert) {
        insert_log($data_user['id'], "Thêm máy chủ WHM (" . Anti_xss($_POST["ip"]) . ").");
        die('<script type="text/javascript">if(!alert("Thêm thành công")){window.history.back().location.reload();}</script>');
    }
    die('<script type="text/javascript">if(!alert("Thêm thất bại")){window.history.back().location.reload();}</script>');
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
$username = "";
$create_gettime = "";
$ip = "";
$shortByDate = "";

if (!empty($_GET["ip"])) {
    $ip = Anti_xss($_GET["ip"]);
    $where .= " AND `ip` LIKE \"%" . $ip . "%\" ";
}

if (!empty($_GET["username"])) {
    $username = Anti_xss($_GET["username"]);
    $where .= " AND `username` LIKE \"%" . $username . "%\" ";
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

$listDatatable = $db->get_list(" SELECT * FROM `whm_info` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `whm_info` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("whm-list&limit=" . $limit . "&shortByDate=" . $shortByDate . "&ip=" . $ip . "&username=" . $username . "&create_gettime=" . $create_gettime . "&"), $from, $totalDatatable, $limit);
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Danh sách máy chủ</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" id="open-card-hide" class="btn btn-primary btn-sm mb-3">
                        <i class="fa-solid fa-plus"></i> Thêm máy chủ mới
                    </button>
                </div>
            </div>
            <div class="col-xl-12" id="card-hide" style="display: none;">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">IP máy chủ:<span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="ip" placeholder="IP máy chủ" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Tài khoản:<span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="username" placeholder="Tài khoản" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Mật khẩu:<span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
                                </div>
                            </div>


                            <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-plus me-1"></i>Thêm ngay</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH MÁY CHỦ
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?php echo $ip; ?>" name="ip" placeholder="IP máy chủ">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?php echo $username; ?>" name="username" placeholder="Tài khoản">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control form-control-sm" id="daterange" value="<?php echo $create_gettime; ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/hosting/server"><i class="fa fa-trash"></i>
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
                                        <th>IP máy chủ</th>
                                        <th>Tài khoản</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $whm) { ?>
                                        <tr onchange="updateForm('<?php echo $whm['id']; ?>')">
                                            <td><?= $whm['ip'] ?></td>
                                            <td><?= $whm["username"]; ?></td>

                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?php echo $whm["id"]; ?>" value="1" <?php echo $whm["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <a type="button" href="/cpanel/hosting/server/edit/<?= $whm["id"] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                    <i class="fa fa-pencil-alt"></i> Edit
                                                </a>
                                                <a type="button" onclick="RemoveRow('<?php echo $whm['id']; ?>')" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
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
                action: 'updateTableWhm',
                id: id,
                status: $('#status' + id + ':checked').val()
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
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
                action: 'removeWHM',
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