<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_POST["submit"]) && $data_user['level'] == "admin") {
    $row = $db->get_row("SELECT * FROM `whm_info` WHERE `status` = 1");
    if (!$row) {
        die('<script type="text/javascript">if(!alert("Hiện chưa có máy chủ WHM!")){window.history.back().location.reload();}</script>');
    }
    $packageData = array(
        'api.version' => 1,
        'name' => Anti_xss($_POST['package_name']),
        'quota' => Anti_xss($_POST['disk_quota']),
        'bwlimit' => Anti_xss($_POST['bandwidth_limit']),
        'maxsub' => Anti_xss($_POST['max_subdomains']),
        'maxpark' => Anti_xss($_POST['max_parked_domains']),
        'maxaddon' => Anti_xss($_POST['max_addon_domains']),
        'language' => Anti_xss($_POST['language']),
        'cpmod' => Anti_xss($_POST['cpanel_module']),
        'cgi' => 1,
    );
    $result = createHostingPackageViaAPI($row['ip'], $row['username'], $row['password'], $packageData);
    if (isset($result['metadata']['result'])) {
        if ($result['metadata']['result'] == 1) {
            $isInsert = $db->insert("hosting_packages", [
                'whm_id' => $row['id'],
                'name' => Anti_xss($_POST['name']),
                'package_name' => Anti_xss($_POST['package_name']),
                'language' => Anti_xss($_POST['language']),
                'cpanel_module' => Anti_xss($_POST['cpanel_module']),
                'price' => Anti_xss($_POST['price']),
                'description' => base64_encode($_POST['description']),
                'disk_quota' => Anti_xss($_POST['disk_quota']),
                'bandwidth_limit' => Anti_xss($_POST['bandwidth_limit']),
                'max_subdomains' => Anti_xss($_POST['max_subdomains']),
                'max_parked_domains' => Anti_xss($_POST['max_parked_domains']),
                'max_addon_domains' => Anti_xss($_POST['max_addon_domains']),
                'created_at' => gettime(),
                'status' => Anti_xss($_POST['status']),
            ]);
            if ($isInsert) {
                insert_log($data_user['id'],"Thêm gói hosting (" . Anti_xss($_POST["name"]) . ").");
                exit("<script type=\"text/javascript\">if(!alert(\"Thêm thành công !\")){location.href = \"\";}</script>");
            }
            exit("<script type=\"text/javascript\">if(!alert(\"Thêm thất bại !\")){window.history.back().location.reload();}</script>");
        } else {
            die('<script type="text/javascript">if(!alert("' . $result['metadata']['reason'] . '")){window.history.back().location.reload();}</script>');
        }
    } else {
        die('<script type="text/javascript">if(!alert("Không nhận được dữ liệu từ RSL!")){window.history.back().location.reload();}</script>');
    }

   
}

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
$where = " `id` > 0 ";
$username = "";
$create_gettime = "";
$ip = "";
$shortByDate = "";

if (!empty($_GET["ip"])) {
    $ip = Anti_xss($_GET["ip"]);
    $where .= " AND `ip` LIKE \"%" . $ip . "%\" ";
}

if (!empty($_GET["username"])) {
    $username = Anti_xss($_GET["username"]);
    $where .= " AND `username` LIKE \"%" . $username . "%\" ";
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

$listDatatable = $db->get_list(" SELECT * FROM `hosting_packages` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `hosting_packages` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("whm-list&limit=" . $limit . "&shortByDate=" . $shortByDate . "&ip=" . $ip . "&username=" . $username . "&create_gettime=" . $create_gettime . "&"), $from, $totalDatatable, $limit);
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Danh sách gói</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" id="open-card-hide" class="btn btn-primary btn-sm mb-3">
                        <i class="fa-solid fa-plus"></i> Thêm gói
                    </button>
                </div>
            </div>
            <div class="col-xl-12" id="card-hide" style="display: none;">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Tên gói:</label>
                                    <div class="form-group">
                                        <input name="name" type="text" class="form-control" placeholder="HOSTING CHEAP 1" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Mã gói:</label>
                                    <div class="form-group">
                                        <input name="package_name" type="text" class="form-control" placeholder="cheap1" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Giá Bán:</label>
                                    <div class="form-group">
                                        <input name="price" type="number" class="form-control" placeholder="Giá bán" required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Dung lượng (MB):</label>
                                    <div class="form-group">
                                        <input name="disk_quota" type="number" class="form-control" placeholder="Dung lượng gói" required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Giới hạn băng thông (MB):</label>
                                    <div class="form-group">
                                        <input name="bandwidth_limit" type="text" value="1048576000" class="form-control" placeholder="Bandwidth Limit ..." required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Miền phụ tối đa:</label>
                                    <div class="form-group">
                                        <input name="max_subdomains" type="text" class="form-control" placeholder="Max Subdomains ..." value="unlimited" required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Tên miền trỏ hướng tối đa:</label>
                                    <div class="form-group">
                                        <input name="max_parked_domains" type="text" class="form-control" placeholder="Max Parked Domains ..." value="unlimited" required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Tên miền bổ sung tối đa:</label>
                                    <div class="form-group">
                                        <input name="max_addon_domains" type="text" class="form-control" placeholder="Max Addon Domains ..." value="unlimited" required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Language:</label>
                                    <div class="form-group">
                                        <select name="language" class="form-control show-tick select2bs4" tabindex="-98">
                                            <option value="vi" selected="selected">Tiếng Việt</option>
                                            <option value="ar">Tiếng A-rập (العربية)</option>
                                            <option value="bg">Tiếng Bun-ga-ri (български)</option>
                                            <option value="cs">Tiếng Séc (čeština)</option>
                                            <option value="da">Tiếng Đan Mạch (dansk)</option>
                                            <option value="de">Tiếng Đức (Deutsch)</option>
                                            <option value="el">Tiếng Hy Lạp (Ελληνικά)</option>
                                            <option value="en">Tiếng Anh (English)</option>
                                            <option value="es">Tiếng Tây Ban Nha (español)</option>
                                            <option value="es_419">Tiếng Tây Ban Nha (Mỹ La tinh) (español
                                                latinoamericano)
                                            </option>
                                            <option value="es_es">Tiếng Tây Ban Nha (I-bê-ri) (español de España)
                                            </option>
                                            <option value="fi">Tiếng Phần Lan (suomi)</option>
                                            <option value="fil">Tiếng Philipin (Filipino)</option>
                                            <option value="fr">Tiếng Pháp (français)</option>
                                            <option value="he">Tiếng Hê-brơ (עברית)</option>
                                            <option value="hu">Tiếng Hung-ga-ri (magyar)</option>
                                            <option value="i_cpanel_snowmen">☃ cPanel Snowmen ☃ - i_cpanel_snowmen
                                            </option>
                                            <option value="i_en">i_en</option>
                                            <option value="id">Tiếng In-đô-nê-xia (Bahasa Indonesia)</option>
                                            <option value="it">Tiếng Ý (italiano)</option>
                                            <option value="ja">Tiếng Nhật (日本語)</option>
                                            <option value="ko">Tiếng Hàn Quốc (한국어)</option>
                                            <option value="ms">Tiếng Ma-lay-xi-a (Bahasa Melayu)</option>
                                            <option value="nb">Tiếng Na Uy (Bokmål) (norsk bokmål)</option>
                                            <option value="nl">Tiếng Hà Lan (Nederlands)</option>
                                            <option value="no">Tiếng Na Uy (Norwegian)</option>
                                            <option value="pl">Tiếng Ba Lan (polski)</option>
                                            <option value="pt">Tiếng Bồ Đào Nha (português)</option>
                                            <option value="pt_br">Tiếng Bồ Đào Nha (Braxin) (português do Brasil)
                                            </option>
                                            <option value="ro">Tiếng Ru-ma-ni (română)</option>
                                            <option value="ru">Tiếng Nga (русский)</option>
                                            <option value="sl">Tiếng Xlô-ven (slovenščina)</option>
                                            <option value="sv">Tiếng Thụy Điển (svenska)</option>
                                            <option value="th">Tiếng Thái (ไทย)</option>
                                            <option value="tr">Tiếng Thổ Nhĩ Kỳ (Türkçe)</option>
                                            <option value="uk">Tiếng U-crai-na (українська)</option>
                                            <option value="zh">Tiếng Trung Quốc (中文)</option>
                                            <option value="zh_cn">Tiếng Trung Quốc (Trung Quốc) (中文（中国）)</option>
                                            <option value="zh_tw">Tiếng Trung Quốc (Đài Loan) (中文（台湾）)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>cPanel Module:</label>
                                    <div class="form-group">
                                        <select name="cpanel_module" class="form-control show-tick select2bs4" tabindex="-98">
                                            <option value="jupiter" selected="selected">Jupiter</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Description:</label>
                                    <div class="form-group">
                                        <textarea name="description" class="form-control" cols="30" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Status:</label>
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="1">Hiển thị</option>
                                            <option value="0">Tạm ngưng</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-plus me-1"></i>Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH GÓI
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?php echo $ip; ?>" name="ip" placeholder="IP máy chủ">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?php echo $username; ?>" name="username" placeholder="Tài khoản">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control form-control-sm" id="daterange" value="<?php echo $create_gettime; ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/hosting/package"><i class="fa fa-trash"></i>
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
                                        <th>Tên gói</th>
                                        <th>Mã gói</th>
                                        <th>Dung lượng</th>
                                        <th>Giá bán</th>
                                        <th>Thống kê</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $package) { ?>
                                        <tr onchange="updateForm('<?php echo $package['id']; ?>')">
                                            <td><?= $package['name'] ?></td>
                                            <td><?= $package["package_name"]; ?></td>
                                            <td><?= $package["disk_quota"]; ?></td>
                                            <td><b style="color:red"><?= format_cash($package["price"]); ?></b></td>
                                            <td><?= $db->num_rows("SELECT * FROM `purchased_hosting` WHERE `package_id` = '{$package['id']}'") ?? 0; ?></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?php echo $package["id"]; ?>" value="1" <?php echo $package["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <a type="button" href="/cpanel/hosting/package/edit/<?= $package["id"] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                    <i class="fa fa-pencil-alt"></i> Edit
                                                </a>
                                                <a type="button" onclick="RemoveRow('<?php echo $package['id']; ?>')" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?php echo $limit; ?> of <?php echo format_cash($totalDatatable); ?> Results</p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3">
                                <?php echo $limit < $totalDatatable ? $urlDatatable : ""; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateForm(id) {
        $.ajax({
            url: "/model/admin/update",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'updateTablePackage',
                id: id,
                status: $('#status' + id + ':checked').val()
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function() {
                alert(html(result));
                location.reload();
            }
        });
    }
</script>

<script type="text/javascript">
    function postRemove(id) {
        $.ajax({
            url: "/model/admin/delete",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'removePackage',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: result.msg
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: result.msg
                    })
                }
            }
        });
    }

   

    function RemoveRow(id) {

        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn có đồng ý xóa mục ID " + id + " này không ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                postRemove(id);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var button = document.getElementById('open-card-hide');
        var card = document.getElementById('card-hide');

        // Thêm sự kiện click cho nút button
        button.addEventListener('click', function() {
            // Kiểm tra nếu card đang hiển thị thì ẩn đi, ngược lại hiển thị
            if (card.style.display === 'none' || card.style.display === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>