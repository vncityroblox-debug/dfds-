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
$content = '';
$limit = '';
$userid = '';

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
    $content = Anti_xss($_GET['content']);
    $where .= ' AND `content` LIKE "%' . $content . '%" ';
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
        $where .= " AND `time` >= '" . $create_date_1[0] . "' AND `time` <= '" . $create_date_1[1] . "' ";
    }
}

$listLogs = $db->get_list("SELECT * FROM `log_balance` WHERE $where $order_by LIMIT $from,$sotin1trang ");
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Biến động số dư</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Lịch sử</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Biến động số dư</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            Biến động số dư
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
                                    <input class="form-control form-control-sm" value="<?= $content ?>" name="content" placeholder="Lý do">
                                </div>
                              
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="createdate" class="form-control form-control-sm" id="daterange" value="<?= $createdate ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Search </button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/transactions"><i class="fa fa-trash"></i>
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
                                        <th>Số dư trước</th>
                                        <th>Số dư thay đổi</th>
                                        <th>Số dư hiện tại</th>
                                        <th>Thời gian</th>
                                        <th>Lý do</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listLogs as $row) : ?>
                                        <tr>
                                            <td><a class="text-primary" href="/cpanel/user/edit/<?= $row['user_id'] ?>"><?= getRowUser($row['user_id'], 'username') ?> [ID <?= $row['user_id'] ?>]</a>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge bg-success-gradient"><?= format_cash($row['money_before']) ?>đ</span>
                                            </td>
                                            <td class="text-right"><span class="badge bg-danger-gradient"><?= format_cash($row['money_change']) ?>đ</span></td>
                                            <td class="text-right"><span class="badge bg-primary-gradient"><?= format_cash($row['money_after']) ?>đ</span>
                                            </td>
                                            <td><span class="badge bg-light text-dark"><?= $row['time'] ?></span></td>
                                            <td><?= $row['content'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">

                            <div class="col-sm-12 col-md-12 mb-3">
                                <div class="pagination-style-1">
                                    <div class="d-flex justify-content-center">
                                        <?php
                                        $total = $db->num_rows("SELECT * FROM `log_balance` WHERE $where");
                                        if ($total > $sotin1trang) {
                                            echo '<center>' . pagination("/cpanel/transactions?user_id=$userid&username=$username&content=$content&createdate=$createdate&limit=$limit&shortByDate=&", $from, $total, $sotin1trang) . '</center>';
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