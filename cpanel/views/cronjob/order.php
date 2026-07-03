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
$url = '';
$server_id = '';
$shortByDate = "";
$create_gettime = "";
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}
if (!empty($_GET['url'])) {
    $url = Anti_xss($_GET['url']);
    $where .= ' AND `url` LIKE "%' . $url . '%" ';
}
if (!empty($_GET['sever_id'])) {
    $sever_id = Anti_xss($_GET['sever_id']);
    $where .= ' AND `sever_id` LIKE "%' . $sever_id . '%" ';
}
if (!empty($_GET['status'])) {
    $status = Anti_xss($_GET['status']);
    if ($status == 'active') {
        $where .= ' AND `status` = "active" ';
    } else if ($status == 'paused') {
        $where .= ' AND `status` = "paused" ';
    } else if ($status == 'expired') {
        $where .= ' AND `status` = "expired" ';
    }
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

$listOrder = $db->get_list(" SELECT * FROM `cronjobs` WHERE $where ORDER BY id DESC LIMIT $from,$limit ");
$cronStats = $db->get_row("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) AS paused,
        SUM(CASE WHEN expires_at < NOW() THEN 1 ELSE 0 END) AS expired,
        SUM(payment) AS total_revenue
    FROM cronjobs
");

$activeCronLinks = $cronStats['active'] ?? 0;
$pausedCronLinks = $cronStats['paused'] ?? 0; 
$expiredCronLinks = $cronStats['expired'] ?? 0;
$totalRevenue = $cronStats['total_revenue'] ?? 0;
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Lịch sử mua cron</h1>
        </div>
        <div class="row">
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= $activeCronLinks ?> </p>
                                <p class="mb-0 text-muted">Đang hoạt động</p>
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
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= $expiredCronLinks  ?></p>
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
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= $pausedCronLinks ?></p>
                                <p class="mb-0 text-muted">Tạm dừng</p>
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
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= format_cash($totalRevenue) ?>đ</p>
                                <p class="mb-0 text-muted">Doanh thu

                                </p>
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
                            DANH SÁCH LINK CRON JOBS
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $username ?>" name="username" placeholder="Khách hàng">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $url ?>" name="url" placeholder="Link cron">
                                </div>
                               
                                <div class="col-lg col-md-4 col-6">
                                    <select name="server_id" class="form-control select2">
                                        <option value="">Máy chủ</option>
                                        <?php foreach($db->get_list("SELECT * FROM `server_cronjobs` WHERE `status` = 1") as $server):?>
                                        <option value="<?=$server['id']?>" <?=$sever_id == $server['id'] ? 'selected':''?>><?=$server['name']?></option>
                                       <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <select name="status" class="form-control select2">
                                        <option value="">Trạng thái</option>
                                        <option value="paused" <?=$status == 'paused' ? 'selected':''?>>Tạm dừng</option>
                                        <option value="active" <?=$status == 'active' ? 'selected':''?>>Hoạt động</option>
                                        <option value="expired" <?=$status == 'expired' ? 'selected':''?>>Hết hạn</option>
                                    </select>
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control" id="daterange" value="<?= $create_gettime ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/cron/order"><i class="fa fa-trash"></i>
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
                                        <th class="text-center">
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input" name="check_all" id="check_all_checkbox" value="option1">
                                            </div>
                                        </th>
                                        <th>Username</th>
                                        <th class="text-center">Link CRON</th>
                                        <th class="text-center">Cấu hình</th>
                                        <th class="text-center">Máy chủ</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Thực hiện lần cuối</th>
                                        <th class="text-center">Thời gian</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($listOrder as $row) :
                                    ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check form-check-md d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input checkbox" data-id="<?= $row['id'] ?>" name="checkbox" value="<?= $row['id'] ?>">
                                                </div>
                                            </td>
                                            <td class="text-center"><a class="text-primary" href="/cpanel/user/edit/<?= $row['user_id'] ?>"><b style="color:blue"><?= getRowRealtime('users', $row['user_id'], 'username'); ?></b>
                                                    [<b><?= getRowRealtime('users', $row['user_id'], 'id'); ?></b>]</a>
                                            </td>
                                            <td>
                                                <small><strong><?= $row['url'] ?></strong></small>
                                            </td>
                                            <td>
                                                Vòng lặp: <strong><?= $row['cron_expression'] ?></strong><br>
                                                Method: <strong><?= $row['method'] ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <a class="text-primary" href="/cpanel/cron/server/edit/<?= $row['server_id'] ?>" target="_blank"><i class="fa-solid fa-pen-to-square me-1"></i><?= getRowRealtime('server_cronjobs', $row['server_id'], 'name'); ?></a>
                                            </td>
                                            <td class="text-center">
                                                <?= status_cron($row["status"]) ?>
                                            </td>
                                            <td class="text-center">
                                                <strong data-toggle="tooltip" data-placement="bottom" data-bs-original-title="<?= $row["last_run"] ?>"><small><?= $row["last_run"] ?></small></strong>
                                                <br>
                                                <?php if (!empty($row["status_code"])): ?>
                                                    <?php if ($row["status_code"] == 200): ?>
                                                        <span class="badge bg-success">Success (<?= $row["status_code"] ?>)</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Error (<?= $row["status_code"] ?>)</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                Ngày tạo: <strong><small><i class="fa-solid fa-calendar"></i>
                                                        <?= $row["created_at"] ?></small></strong><br>
                                                Hết hạn: <strong><small><i class="fa-solid fa-calendar-days"></i>
                                                        <?= $row["expires_at"] ?></small></strong>
                                            </td>
                                            <td class="text-center">

                                                <?php if ($row['status'] == "active"): ?>
                                                    <button type="button" id="btnPause<?= $row['id'] ?>" onclick="pauseCron(`<?= $row['id'] ?>`)" class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-pause"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" id="btnActive<?= $row['id'] ?>" onclick="activeCron(`<?= $row['id'] ?>`)" class="btn btn-info btn-sm">
                                                        <i class="fa-solid fa-play"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <a type="button" href="/cpanel/cron/order/edit/<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>

                                                <button type="button" id="btnDeleteCron13" onclick="deleteCron(`<?= $row['id'] ?>`)" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="btn-list">
                                                    <button type="button" id="btn_pause" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" data-bs-original-title="Tạm dừng các link đã chọn">
                                                        <i class="fa-solid fa-pause"></i> Tạm dừng </button>
                                                    <button type="button" id="btn_active" data-toggle="tooltip" data-placement="bottom" class="btn btn-info btn-sm" data-bs-original-title="Kích hoạt các link đã chọn">
                                                        <i class="fa-solid fa-play"></i> Kích hoạt </button>
                                                    <button type="button" id="btn_delete" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" data-bs-original-title="Xóa các link đã chọn">
                                                        <i class="fa-solid fa-trash"></i> Xóa </button>
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
                                $total = $db->num_rows("SELECT * FROM `cronjobs` WHERE $where ORDER BY `id` DESC ");
                                if ($total > $limit) {
                                    echo '<center>' . pagination("/cpanel/cron/order?limit=" . $limit . "&shortByDate=" . $shortByDate . "&url=" . $url . "&status=" . $status . "&server_id=" . $server_id . "&create_gettime=" . $create_gettime . "&", $from, $total, $limit) . '</center>';
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
            $('#check_all_checkbox').prop('checked', $('.checkbox:checked')
                .length === $('.checkbox').length);
        });
    });
</script>
<script>
    $("#btn_pause").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào link cần Tạm dừng.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống tạm dừng " + checkboxes.length +
                " link bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                pause_records();
            }
        });
    });

    function pause_records() {
        var checkbox = document.getElementsByName('checkbox');

        function postUpdatesSequentially(index) {
            if (index < checkbox.length) {
                if (checkbox[index].checked === true) {
                    post_pause(checkbox[index].value);
                }
                setTimeout(function() {
                    postUpdatesSequentially(index + 1);
                }, 100);
            } else {
                Swal.fire({
                    title: "Thành công!",
                    text: "Tạm dừng thành công",
                    icon: "success"
                });
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        }
        postUpdatesSequentially(0);
    }

    function post_pause(id) {
        $.ajax({
            url: "/model/admin/update",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: 'pauseCron'
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function() {
                alert(html(response));
                location.reload();
            }
        });
    }

    function pauseCron(id) {
        $('#btnPause' + id).html('<span><i class="fa fa-spinner fa-spin"></i></span>')
            .prop('disabled', true);
        post_pause(id);
        setTimeout(() => {
            location.reload();
        }, 100);
    }
</script>
<script>
    $("#btn_active").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào link cần Kích hoạt.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống Kích hoạt " + checkboxes.length +
                " link bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                active_records();
            }
        });
    });

    function active_records() {
        var checkbox = document.getElementsByName('checkbox');

        function postUpdatesSequentially(index) {
            if (index < checkbox.length) {
                if (checkbox[index].checked === true) {
                    post_active(checkbox[index].value);
                }
                setTimeout(function() {
                    postUpdatesSequentially(index + 1);
                }, 100);
            } else {
                Swal.fire({
                    title: "Thành công!",
                    text: "Kích hoạt thành công",
                    icon: "success"
                });
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        }
        postUpdatesSequentially(0);
    }

    function post_active(id) {
        $.ajax({
            url: "/model/admin/update",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: 'activeCron'
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function() {
                alert(html(response));
                location.reload();
            }
        });
    }

    function activeCron(id) {
        $('#btnActive' + id).html('<span><i class="fa fa-spinner fa-spin"></i></span>')
            .prop('disabled', true);
        post_active(id);
        setTimeout(() => {
            location.reload();
        }, 100);
    }
</script>

<script>
    $("#btn_delete").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào link cần xóa.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ XÓA " + checkboxes.length +
                " link bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                delete_records();
            }
        });
    });

    function delete_records() {
        var checkbox = document.getElementsByName('checkbox');

        function postUpdatesSequentially(index) {
            if (index < checkbox.length) {
                if (checkbox[index].checked === true) {
                    post_delete(checkbox[index].value);
                }
                setTimeout(function() {
                    postUpdatesSequentially(index + 1);
                }, 100);
            } else {
                Swal.fire({
                    title: "Thành công!",
                    text: "Xóa link thành công",
                    icon: "success"
                });
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        }
        postUpdatesSequentially(0);
    }

    function post_delete(id) {
        $.ajax({
            url: "/model/admin/delete",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: 'deleteCron'
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function() {
                alert(html(response));
                location.reload();
            }
        });
    }

    function deleteCron(id) {
        const originalContent = $('#btnDeleteCron' + id).html(); // Save the original button content
        $('#btnDeleteCron' + id).html('<span><i class="fa fa-spinner fa-spin"></i></span>')
            .prop('disabled', true);

        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ xóa Link CRON này nếu bạn nhấn Đồng ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                post_delete(id);
                setTimeout(() => {
                    location.reload();
                }, 500);
            }
        }).finally(() => {
            // Restore the button content and enable it when Swal closes
            $('#btnDeleteCron' + id).html(originalContent)
                .prop('disabled', false);
        });
    }
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>