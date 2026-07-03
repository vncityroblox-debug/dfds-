<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (isset($_GET['id'])) {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `products` WHERE `seller_id` = '{$id}'");
    $rows = $db->get_row("SELECT * FROM `users` WHERE `id` = '{$id}' AND `ctv` = 1");
    if ($rows) {
    } else {
        new Redirect('/');
    }
}
$title = 'Cửa hàng - ' . $db->site('title');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>
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
                            <li class="breadcrumb-item" aria-current="page">Chi tiết người bán</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                        Chi tiết người bán
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <section class="py-110">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <aside
                        class="freelancer-details-sidebar d-flex flex-column gap-4">
                        <div
                            class="freelancer-sidebar-card p-4 rounded-4 bg-white position-relative shadow-sm">
                            <div
                                class="freelancer-sidebar-card-header d-flex flex-column justify-content-center align-items-center py-4">
                                <div class="custom-reletive">

                                    <img class="freelancer-avatar rounded-circle mb-4" src="<?= !empty($rows['profile_picture']) ? $rows['profile_picture'] : '/assets/images/avt.png'; ?>" alt="" />

                                    <span class="online-indicator1"></span>
                                </div>
                                <h3 class="fw-bold freelancer-name text-dark-300 mb-2 relative">
                                    <?=$rows['name']?>
                                    <button class="varified-badge">
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M10.007 2.10377C8.60544 1.65006 7.08181 2.28116 6.41156 3.59306L5.60578 5.17023C5.51004 5.35763 5.35763 5.51004 5.17023 5.60578L3.59306 6.41156C2.28116 7.08181 1.65006 8.60544 2.10377 10.007L2.64923 11.692C2.71404 11.8922 2.71404 12.1078 2.64923 12.308L2.10377 13.993C1.65006 15.3946 2.28116 16.9182 3.59306 17.5885L5.17023 18.3942C5.35763 18.49 5.51004 18.6424 5.60578 18.8298L6.41156 20.407C7.08181 21.7189 8.60544 22.35 10.007 21.8963L11.692 21.3508C11.8922 21.286 12.1078 21.286 12.308 21.3508L13.993 21.8963C15.3946 22.35 16.9182 21.7189 17.5885 20.407L18.3942 18.8298C18.49 18.6424 18.6424 18.49 18.8298 18.3942L20.407 17.5885C21.7189 16.9182 22.35 15.3946 21.8963 13.993L21.3508 12.308C21.286 12.1078 21.286 11.8922 21.3508 11.692L21.8963 10.007C22.35 8.60544 21.7189 7.08181 20.407 6.41156L18.8298 5.60578C18.6424 5.51004 18.49 5.35763 18.3942 5.17023L17.5885 3.59306C16.9182 2.28116 15.3946 1.65006 13.993 2.10377L12.308 2.64923C12.1078 2.71403 11.8922 2.71404 11.692 2.64923L10.007 2.10377ZM6.75977 11.7573L8.17399 10.343L11.0024 13.1715L16.6593 7.51465L18.0735 8.92886L11.0024 15.9999L6.75977 11.7573Z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </h3>

                            </div>
                            <div
                                class="d-flex gap-4 justify-content-between sidebar-rate-card bg-offWhite p-4 rounded-4 ">
                                <div>
                                    <p class="text-dark-300 fw-medium">Sản phẩm</p>
                                    <p class="text-dark-200"><?= $db->get_row("SELECT COUNT(product_id) as total FROM `products` WHERE `seller_id` = '{$row['seller_id']}'")['total'] ?? 0 ?></p>
                                </div>

                                <div>
                                    <p class="text-dark-300 fw-medium">Đã bán</p>
                                    <p class="text-dark-200"><?= $db->get_row("SELECT SUM(sales_count) as total FROM `products` WHERE `seller_id` = '{$row['seller_id']}'")['total'] ?? 0 ?></p>
                                </div>
                            </div>
                            <ul class="py-4">
                                <li
                                    class="py-1 d-flex justify-content-between align-items-center">
                                    <p class="text-dark-200">Địa chỉ:</p>
                                    <p class="text-dark-300 fw-medium"><?=$rows['address']?></p>
                                </li>
                                <li
                                    class="py-1 d-flex justify-content-between align-items-center">
                                    <p class="text-dark-200">Thành viên từ:</p>
                                    <p class="text-dark-300 fw-medium"><?=$rows['create_date']?></p>
                                </li>
                                <li
                                    class="py-1 d-flex justify-content-between align-items-center">
                                    <p class="text-dark-200">Giới tính:</p>
                                    <p class="text-dark-300 fw-medium">Nam</p>
                                </li>

                            </ul>

                            <a href="javascript:;" class="btn btn-primary w-100 mt-3 beforeLoginForChat" data-bs-toggle="modal"
                                data-bs-target="#modalContact">
                                Gửi tin nhắn
                                <svg width="14" height="10" viewBox="0 0 14 10" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 9L13 5M13 5L9 1M13 5L1 5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>

                        </div>
                        <div class="freelancer-sidebar-card p-4 rounded-4 bg-white shadow-sm">
                            <div class="freelancer-single-info  pb-4">
                                <h4
                                    class="freelancer-sidebar-title text-dark-300 fw-semibold">
                                    Giới thiệu về bản thân
                                </h4>
                                <p class="text-dark-200 fs-6">
                                    <?=$rows['description']?>
                                </p>
                            </div>
                            <div class="freelancer-single-info py-4">
                                <h4
                                    class="freelancer-sidebar-title text-dark-300 fw-semibold">
                                    Kỹ năng
                                </h4>
                                <div class="freelancer-skills d-flex flex-wrap gap-3">
                                    <?php
                                        $atm = $rows['skill'];
                                        $delimiters = array(",");
                                        $atm = str_replace($delimiters, $delimiters[0], $atm);
                                        $arrKeyword= explode($delimiters[0], $atm);
                                        foreach ($arrKeyword as $key)
                                        {
                                           echo '<span class="single-skill">'.$key.'</span>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
                <div class="col-xl-9 col-lg-8 mt-4 mt-lg-0">
                    <div>
                        <div
                            class="bg-white d-flex gap-3 p-4 freelancer-tab mb-4"
                            id="nav-tab"
                            role="tablist">
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
                        <div class="row g-4">
                            <div id="loading-indicator" class="loading-indicator">
                                <div class="spinner"></div>
                            </div>
                        
                        <?php
                        foreach ($db->get_list("SELECT * FROM `products` WHERE (`status` = 1 OR `is_pinned` = 1) AND `seller_id` = '{$id}' ORDER BY `is_pinned` DESC") as $product):
                        ?>
                        <article class="col-xl-4 col-md-6 grid-item category1 category2 category4 <?php echo (isset($product['is_new']) && $product['is_new'] == '1') ? 'category2 ' : ''; ?> 
                        <?php echo (isset($product['sales_count']) && $product['sales_count'] >= 20) ? 'category3 ' : ''; ?> 
                        <?php echo (isset($product['is_cheap']) && $product['is_cheap'] == '1') ? 'category4 ' : ''; ?> <?php echo (isset($product['is_free']) && $product['is_free'] == '1') ? 'category5 ' : ''; ?>">
                       
                        <div class="gigs-grid">
                            <div class="gigs-img">
                                <div class="">
                                    <a href="/product/<?=$product['slug']?>"><img src="/assets/images/lazyload.gif" data-src="<?=$product['thumbnail']?>" class="lazyLoad w-100" height="180" alt="<?=$product['title']?>"></a>
                                </div>
                                <?php if ($product['is_pinned'] == 1): ?>
                                <div class="card-overlay-badge">
                                    <a href="/product/<?=$product['slug']?>">
                                        <span class="badge bg-danger"><i class="fa-solid fa-meteor"></i>Ghim</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div class="fav-selection">
                                    <a href="javascript:void(0);" class="fav-icon <?= isset($data_user['id']) && $db->get_row("SELECT 1 FROM favorites WHERE user_id = '{$data_user['id']}' AND product_id = '{$product['product_id']}'") ? 'favourite' : ''; ?>" data-product-id="<?= $product['product_id'] ?>">
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
                                            <?=$product['title']?>
                                        </a>
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
                </div>
            </div>
        </div>
    </section>
</main>
<div class="modal new-modal fade" id="modalContact" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông tin liên hệ</h5>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body service-modal">
                <?php
                $contact_links_json = $rows['contact_links'];
                if (!empty($contact_links_json)) {
                    $contact_links = json_decode($contact_links_json, true);
                    ksort($contact_links);
                if (empty($contact_links)) {
                    echo '<p>Không có thông tin liên hệ nào được tìm thấy.</p>';
                    
                } else {
                    foreach ($contact_links as $link) {
                    echo '<div>
                    <p><strong>Nền tảng:</strong> ' . htmlspecialchars($link['platform']) . '</p>
                    <p><strong>URL:</strong> <a href="' . htmlspecialchars($link['url']) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($link['url']) . '</a></p>
                    <hr>
                    </div>';
                    }
                }
                } else {
                    echo '<p>Không có thông tin liên hệ nào được tìm thấy.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
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