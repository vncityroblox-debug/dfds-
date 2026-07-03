<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

$limit = 20;
if (isset($_GET['page'])) {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = ' `id` > 0 ';
$username = '';
$status = '';
$domain = '';
$email = '';
$shortByDate = "";
$create_gettime = "";
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}
if (!empty($_GET['domain'])) {
    $domain = Anti_xss($_GET['domain']);
    $where .= ' AND `domain_name` LIKE "%' . $domain . '%" ';
}
if (!empty($_GET['email'])) {
    $email = Anti_xss($_GET['email']);
    $where .= ' AND `email` LIKE "%' . $email . '%" ';
}
if (!empty($_GET['status'])) {
    $status = Anti_xss($_GET['status']);
    if ($status == 'active') {
        $where .= ' AND `status` = "active" ';
    } else if ($status == 'suspended') {
        $where .= ' AND `status` = "suspended" ';
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

$listOrder = $db->get_list(" SELECT * FROM `purchased_hosting` WHERE $where ORDER BY id DESC LIMIT $from,$limit ");
$total_hosting_active = $db->get_row("SELECT COUNT(id) as total FROM purchased_hosting WHERE `status` = 'active'")['total'] ?? 0;
$total_hosting_expired = $db->get_row("SELECT COUNT(id) as total FROM purchased_hosting WHERE `status` = 'expired'")['total'] ?? 0;
$total_hosting_suspended = $db->get_row("SELECT COUNT(id) as total FROM purchased_hosting WHERE `status`='suspended'")['total'] ?? 0;
$total_amount = $db->get_row("SELECT SUM(total) as total FROM purchased_hosting")['total'] ?? 0;
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Lịch sử mua hosting</h1>
        </div>
        <div class="row">
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= $total_hosting_active ?> </p>
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
                                    <?= $total_hosting_expired ?></p>
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
                                    <?= $total_hosting_suspended ?></p>
                                <p class="mb-0 text-muted">Tạm khóa</p>
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
                                    <?= format_cash($total_amount) ?>đ</p>
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
                            DANH SÁCH ĐƠN HÀNG
                        </div>
                        <div class="d-flex">
                            <button data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2" class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light me-2"><i class="ri-add-line fw-semibold align-middle"></i> Thêm tự động</button>
                            <button data-bs-toggle="modal" data-bs-target="#thucong" class="btn btn-sm btn-success btn-wave waves-light waves-effect waves-light"><i class="ri-add-line fw-semibold align-middle"></i> Thêm thủ công</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $username ?>" name="username" placeholder="Khách hàng">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $domain ?>" name="domain" placeholder="Tên miền">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $email ?>" name="email" placeholder="Email">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <select name="status" class="form-control select2">
                                        <option value="">Trạng thái</option>
                                        <option value="suspended">Tạm dừng</option>
                                        <option value="active">Hoạt động</option>
                                        <option value="expired">Hết hạn</option>
                                    </select>
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control" id="daterange" value="<?= $create_gettime ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/hosting/history"><i class="fa fa-trash"></i>
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
                                        <th>Khách hàng</th>
                                        <th>Thông tin</th>
                                        <th>Thanh toán</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($listOrder as $row) :
                                        $server_whm = json_decode(($row['server_whm']), true);
                                        $info_package = json_decode($row['info_package'], true);
                                    ?>
                                        <tr>
                                            <td>
                                                <ul>
                                                    <li>Khách hàng: <b style="color:blue"><?= getRowRealtime('users', $row['user_id'], 'username'); ?></b>
                                                        [<b><?= getRowRealtime('users', $row['user_id'], 'id'); ?></b>]
                                                    </li>
                                                    <li>Gói hosting: <b><?= $info_package['package_name'] ?></b></li>
                                                    <li>Máy chủ: <b><a target="_blank" href="http://<?= $server_whm['ip'] ?>:2082"><?= $server_whm['ip'] ?>:2082</a></b>
                                                    </li>
                                                </ul>
                                            </td>

                                            <td>
                                                <ul>
                                                    <li>Tài khoản: <b><?= ($row['username']); ?></b></li>
                                                    <li>Mật khẩu: <b><?= ($row['password']); ?></b></li>
                                                    <li>Tên miền: <b style="color:red"><?= $row['domain_name']; ?></b></li>
                                                    <li>Email: <b class="text-dark"><?= $row['email']; ?></b></li>
                                                </ul>
                                            </td>
                                            <td>
                                                <ul>
                                                    <li>Số tiền: <b class="text-danger"><?= format_cash($row['price']); ?></b> -
                                                        Tổng: <b class="text-danger"><?= format_cash($row['total']); ?></b>
                                                    </li>
                                                    <li>Ngày mua: <b class="text-info"><?= date('H:i:s d-m-Y', $row['start_date']) ?></b>
                                                    </li>
                                                    <li>Ngày hết hạn: <b class="text-dark"><?= date('H:i:s d-m-Y', $row['end_date']) ?></b>
                                                    </li>
                                                    <li>Trạng thái: <b><?= status_hosting($row['status']); ?></b></li>
                                                </ul>
                                            </td>

                                            <td>
                                                <a href="/cpanel/hosting/server/edit/<?= $row['id'] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-original-title="Chỉnh sửa">
                                                    <i class="fa fa-pencil-alt"></i> Edit
                                                </a>
                                                <button onclick="confirmAction(<?= $row['id'] ?>,'remove')" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-original-title="Xóa">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-center">
                                <?php
                                $total = $db->num_rows("SELECT * FROM `purchased_hosting` WHERE $where ORDER BY `id` DESC ");
                                if ($total > $limit) {
                                    echo '<center>' . pagination("/cpanel/hosting/history?limit=" . $limit . "&shortByDate=" . $shortByDate . "&domain=" . $domain . "&username=" . $username . "&create_gettime=" . $create_gettime . "&email=" . $email . "&", $from, $total, $limit) . '</center>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2" data-bs-keyboard="false" aria-hidden="true">
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2">Thêm đơn mới</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Tài khoản</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="username" placeholder="Nhập tài khoản" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Tên miền</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="domain" placeholder="Nhập tên miền" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Gói</label>
                        <div class="col-sm-8">
                            <select id="package" class="form-control select2">
                                <option value="">Chọn gói</option>
                                <?php foreach ($db->get_list("SELECT * FROM `hosting_packages` WHERE `status` = 1 AND `whm_id` = '" . $db->get_row("SELECT * FROM `whm_info` WHERE `status` = 1")['id'] . "' ORDER BY `id` ASC") as $host) :
                                ?>
                                    <option value="<?= $host['id'] ?>"><?= $host['name'] ?> - <?= format_cash($host['price']) ?>/Tháng</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Thời gian</label>
                        <div class="col-sm-8">
                            <select id="month" class="form-control select2">
                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <option value="<?= $i ?>"><?= $i ?> Tháng</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="AddHosting()" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="thucong" tabindex="-1" aria-labelledby="thucong" data-bs-keyboard="false" aria-hidden="true">
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2">Thêm đơn thủ công</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
                        Chức năng này dành cho chuyển dữ liệu từ website cũ sang
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" fdprocessedid="ax66hm"><i class="bi bi-x"></i></button>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Khách hàng</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="username_handmade" placeholder="Nhập tài khoản khách hàng" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Tên miền</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="domain_handmade" placeholder="Nhập tên miền" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">IP</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="ip" placeholder="Nhập ip" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Tài khoản</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="account" placeholder="Nhập tài khoản" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Mật khẩu</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="password" placeholder="Nhập mật khẩu" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Tài khoản máy chủ</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="account_server" placeholder="Nhập tài khoản máy chủ VD:sieuthicode" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Gói</label>
                        <div class="col-sm-8">
                            <select id="package_handmade" class="form-control select2">
                                <option value="">Chọn gói</option>
                                <?php foreach ($db->get_list("SELECT * FROM `hosting_packages` WHERE `whm_id` = '" . $db->get_row("SELECT * FROM `whm_info` WHERE `status` = 1")['id'] . "' ORDER BY `id` ASC") as $host) :
                                ?>
                                    <option value="<?= $host['id'] ?>"><?= $host['name'] ?> - <?= format_cash($host['price']) ?>/Tháng</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Chu kỳ</label>
                        <div class="col-sm-8">
                            <select id="month_handmade" class="form-control select2">
                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <option value="<?= $i ?>"><?= $i ?> Tháng</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="AddhandmadeHosting()" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    const confirmAction = (param, action) => {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện chức năng này chứ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await Item(param, action);
            }
        });
    }

    const Item = async (param, action) => {
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
            url: '/model/admin/hosting',
            method: "POST",
            dataType: "JSON",
            data: {
                param: param,
                action: action
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.href = '';
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

    function AddHosting() {
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
            url: '/model/admin/hosting/add',
            method: "POST",
            dataType: "JSON",
            data: {
                package: $('#package').val(),
                month: $('#month').val(),
                domain: $('#domain').val(),
                username: $('#username').val(),
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.href = '';
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
    function AddhandmadeHosting() {
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
            url: '/model/admin/hosting/handmade',
            method: "POST",
            dataType: "JSON",
            data: {
                package: $('#package_handmade').val(),
                month: $('#month_handmade').val(),
                domain: $('#domain_handmade').val(),
                username: $('#username_handmade').val(),
                ip: $('#ip').val(),
                account: $('#account').val(),
                password: $('#password').val(),
                account_server: $('#account_server').val(),
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.href = '';
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