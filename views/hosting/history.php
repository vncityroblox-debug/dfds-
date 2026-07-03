<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

// Kiểm tra đăng nhập
if (!@$user) {
    new Redirect('/login');
    exit;
}

$title = 'Lịch sử Hosting';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

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
$where = " `user_id` = '" . $data_user["id"] . "' ";
$shortByDate = "";
$domain = "";
$ip = "";
if (!empty($_GET["domain"])) {
    $domain = Anti_xss($_GET["domain"]);
    $where .= " AND `domain_name` LIKE \"%{$domain}%\" ";
}
if (!empty($_GET["ip"])) {
    $ip = Anti_xss($_GET["ip"]);
    $where .= " AND `ip` LIKE \"%{$ip}%\" ";
}
$createdate = '';
if (!empty($_GET['purchase_date'])) {
    $createdate = Anti_xss($_GET['purchase_date']);
    $create_date_1 = $createdate;
    $create_date_1 = explode(' to ', $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . ' 00:00:00', $create_date_1[1] . ' 23:59:59'];
        $where .= " AND `created_at` >= '" . $create_date_1[0] . "' AND `created_at` <= '" . $create_date_1[1] . "' ";
    }
}
$listDatatable = $db->get_list(" SELECT * FROM `purchased_hosting` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `purchased_hosting` WHERE " . $where . " ORDER BY id DESC ");
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css">
<section class="py-110">
    <div class="container">
        <?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php'); ?>
        <div class="row gy-4 dashboard-row-wrapper">
            <div class="col-md-12">
                 <form method="GET" action="" class="row">
                    <div class="col-lg col-md-4 col-6">
                        <input class="form-control shadow-none col-sm-2 mb-2" name="ip" value="<?php echo htmlspecialchars($ip); ?>" type="text" placeholder="IP">
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <select class="custom-style-select nice-select select-dropdown" id="select_status_vps" name="select_status_vps">
                            <option value="">Chọn trạng thái của Hosting</option>
                            <option value="on">Trạng thái đang bật</option>
                            <option value="off">Trạng thái đã tắt</option>
                        </select>
                    </div>                  
                        <div class="col-lg col-md-4 col-6">
                            <input type="text" class="form-control shadow-none mb-2 flatpickr-input" name="purchase_date" id="purchase_date" value="" placeholder="Chọn khoảng thời gian" readonly="readonly">
                        </div>
                        <div class="col-lg col-md-4 col-6">
                            <button class="shop-widget-btn mb-2"><i class="fas fa-search"></i><span>Tìm kiếm</span></button>
                        </div>
                        <div class="col-lg col-md-4 col-6">
                            <a href="/user/history/hosting" class="shop-widget-btn mb-2"><i class="far fa-trash-alt"></i><span>Bỏ lọc</span></a>
                        </div>
                    </form>
                    <div class="overflow-x-auto">
                        <div class="w-100">
                            <table class="w-100 dashboard-table text-nowrap table">
                                <thead class="pb-3">
                                    <tr>
                                        <th scope="col" class="py-2 px-4">
                                            Tên gói
                                        </th>
                                        <th scope="col" class="py-2 px-4">
                                            Giá
                                        </th>
                                        <th scope="col" class="py-2 px-4">
                                            Chu kỳ
                                        </th>
                                        <th scope="col" class="py-2 px-4">
                                            Tên miền
                                        </th>
                                        <th scope="col" class="py-2 px-4">
                                            IP
                                        </th>
                                        <th scope="col" class="py-2 px-4">
                                            Ngày hết hạn
                                        </th>
                                        <th scope="col" class="py-2 px-4">
                                            Tự gia hạn
                                        </th>
                                        <th scope="col" class="py-2 px-4">
                                            Chức năng
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($listDatatable as $order) :
                                    $info_package = json_decode($order['info_package'], true);
                                ?>
                                        <tr>
                                            <td><?= $info_package['name'] ?> <?= status_hosting($order["status"]) ?></td>
                                            <td><?= format_cash($order['price']); ?>đ</td>
                                            <td><?= $order["month"] ?> Tháng</td>
                                            <td><?= $order["domain_name"] ?></td>
                                            <td><?= $order["ip"] ?></td>
                                            <td><?= date('H:i:s d-m-Y', $order["end_date"]) ?></td>
                                            <td>
                                                <div class="status-toggle d-flex align-items-center text-center">
                                                    <input type="checkbox" id="toggle" class="hidden extend<?= $order["id"]; ?>" value="1" <?= $order["extend"] == 1 ? "checked=\"\"" : ""; ?>>
                                                    <label for="toggle" class="checktoggle"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <button onclick="location.href='/user/history/hosting/dashboard/<?= $order['id'] ?>';" class="btn btn-dark btn-sm"><i class="bx bx-cog mr-1"></i>Quản lý</button>
                                            </td>
                                        </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php require realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>