<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

$sotin1trang = 10;
if (isset($_GET['page']) && $data_user['level'] == 'admin') {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}

$from = ($page - 1) * $sotin1trang;
$where = ' `id` > 0 ';
$order_by = 'ORDER BY id DESC';
$username = '';
$transid = '';
$limit = '';
$description = '';
$userid = '';
$method = '';

if (!empty($_GET['user_id'])) {
    $userid = Anti_xss($_GET['user_id']);
    $where .= ' AND `user_id` = ' . $userid . ' ';
}
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}

if (!empty($_GET['content'])) {
    $description = Anti_xss($_GET['content']);
    $where .= ' AND `description` LIKE "%' . $description . '%" ';
}
if (!empty($_GET['transid'])) {
    $transid = Anti_xss($_GET['transid']);
    $where .= ' AND `trans_id` LIKE "%' . $transid . '%" ';
}
if (!empty($_GET['method'])) {
    $method = Anti_xss($_GET['method']);
    $where .= ' AND `payment_method` LIKE "%' . $method . '%" ';
}
if (!empty($_GET['limit'])) {
    $limit = Anti_xss($_GET['limit']);
    $sotin1trang = $limit;
}
$createdate = '';
if (!empty($_GET['createdate'])) {
    $createdate = Anti_xss($_GET['createdate']);
    $create_date_1 = $createdate;
    $create_date_1 = explode(' to ', $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . ' 00:00:00', $create_date_1[1] . ' 23:59:59'];
        $where .= " AND `create_date` >= '" . $create_date_1[0] . "' AND `create_date` <= '" . $create_date_1[1] . "' ";
    }
}

$invoices = $db->get_list("SELECT * FROM `invoices` WHERE $where $order_by LIMIT $from,$sotin1trang ");
$dataSumary = $db->get_list("SELECT * FROM `invoices` WHERE $where");
$totalTransactions = 0;
$totalAmount = 0;
foreach ($dataSumary as $transaction) {
    $totalAmount += $transaction['amount'];
    $totalTransactions++;
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Ngân hàng</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Nạp tiền</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ngân hàng</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <a class="btn btn-primary label-btn mb-3" href="/cpanel/recharge/bank/config">
                        <i class="ri-settings-4-line label-btn-icon me-2"></i> CẤU HÌNH
                    </a>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?= format_cash($db->get_row("SELECT SUM(`amount`) FROM `invoices` ")['SUM(`amount`)'] ?? 0); ?>đ</p>
                                        <p class="mb-0 text-muted">Toàn thời gian</p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar text-bg-danger rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?= rechargeBankMonth(); ?>đ </p>
                                        <p class="mb-0 text-muted">Tháng <?= date('m') ?></p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar text-bg-info rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?= rechargeBankWeekday() ?>đ </p>
                                        <p class="mb-0 text-muted">Trong tuần</p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar text-bg-warning rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?= rechargeBankDay() ?>đ</p>
                                        <p class="mb-0 text-muted">Hôm nay

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
            </div>
            <div class="col-xl-7">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">THỐNG KÊ NẠP TIỀN THÁNG 04</div>
                    </div>
                    <div class="card-body">
                        <canvas id="chartjs-line" class="chartjs-chart"></canvas>
                        <script>
                            (function() {
                                /* line chart  */
                                Chart.defaults.borderColor = "rgba(142, 156, 173,0.1)", Chart.defaults.color =
                                    "#8c9097";
                                const labels = [
                                    <?php
                                    $month = date('m');
                                    $year = date('Y');
                                    $numOfDays = custom_cal_days_in_month($month, $year);

                                    for ($day = 1; $day <= $numOfDays; $day++) {
                                        echo "\"$day/$month/$year\",";
                                    }
                                    ?>
                                ];
                                const data = {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Nạp tiền tự động',
                                        backgroundColor: 'rgb(132, 90, 223)',
                                        borderColor: 'rgb(132, 90, 223)',
                                        data: [
                                            <?php
                                            $data = [];
                                            for ($day = 1; $day <= $numOfDays; $day++) {
                                                $date = "$year-$month-$day";
                                                $row = $db->get_row("SELECT SUM(`amount`) FROM `invoices` WHERE DATE(FROM_UNIXTIME(create_time)) = '$date'");
                                                $data[$day - 1] = $row['SUM(`amount`)'];
                                            }
                                            for ($i = 0; $i < $numOfDays; $i++) {
                                                echo "$data[$i],";
                                            }
                                            ?>
                                        ],
                                    }]
                                };
                                const config = {
                                    type: 'bar',
                                    data: data,
                                    options: {}
                                };
                                const myChart = new Chart(
                                    document.getElementById('chartjs-line'),
                                    config
                                );



                            })();
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            LỊCH SỬ NẠP TIỀN TỰ ĐỘNG
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $userid ?>" name="user_id" placeholder="ID User">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $username ?>" name="username" placeholder="Username">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $transid ?>" name="transid" placeholder="Mã giao dịch">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $description ?>" name="content" placeholder="Nội dung nạp">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $method ?>" name="method" placeholder="Ngân hàng">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="createdate" class="form-control form-control-sm" id="daterange" value="<?= $createdate ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Search </button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/recharge"><i class="fa fa-trash"></i>
                                        Clear filter </a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="5">5</option>
                                        <option selected value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1.000</option>
                                        <option value="5000">5.000</option>
                                        <option value="10000">10.000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label">Short by Date:</label>
                                    <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option value="1">Hôm nay </option>
                                        <option value="2">Tuần này </option>
                                        <option value="3">
                                            Tháng này </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive table-wrapper mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Thời gian</th>
                                        <th class="text-right">Số tiền nạp</th>
                                        <th class="text-right">Thực nhận</th>
                                        <th class="text-center">Ngân hàng</th>
                                        <th class="text-center">Mã giao dịch</th>
                                        <th>Nội dung chuyển khoản</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $row) : ?>
                                        <tr>
                                            <td class="text-center"><a class="text-primary" href="/cpanel/user/edit/<?= $row['user_id'] ?>"><?= getRowUser($row['user_id'], 'username') ?> [ID <?= $row['user_id'] ?>]</a>
                                            </td>
                                            <td><?= date('Y-m-d H:i:s', $row['create_time']) ?></td>
                                            <td class="text-right"><b style="color: green;"><?= format_cash($row['amount']) ?>đ</b>
                                            </td>
                                            <td class="text-right"><b style="color: red;"><?= format_cash($row['amount']) ?>đ</b>
                                            </td>
                                            <td class="text-center"><b><?= $row['payment_method'] ?></b></td>
                                            <td class="text-center"><b><?= $row['trans_id'] ?></b></td>
                                            <td><small><?= $row['description'] ?></small></td>
                                        </tr>

                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">
                                            <div class="float-right">
                                                Tổng Giao dịch: <strong style="color:green;"><?= format_cash($totalTransactions) ?></strong>
                                                |
                                                Đã thanh toán: <strong style="color:red;"><?= format_cash($totalAmount) ?>đ</strong>
                                                |
                                                Thực nhận: <strong style="color:blue;"><?= format_cash($totalAmount) ?>đ</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">

                            <div class="col-sm-12 col-md-12 mb-3">
                                <div class="pagination-style-1">
                                    <div class="d-flex justify-content-center">
                                        <?php
                                        $total = $db->num_rows("SELECT * FROM `invoices` WHERE $where");
                                        if ($total > $sotin1trang) {
                                            echo '<center>' . pagination("/cpanel/recharge?user_id=$userid&username=$username&transid=$transid&content=$description&method=$method&createdate=$createdate&limit=$limit&shortByDate=&", $from, $total, $sotin1trang) . '</center>';
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>