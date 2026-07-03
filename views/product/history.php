<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php'); // Include your database initialization

if (!@$user) {
    new Redirect('/login');
    exit;
}

$title = 'Lịch sử mua hàng';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$conditions = ["`user_id` = '{$data_user['id']}'"];

$where = implode(" AND ", $conditions);
$total_items = $db->get_row("SELECT COUNT(*) as total FROM order_items WHERE $where ORDER BY `id` DESC")['total'] ?? 0;
$orders = $db->get_list("SELECT * FROM order_items WHERE $where ORDER BY `id` DESC LIMIT $limit OFFSET $offset");

$url = "/user/history/product?&";

?>
<section class="py-110">
    <div class="container">
        <?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php');?>
                    <div class="row">
                <div class="col-md-12">
                    <h3 class="text-24 fw-bold text-dark-300 mb-2">LỊCH SỬ ĐƠN HÀNG</h3>
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <div class="w-100">
                            <table class="w-100 dashboard-table table text-nowrap">
                                <thead class="pb-3">
                                    <tr>
                                        <th scope="col" class="py-2 px-4">Sản phẩm</th>
                                        <th scope="col" class="py-2 px-4">Purchase Code</th>
                                        <th scope="col" class="py-2 px-4">Thanh toán</th>
                                        <th scope="col" class="py-2 px-4">Vào lúc</th>
                                        <th scope="col" class="py-2 px-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                                                                            <tr>
                                            <td>
                                                <div class="d-flex gap-3 align-items-center project-name">
                                                    <div class="rounded-3 admin-job-icon">
                                                        <img src="<?=getRowReal('products', $order['product_id'], 'thumbnail')?>" alt="">
                                                    </div>
                                                    <div>
                                                        <p class="text-dark" role="button" onclick="location.href='/product/<?=getRowReal('products',$order['product_id'],'slug')?>';">
                                                            <?=getRowReal('products',$order['product_id'],'title')?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-dark"><?= $order['purchase_code'] ?> 
                                            </td>
                                            <td class="text-dark"><?= format_cash($order['product_price']) ?></td>

                                            <td>
                                                <span class="status-badge pending">
                                                    <?= $order['created_at'] ?>                                                 </span>
                                            </td>
                                            <td class="text-dark text-nowrap">
                                                <div class="d-flex flex-wrap gap-1">
                                                    <a href="<?=getRowReal('products',$order['product_id'],'file_url')?>" class="btn btn-outline--base btn--sm">
                                                        <i class="fa fa-download"></i> Tải xuống </a>
                                                    <button class="btn btn-outline-success btn--sm review_button" data-product-id="<?= $order['product_id'] ?> ">
                                                        <i class="fa fa-star"></i> Đánh giá </button>
                                                </div>
                                            </td>
                                        </tr>
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
                                                                    </tbody>
                            </table>
                                                    </div>
                        <div class="d-flex justify-content-end">
                            <?= pagination_client($url, $page, $total_items, $limit); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>
<div id="reviewModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Đánh giá sản phẩm</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>

            <form id="ratingForm">
                <div class="modal-body">

                    <label class="form-label me-2" for="rating">Đánh giá của bạn</label>
                    <div class="stars">
                        <input type="radio" name="star" class="star" id="star5" value="5">
                        <label for="star5">★</label>
                        <input type="radio" name="star" class="star" id="star4" value="4">
                        <label for="star4">★</label>
                        <input type="radio" name="star" class="star" id="star3" value="3">
                        <label for="star3">★</label>
                        <input type="radio" name="star" class="star" id="star2" value="2">
                        <label for="star2">★</label>
                        <input type="radio" name="star" class="star" id="star1" value="1">
                        <label for="star1">★</label>
                    </div>
                    <div>
                        <p class="form-label">Nội dung</p>
                        <textarea id="reviews" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Đánh Giá</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let productId;
    document.querySelectorAll('.review_button').forEach(button => {
        button.addEventListener('click', function() {
            productId = this.getAttribute('data-product-id');
            $('#reviewModal').modal('show');
        });
    });
    document.getElementById('ratingForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const rating = document.querySelector('input[name="star"]:checked');
        const review = document.getElementById('reviews').value;

        if (rating && review) {
            $.ajax({
                url: "/model/reviews",
                method: "POST",
                dataType: "JSON",
                data: {
                    'product_id': productId,
                    'rating': rating.value,
                    'review': review
                },
                success: function(respone) {
                    if (respone.status == 'success') {
                        showMessage(respone.msg, respone.status);
                    } else {
                        showMessage(respone.msg, respone.status);
                    }

                },
            });
        } else {
            showMessage('Vui lòng chọn số sao và viết đánh giá của bạn.', 'error');
        }

    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>
