<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
$title = 'Dịch vụ của chúng tôi - ' . $db->site('title');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$category = isset($_GET['category']) ? intval($_GET['category']) : '';
$sort_type = isset($_GET['sort_type']) ? $_GET['sort_type'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$hashtag = isset($_GET['hashtag']) ? $_GET['hashtag'] : '';

$where = ' `status` = 1 ';
if ($category) {
    $where .= " AND `category_id` = $category";
}
if ($name) {
    $where .= " AND `product_name` LIKE '%$name%'";
}
if ($hashtag) {
    // Lọc sản phẩm có liên kết với hashtag
    $where .= " AND `product_id` IN (SELECT `product_id` FROM `product_hashtags` WHERE `hashtag` LIKE '%$hashtag%')";
}
if ($sort_type) {
    if ($sort_type == 'is_new') {
        $where .= " AND `is_new` = 1"; // Sản phẩm mới
    } elseif ($sort_type == 'sales_count') {
        $where .= " AND `sales_count` >= 20"; // Sản phẩm bán chạy (sales_count > 10)
    } elseif ($sort_type == 'is_cheap') {
        $where .= " AND `is_cheap` = 1"; // Sản phẩm giá rẻ
    } elseif ($sort_type == 'is_free') {
        $where .= " AND `is_free` = 1"; // Sản phẩm miễn phí
    }
}

$order = " ORDER BY `is_pinned` DESC, `ditme` ASC";
if ($sort_by == 'a_to_z') $order = " ORDER BY `title` ASC";
elseif ($sort_by == 'z_to_a') $order = " ORDER BY `title` DESC";

$from = ($page - 1) * $limit;
$sql = "SELECT * FROM `products` WHERE $where $order LIMIT $from, $limit";
$products = $db->get_list($sql);

$total_items = $db->num_rows("SELECT * FROM `products` WHERE $where");
$total_pages = ceil($total_items / $limit);

$url = "/services?name=" . urlencode($name) . "&category=$category&sort_type=$sort_type&sort_by=$sort_by&limit=$limit&";
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
                        <li class="breadcrumb-item" aria-current="page">Dịch vụ của chúng tôi</li>
                    </ol>
                </nav>
                <h2 class="breadcrumb-title">
                    Dịch vụ của chúng tôi
                </h2>
            </div>
        </div>
    </div>
</div>

<section class="py-110">
    <div class="container">
        <form action="" id="searchFormId" method="GET">
            <div class="row justify-content-between mb-40">
                <div class="col-xl-auto">
                    <div class="d-flex flex-column flex-wrap flex-md-row gap-3">
                        <!-- Input -->
                        <div class=''>
                            <input type="text" class="form-control" placeholder="Tên sản phẩm.." name="name" 
                                value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
                        </div>

                        <!-- Danh mục -->
                        <div>
                            <select name="category" id="category" class="custom-style-select nice-select select-dropdown" onchange="this.form.submit()">
                                <option value="">Tất cả danh mục</option>
                                <?php
                                $categories = $db->get_list("SELECT * FROM `categories` WHERE `status` = 1 ORDER BY `category_id` DESC");
                                foreach ($categories as $category_item):
                                ?>
                                    <option value="<?= $category_item['category_id'] ?>" 
                                        <?= (isset($_GET['category']) && $_GET['category'] == $category_item['category_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category_item['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Phân loại -->
                        <div>
                            <select name="sort_type" id="sort_type" class="custom-style-select nice-select select-dropdown" onchange="this.form.submit()">
                                <option value="">Phân loại</option>
                                <option value="is_new" <?= ($_GET['sort_type'] ?? '') == 'is_new' ? 'selected' : '' ?>>Sản phẩm mới</option>
                                <option value="sales_count" <?= ($_GET['sort_type'] ?? '') == 'sales_count' ? 'selected' : '' ?>>Bán chạy</option>
                                <option value="is_cheap" <?= ($_GET['sort_type'] ?? '') == 'is_cheap' ? 'selected' : '' ?>>Giá rẻ</option>
                                <option value="is_free" <?= ($_GET['sort_type'] ?? '') == 'is_free' ? 'selected' : '' ?>>Miễn phí</option>
                            </select>
                        </div>

                        <!-- Sắp xếp -->
                        <div>
                            <select name="sort_by" id="sort_by" class="custom-style-select nice-select select-dropdown" onchange="this.form.submit()">
                                <option value="">Sắp xếp</option>
                                <option value="a_to_z" <?= ($_GET['sort_by'] ?? '') == 'a_to_z' ? 'selected' : '' ?>>A Đến Z (ASC)</option>
                                <option value="z_to_a" <?= ($_GET['sort_by'] ?? '') == 'z_to_a' ? 'selected' : '' ?>>Z Đến A (DSC)</option>
                            </select>
                        </div>

                        <!-- Giới hạn sản phẩm -->
                        <div>
                            <select name="limit" id="limit" class="custom-style-select nice-select select-dropdown" onchange="this.form.submit()">
                                <option value="12" <?= ($_GET['limit'] ?? '12') == '12' ? 'selected' : '' ?>>12 sản phẩm</option>
                                <option value="24" <?= ($_GET['limit'] ?? '12') == '24' ? 'selected' : '' ?>>24 sản phẩm</option>
                                <option value="48" <?= ($_GET['limit'] ?? '12') == '48' ? 'selected' : '' ?>>48 sản phẩm</option>
                            </select>
                        </div>
                        <div><button class="shop-widget-btn mb-2"><i class="fas fa-search"></i><span>Tìm kiếm</span></button></div>
                        <div><a href="/services" class="shop-widget-btn mb-2"><i class="far fa-trash-alt"></i><span>Bỏ lọc</span></a></div>
                    </div>
                </div>
            </div>
            <!-- Content -->
        </form>

        <div class="row">
            <?php if ($products): ?>
                <?php foreach ($products as $product): ?>
                    <article class="col-xl-3 col-lg-4 col-md-6 mb-4">
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
                                        <span><i class="fa-solid fa-star"></i><span id="averageRating" class="me-1"><?= $db->get_row("SELECT SUM(rating) as total FROM `reviews` WHERE `product_id` = '{$product['product_id']}'")['total'] ?? 0 ?></span> (<?= $db->get_row("SELECT COUNT(id) as total FROM `reviews` WHERE `product_id` = '{$product['product_id']}'")['total'] ?? 0 ?> Reviews)</span>
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
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <svg width="184" height="152" viewBox="0 0 184 152" xmlns="http://www.w3.org/2000/svg">
                        <g fill="none" fill-rule="evenodd">
                            <g transform="translate(24 31.67)">
                                <ellipse fill-opacity=".8" fill="#F5F5F7" cx="67.797" cy="106.89" rx="67.797" ry="12.668"></ellipse>
                                <path d="M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z" fill="#AEB8C2"></path>
                                <path d="M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z" fill="url(#linearGradient-1)" transform="translate(13.56)"></path>
                                <path d="M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z" fill="#F5F5F7"></path>
                                <path d="M42.678 9.953h50.237a2 2 0 0 1 2 2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z" fill="#DCE0E6"></path>
                            </g>
                            <path d="M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z" fill="#DCE0E6"></path>
                            <g transform="translate(149.65 15.383)" fill="#FFF">
                                <ellipse cx="20.654" cy="3.167" rx="2.849" ry="2.815"></ellipse>
                                <path d="M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z"></path>
                            </g>
                        </g>
                    </svg>
                    <p>Không có dữ liệu</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-center">
            <?= pagination_client($url, $page, $total_items, $limit); ?>
        </div>
    </div>
</section>
<!-- Services End -->
</main>
<!-- Main End -->

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>