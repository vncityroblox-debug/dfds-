<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (isset($_GET['id'])) {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `products` WHERE `slug` = '{$id}' AND (`status` = 1 OR `is_pinned` = 1)");
    if ($row) {
        $db->query("UPDATE products SET time_session = " . time() . " WHERE product_id = {$row['product_id']}");
        $db->cong("products", "view", 1, " `product_id` = '".$row['product_id']."' ");
    } else {
        new Redirect('/');
    }
} else {
    new Redirect('/');
}
$title = $row['title'] . ' - ' . $db->site('title');
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
            <div class="row align-items-center">
                <div class="col-lg-7 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="/services?name=&category=<?=$row['category_id']?>">Danh mục</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page"><?=getsvcategories($row['category_id'])?></li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                        <?=$row['title']?>                    </h2>

                </div>
                <div class="col-lg-5 col-12">
                    <ul class="breadcrumb-links">
                        <li>
                            <a href="javascript:void(0);" class="fav-icon <?= isset($data_user['id']) && $db->get_row("SELECT 1 FROM favorites WHERE user_id = '{$data_user['id']}' AND product_id = '{$row['product_id']}'") ? 'favourite' : ''; ?>" data-product-id="<?=$row['product_id']?>">
                                <span><i class="fa-<?= isset($data_user['id']) && $db->get_row("SELECT 1 FROM favorites WHERE user_id = '{$data_user['id']}' AND product_id = '{$row['product_id']}'") ? 'solid' : 'regular'; ?> fa-heart"></i></span> Yêu thích
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?=$base_url;?>/product/<?=$row['slug']?>"><span><i class="fa-brands fa-facebook"></i></span>Chia sẻ</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="py-110">
        <div class="container">
            <div class="row">
                <div class="col-xl-9 col-lg-8">
                    <div class="slider-card">
                        <div class="slider service-slider">
                        <?php 
foreach ($db->get_list("SELECT * FROM `product_images` WHERE `product_id` = {$row['product_id']} ORDER BY `image_id` DESC") as $image):?>
                                                            <div class="service-img-wrap">
                                    <img src="<?=$image['image_url']?>" class="img-fluid gallery" alt="<?=$image['image_id']?>">
                                </div>
                                <?php endforeach; ?>
                                                    </div>
                        <div class="slider slider-nav-thumbnails">
                        <?php 
                        foreach ($db->get_list("SELECT * FROM `product_images` WHERE `product_id` = {$row['product_id']} ORDER BY `image_id` DESC") as $image):?>
                            <div><img src="<?=$image['image_url']?>" class="img-fluid gallery" alt="<?=$image['image_id']?>"></div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mt-40">
                        <div class="service_details legal-content">
                            <div class="content-details service-wrap">
                                <?=Anti_xsss(base64_decode($row['detail']));?>
                            </div>
                        </div>

                    </div>

                </div>
                <!-- Right -->
                <div class="col-xl-3 col-lg-4 mt-30 mt-xl-0">
                    <aside class="d-flex flex-column gap-4">
                        <div class="service-widget">
                            <div class="service-amt d-flex align-items-center justify-content-between">
                                <p>Giá bán</p>
                                <h2><?=format_cash($row['sale_price']) ?>đ</h2>
                            </div>
                            <ul class="mb-4">
                                                                    <?php
                                        $atm = $row['description'];
                                        $delimiters = array("\n");
                                        $atm = str_replace($delimiters, $delimiters[0], $atm);
                                        $arrKeyword= explode($delimiters[0], $atm);
                                        foreach ($arrKeyword as $key)
                                        {
                                           echo '<li class="fs-6 d-flex align-items-center gap-3 text-dark-200">
                                        <i class="fa fa-check"></i>'.$key.'</li>';
                                        }
                                        ?>
                                                            </ul>
                            <a href="#" data-bs-toggle="modal"
                                data-bs-target="#stripePayment" class="btn btn-primary w-100"><i class="fa fa-shopping-cart"></i> Thanh Toán</a>
                            <div class="row gx-3 row-gap-3">

                                <div class="col-xl-6 col-lg-6 col-sm-4 col-6">
                                    <div class="buy-box">
                                        <i class="feather-cloud"></i>
                                        <p>Tổng số lượt bán</p>
                                        <h6><?=$row['sales_count']?></h6>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-sm-4 col-6">
                                    <div class="buy-box">
                                        <i class="feather-eye"></i>
                                        <p>Tổng số lượt xem</p>
                                        <h6><?=$row['view']?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="service-wrap tags-widget">
                            <h3>Thẻ liên quan</h3>
                            <ul class="tags">
                                <ul>
                                    <?php foreach ($db->get_list("SELECT * FROM `product_hashtags` WHERE `product_id` = {$row['product_id']} ORDER BY `id` DESC") as $hashtag):?>
                                    <li>
                                        <a href="/services?hashtag=<?=urlencode($hashtag['hashtag'])?>"><?=$hashtag['hashtag']?></a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </ul>
                        </div>
                        <!-- Card -->
                        <div class="service-widget member-widget">
                            <div class="user-details">
                                <div class="user-img">
                                    <img src="<?= !empty(getRowUser($row['seller_id'], 'profile_picture')) ? getRowUser($row['seller_id'], 'profile_picture') : '/assets/images/avt.png'; ?>" alt="img">
                                </div>
                                <div class="user-info">
                                    <h5><span class="me-2"><?= getRowUser($row['seller_id'], 'name') ?></span>
                                        <?= online(getRowUser($row['seller_id'], 'time_session')) ?>
                                    </h5>
                                </div>
                            </div>
                            <ul class="member-info">
                                <li>
                                    Địa chỉ
                                    <span><?= getRowUser($row['seller_id'], 'address') ?></span>
                                </li>
                                <li>
                                    Tổng số sản phẩm
                                    <span><?= $db->get_row("SELECT COUNT(product_id) as total FROM `products` WHERE `seller_id` = '{$row['seller_id']}'")['total'] ?? 0 ?></span>
                                </li>
                                <li>
                                    Đã bán
                                    <span><?= $db->get_row("SELECT SUM(sales_count) as total FROM `products` WHERE `seller_id` = '{$row['seller_id']}'")['total'] ?? 0 ?></span>
                                </li>

                            </ul>
                            <a href="/seller/<?= $row['seller_id'] ?>" class="btn btn-primary mb-0 w-100">
                                Xem cửa hàng
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
    <!-- Services Details End -->
</main>
<div class="modal new-modal fade" id="stripePayment" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận thanh toán</h5>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body service-modal">
                <div class="row">
                    <div class="col-md-12">
                        <div class="order-status">
                            <div class="order-item">
                                <div class="order-img">
                                    <img src="<?=$row['thumbnail']?>" alt="img">
                                </div>
                                <div class="order-info">
                                    <h5><?=$row['title']?></h5>
                                    <ul>
                                        <li>ID : #<?=$row['product_id']?></li>
                                        <li>Ngày cập nhật : <?=$row['updated_at']?></li>
                                    </ul>
                                </div>
                            </div>
                            <h6 class="title">Người bán</h6>
                            <div class="user-details">
                                <div class="user-img">
                                    <img src="<?= !empty(getRowUser($row['seller_id'], 'profile_picture')) ? getRowUser($row['seller_id'], 'profile_picture') : '/assets/images/avt.png'; ?>" alt="img">
                                </div>
                                <div class="user-info">
                                    <h5><?= getRowUser($row['seller_id'], 'name'); ?> <span class="location"><?= getRowUser($row['seller_id'], 'address'); ?></span></h5>

                                </div>
                            </div>
                            <h6 class="title">Chi tiết thanh toán</h6>
                            <div class="detail-table table-responsive">
                                <table class="table">
                                <?php if (@$user): ?>
<tbody>
    <tr>
        <td>Mã giảm giá</td>
        <td>
            <input type="text" class="form-control shadow-none" id="coupon" name="coupon" onchange="totalPayment()" onkeyup="totalPayment()" placeholder="Nhập mã giảm giá" />
        </td>
    </tr>
</tbody>
<?php endif; ?>
                                    <tfoot>
                                        <tr>
                                            <th colspan="1">Tổng tiền</th>
                                            <th class="text-primary"><b id="total"><?=format_cash($row['sale_price']) ?>đ</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="modal-btn">
                                <div class="row gx-2">
                                    <div class="col-6">
                                        <a href="#" data-bs-dismiss="modal" class="btn btn-secondary w-100 justify-content-center">Đóng</a>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-primary w-100" id="btnBuy" onclick="buyProduct()">Thanh Toán</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function buyProduct() {
        $('#btnBuy').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop(
            'disabled',
            true);
        $.ajax({
            url: "/model/order/product",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                product_id: <?=$row['product_id']?>,
                coupon: $("#coupon").val()
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công !',
                        text: result.msg,
                        showDenyButton: true,
                        confirmButtonText: 'Mua thêm',
                        denyButtonText: `Xem chi tiết đơn hàng`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        } else if (result.isDenied) {
                            window.location.href =
                                '/user/history/product';
                        }
                    });
                } else {
                    Swal.fire('Thất bại!', result.msg, 'error');
                }
                $('#btnBuy').html(
                    '<i class="fa-solid fa-cart-shopping"></i> <span>Thanh toán</span>').prop(
                    'disabled',
                    false);
            },
            error: function() {
                showMessage('Vui lòng liên hệ Developer', 'error');
            }
        });
    }

function totalPayment() {
        $('#total').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');
        var month = $("#month").val();
        $.ajax({
            url: "/model/total/cash",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                product_id: <?=$row['product_id']?>,
                coupon: $("#coupon").val(),
                action: 'product'
            },
            success: function(response) {
                $("#total").html(response.newTotal);
            },
            error: function() {
                showMessage('Không thể tính kết quả thanh toán', 'error');
            }
        });
    }
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>