<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if ($db->site('design_status') != 1) {
    new Redirect('/');
    exit;
}

$title = 'Thuê dịch vụ thiết kế theo yêu cầu';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

$design_packages = $db->get_list("SELECT * FROM `design_config` WHERE `status` = '1'");

$title_base64        = $db->site('page_design_title');
$description_base64  = $db->site('page_design_description');
$title_decoded       = base64_decode($title_base64);
$description_decoded = base64_decode($description_base64);
?>



<section class="py-5 bg-light">
  <div class="container">
    <!-- Title & Description -->
    <div class="text-center mb-5">
     <br> <h2 class="text-28 fw-bold text-info mb-2">
        <?= $title_decoded ?>
      </h2>
      <p class="text-16 text-gray-700 mx-auto" style="max-width:800px; line-height:1.6;">
        <?= $description_decoded ?>
      </p>
    </div>

    <!-- Package Cards -->
    <div class="row g-4">
      <?php foreach ($design_packages as $package): ?>
        <?php
          $bonus_features = explode("\n", $package['bonus_features']);
        ?>
        <div class="col-lg-4 col-md-6 d-flex">
          <div class="card card-package w-100 h-100">
            <div class="card-header text-center">
              <h5 class="mb-0"><?= htmlspecialchars($package['name']) ?></h5>
            </div>
            <div class="card-body flex-fill d-flex flex-column">
              <!-- Giá -->
              <div class="text-center price fw-bold">
                <?= number_format($package['price'], 0, ',', '.') ?> ₫
              </div>
              <!-- Mô tả ngắn -->
              <div class="short-desc text-center">
                <?= htmlspecialchars($package['short_description']) ?>
              </div>
              <!-- Danh sách tính năng với dấu tích -->
              <ul class="list-group list-group-flush mb-4">
                <?php foreach ($bonus_features as $feat): ?>
                  <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($feat) ?>
                  </li>
                <?php endforeach; ?>
              </ul>
              <!-- Nút đăng ký -->
             <div class="mt-auto text-center">
  <a href="https://fb.com/kmstdev1907" target="_blank" class="btn btn-info btn-lg w-100">
    Đăng Ký Ngay
  </a>
</div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');
?>
