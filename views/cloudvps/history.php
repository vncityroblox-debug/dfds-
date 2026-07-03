<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

// Kiểm tra đăng nhập
if (!@$user) {
    new Redirect('/login');
    exit;
}

$title = 'Lịch Sử Mua VPS';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

$sotin1trang = 5;
if (isset($_GET['page'])) {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}
$from = ($page - 1) * $sotin1trang;

if (isset($_GET['pages'])) {
    $pages = Anti_xss(intval($_GET['pages']));
} else {
    $pages = 1;
}

$froms = ($pages - 1) * $sotin1trang;

$where = ' `id` > 0 AND `user_id` ="' . $data_user['id'] . '"';

$select_status_vps  = '';
$ip = '';

if (!empty($_GET['select_status_vps'])) {
    $select_status_vps = Anti_xss($_GET['select_status_vps']);
    $listCloudVpsUser = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE `user_id` = '" . $data_user['id'] . "' AND `site` IN ('VNCLOUD', 'H2CLOUD') ORDER BY `id` DESC");
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
    } else {
        $where .= " AND `id` = 0";
    }
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
if (!empty($_GET['ip'])) {
    $ip = Anti_xss($_GET['ip']);
    $listCloudVpsUserIp = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE `user_id` = '" . $data_user['id'] . "' ORDER BY `id` DESC");
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
    } else {
        $where .= " AND `id` = 0";
    }
}

$listCloudVps = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE $where ORDER BY `id` DESC");
$groupedVps = [];
foreach ($listCloudVps as $data) {
    $site = $data['site'] ?? 'H2CLOUD'; 
    $groupedVps[$site][] = (int)$data['vps_id'];
}

foreach ($groupedVps as $site => $vpsIds) {
    $jsonString = json_encode($vpsIds);
    if ($site === 'H2CLOUD') {
        $result = infoListVpsH2($jsonString);
        if (isset($result['error']) && $result['error'] == 0) {
            foreach ($result['list-service'] as $infovps) {
                $db->update("tbl_purchased_cloudvps", [
                    'info' => encryptAES(json_encode($infovps)),
                    'status' => $infovps['vps-status']
                ], " `vps_id` = '" . $infovps['vps-id'] . "' ");
            }
        }
    } else {
        $result = infoListVps($jsonString);
        if (isset($result['error']) && $result['error'] == 0) {
            foreach ($result['data'] as $infovps) {
                $db->update("tbl_purchased_cloudvps", [
                    'info' => encryptAES(json_encode($infovps)),
                    'status' => $infovps['vps-status'],
                ], " `vps_id` = '" . $infovps['vps-id'] . "' ");
            }
        }
    }
}


$listCloud = $db->get_list("SELECT * FROM `tbl_purchased_cloudvps` WHERE $where ORDER BY `id` DESC LIMIT $from,$sotin1trang ");


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css">
<section class="py-110">
    <div class="container">
        <?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php'); ?>
        <div class="row gy-4 dashboard-row-wrapper">
            <div class="col-md-12">
                <!-- Form lọc -->
                <form method="GET" action="" class="row">
                    <div class="col-lg col-md-4 col-6">
                        <input class="form-control shadow-none col-sm-2 mb-2" name="ip" value="<?= $ip ?>" type="text" placeholder="IP">
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <select class="custom-style-select nice-select select-dropdown" id="select_status_vps" name="select_status_vps">
                            <option value="" selected="selected">Chọn trạng thái của VPS</option>
                            <option value="on">Trạng thái đang bật</option>
                            <option value="off">Trạng thái đã tắt</option>
                            <option value="progressing">Trạng thái đang tạo</option>
                            <option value="waiting">Trạng thái đang chờ tạo</option>
                            <option value="rebuild">Trạng thái đang cài lại</option>
                            <option value="expire">Trạng thái hết hạn</option>
                            <option value="suspend">Trạng thái đã khóa</option>
                            <option value="delete_vps">Trạng thái đã xóa</option>
                            <option value="cancel">Trạng thái đã hủy</option>
                        </select>
                    </div>                   

                    <div class="col-lg col-md-4 col-6">
                        <input type="text" class="form-control shadow-none mb-2 flatpickr-input" name="purchase_date" id="purchase_date" value="<?= $createdate ?>" placeholder="Chọn khoảng thời gian" readonly="readonly">
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <button class="shop-widget-btn mb-2"><i class="fas fa-search"></i><span>Tìm kiếm</span></button>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="/user/history/vps" class="shop-widget-btn mb-2"><i class="far fa-trash-alt"></i><span>Bỏ lọc</span></a>
                    </div>
                </form>

                <!-- Bảng hiển thị dữ liệu -->
                <div class="overflow-x-auto">
                    <div class="w-100">
                        <table class="w-100 dashboard-table text-nowrap table">
                            <thead class="pb-3">
                                <tr>
                                    <th scope="col" class="py-2 px-4">Tên gói</th>
                                    <th scope="col" class="py-2 px-4">Giá</th>
                                    <th scope="col" class="py-2 px-4">Chu kỳ</th>
                                    <th scope="col" class="py-2 px-4">IP</th>
                                    <th scope="col" class="py-2 px-4">Ngày hết hạn</th>
                                    <th scope="col" class="py-2 px-4">Trạng thái</th>
                                    <th scope="col" class="py-2 px-4">Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($listCloud as $row) :
                                    $detail = json_decode(decryptAES($row['info']), true);
                                ?>
                                    <tr onchange="updateForm('<?= $row['id']; ?>')">
                                        <td class="py-2 px-4">
                                            <div class="flex items-center">
                                                <div class="">
                                                    <p><?= $detail['text-config'] ?></p>
                                                </div>
                                            </div>
                                            
                                        </td>
                                        <td class="py-2 px-4">
                                            <p><?= format_cash($row['price']) ?>đ</p>
                                        </td>
                                        <td class="py-2 px-4">
                                            <p><?= $detail['billing-cycle'] ?></p>
                                        </td>
                                       
                                        <td class="py-2 px-4">
                                            <p><?= $detail['ip'] ?></p>
                                        </td>
                                        <td class="py-2 px-4">
                                            <p><?= $detail['next_due_date'] ?></p>
                                        </td>
                                        <td class="py-2 px-4">
                                            <p><?= status_order_cloud($detail['vps-status']) ?></p>
                                        </td>
                                        <td class="py-2 px-4">
                                            <button onclick="location.href='/user/history/vps/dashboard/<?= $row['id'] ?>';" class="btn btn-dark btn-sm"><i class="bx bx-cog mr-1"></i>Quản lý</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-end">
                    <?php
                    $total = $db->num_rows("SELECT * FROM `tbl_purchased_cloudvps` WHERE $where ORDER BY `id` DESC ");
                    if ($total > $sotin1trang) {
                        echo  pagination_client("/user/history/vps?ip=" . $ip . "&purchase_date=" . $createdate . "&", $from, $total, $sotin1trang);
                    } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php require realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>