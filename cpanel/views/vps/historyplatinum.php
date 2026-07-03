<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET["limit"])) {
    $limit = (int) Anti_xss($_GET["limit"]);
} else {
    $limit = 5;
}
if (isset($_GET["page"])) {
    $page = Anti_xss((int) $_GET["page"]);
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `id` > 0 AND `site` = 'CLOUDNEST'";
$username = "";
$create_gettime = "";
$title = "";
$shortByDate = "";
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `tbl_users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}

$select_status_vps  = '';
$ip = '';

if (isset($_POST['addVPS']) && $data_user['level'] == 'admin') {
    $email = Anti_xss($_POST['email']);
    $vpsid = Anti_xss($_POST['vpsid']);
    $billingcycle = Anti_xss($_POST['billingcycle']);
    $billingcycleday = Anti_xss($_POST['billingcycleday']);
    $price = Anti_xss($_POST['price']);
    $cost = Anti_xss($_POST['cost']);
    if (empty($email) || empty($vpsid) || empty($billingcycleday) || empty($billingcycle) || empty($price) || empty($cost)) {
        die('<script type="text/javascript">if(!alert("Vui lòng nhập đủ dữ liệu!")){window.history.back().location.reload();}</script>');
    }
    if (check_email($email) == false) {
        die('<script type="text/javascript">if(!alert("Địa chỉ email không đúng định dạng!")){window.history.back().location.reload();}</script>');
    }
    if (!$check_user = $db->get_row("SELECT * FROM `users` WHERE `email` = '" . $email . "'")) {
        die('<script type="text/javascript">if(!alert("Địa chỉ email không tồn tại!")){window.history.back().location.reload();}</script>');
    }


    $isInsert = $db->insert("tbl_purchased_cloudvps", array(
        'user_id' => $check_user['id'],
        'vps_id' => $vpsid,
        'billingcycle' => $billingcycle,
        'billingcycleday' => $billingcycleday,
        'price' => $price,
        'cost' => $cost,
        'total_price' => $price,
        'total_cost' => $cost,
        'created_at' => gettime(),
        'updated_at' => gettime(),
        'site' =>'CLOUDNEST'
    ));
    if ($isInsert) {
        die('<script type="text/javascript">if(!alert("Thêm thành công !")){location.href = "/cpanel/vps/history";}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Thêm thất bại !")){window.history.back().location.reload();}</script>');
    }
}

if (!empty($_GET['status'])) {
    $select_status_vps = Anti_xss($_GET['status']);
    $listCloudVpsUser = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE `site` = 'CLOUDNEST' ORDER BY `id` DESC");
    $liststatus = array();
    foreach ($listCloudVpsUser as $CloudVpsStatus) {
        $info = json_decode(decryptAES($CloudVpsStatus['info']), true);
        if ($info['vps-status'] == $select_status_vps) {
            $liststatus[] = $CloudVpsStatus['id'];
        }
    }
    if (!empty($liststatus)) {
        $id_list = implode(',', $liststatus);
        $where .= " AND `id` IN ($id_list)";
    }else{
        $where .= " AND `id` = 0";
    }
}

if (!empty($_GET['ip'])) {
    $ip = Anti_xss($_GET['ip']);
    $listCloudVpsUserIp = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE `site` = 'CLOUDNEST' ORDER BY `id` DESC");
    $listip = array();
    foreach ($listCloudVpsUserIp as $CloudVpsIp) {
        $infoip = json_decode(decryptAES($CloudVpsIp['info']), true);
        if (strpos($infoip['ip'], $ip) !== false) {
            $listip[] = $CloudVpsIp['id'];
        }
    }
    if (!empty($listip)) {
        $id_list_ip = implode(',', $listip);
        $where .= " AND `id` IN ($id_list_ip)";
    }else{
        $where .= " AND `id` = 0";
    }
}

$listCloudVps = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE `site` = 'CLOUDNEST'");
$array = array();
foreach ($listCloudVps as $data) {
    array_push($array, (int)$data['vps_id']);
}
if (!empty($array)) {
    $jsonString = json_encode($array);
    $result = infoListVpsCloudNest($jsonString);
    if (isset($result['error']) && $result['error'] == 0) {
        foreach ($result['data'] as $infovps) {
            $db->update("tbl_purchased_cloudvps", array(
                'info' => encryptAES(json_encode($infovps)),
                'status' => $infovps['vps-status'],
            ), " `vps_id` = '" . $infovps['vps-id'] . "' ");
        }
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
$listDatatable = $db->get_list(" SELECT * FROM `tbl_purchased_cloudvps` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `tbl_purchased_cloudvps` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("/cpanel/vps/platinum/history&limit=" . $limit . "&shortByDate=" . $shortByDate . "&ip=" . $ip . "&status=" . $select_status_vps . "&"), $from, $totalDatatable, $limit);

$total_vps_active = $db->get_row("SELECT COUNT(id) as total FROM tbl_purchased_cloudvps WHERE `site` = 'CLOUDNEST' AND `status` = 'on' ")['total'] ?? 0;
$total_vps_expire = $db->get_row("SELECT COUNT(id) as total FROM tbl_purchased_cloudvps WHERE `site` = 'CLOUDNEST' AND `status` = 'expire'")['total'] ?? 0;
$total_price = $db->get_row("SELECT SUM(total_price) as total FROM tbl_purchased_cloudvps WHERE `site` = 'CLOUDNEST'")['total'] ?? 0;
$total_cost = $db->get_row("SELECT SUM(total_cost) as total FROM tbl_purchased_cloudvps WHERE `site` = 'CLOUDNEST'")['total'] ?? 0;
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Đơn hàng cloudvps</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Đơn hàng cloudvps</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= $total_vps_active ?> </p>
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
                                    <?=$total_vps_expire?></p>
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
                                <?= format_cash($total_price) ?></p>
                                <p class="mb-0 text-muted">Doanh thu</p>
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
                                <?= format_cash($total_price - $total_cost) ?>đ</p>
                                <p class="mb-0 text-muted">Lợi nhuận

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
                            <a type="button" data-bs-toggle="modal" data-bs-target="#exampleModalgrid" class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light"><i class="ri-add-line fw-semibold align-middle"></i> Tạo VPS</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row">
                                <div class="col-sm-3 mb-2">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" name="username" class="form-control" placeholder="Khách hàng">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" name="ip" class="form-control" placeholder="IP">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-2">
                                    <select name="status" class="form-control select2">
                                        <option value="" selected="selected">Chọn trạng thái của VPS</option>
                                        <option value="on" <?= $select_status_vps == "on" ? "selected" : "" ?>>Trạng thái đang bật</option>
                                        <option value="off" <?= $select_status_vps == "off" ? "selected" : "" ?>>Trạng thái đã tắt</option>
                                        <option value="progressing" <?= $select_status_vps == "progressing" ? "selected" : "" ?>>Trạng thái đang tạo</option>
                                        <option value="waiting" <?= $select_status_vps == "waiting" ? "selected" : "" ?>>Trạng thái đang chờ tạo</option>
                                        <option value="rebuild" <?= $select_status_vps == "rebuild" ? "selected" : "" ?>>Trạng thái đang cài lại</option>
                                        <option value="expire" <?= $select_status_vps == "expire" ? "selected" : "" ?>>Trạng thái hết hạn</option>
                                        <option value="suspend" <?= $select_status_vps == "suspend" ? "selected" : "" ?>>Trạng thái đã khóa</option>
                                        <option value="delete_vps" <?= $select_status_vps == "delete_vps" ? "selected" : "" ?>>Trạng thái đã xóa</option>
                                        <option value="cancel" <?= $select_status_vps == "cancel" ? "selected" : "" ?>>Trạng thái đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button class="btn btn-info waves-effect" type="submit" name="filter">Tìm
                                        kiếm</button>
                                    <a href="/cpanel/vps/platinum/history" class="btn btn-danger waves-effect">Tất
                                        cả</a>
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
                                        <th>Khách hàng</th>
                                        <th>Thông tin</th>
                                        <th>Thanh toán</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) :
                                        $detail = json_decode(decryptAES($row['info']), true);
                                    ?>
                                        <tr>
                                            <td>
                                                <ul>
                                                    <li>Khách hàng: <b style="color:blue"><?= getRowRealtime('users', $row['user_id'], 'username'); ?></b>
                                                        [<b><?= getRowRealtime('users', $row['user_id'], 'id'); ?></b>]
                                                    </li>
                                                    <li>Gói VPS:
                                                        <b><?= isset($detail['text-config']) ? $detail['text-config'] : $detail['name']; ?> <b class="text-danger">[<?= $row['vps_id'] ?>]</b></b>
                                                    </li>

                                                </ul>
                                            </td>

                                            <td>
                                                <ul>
                                                    <li>IP: <b><?= $detail['ip']; ?></b></li>
                                                    <li>Tài khoản: <b><?= $detail['username']; ?></b></li>
                                                    <li>Mật khẩu: <b><?= $detail['password']; ?></b></li>

                                                </ul>
                                            </td>
                                            <td>
                                                <ul>
                                                    <li>Khách thanh toán: <b class="text-danger"><?= format_cash($row['price']); ?></b>
                                                    </li>
                                                    <li>Admin thanh toán: <b class="text-success"><?= format_cash($row['cost']); ?></b>
                                                    </li>
                                                    <li>Lợi nhuận chu kỳ: <b class="text-primary"><?= format_cash($row['price'] - $row['cost']); ?></b>
                                                    </li>
                                                    <li>Lợi nhuận tổng: <b class="text-secondary"><?= format_cash($row['total_price'] - $row['total_cost']); ?></b>
                                                    </li>
                                                    <li>Ngày mua: <b class="text-info"><?= $detail['date_create'] ?></b>
                                                    </li>
                                                    <li>Ngày hết hạn: <b class="text-dark"><?= $detail['next_due_date'] ?></b>
                                                    </li>
                                                    <li>Trạng thái: <b><?= $detail['html-vps-status'] ?></b></li>
                                                </ul>
                                            </td>
                                            <td>
                                                <a href="/cpanel/vps/platinum/history/edit/<?= $row['id'] ?>">
                                                    <button data-toggle="tooltip" data-placement="top" title="" data-original-title="Chỉnh sửa đơn hàng" type="button" class="btn btn-info btn-outline btn-xs m-r-5 tooltip-info"><i class="ri-edit-line align-bottom"></i></button>
                                                </a>
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
<div class="modal fade" id="exampleModalgrid" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">Thêm VPS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="row g-3">
                        <div class="col-xxl-6">
                            <div>
                                <label for="firstName" class="form-label">EMAIL</label>
                                <input type="email" class="form-control" name="email" placeholder="example@gmail.com" required>
                            </div>
                        </div><!--end col-->
                        <div class="col-xxl-6">
                            <div>
                                <label for="lastName" class="form-label">VPS ID</label>
                                <input type="number" class="form-control" name="vpsid" placeholder="VPS ID" required>
                            </div>
                        </div><!--end col-->
                        <div class="col-xxl-6">
                            <div>
                                <label for="emailInput" class="form-label">CHU KỲ</label>
                                <select name="billingcycle" class="form-control">
                                    <option value="">--Chọn chu kỳ--</option>
                                    <option value="monthly">1 Tháng</option>
                                    <option value="twomonthly">2 Tháng</option>
                                    <option value="quarterly">3 Tháng</option>
                                    <option value="semi_annually">6 Tháng</option>
                                    <option value="annually">1 Năm</option>
                                    <option value="biennially">2 Năm</option>
                                    <option value="triennially">3 Năm</option>
                                </select>
                            </div>
                        </div><!--end col-->
                        <div class="col-xxl-6">
                            <div>
                                <label for="emailInput" class="form-label">SỐ NGÀY CHU KỲ</label>
                                <input type="number" class="form-control" name="billingcycleday" placeholder="Ví dụ 30" required>
                            </div>
                        </div><!--end col-->
                        <div class="col-xxl-6">
                            <div>
                                <label for="firstName" class="form-label">Giá bán</label>
                                <input type="number" class="form-control" name="price" placeholder="50000" required>
                            </div>
                        </div><!--end col-->
                        <div class="col-xxl-6">
                            <div>
                                <label for="lastName" class="form-label">Giá gốc</label>
                                <input type="number" class="form-control" name="cost" placeholder="40000" required>
                            </div>
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary" name="addVPS">Thêm Ngay</button>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>