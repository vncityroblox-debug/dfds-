<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if ($db->site('hosting_status') != 1) {
    new Redirect('/');
    exit;
}

$title = 'Thuê dịch vụ hosting';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>

<main>
    <div class="w-breadcrumb-area">
        <div class="breadcrumb-img">
            <div class="breadcrumb-left">
                <img src="/assets/images/banner-bg-03.png" alt="img">
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Hosting</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                        Các gói dịch vụ
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <section class="py-110">
        <div class="container">
            <div class="row">
                <?php foreach ($db->get_list("SELECT * FROM `hosting_packages` WHERE `status` = 1 AND `whm_id` = '" . $db->get_row("SELECT * FROM `whm_info` WHERE `status` = 1")['id'] . "' ORDER BY `id` ASC") as $host) :
                $ck = @$user ? $data_user['chietkhau'] : 0; ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="price-card rounded-3 border border-info-subtle shadow-sm p-4 d-flex flex-column justify-content-between h-100">
                            <div>
                                <div class="plan-type mb-2">
                                    <h3 class="fw-bold text-primary-emphasis mb-1 text-uppercase" style="font-size: 1.4rem;"><?= strtoupper($host['name']) ?></h3>
                                </div>
                                <div class="amt-item mb-3 d-flex align-items-end gap-2">
                                    <h2 class="text-primary-emphasis m-0" style="font-size: 2.2rem; font-weight: 800;"><?= format_cash($host['price']) ?></h2>
                                    <p class="text-muted mb-1">/ Tháng</p>
                                </div>
                            </div>
                            <div class="price-features mt-2 flex-grow-1">
                                <h6 class="text-info-emphasis fw-semibold mb-2">Bao gồm</h6>
                                <ul class="text-sm mb-0 ps-3" style="line-height: 1.45;">
                                    <li><i class="bx bx-check-double text-info"></i> Dung Lượng: <?= format_cash($host['disk_quota']) ?> MB</li>
                                    <li><i class="bx bx-check-double text-info"></i> Băng Thông: Không giới hạn</li>
                                    <li><i class="bx bx-check-double text-info"></i> Miễn Phí: Chứng Chỉ SSL</li>
                                    <li><i class="bx bx-check-double text-info"></i> Miền Khác: không giới hạn</li>
                                    <li><i class="bx bx-check-double text-info"></i> Miền Bí Danh: không giới hạn</li>
                                    <li><i class="bx bx-check-double text-info"></i> Các Thông Số Khác: không giới hạn</li>
                                    <li><i class="bx bx-check-double text-info"></i> Vị Trí Máy Chủ: Việt Nam</li>
                                    <li><i class="bx bx-check-double text-info"></i> Backup: Không có</li>
                                    <?= base64_decode($host['description']) ?>
                                </ul>
                            </div>
                            <div class="price-btn mt-4">
                                <a href="/hosting/<?= htmlspecialchars($host['id']); ?>" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2">
                                    Chọn gói <i class="feather-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<style>
/* Card hosting giống cloud vps */
.price-card {
    background: #fff;
    border: 1.5px solid #cce4f7;
    border-radius: 12px;
    padding: 20px 18px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.price-card:hover {
    border-color: #1e88e5;
    box-shadow: 0 6px 12px rgba(30,136,229,0.15);
}

/* Tiêu đề gói */
.plan-type h3 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #0d47a1;
}

/* Giá tiền */
.amt-item h2 {
    font-size: 2rem;
    font-weight: 800;
    color: #0d47a1;
    margin: 0;
}
.amt-item p {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 4px 0 0 0;
}

/* Danh sách tính năng */
.price-features h6 {
    font-weight: 600;
    margin: 1.2rem 0 0.6rem;
    color: #1e88e5;
}
.price-features ul {
    list-style: none;
    padding-left: 0;
    font-size: 0.95rem;
    color: #2e3a59;
    margin: 0;
}
.price-features ul li {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    line-height: 1.3;
}
.price-features ul li span i {
    color: #1e88e5;
    font-size: 1.1rem;
}

/* Nút chọn gói */
.price-btn .btn-primary {
    background: linear-gradient(90deg, #1e88e5, #42a5f5);
    border: none;
    color: white !important;
    font-weight: 600;
    padding: 11px 0;
    border-radius: 30px;
    text-transform: uppercase;
    font-size: 1.05rem;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 7px;
    width: 100%;
    transition: background 0.3s ease;
    text-decoration: none;
    margin-top: 1.2rem;
}
.price-btn .btn-primary:hover {
    background: linear-gradient(90deg, #1565c0, #1976d2);
    color: white !important;
    text-decoration: none;
}
</style>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>
