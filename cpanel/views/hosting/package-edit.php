<?php
$title = "Cập nhật gói hosting";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `hosting_packages` WHERE `id` = '" . $id . "'");
    if (!$row) {
        new Redirect("/cpanel/hosting/package");
    }
} else {
    new Redirect("/cpanel/hosting/package");
}
?>
<?php
if (isset($_POST['editPackage']) && $data_user['level'] == 'admin') {

    $package_name = Anti_xss($_POST['package_name']);
    $whm = $db->get_row("SELECT * FROM `whm_info` WHERE `id` = {$row['whm_id']}");
    if (!$whm) {
        die('<script type="text/javascript">if(!alert("Máy chủ không tồn tại")){window.history.back().location.reload();}</script>');
    }
    $packageData = array(
        'api.version' => 1,
        'name' => $package_name,
        'quota' => Anti_xss($_POST['disk_quota']),
        'bwlimit' => Anti_xss($_POST['bandwidth_limit']),
        'maxsub' => Anti_xss($_POST['max_subdomains']),
        'maxpark' => Anti_xss($_POST['max_parked_domains']),
        'maxaddon' => Anti_xss($_POST['max_addon_domains']),
        'language' => Anti_xss($_POST['language']),
        'cpmod' => Anti_xss($_POST['cpanel_module']),
        'cgi' => 1,
    );
    $result = updateHostingPackageViaAPI($whm['ip'], $whm['username'], $whm['password'], $packageData);
    if (isset($result['metadata']['result'])) {
        if ($result['metadata']['result'] == 1) {
            $isUpdate = $db->update("hosting_packages", array(
                'package_name' => $package_name,
                'language' => Anti_xss($_POST['language']),
                'cpanel_module' => Anti_xss($_POST['cpanel_module']),
                'price' => Anti_xss($_POST['price']),
                'description' => base64_encode($_POST['description']),
                'disk_quota' => Anti_xss($_POST['disk_quota']),
                'bandwidth_limit' => Anti_xss($_POST['bandwidth_limit']),
                'max_subdomains' => Anti_xss($_POST['max_subdomains']),
                'max_parked_domains' => Anti_xss($_POST['max_parked_domains']),
                'max_addon_domains' => Anti_xss($_POST['max_addon_domains']),
                'status' => Anti_xss($_POST['status']),
            ), " `id` = '" . $row['id'] . "' ");
            if ($isUpdate) {
                die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
            } else {
                die('<script type="text/javascript">if(!alert("Lưu thất bại !")){window.history.back().location.reload();}</script>');
            }
        } else {
            die('<script type="text/javascript">if(!alert("' . $result['metadata']['reason'] . '")){window.history.back().location.reload();}</script>');
        }
    } else {
        die('<script type="text/javascript">if(!alert("Không nhận được dữ liệu từ RSL!")){window.history.back().location.reload();}</script>');
    }
}

?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><a type="button" class="btn btn-dark btn-raised-shadow btn-wave btn-sm me-1" href="/cpanel/hosting/package"><i class="fa-solid fa-arrow-left"></i></a> Chỉnh sửa gói hosting [<?= $row['package_name'] ?>]</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA GÓI HOSTING - <?= $row['package_name'] ?>
                        </div>
                    </div>

                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Package Name:</label>
                                    <div class="form-group">
                                        <input name="package_name" type="text" value="<?= $row['package_name'] ?>" class="form-control" placeholder="package_name ..." required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Giá Bán:</label>
                                    <div class="form-group">
                                        <input name="price" type="number" value="<?= $row['price'] ?>" class="form-control" placeholder="Giá bán ..." required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Disk Quota (MB):</label>
                                    <div class="form-group">
                                        <input name="disk_quota" type="number" value="<?= $row['disk_quota'] ?>" class="form-control" placeholder="Disk Quota ..." required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Bandwidth Limit (MB):</label>
                                    <div class="form-group">
                                        <input name="bandwidth_limit" type="text" value="<?= $row['bandwidth_limit'] ?>" class="form-control" placeholder="Bandwidth Limit ..." required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Max Subdomains:</label>
                                    <div class="form-group">
                                        <input name="max_subdomains" type="text" value="<?= $row['max_subdomains'] ?>" class="form-control" placeholder="Max Subdomains ..." required>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Max Parked Domains:</label>
                                    <div class="form-group">
                                        <input name="max_parked_domains" type="text" value="<?= $row['max_parked_domains'] ?>" class="form-control" placeholder="Max Parked Domains ...">
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Max Addon Domains:</label>
                                    <div class="form-group">
                                        <input name="max_addon_domains" type="text" value="<?= $row['max_addon_domains'] ?>" class="form-control" placeholder="Max Addon Domains ..." required>
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
                                        <textarea name="description" class="form-control" cols="30" rows="3"><?= base64_decode($row['description']) ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xs-12 mb-2">
                                    <label>Status:</label>
                                    <div class="form-group">
                                        <select name="status" class="form-control" data-toggle="select2" required>
                                            <option value="1" <?= $row['status'] == '1' ? 'selected' : '' ?>>Hiển thị
                                            </option>
                                            <option value="0" <?= $row['status'] == '0' ? 'selected' : '' ?>>Ẩn</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="editPackage" class="btn btn-info"><i class="fas fa-plus"></i>
                                Lưu ngay</button>
                            <a href="/cpanel/hosting/package" type="button" class="btn btn-danger">Quay lại</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');
?>