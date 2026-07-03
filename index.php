<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
$title = $db->site('title');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

?>
    <?php $vps_count = $db->num_rows("SELECT * FROM `list_vps`"); ?>

<div class="breadcrumb-bar breadcrumb-bar-info">
    <div class="breadcrumb-img">
        <div class="breadcrumb-left">
            <img src="/assets/images/banner-bg-03.png" alt="img">
        </div>
    </div>
    <div class="container">
        <div class="row mt-3">
            <div class="col-md-12 col-12">

                <div class="slide-title-wrap">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="slider-title">
                                <h2>Các danh mục phổ biến của chúng tôi</h2>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="owl-nav service-nav nav-control nav-top"></div>
                        </div>
                    </div>
                </div>

                <div class="service-sliders owl-carousel">
                    <?php if ($db->site('cron_status') == 1): ?>
                        <div class="service-box">
                            <div class="service-info">
                                <span class="service-icon">
                                    <img src="/assets/images/cron_jobs.png" alt="icon">
                                </span>
                                <div class="servive-name">
                                    <h5><a href="/cronjob">Dịch Vụ Cron</a></h5>
                                    <p>1 Dịch vụ</p>
                                </div>
                            </div>
                            <a href="/cronjob"><i class="feather-arrow-up-right"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if ($db->site('vps_status') == 1): ?>
                        <div class="service-box">
                            <div class="service-info">
                                <span class="service-icon">
                                    <img src="/assets/images/vps.svg" alt="icon">
                                </span>
                                <div class="servive-name">
                                    <h5><a href="/cloudvps">Cloud VPS</a></h5>
                                    <p>1 Dịch vụ</p>
                                </div>
                            </div>
                            <a href="/cloudvps"><i class="feather-arrow-up-right"></i></a>
                        </div>
                    <?php endif; ?>
                         <?php if ($db->site('hosting_status') == 1): ?>
                        <div class="service-box">
                            <div class="service-info">
                                <span class="service-icon">
                                    <img src="/assets/images/vps.svg" alt="icon">
                                </span>
                                <div class="servive-name">
                                    <h5><a href="/hosting">Hosting</a></h5>
                                    <p>1 Dịch vụ</p>
                                </div>
                            </div>
                            <a href="/hosting"><i class="feather-arrow-up-right"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($db->get_list("SELECT * FROM `categories` WHERE `status` = 1 ORDER BY `category_id` ASC") as $category): ?>
                        <div class="service-box">
                            <div class="service-info">
                                <span class="service-icon">
                                    <img src="<?= $category['thumbnail'] ?>" alt="icon" style="width:40px !important">
                                </span>
                                <div class="servive-name">
                                    <h5><a href="/services?category=<?= $category['category_id'] ?>"><?= $category['name'] ?></a></h5>
                                    <p><?= categories_in_server($category['category_id']) ?> Dịch vụ</p>
                                </div>
                            </div>
                            <a href="/services?category=<?= $category['category_id'] ?>"><i class="feather-arrow-up-right"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</div>


   
            <section class="services-filter py-5">
        <div class="container">
            <div class="row mb-40 justify-content-between align-items-end">
                <div class="col-auto">
                    <h2 class="fw-bold section-title">Sản phẩm nổi bật</h2>
                    <p class="section-desc">
                        Dịch vụ tốt nhất cho công việc của bạn
                    </p>
                </div>
                <div class="col-auto mt-30 mt-xl-0">
                    <div class="filters-btns d-flex flex-wrap align-items-center gap-3">
                        <button class="service-filter-btn active" data-filter=".category1">
                            Tất cả
                        </button>
                        <button class="service-filter-btn" data-filter=".category2">
                            Sản phẩm mới
                        </button>
                        <button class="service-filter-btn" data-filter=".category3">
                            Bán chạy
                        </button>
                        <button class="service-filter-btn" data-filter=".category4">
                            Giá rẻ
                        </button>
                        <button class="service-filter-btn" data-filter=".category5">
                            Miễn phí
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="loading-indicator" class="loading-indicator">
                    <div class="spinner"></div>
                </div>
                <?php
foreach ($db->get_list("SELECT * FROM `products` WHERE `status` = 1 OR `is_pinned` = 1 ORDER BY `is_pinned` DESC, `ditme` ASC") as $product):
?>

                                    <article class="col-xl-3 col-lg-4 col-md-6 mb-4 grid-item category1 category2 category3 category4<?php echo (isset($product['is_new']) && $product['is_new'] == '1') ? ' category2 ' : ''; ?><?php echo (isset($product['sales_count']) && $product['sales_count'] >= 20) ? ' category3 ' : ''; ?><?php echo (isset($product['is_cheap']) && $product['is_cheap'] == '1') ? ' category4 ' : ''; ?><?php echo (isset($product['is_free']) && $product['is_free'] == '1') ? ' category5 ' : ''; ?>">

                       
                        <div class="gigs-grid">
                            <div class="gigs-img">
                                <div class="">
                                    <a href="/product/<?=$product['slug']?>"><img src="/assets/images/lazyload.gif" data-src="<?=$product['thumbnail']?>" class="lazyLoad w-100"
                                            height="180" alt="<?=$product['title']?>"></a>
                                </div>
                                                                    <?php if ($product['is_pinned'] == 1): ?>
    <div class="card-overlay-badge">
        <a href="/product/<?=$product['slug']?>">
            <span class="badge bg-danger"><i class="fa-solid fa-meteor"></i>Ghim</span>
        </a>
    </div>
<?php endif; ?>

                                                                <div class="fav-selection">
    <a href="javascript:void(0);" class="fav-icon <?= isset($data_user['id']) && $db->get_row("SELECT 1 FROM favorites WHERE user_id = '{$data_user['id']}' AND product_id = '{$product['product_id']}'") ? 'favourite' : ''; ?>" 
       data-product-id="<?= $product['product_id'] ?>">
        <i class="fa-<?= isset($data_user['id']) && $db->get_row("SELECT 1 FROM favorites WHERE user_id = '{$data_user['id']}' AND product_id = '{$product['product_id']}'") ? 'solid' : 'regular'; ?> fa-heart"></i>
    </a>
</div>
                                <div class="user-thumb">
                                    <a href="/seller/<?= $product['seller_id'] ?>">
                                        <img src="<?= !empty(getRowUser($product['seller_id'], 'profile_picture')) ? getRowUser($product['seller_id'], 'profile_picture') : '/assets/images/avt.png'; ?>" alt="User">
                                    </a>
                                </div>
                            </div>
                            <div class="gigs-content">
                                <div class="gigs-info">
                                    <a href="/services?name=&category=<?=$product['category_id']?>" class="badge bg-primary-light"><?=getsvcategories($product['category_id'])?></a>
                                    <div class="star-rate">
                                        <span><i class="fa-solid fa-star"></i><span id="averageRating" class="me-1">0</span> (<?= $db->get_row("SELECT COUNT(id) as total FROM `reviews` WHERE `product_id` = '{$product['product_id']}'")['total'] ?? 0 ?> Reviews)</span>
                                    </div>
                                </div>
                                <div class="gigs-title">
                                    <h3>
                                        <a href="/product/<?=$product['slug']?>" class="truncate-2-lines">
                                            <?=$product['title']?>                                        </a>
                                    </h3>
                                </div>

                                <div class="gigs-card-footer">
                                    <div class="gigs-share">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?=$base_url;?>/product/<?=$product['slug']?>">
                                            <i class="fa fa-share-alt"></i>
                                        </a>
                                        <span class="badge"><?=timeAgo($product['time_session'])?></span>
                                    </div>
                                    <h5><?=format_cash($product['sale_price']) ?>đ</h5>
                                </div>
                            </div>
                        </div>
                    </article>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const averageRating = <?= $db->get_row("SELECT SUM(rating) as total FROM `reviews` WHERE `product_id` = '{$product['product_id']}'")['total'] ?? 0 ?>;
                            displayStars(averageRating);
                        });
                    </script>
                            <?php endforeach;?>
        </div>
        </div>
    </section>
</main>
            
<!-- Main End -->
<div class="modal new-modal fade" id="modal_notification" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông báo</h5>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body service-modal">
                <p style="text-align:center"><?=$db->site('popup_home');?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="dontShowAgainBtn">Không hiển thị lại trong 2 giờ</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById('modal_notification');
        var dontShowAgainBtn = document.getElementById('dontShowAgainBtn');
        var modalClosedTime = localStorage.getItem('modalClosedTime');
        if (!modalClosedTime || (Date.now() - parseInt(modalClosedTime) > 2 * 60 * 60 * 1000)) {
            var bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
        dontShowAgainBtn.addEventListener('click', function() {
            localStorage.setItem('modalClosedTime', Date.now());
            var bootstrapModal = bootstrap.Modal.getInstance(modal);
            bootstrapModal.hide();
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        const filterButtons = document.querySelectorAll('.service-filter-btn');
        const gridItems = document.querySelectorAll('.grid-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const filterValue = this.getAttribute('data-filter');
                gridItems.forEach(item => {
                    if (filterValue === '.category1' || item.classList.contains(filterValue.replace('.', ''))) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>