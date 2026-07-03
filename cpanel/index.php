<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2"><i class="fa-solid fa-chart-line"></i> Dashboard</h1>
            <div class="float-right">

            </div>
        </div>
        <div class="alert alert-secondary alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <h5><?=$config["project"];?> Version: <strong style="color:blue;"><?=$config["version"];?></strong></h5>
            <small>Hệ thống không tự động cập nhật, nếu muốn cập nhật liên hệ <a class="text-primary" href="https://t.me/BuiDucThanh" target="_blank">Bùi Đức Thành</a></small>
            <br>
            <div class="mt-3">
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-telegram text-primary me-2"></i>
                            <small>Kênh thông báo cập nhật:</small>
                            <span class="badge bg-warning">Chờ cập nhật thêm</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-telegram text-primary me-2"></i>
                            <small>Nhóm tìm kiếm API:</small>
                            <span class="badge bg-warning">Chờ cập nhật thêm</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-rocketchat text-success me-2"></i>
                            <small>Nhóm Zalo thông báo:</small>
                            <span class="badge bg-warning">Chờ cập nhật thêm</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-rocketchat text-success me-2"></i>
                            <small>Nhóm Zalo trao đổi API:</small>
                            <span class="badge bg-warning">Chờ cập nhật thêm</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <a class="btn btn-outline-primary btn-wave btn-sm" type="button"
                    href="https://client.azviet.net" target="_blank"><i class="fa-brands fa-chrome"></i> Kiểm tra bản quyền </a>
            <a class="btn btn-outline-secondary btn-wave btn-sm" type="button" href="https://t.me/LicensedCode_Bot"
                target="_blank"><i class="fa-brands fa-telegram"></i> Bot kiểm tra bản quyền </a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i
                    class="bi bi-x"></i></button>
        </div>
        <?php if (empty($db->site('email_smtp')) || empty($db->site('pass_email_smtp'))): ?>
<div class="alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
    <svg class="svg-warning" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
        <path d="M0 0h24v24H0z" fill="none" />
        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
    </svg>
    Vui lòng cấu hình <b>SMTP</b> để sử dụng toàn bộ tiện ích từ Mail:
    <a class="text-primary" href="https://help.cmsnt.co/huong-dan/huong-dan-cau-hinh-smtp-vao-website-shopclone7/" target="_blank">Xem Hướng Dẫn</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
</div>
<?php endif; ?>
        <div class="row">
                        <div class="col-12">
                <div class="text-right mb-3">
                    <img src="/cpanel/assets/images/gif-live.gif" width="60px">
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card custom-card hrm-main-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-primary">
                                    <i class="fa-solid fa-users fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2">Thành viên đăng ký</span>
                                <h5 class="fw-semibold mb-2">
                                    <?= usersTotal() ?></h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary-transparent">Toàn thời gian</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card custom-card hrm-main-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-danger">
                                    <i class="fa-solid fa-money-bill-trend-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2">Doanh thu</span>
                                <h5 class="fw-semibold mb-2">
                                    <?= revenueTotal() ?>đ </h5>
                                <p class="mb-0">
                                    <span class="badge bg-danger-transparent">Toàn thời gian</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card custom-card hrm-main-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-primary">
                                    <i class="fa-solid fa-users fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2">Thành viên đăng ký</span>
                                <h5 class="fw-semibold mb-2">
                                    <?= usersMonthTotal() ?> </h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary-transparent">Tháng <?= date('m') ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card custom-card hrm-main-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-danger">
                                    <i class="fa-solid fa-money-bill-trend-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2">Doanh thu</span>
                                <h5 class="fw-semibold mb-2">
                                    <?= revenueMonthTotal() ?>đ </h5>
                                <p class="mb-0">
                                    <span class="badge bg-danger-transparent">Tháng <?= date('m') ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card custom-card hrm-main-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-primary">
                                    <i class="fa-solid fa-users fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2">Thành viên đăng ký</span>
                                <h5 class="fw-semibold mb-2">
                                    <?= usersDayTotal() ?> </h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary-transparent">Hôm nay</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card custom-card hrm-main-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-danger">
                                    <i class="fa-solid fa-money-bill-trend-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2">Doanh thu</span>
                                <h5 class="fw-semibold mb-2">
                                    <?= revenueDayTotal() ?>đ </h5>
                                <p class="mb-0">
                                    <span class="badge bg-danger-transparent">Hôm nay</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="row">
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">HOẠT ĐỘNG GẦN ĐÂY</div>
                        <div class="ms-auto">
                            <img class="text-right" src="/cpanel/assets/images/gif-live.gif" width="60px">
                        </div>
                    </div>
                </div>
                <ul class="timeline list-unstyled" style="height:500px;overflow-x:hidden;overflow-y:auto;">
                    <?php foreach ($db->get_list("SELECT * FROM `logs` ORDER BY `id` DESC LIMIT 20") as $log) : ?>
                        <li>
                            <div class="timeline-time text-end">
                                <span class="date"><?= timeAgo($log['create_time']) ?></span>
                            </div>
                            <div class="timeline-icon">
                                <a href="javascript:void(0);"></a>
                            </div>
                            <div class="timeline-body">
                                <div class="d-flex align-items-top timeline-main-content flex-wrap mt-0">
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center">
                                            <div class="mt-sm-0 mt-2">
                                                <p class="mb-0 text-muted"><b style="color: green;"><?= getRowUser($log['user_id'], 'username') ?></b>
                                                    vừa <b style="color: dark;"><?= $log['action'] ?></b> vào lúc <b style="color: red;"><?= $log['create_date'] ?></b>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">NẠP TIỀN GẦN ĐÂY</div>
                        <div class="ms-auto">
                            <img class="text-right" src="/cpanel/assets/images/gif-live.gif" width="60px">
                        </div>
                    </div>
                </div>
                <ul class="timeline list-unstyled" style="height:500px;overflow-x:hidden;overflow-y:auto;">
                    <?php foreach ($db->get_list("SELECT * FROM `invoices` ORDER BY `id` DESC LIMIT 10") as $balance) : ?>
                        <li>
                            <div class="timeline-time text-end">
                                <span class="date"><?= timeAgo($balance['create_time']) ?></span>
                            </div>
                            <div class="timeline-icon">
                                <a href="javascript:void(0);"></a>
                            </div>
                            <div class="timeline-body">
                                <div class="d-flex align-items-top timeline-main-content flex-wrap mt-0">
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center">
                                            <div class="mt-sm-0 mt-2">
                                                <p class="mb-0 text-muted"><b style="color: green;"><?= getRowUser($balance['user_id'], 'username') ?></b>
                                                    thực hiện nạp <b style="color: blue;"><?= format_cash($balance['amount']) ?>đ</b>
                                                    bằng <b style="color:red"><?= $balance['payment_method'] ?></b> thực nhận <b style="color:blue;"><?= format_cash($balance['amount']) ?>đ</b>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>