<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php'); // Include your database initialization

// Kiểm tra người dùng đã đăng nhập chưa
if (!@$user) {
    new Redirect('/login');
    exit;
}
if (!@$user || $data_user['ctv'] != '1') {
    new Redirect('/');
    exit;
}
$title = 'Danh sách sản phẩm';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

// Lấy các tham số từ form hoặc URL
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;  // Số lượng kết quả trên mỗi trang
$offset = ($page - 1) * $limit;

// Xây dựng điều kiện WHERE cho truy vấn SQL
$conditions = ["`seller_id` = '{$data_user['id']}'"];
if ($search) {
    // Tìm kiếm theo tiêu đề sản phẩm
    $conditions[] = "title LIKE '%$search%'";
}

// Thêm điều kiện WHERE vào truy vấn SQL
$where = implode(" AND ", $conditions);
$where = $where ? "WHERE $where" : ''; // Nếu có điều kiện, thêm `WHERE` vào câu truy vấn

// Truy vấn tổng số sản phẩm
$total_items = $db->get_row("SELECT COUNT(*) as total FROM products $where")['total'] ?? 0;

// Truy vấn danh sách sản phẩm theo điều kiện và phân trang
$products = $db->get_list("SELECT * FROM products $where LIMIT $limit OFFSET $offset");

// Tạo URL phân trang
$url = "/user/product/list?search=$search&";
?>
<section class="py-110">
    <div class="container">
        <?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php');?>
        <div class="row justify-content-center gy-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0">Danh sách sản phẩm</h6>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <form action="" method="GET" class="d-flex flex-wrap gap-2">
                                <div class="input-group w-auto flex-fill">
                                    <input type="search" name="search" class="form-control form-control-sm search bg--white form-control" placeholder="Tìm kiếm sản phẩm">
                                    <button class="btn btn--base" type="submit"><i class="fa fa-search text-white"></i></button>
                                </div>
                            </form>
                            <a href="/user/product/upload" class="btn btn-outline--base"><i
                                    class="fa fa-plus"></i> Thêm sản phẩm</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row gy-4">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                             <div class="col-xl-6 col-lg-6 col-sm-6 col-xsm-6 list-view">
                                <div class="product-card h-100 border shadow-sm">
                                    <div class="product-card__thumb">
                                        <a href="/product/<?= Anti_xss($product['slug']); ?>" class="link" title="test">
                                            <img src="<?= Anti_xss($product['thumbnail']); ?>" alt="Product Image">
                                        </a>
                                    </div>
                                    <div class="product-card__content h-100">
                                        <div class="product-card__content-inner">
                                            <div class="product-card__top d-flex w-100 justify-content-between ">
                                                <div class="product-card-title-wrapper">
                                                    <h6 class="product-card__title">
                                                        <a href="/product/<?= Anti_xss($product['slug']); ?>" class="link border-effect">
                                                            <?= Anti_xss($product['title']); ?>                                                </a>
                                                    </h6>
                                                    <span class="product-card__author">Tác giả <a href="/seller/<?= Anti_xss($product['seller_id']); ?>" class="link"><?= getRowUser($product['seller_id'], 'name') ?></a>
                                                    </span>
                                                </div>
                                                <span class="product-card__price"><?= format_cash(Anti_xss($product['sale_price'])); ?>đ</span>
                                            </div>
                                            <div class="collection-list list-style">
                                                <a class="collection-list__button collection-btn product-edit-btn" href="/user/product/edit/<?= Anti_xss($product['product_id']); ?>  ">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                <a data-product-id="1" data-product_title="test" href="" class="collection-list__button collection-btn  add-collection-btn " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Collection" data-bs-original-title="" title="">
                                                    <i class="fa fa-folder-plus"></i>
                                                </a>

                                            </div>
                                        </div>
                                        <div class="flex-between align-items-center">
                                            <div class="product-card__rating">
                                                                                                <span class="product-card__sales">Đã bán: <?= format_cash(Anti_xss($product['sales_count'])); ?></span>
                                            </div>
                                            <a href="/product/<?= Anti_xss($product['slug']); ?>" target="_blank" class="btn btn-outline--light btn--sm mt-1">Live Preview</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                        <td colspan="8" class="text-center">
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
                            </td>
                    </tr>
                    <?php endif; ?>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-center">
                    <?= pagination_client($url, $page, $total_items, $limit); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>
