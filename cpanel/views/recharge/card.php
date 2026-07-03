<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_GET["limit"]) && $data_user['level'] == 'admin') {
    $limit = (int) Anti_xss($_GET["limit"]);
} else {
    $limit = 10;
}
if (isset($_GET['page'])) {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}

$from = ($page - 1) * $limit;
$where = ' `id` > 0 ';
$order_by = 'ORDER BY id DESC';
$username = '';
$transid = '';
$userid = '';
$pin = "";
$createdate = "";
$serial = "";
$status = "";
$shortByDate = "";
if (!empty($_GET['user_id'])) {
    $userid = Anti_xss($_GET['user_id']);
    $where .= ' AND `user_id` = ' . $userid . ' ';
}
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}

if (!empty($_GET["status"])) {
    $status = Anti_xss($_GET["status"]);
    $where .= " AND `status` = \"" . $status . "\" ";
}
if (!empty($_GET["pin"])) {
    $pin = Anti_xss($_GET["pin"]);
    $where .= " AND `pin` LIKE \"%" . $pin . "%\" ";
}
if (!empty($_GET["serial"])) {
    $serial = Anti_xss($_GET["serial"]);
    $where .= " AND `serial` LIKE \"%" . $serial . "%\" ";
}
if (!empty($_GET["create_date"])) {
    $create_date = Anti_xss($_GET["create_date"]);
    $createdate = $create_date;
    $create_date_1 = str_replace("-", "/", $create_date);
    $create_date_1 = explode(" to ", $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . " 00:00:00", $create_date_1[1] . " 23:59:59"];
        $where .= " AND `create_date` >= '" . $create_date_1[0] . "' AND `create_date` <= '" . $create_date_1[1] . "' ";
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
        $where .= " AND `create_date` LIKE '%" . $currentDate . "%' ";
    }
    if ($shortByDate == 2) {
        $where .= " AND YEAR(create_date) = " . $currentYear . " AND WEEK(create_date, 1) = " . $currentWeek . " ";
    }
    if ($shortByDate == 3) {
        $where .= " AND MONTH(create_date) = '" . $currentMonth . "' AND YEAR(create_date) = '" . $currentYear . "' ";
    }
}

$invoices = $db->get_list("SELECT * FROM `cards` WHERE $where $order_by LIMIT $from,$limit ");
$dataSumary = $db->get_list("SELECT * FROM `cards` WHERE $where");
$totalTransactions = 0;
$totalAmount = 0;
$totalPrice = 0;
foreach ($dataSumary as $transaction) {
    $totalAmount += $transaction['amount'];
    $totalPrice += $transaction['price'];
    $totalTransactions++;
}
$yesterday = date("Y-m-d", strtotime("-1 day"));
$currentWeek = date("W");
$currentMonth = date("m");
$currentYear = date("Y");
$currentDate = date("Y-m-d");
$total_all_time = $db->get_row("SELECT SUM(amount) FROM cards WHERE  `status` = 'completed' ")["SUM(amount)"] ?? 0;
$total_today = $db->get_row("SELECT SUM(amount) FROM cards WHERE  `status` = 'completed' AND `create_date` LIKE '%" . $currentDate . "%' ")["SUM(amount)"] ?? 0;
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Nạp tiền bằng thẻ Điện Thoại, thẻ Game</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Nạp tiền</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thẻ cào</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <a class="btn btn-primary label-btn mb-3" href="/cpanel/recharge/card/config">
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
                                            <?= format_cash($total_all_time); ?>đ
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
                                            <?= format_cash($db->get_row("SELECT SUM(amount) FROM cards WHERE `status`='completed' AND MONTH(create_date)='" . $currentMonth . "'AND YEAR(create_date)='" . $currentYear . "'")["SUM(amount)"] ?? 0) ?>đ</p>
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
                                            <?= format_cash($db->get_row("SELECT SUM(amount) FROM cards WHERE `status`='completed' AND YEAR(create_date)=" . $currentYear . " AND WEEK(create_date,1)=" . $currentWeek . "")["SUM(amount)"] ?? 0) ?>đ</p>
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
                                            <?= format_cash($total_today) ?>đ</p>
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
                        <div class="card-title">THỐNG KÊ NẠP TIỀN THÁNG <?= date('m') ?></div>
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
                                                $row = $db->get_row("SELECT SUM(amount) FROM cards WHERE DATE(create_date) = '$date' AND `status` = 'completed'");
                                                $data[$day - 1] = $row["SUM(amount)"] ?? 0;
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
                            LỊCH SỬ NẠP THẺ CÀO
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?= $userid ?>" name="user_id" placeholder="Search ID User">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?= $username ?>" name="username" placeholder="Search Username">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?= $pin ?>" name="pins" placeholder="Search Pin">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?= $serial ?>" name="sesrial" placeholder="Search Serial">
                                </div>
                                <div class="col-md-3 col-6">
                                    <select class="form-control form-control-sm mb-1" name="status">
                                        <option value="">Status </option>
                                        <option <?= $status == "pending" ? "selected" : "" ?> value="pending"> Đang chờ xử lý
                                        </option>
                                        <option <?= $status == "error" ? "selected" : "" ?> value="error"> Thẻ lỗi </option>
                                        <option <?= $status == "completed" ? "selected" : "" ?> value="completed"> Thành công
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_date" class="form-control form-control-sm js-flatpickr" id="example-flatpickr-range" value="<?= $createdate ?>" placeholder="Chọn thời gian" data-mode="range">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Search
                                    </button> <a class="btn btn-sm btn-danger" href="/cpanel/recharge/card"><i class="fa fa-trash"></i>
                                        Clearfilter </a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show"> <label class="filter-label">Giới hạn :</label> <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option <?= $limit == 5 ? "selected" : "" ?> value="5">5</option>
                                        <option <?= $limit == 10 ? "selected" : "" ?> value="10">10</option>
                                        <option <?= $limit == 20 ? "selected" : "" ?> value="20">20</option>
                                        <option <?= $limit == 50 ? "selected" : "" ?> value="50">50</option>
                                        <option <?= $limit == 100 ? "selected" : "" ?> value="100">100</option>
                                        <option <?= $limit == 500 ? "selected" : "" ?> value="500">500</option>
                                        <option <?= $limit == 1000 ? "selected" : "" ?> value="1000">1000 </option>
                                    </select> </div>
                                <div class="filter-short"> <label class="filter-label">Short by Date: </label> <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả </option>
                                        <option <?= $shortByDate == 1 ? "selected" : "" ?> value="1"> Hôm nay </option>
                                        <option <?= $shortByDate == 2 ? "selected" : "" ?> value="2"> Tuần này </option>
                                        <option <?= $shortByDate == 3 ? "selected" : "" ?> value="3"> Tháng này </option>
                                    </select> </div>
                            </div>
                        </form>
                        <div class="table-responsive table-wrapper mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th class="text-center">Telco</th>
                                        <th class="text-center">Serial</th>
                                        <th class="text-center">Pin</th>
                                        <th class="text-center">Mệnhgiá</th>
                                        <th class="text-center">Thựcnhận</th>
                                        <th class="text-center">Trạngthái</th>
                                        <th class="text-center">Createdate</th>
                                        <th class="text-center">Updatedate</th>
                                        <th class="text-center">Lýdo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $row) : ?>
                                        <tr>
                                            <td class="text-center"><a class="text-primary" href="/cpanel/user/edit/<?= $row['user_id'] ?>"><?= getRowUser($row['user_id'], 'username') ?>
                                                    [ID <?= $row['user_id'] ?>]</a> </td>
                                            <td class="text-center"><?= $row["telco"] ?> </td>
                                            <td class="text-center"><?= $row["serial"] ?> </td>
                                            <td class="text-center"><?= $row["pin"] ?> </td>
                                            <td class="text-right"><b style="color: red;"><?= format_cash($row["amount"]) ?> </b>
                                            </td>
                                            <td class="text-right"><b style="color: green;"><?= format_cash($row["price"]) ?> </b>
                                            </td>
                                            <td class="text-center"><?= display_service_admin($row["status"]) ?> </td>
                                            <td><?= $row["create_date"] ?> </td>
                                            <td><?= $row["update_date"] ?> </td>
                                            <td><?= $row["reason"] ?> </td>
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
                                                Thực nhận: <strong style="color:blue;"><?= format_cash($totalPrice) ?>đ</strong>
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
                                        $total = $db->num_rows("SELECT * FROM `cards` WHERE $where");
                                        if ($total > $limit) {
                                            echo '<center>' . pagination("/cpanel/recharge/card?user_id=$userid&username=$username&pin=$pin&serial=$serial&status=$status&create_date=$createdate&limit=$limit&shortByDate=$shortByDate&", $from, $total, $limit) . '</center>';
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
</div>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>