<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

// Xử lý thêm VPS mới
if (isset($_POST["submit"]) && $data_user['level'] == "admin") {
    $data = [
        'package_name' => Anti_xss($_POST['package_name']),
        'cpu' => Anti_xss($_POST['cpu']),
        'ram' => Anti_xss($_POST['ram']),
        'disk' => Anti_xss($_POST['disk']),
        'ip' => Anti_xss($_POST['ip']),
        'bandwidth' => Anti_xss($_POST['bandwidth']),
        'os' => Anti_xss($_POST['os']),
        'price' => Anti_xss($_POST['price']),
        'period' => Anti_xss($_POST['period'])
    ];

    if ($db->insert('list_vps', $data)) {
        die('<script type="text/javascript">alert("VPS đã được thêm thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Thêm VPS thất bại"); window.history.back();</script>');
    }
}

// Xử lý phân trang
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
$shortByDate = "";

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

// Truy vấn dữ liệu từ bảng list_vps
$listDatatable = $db->get_list(" SELECT * FROM `os_vps` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `os_vps` WHERE " . $where . " ORDER BY `id` DESC ");
$urlDatatable = pagination(("addon-os?limit=" . $limit . "&shortByDate=" . $shortByDate . "&"), $from, $totalDatatable, $limit);
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Quản lý VPS</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" onclick="confirmAddOS()" class="btn btn-primary btn-sm mb-3"><i class="fa-solid fa-plus"></i> Thêm Hệ Điều Hành</button>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2" class="btn btn-success btn-sm mb-3">
                        <i class="fa-solid fa-plus"></i> Thêm qua API
                    </button>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH HỆ ĐIỀU HÀNH
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
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
                        </form><?php echo getOS(); ?>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên OS</th>
                                        <th>Mã OS</th>
                                        <th>Image</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) { ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['os_name'] ?></td>
                                            <td><?= $row['os_id'] ?></td>
                                            <td><img src="<?= $row['image'] ?>" ></td>
                                            <td>
                                                <a type="button" href="/cpanel/vps/server/edit/<?= $row["id"] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                    <i class="fa fa-pencil-alt"></i> Edit
                                                </a>
                                                <a type="button" onclick="RemoveRow('<?= $row['id']; ?>')" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
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
<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2"
    data-bs-keyboard="false" aria-hidden="true">
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-centered modal-lg dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2"><i class="fa-solid fa-plus"></i>
                    Thêm gói VPS                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Lãi % (<span
                                class="text-danger">*</span>)</label>
                        <div class="col-sm-8">
                             <input type="text" class="form-control" id="ck" name="ck" required>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light " data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="AddServer" class="btn btn-primary shadow-primary btn-wave"><i
                            class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>


<script>
    function postRemove(id) {
        $.ajax({
            url: "/model/admin/delete",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'removeListVps',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: result.msg
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: result.msg
                    });
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
    function postAddOS() {
        $.ajax({
            url: "/model/admin/vps",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'AddOS'
            },
            success: function(result) {
                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: result.msg
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: result.msg
                    });
                }
            }
        });
    }
    function confirmAddOS() {
        Swal.fire({
            title: 'Xác nhận!',
            text: "Bạn có muốn thêm hệ điều hành mới không?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Thêm',
            cancelButtonText: 'Hủy'
        }).then((confirm) => {
            if (confirm.isConfirmed) {
                postAddOS();
            }
        });
    }
</script>


<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');
?>