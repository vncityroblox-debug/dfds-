<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

$limit = 10;
if (isset($_GET['page'])) {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = ' `id` > 0 ';
$username = '';
$status = '';
$ip = '';
$shortByDate = "";
$create_gettime = "";

if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}
if (!empty($_GET['ip'])) {
    $ip = Anti_xss($_GET['ip']);
    $where .= ' AND `ip` LIKE "%' . $ip . '%" ';
}
if (!empty($_GET['status'])) {
    $status = Anti_xss($_GET['status']);
    $where .= ' AND `status` = "' . $status . '" ';
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

$listOrder = $db->get_list(" SELECT * FROM `order_vps` WHERE $where ORDER BY id DESC LIMIT $from,$limit ");
$vpsStats = $db->get_row("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'on' THEN 1 ELSE 0 END) AS on_vps,
        SUM(CASE WHEN status = 'off' THEN 1 ELSE 0 END) AS off_vps,
        SUM(CASE WHEN status = 'progressing' THEN 1 ELSE 0 END) AS progressing_vps,
        SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) AS waiting_vps,
        SUM(CASE WHEN status = 'rebuild' THEN 1 ELSE 0 END) AS rebuild_vps,
        SUM(CASE WHEN status = 'expire' THEN 1 ELSE 0 END) AS expire_vps,
        SUM(CASE WHEN status = 'suspend' THEN 1 ELSE 0 END) AS suspend_vps,
        SUM(CASE WHEN status = 'delete_vps' THEN 1 ELSE 0 END) AS delete_vps,
        SUM(CASE WHEN status = 'cancel' THEN 1 ELSE 0 END) AS cancel_vps,
        SUM(price) AS total_revenue
    FROM order_vps
");

$onVps = $vpsStats['on_vps'] ?? 0;
$offVps = $vpsStats['off_vps'] ?? 0;
$progressingVps = $vpsStats['progressing_vps'] ?? 0;
$waitingVps = $vpsStats['waiting_vps'] ?? 0;
$rebuildVps = $vpsStats['rebuild_vps'] ?? 0;
$expireVps = $vpsStats['expire_vps'] ?? 0;
$suspendVps = $vpsStats['suspend_vps'] ?? 0;
$deleteVps = $vpsStats['delete_vps'] ?? 0;
$cancelVps = $vpsStats['cancel_vps'] ?? 0;
$totalRevenue = $vpsStats['total_revenue'] ?? 0;
active_license();

function status_vps($status) {
    switch ($status) {
        case 'on':
            return '<span class="badge bg-success">Đang bật</span>';
        case 'off':
            return '<span class="badge bg-secondary">Đã tắt</span>';
        case 'progressing':
            return '<span class="badge bg-info">Đang tạo</span>';
        case 'waiting':
            return '<span class="badge bg-warning">Đang chờ tạo</span>';
        case 'rebuild':
            return '<span class="badge bg-primary">Đang cài lại</span>';
        case 'expire':
            return '<span class="badge bg-danger">Hết hạn</span>';
        case 'suspend':
            return '<span class="badge bg-dark">Đã khóa</span>';
        case 'delete_vps':
            return '<span class="badge bg-danger">Đã xóa</span>';
        case 'cancel':
            return '<span class="badge bg-secondary">Đã hủy</span>';
        default:
            return '<span class="badge bg-light text-dark">Không xác định</span>';
    }
}
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Lịch sử mua VPS</h1>
        </div>
        <div class="row">
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default"><?= $onVps ?></p>
                                <p class="mb-0 text-muted">Đang bật</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-success rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default"><?= $expireVps ?></p>
                                <p class="mb-0 text-muted">Hết hạn</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-danger rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default"><?= $suspendVps ?></p>
                                <p class="mb-0 text-muted">Đã khóa</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-warning rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default"><?= format_cash($totalRevenue) ?>đ</p>
                                <p class="mb-0 text-muted">Doanh thu</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-primary rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH ĐƠN HÀNG VPS
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $username ?>" name="username" placeholder="Khách hàng">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $ip ?>" name="ip" placeholder="IP VPS">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <select name="status" class="form-control select2">
                                        <option value="">Chọn trạng thái</option>
                                        <option value="on" <?= $status == 'on' ? 'selected' : '' ?>>Đang bật</option>
                                        <option value="off" <?= $status == 'off' ? 'selected' : '' ?>>Đã tắt</option>
                                        <option value="progressing" <?= $status == 'progressing' ? 'selected' : '' ?>>Đang tạo</option>
                                        <option value="waiting" <?= $status == 'waiting' ? 'selected' : '' ?>>Đang chờ tạo</option>
                                        <option value="rebuild" <?= $status == 'rebuild' ? 'selected' : '' ?>>Đang cài lại</option>
                                        <option value="expire" <?= $status == 'expire' ? 'selected' : '' ?>>Hết hạn</option>
                                        <option value="suspend" <?= $status == 'suspend' ? 'selected' : '' ?>>Đã khóa</option>
                                        <option value="delete_vps" <?= $status == 'delete_vps' ? 'selected' : '' ?>>Đã xóa</option>
                                        <option value="cancel" <?= $status == 'cancel' ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control" id="daterange" value="<?= $create_gettime ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/vps/order"><i class="fa fa-trash"></i> Bỏ lọc</a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option <?= $limit == 5 ? "selected" : "" ?> value="5">5</option>
                                        <option <?= $limit == 10 ? "selected" : "" ?> value="10">10</option>
                                        <option <?= $limit == 20 ? "selected" : "" ?> value="20">20</option>
                                        <option <?= $limit == 50 ? "selected" : "" ?> value="50">50</option>
                                        <option <?= $limit == 100 ? "selected" : "" ?> value="100">100</option>
                                        <option <?= $limit == 500 ? "selected" : "" ?> value="500">500</option>
                                        <option <?= $limit == 1000 ? "selected" : "" ?> value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label">Short by Date:</label>
                                    <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option <?= $shortByDate == 1 ? "selected" : "" ?> value="1">Hôm nay</option>
                                        <option <?= $shortByDate == 2 ? "selected" : "" ?> value="2">Tuần này</option>
                                        <option <?= $shortByDate == 3 ? "selected" : "" ?> value="3">Tháng này</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input" name="check_all" id="check_all_checkbox" value="option1">
                                            </div>
                                        </th>
                                        <th>Username</th>
                                        <th>Tên gói VPS</th>
                                        <th>Giá</th>
                                        <th>Chu kỳ</th>
                                        <th>IP</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listOrder as $row): ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check form-check-md d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input checkbox" data-id="<?= $row['id'] ?>" name="checkbox" value="<?= $row['id'] ?>">
                                                </div>
                                            </td>
                                            <td><a class="text-primary" href="/cpanel/user/edit/<?= $row['user_id'] ?>"><?= getRowRealtime('users', $row['user_id'], 'username') ?></a></td>
                                            <td><?= $row['plan_name'] ?></td>
                                            <td><?= format_cash($row['price']) ?>đ</td>
                                            <td><?= $row['cycle'] ?></td>
                                            <td><?= $row['ip'] ?></td>
                                            <td><?= $row['expired_at'] ?></td>
                                            <td><?= status_vps($row['status']) ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'on'): ?>
                                                    <button type="button" onclick="turnOffVps(<?= $row['id'] ?>)" class="btn btn-secondary btn-sm">Tắt VPS</button>
                                                <?php else: ?>
                                                    <button type="button" onclick="turnOnVps(<?= $row['id'] ?>)" class="btn btn-success btn-sm">Bật VPS</button>
                                                <?php endif; ?>
                                                <button type="button" onclick="extendVps(<?= $row['id'] ?>)" class="btn btn-info btn-sm">Cộng thêm tháng</button>
                                                <button type="button" onclick="deleteVps(<?= $row['id'] ?>)" class="btn btn-danger btn-sm">Xóa</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="btn-list">
                                                    <button type="button" id="btn_turn_off" class="btn btn-secondary btn-sm">Tắt VPS</button>
                                                    <button type="button" id="btn_turn_on" class="btn btn-success btn-sm">Bật VPS</button>
                                                    <button type="button" id="btn_extend" class="btn btn-info btn-sm">Cộng thêm tháng</button>
                                                    <button type="button" id="btn_delete" class="btn btn-danger btn-sm">Xóa</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-center">
                                <?php
                                $total = $db->num_rows("SELECT * FROM `order_vps` WHERE $where ORDER BY `id` DESC ");
                                if ($total > $limit) {
                                    echo '<center>' . pagination("/cpanel/vps/order?limit=" . $limit . "&shortByDate=" . $shortByDate . "&ip=" . $ip . "&status=" . $status . "&create_gettime=" . $create_gettime . "&", $from, $total, $limit) . '</center>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#check_all_checkbox').on('click', function() {
            $('.checkbox').prop('checked', this.checked);
        });
        $('.checkbox').on('click', function() {
            $('#check_all_checkbox').prop('checked', $('.checkbox:checked').length === $('.checkbox').length);
        });
    });

    function turnOnVps(id) {
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ bật VPS này nếu bạn nhấn Đồng ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/model/admin/update",
                    method: "POST",
                    dataType: "JSON",
                    data: { id: id, action: 'turnOnVps' },
                    success: function(result) {
                        showMessage(result.msg, result.status);
                        if (result.status == 'success') {
                            setTimeout(() => location.reload(), 500);
                        }
                    },
                    error: function() {
                        alert("Có lỗi xảy ra, vui lòng thử lại!");
                        location.reload();
                    }
                });
            }
        });
    }

    function turnOffVps(id) {
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ tắt VPS này nếu bạn nhấn Đồng ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/model/admin/update",
                    method: "POST",
                    dataType: "JSON",
                    data: { id: id, action: 'turnOffVps' },
                    success: function(result) {
                        showMessage(result.msg, result.status);
                        if (result.status == 'success') {
                            setTimeout(() => location.reload(), 500);
                        }
                    },
                    error: function() {
                        alert("Có lỗi xảy ra, vui lòng thử lại!");
                        location.reload();
                    }
                });
            }
        });
    }

    function extendVps(id) {
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ cộng thêm 1 tháng cho VPS này nếu bạn nhấn Đồng ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/model/admin/update",
                    method: "POST",
                    dataType: "JSON",
                    data: { id: id, action: 'extendVps' },
                    success: function(result) {
                        showMessage(result.msg, result.status);
                        if (result.status == 'success') {
                            setTimeout(() => location.reload(), 500);
                        }
                    },
                    error: function() {
                        alert("Có lỗi xảy ra, vui lòng thử lại!");
                        location.reload();
                    }
                });
            }
        });
    }

    function deleteVps(id) {
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ xóa VPS này nếu bạn nhấn Đồng ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/model/admin/update",
                    method: "POST",
                    dataType: "JSON",
                    data: { id: id, action: 'deleteVps' },
                    success: function(result) {
                        showMessage(result.msg, result.status);
                        if (result.status == 'success') {
                            setTimeout(() => location.reload(), 500);
                        }
                    },
                    error: function() {
                        alert("Có lỗi xảy ra, vui lòng thử lại!");
                        location.reload();
                    }
                });
            }
        });
    }

    $("#btn_turn_on").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào VPS cần bật.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ bật " + checkboxes.length + " VPS bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                var checkbox = document.getElementsByName('checkbox');
                function turnOnSequentially(index) {
                    if (index < checkbox.length) {
                        if (checkbox[index].checked) {
                            turnOnVps(checkbox[index].value);
                        }
                        setTimeout(() => turnOnSequentially(index + 1), 100);
                    }
                }
                turnOnSequentially(0);
            }
        });
    });

    $("#btn_turn_off").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào VPS cần tắt.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ tắt " + checkboxes.length + " VPS bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                var checkbox = document.getElementsByName('checkbox');
                function turnOffSequentially(index) {
                    if (index < checkbox.length) {
                        if (checkbox[index].checked) {
                            turnOffVps(checkbox[index].value);
                        }
                        setTimeout(() => turnOffSequentially(index + 1), 100);
                    }
                }
                turnOffSequentially(0);
            }
        });
    });

    $("#btn_extend").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào VPS cần cộng thêm tháng.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ cộng thêm 1 tháng cho " + checkboxes.length + " VPS bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                var checkbox = document.getElementsByName('checkbox');
                function extendSequentially(index) {
                    if (index < checkbox.length) {
                        if (checkbox[index].checked) {
                            extendVps(checkbox[index].value);
                        }
                        setTimeout(() => extendSequentially(index + 1), 100);
                    }
                }
                extendSequentially(0);
            }
        });
    });

    $("#btn_delete").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào VPS cần xóa.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ xóa " + checkboxes.length + " VPS bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                var checkbox = document.getElementsByName('checkbox');
                function deleteSequentially(index) {
                    if (index < checkbox.length) {
                        if (checkbox[index].checked) {
                            deleteVps(checkbox[index].value);
                        }
                        setTimeout(() => deleteSequentially(index + 1), 100);
                    }
                }
                deleteSequentially(0);
            }
        });
    });
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');
?>