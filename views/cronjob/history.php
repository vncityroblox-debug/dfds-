<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php'); // Include database initialization

if (!@$user) {
    new Redirect('/login');
    exit;
}
if ($db->site('cron_status') != 1) {
    new Redirect('/');
    exit;
}
$title = 'Lịch sử CRON';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

$url = $_GET['url'] ?? '';  // Lọc theo URL
$status = $_GET['status'] ?? '';  // Lọc theo trạng thái
$purchase_date = $_GET['purchase_date'] ?? '';  // Lọc theo khoảng thời gian
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;  // Trang hiện tại
$limit = 10;  // Số lượng kết quả mỗi trang
$offset = ($page - 1) * $limit;  // Vị trí bắt đầu của kết quả

// Các điều kiện lọc
$conditions = ["`user_id` = '{$data_user['id']}'"];
if ($url) $conditions[] = "url LIKE '%$url%'";
if ($status) $conditions[] = "status LIKE '%$status%'";
if ($purchase_date) {
    $dates = explode(" to ", $purchase_date);
    if (count($dates) == 2) $conditions[] = "created_at BETWEEN '{$dates[0]} 00:00:00' AND '{$dates[1]} 23:59:59'";
}

$where = implode(" AND ", $conditions);
$total_items = $db->get_row("SELECT COUNT(*) as total FROM cronjobs WHERE $where")['total'] ?? 0;
$cron_jobs = $db->get_list("SELECT * FROM cronjobs WHERE $where LIMIT $limit OFFSET $offset");

$urls = "/user/history/cronjob?action=$url&status=$status&purchase_date=$purchase_date&";
$cronStats = $db->get_row("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) AS paused,
        SUM(CASE WHEN expires_at < NOW() THEN 1 ELSE 0 END) AS expired
        FROM cronjobs
    WHERE user_id = '{$data_user['id']}'
");
$totalCronLinks = $cronStats['total'] ?? 0; 
$activeCronLinks = $cronStats['active'] ?? 0;
$pausedCronLinks = $cronStats['paused'] ?? 0; 
$expiredCronLinks = $cronStats['expired'] ?? 0;
?>

<section class="py-110">
        <div class="container">
            <div class="rounded-3">

                <section class="space-y-6">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="p-3 d-flex align-items-center dashobard-widget justify-content-between rounded border shadow-sm">
                                <div>
                                    <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                        <?= $totalCronLinks ?>                                    </h3>
                                    <p class="text-18 text-dark-200">Tổng số Link CRON</p>
                                </div>
                                <div>
                                    <img src="/assets/images/all.png" width="70" height="70" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="p-3 d-flex align-items-center dashobard-widget justify-content-between rounded border shadow-sm">
                                <div>
                                    <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                        <?= $activeCronLinks ?>                                    </h3>
                                    <p class="text-18 text-dark-200">Link CRON đang chạy</p>
                                </div>
                                <div>
                                    <img src="/assets/images/play.png" width="70" height="70" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="p-3 d-flex align-items-center dashobard-widget justify-content-between rounded border shadow-sm">
                                <div>
                                    <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                        <?= $pausedCronLinks ?>                                    </h3>
                                    <p class="text-18 text-dark-200">Link CRON tạm dừng</p>
                                </div>
                                <div>
                                    <img src="/assets/images/pause.png" width="70" height="70" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="p-3 d-flex align-items-center dashobard-widget justify-content-between rounded border shadow-sm">
                                <div>
                                    <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                        <?= $expiredCronLinks ?>                                    </h3>
                                    <p class="text-18 text-dark-200">Link CRON hết hạn</p>
                                </div>
                                <div>
                                    <img src="/assets/images/expired.png" width="70" height="70" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <h3 class="text-24 fw-bold text-dark-300 mb-2">LỊCH SỬ CRON</h3>
                            <!-- Table -->
                            <form method="GET" action="" class="row">
                                <div class="col-lg col-md-4 col-6">
                        <input class="form-control shadow-none col-sm-2 mb-2" name="url" type="text" value="<?= htmlspecialchars($url) ?>" placeholder="Link cron">
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <select class="custom-style-select nice-select select-dropdown" id="status" name="status">
                            <option value="">-- Trạng thái --</option>
                            <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="paused" <?= $status == 'paused' ? 'selected' : '' ?>>Tạm dừng</option>
                            <option value="expired" <?= $status == 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                        </select>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <input type="text" class="form-control shadow-none mb-2" name="purchase_date" id="purchase_date" value="<?= htmlspecialchars($purchase_date) ?>" placeholder="Chọn khoảng thời gian">
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <button class="shop-widget-btn mb-2"><i class="fas fa-search"></i><span>Tìm kiếm</span></button>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="/user/history/cronjob" class="shop-widget-btn mb-2"><i class="far fa-trash-alt"></i><span>Bỏ lọc</span></a>
                    </div>
                </form>
                <div class="overflow-x-auto">
                                <div class="w-100">
                                    <table class="w-100 dashboard-table text-nowrap table">
                                        <thead class="pb-3">
                                            <tr>
                                                <th class="text-center py-2 px-4">
                                                    <input type="checkbox" class="form-check-input" name="check_all" id="check_all_checkbox" value="option1">
                                                </th>
                                                <th scope="col" class="py-2 px-4">STT</th>
                                                <th scope="col" class="py-2 px-4">Trans Id</th>
                                                <th scope="col" class="py-2 px-4">Link CRON</th>
                                                <th scope="col" class="py-2 px-4">Cấu hình</th>
                                                <th scope="col" class="py-2 px-4">Trạng thái</th>
                                                <th scope="col" class="py-2 px-4">Chạy gần đây</th>
                                                <th scope="col" class="py-2 px-4">Thời gian</th>
                                                <th scope="col" class="py-2 px-4">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                <?php if (!empty($cron_jobs)): ?>
                                    <?php foreach ($cron_jobs as $row): ?>
                                        <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input checkbox" data-id="<?= $row['id'] ?>" name="checkbox" value="<?= $row['id'] ?>">
                                                    </td>
                                                    
                                                    <td class="text-dark"><?= $row['id'] ?></td>
                                                    <td class="text-dark"><?= $row['trans_id'] ?></td>
                                                    <td class="text-dark"><?= $row['url'] ?></td>
                                                    <td class="text-dark">
                                                        <p>Vòng lặp: <?= $row['cron_expression'] ?></p>
                                                        <p>Method: <?= $row['method'] ?></p>
                                                    </td>
                                                    <td class="text-dark">
                                                        <?= status_cron($row["status"]) ?>                                                </td>
                                                    <td class="text-dark">
                                                        <?= $row["last_run"] ?>                                                                                                                                                                                    <?php if (!empty($row["status_code"])): ?>
                                                    <?php if ($row["status_code"] == 200): ?>
                                                        <span class="badge bg-success">Success (<?= $row["status_code"] ?>)</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Error (<?= $row["status_code"] ?>)</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                                                                                                                                        </td>
                                                    <td class="text-dark">
                                                        <p>Ngày mua: <?= $row["created_at"] ?></p>
                                                        <p>Hết hạn: <?= $row["expires_at"] ?></p>
                                                    </td>
                                                    <td class="d-flex align-items-center justify-content-between">
                                                                                                                                                                                    <?php if ($row['status'] == "active"): ?>
                                                    <button type="button" id="btnPause<?= $row['id'] ?>" data-toggle="tooltip" data-placement="bottom" title="" onclick="pauseCron(<?= $row['id'] ?>)" class="custom-btn btn-danger-custom" data-bs-original-title="Tạm dừng">
                                                                    <i class="fa-solid fa-pause"></i>
                                                                </button>
                                                <?php else: ?>
                                                    <button type="button" id="btnActive<?= $row['id'] ?>" data-toggle="tooltip" data-placement="bottom" title="" onclick="activeCron(<?= $row['id'] ?>)" class="custom-btn btn-info-custom" data-bs-original-title="Tạm dừng">
                                                                    <i class="fa-solid fa-play"></i>
                                                                </button>
                                                <?php endif; ?>
                                                                                                                        <button type="button" data-toggle="tooltip" data-placement="bottom" title="" onclick="edit(<?= $row['id'] ?>)" class="custom-btn btn-primary-custom" data-bs-original-title="Chỉnh sửa">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                                                                                <button type="button" data-toggle="tooltip" data-placement="bottom" title="" onclick="extend(<?= $row['id'] ?>)" class="custom-btn btn-success-custom" data-bs-original-title="Gia hạn">
                                                            <i class="fa-solid fa-rotate-right"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                        <td colspan="8" class="text-center">
                        <div class="empty-state">
                                <svg width="184" height="152" viewBox="0 0 184 152" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd">
                                        <g transform="translate(24 31.67)">
                                            <ellipse fill-opacity=".8" fill="#F5F5F7" cx="67.797" cy="106.89" rx="67.797" ry="12.668"></ellipse>
                                            <path d="M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z" fill="#AEB8C2"></path>
                                            <path d="M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z" fill="url(#linearGradient-1)" transform="translate(13.56)"></path>
                                            <path d="M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z" fill="#F5F5F7"></path>
                                            <path d="M42.678 9.953h50.237a2 2 0 0 1 2 2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z" fill="#DCE0E6"></path>
                                        </g>
                                        <path d="M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z" fill="#DCE0E6"></path>
                                        <g transform="translate(149.65 15.383)" fill="#FFF">
                                            <ellipse cx="20.654" cy="3.167" rx="2.849" ry="2.815"></ellipse>
                                            <path d="M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z"></path>
                                        </g>
                                    </g>
                                </svg>
                                <p>Không có dữ liệu</p>
                            </div>
                            </td>
                    </tr>
                                <?php endif; ?>
                            </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8">
                                                    <button type="button" id="btn_pause" class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-pause me-1"></i> Tạm dừng </button>
                                                    <button type="button" id="btn_active" class="btn btn-info btn-sm text-white">
                                                        <i class="fa-solid fa-play me-1"></i> Kích hoạt </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                                                    </div>
                                <div class="d-flex justify-content-end">
                        <?= pagination_client($urls, $page, $total_items, $limit); ?>
                    </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</main>
<div class="modal fade categor" id="modalExtend" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-4 d-flex justify-content-between items-placeholder border-bottom">
                <div>
                    <h3 class="text-dark-300 fw-bold text-24">Gia hạn cron</h3>
                </div>
                <div>
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="16" r="16" fill="#FF3838" />
                            <path d="M22.2188 9.77759L8.88614 23.1109" stroke="white" stroke-width="1.8" stroke-linecap="round" />
                            <path d="M22.2188 23.1099L8.88614 9.77654" stroke="white" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body py-4" id="modalViewExtend">
                <div class="row">

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade categor" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header py-4 d-flex justify-content-between items-placeholder border-bottom">
                <div>
                    <h3 class="text-dark-300 fw-bold text-24">Chỉnh sửa cron</h3>
                </div>
                <div>
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="16" r="16" fill="#FF3838" />
                            <path d="M22.2188 9.77759L8.88614 23.1109" stroke="white" stroke-width="1.8" stroke-linecap="round" />
                            <path d="M22.2188 23.1099L8.88614 9.77654" stroke="white" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body py-4" id="modalViewEdit">
                <div class="row">

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
            url: "/model/update/cron",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: 'pause'
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
            url: "/model/update/cron",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: 'play'
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

<script type="text/javascript">
    function extend(id) {
        $.ajax({
            url: "/model/modal/cron/extend",
            method: "POST",
            data: {
                csrf_token: csrf_token,
                id: id
            },
            success: function(data) {
                $("#modalViewExtend").html(data);
                $("#modalExtend").modal('show')
            }
        });
    }

    function edit(id) {
        $.ajax({
            url: "/model/modal/cron/edit",
            method: "POST",
            data: {
                csrf_token: csrf_token,
                id: id
            },
            success: function(data) {
                $("#modalViewEdit").html(data);
                $("#modalEdit").modal('show')
            }
        });
    }


    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#purchase_date", {
            mode: "range",
            dateFormat: "Y-m-d"
        });
    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>
