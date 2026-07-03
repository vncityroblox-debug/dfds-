<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if ($db->site('vps_status') != 1) {
    new Redirect('/');
    exit;
}

$title = 'Thuê dịch vụ cloud vps';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

$sites = ['VNCLOUD', 'CLOUDNEST', 'H2CLOUD'];
$site_names = [
    'VNCLOUD' => 'CLOUD SERVER GOLD',
    'CLOUDNEST' => 'CLOUD SERVER PLATINUM',
    'H2CLOUD' => 'CLOUD GIÁ RẺ'
];
$ck = @$user ? $data_user['chietkhau'] : 0;

$available_sites = [];
foreach ($sites as $site) {
    $vps_packages = $db->get_list("SELECT * FROM `tbl_cloudvps` WHERE `status` = 1 AND `site` = '$site' ORDER BY `id` ASC");
    if (!empty($vps_packages)) {
        $available_sites[$site] = [
            'name' => $site_names[$site],
            'packages' => $vps_packages
        ];
    }
}
?>
<main>
  <div class="w-breadcrumb-area">
    <div class="breadcrumb-img">
      <div class="breadcrumb-left">
        <img src="/assets/images/banner-bg-03.png" alt="img" class="img-fluid" />
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav aria-label="breadcrumb" class="page-breadcrumb py-3">
            <ol class="breadcrumb bg-transparent p-0 mb-2">
              <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
              <li class="breadcrumb-item active" aria-current="page">Cloud VPS</li>
            </ol>
          </nav>
          <h2 class="breadcrumb-title fw-bold mb-4">Các gói dịch vụ</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="tab-content" id="vpsTabContent">
    <?php $i = 0; foreach ($available_sites as $site => $info): ?>
      <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="<?= $site ?>" role="tabpanel" aria-labelledby="<?= $site ?>-tab">
        <section class="py-5">
          <div class="container">
            <div class="row justify-content-center gx-4 gy-4">
              <?php foreach ($info['packages'] as $package):
                $price = json_decode($package['price'], true);
                $detail = json_decode($package['detail'], true);
                $finalPrice = number_format($price['monthly']['amount'] - $price['monthly']['amount'] * $ck / 100, 0, '.', '.');
              ?>
                <div class="col-lg-3 col-md-6">
                  <div class="flat-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                      <h3 class="package-title"><?= htmlspecialchars($package['name']); ?></h3>
                      <div class="price-section">
                        <span class="price-number"><?= $finalPrice ?></span><span class="price-currency">₫</span>
                        <small class="price-duration">/ Tháng</small>
                      </div>
                      <ul class="features-list ps-0">
                        <li><i class="bx bx-check-circle"></i> <?= htmlspecialchars($detail['cpu']); ?> Core CPU Platinum 8171M</li>
                        <li><i class="bx bx-check-circle"></i> <?= htmlspecialchars($detail['ram']); ?> GB RAM DDR4 2666MHz</li>
                        <li><i class="bx bx-check-circle"></i> <?= htmlspecialchars($detail['disk']); ?> GB NVMe U.2 – vSAN tốc độ cao</li>
                        <li><i class="bx bx-check-circle"></i> IP RIÊNG: <?= htmlspecialchars($detail['ip']); ?></li>
                        <li><i class="bx bx-check-circle"></i> Băng thông: <?= htmlspecialchars($detail['bandwidth']); ?></li>
                        <li><i class="bx bx-check-circle"></i> Hệ điều hành: <?= htmlspecialchars($detail['os']); ?></li>
                      </ul>
                    </div>
                    <a href="/cloudvps/<?= htmlspecialchars($package['id']); ?>" class="btn-flat btn-block mt-4 text-center">
                      Chọn gói <i class="feather-arrow-right"></i>
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      </div>
    <?php $i++; endforeach; ?>
  </div>
</main>

<style>
  /* Card nền trắng, bo góc, viền pastel */
  .flat-card {
    background: #fff;
    border: 1.5px solid #cce4f7;
    border-radius: 12px;
    box-shadow: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    padding: 18px 16px;
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .flat-card:hover {
    border-color: #1e88e5;
    box-shadow: 0 6px 12px rgba(30, 136, 229, 0.2);
  }

  /* Tiêu đề gói */
  .package-title {
    margin-bottom: 0.4rem;
    font-weight: 700;
    font-size: 1.5rem;
    color: #0d47a1;
  }

  /* Giá tiền */
 /* Giá tiền */
.price-section {
    font-weight: 900;
    font-size: 2.4rem; /* tăng từ 1.9rem lên 2.4rem */
    color: #0d47a1;
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.price-number {
    font-size: 2.4rem;
}

.price-currency {
    font-size: 1.2rem;
    margin-left: 2px;
    color: #0d47a1;
}

.price-duration {
    font-size: 0.95rem;
    color: #6c757d;
    margin-left: 6px;
}

  /* Danh sách tính năng */
  .features-list {
    list-style: none;
    color: #2e3a59;
    font-size: 0.95rem;
    padding-left: 0;
    flex-grow: 1;
    margin-bottom: 0; /* bỏ margin cho gọn */
  }

  .features-list li {
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 7px;
    line-height: 1.25;
  }

  .features-list li:last-child {
    margin-bottom: 0;
  }

  .features-list li i.bx-check-circle {
    color: #1e88e5;
    font-size: 1.1rem;
  }

  /* Nút chọn gói */
  .btn-flat {
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
    transition: background 0.3s ease;
    width: 100%;
    cursor: pointer;
    text-decoration: none;
    margin-top: 1rem;
  }

  .btn-flat:hover {
    background: linear-gradient(90deg, #1565c0, #1976d2);
    text-decoration: none;
    color: white !important;
  }
</style>



<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?> 