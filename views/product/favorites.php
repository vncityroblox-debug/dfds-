<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
$title = 'Sản phẩm yêu thích';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>
<section class="py-110">
        <div class="container">
            <div class="row">
                <?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php');?>
                <div class="col-md-9">
                    <h3 class="text-24 fw-bold text-dark-300 mb-2">SẢN PHẨM YÊU THÍCH</h3>

                    <div class="row">
                    <?php
$products = isset($data_user['id']) ? $db->get_list("SELECT * FROM `products` 
    WHERE product_id IN (SELECT product_id FROM favorites WHERE user_id = '{$data_user['id']}') 
    AND (`status` = 1 OR `is_pinned` = 1) 
    ORDER BY `is_pinned` DESC, `product_id` DESC") : [];

if ($products): 
    foreach ($products as $product): ?>
    
                                                    <div class="col-xl-4 col-md-6">
                                <div class="gigs-grid">
                                    <div class="gigs-img">
                                        <div class="">
                                            <a href="/product/<?=$product['slug']?>"><img src="/assets/images/lazyload.gif" data-src="<?=$product['thumbnail']?>" class="lazyLoad w-100"
                                            height="180" alt="<?=$product['title']?>"></a>
                                        </div>
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
                            </div>
                          <?php endforeach; 
else: ?>
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
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>