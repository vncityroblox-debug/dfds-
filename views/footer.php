
<script>
    $(function() {
        $("img.lazyLoad").lazyload({
            effect: "fadeIn"
        });
    });
    function displayStars(averageRating) {
        const starsContainer = document.querySelector('.rating');
        const averageRatingElement = document.getElementById('averageRating');
        const roundedRating = Math.round(averageRating);

        averageRatingElement.textContent = averageRating.toFixed(1);

        const allStars = starsContainer.querySelectorAll('input[name="rating"]');
        allStars.forEach(star => (star.checked = false));

        const selectedStar = starsContainer.querySelector(`#stars${roundedRating}`);
        if (selectedStar) {
            selectedStar.checked = true;
        }
    }

    $(document).ready(function() {
        $('.service-filter-btn').on('click', function() {
            $('#loading-indicator').addClass('show');
            setTimeout(function() {
                $('#loading-indicator').removeClass('show');
            }, 300);
        });
    });

    $(document).on('click', '.fav-icon', function() {
        var productId = $(this).data('product-id');
        var $icon = $(this).find('i');

        $.ajax({
            url: '/model/favorite',
            type: 'POST',
            data: {
                product_id: productId,
                csrf_token: csrf_token
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'added') {
                    $icon.removeClass('fa-regular').addClass('fa-solid');
                    $('#numFavorites').text(result.fav_count);
                    showMessage(result.msg, 'success');
                } else if (result.status === 'removed') {
                    $icon.removeClass('fa-solid').addClass('fa-regular');
                    $('#numFavorites').text(result.fav_count);
                    showMessage(result.msg, 'success');
                } else {
                    showMessage(result.msg, 'error');
                }
            },
            error: function() {
                showMessage("Có lỗi xảy ra. Vui lòng thử lại sau!", 'error');
            }
        });
    });
</script>
<div class="modal new-modal fade" id="logoutModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận đăng xuất</h5>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body service-modal">
                <div class="row">
                    <div class="col-md-12">
                        Bạn có chắc chắn muốn Đăng xuất không?
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <div class="btn-item">
                    <a href="#" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</a>
                    <a href="/logout" class="btn btn-primary" type="submit">Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer  -->
<footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="footer-widget">
                        <a href="/">
                            <img src="<?= $db->site('logo') ?>" width="150" alt="logo">
                        </a>
                        <p><?= $db->site('description') ?></p>
                        <div class="social-links">
                            <ul>
                                <li><a href="javascript:void(0);"><i class="fa-brands fa-facebook"></i></a></li>
                                <li><a href="javascript:void(0);"><i class="fa-brands fa-x-twitter"></i></a></li>
                                <li><a href="javascript:void(0);"><i class="fa-brands fa-instagram"></i></a></li>
                                <li><a href="javascript:void(0);"><i class="fa-brands fa-google"></i></a></li>
                                <li><a href="javascript:void(0);"><i class="fa-brands fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Danh mục nổi bật</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="menu-items">
                                <?php foreach ($db->get_list("SELECT * FROM `categories` WHERE `status` = 1 ORDER BY `category_id` DESC") as $category):?>
                                    <li><a href="/services?category=<?=$category['category_id']?>"><?=$category['name']?></a></li>
                                <?php endforeach;?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Thể loại blog</h3>
                        <ul class="menu-items">
                        <?php foreach ($db->get_list("SELECT * FROM `post_category` WHERE `status` = 1 ORDER BY `id` ASC") as $blog):?>
                            <li><a href="/blogs?category=<?=$blog['id']?>"><?=$blog['name']?></a></li>
                        <?php endforeach;?>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Dịch vụ khác</h3>
                        <ul class="menu-items">
                            <li><a href="/cloudvps">Cloud VPS</a></li>
                            <li><a href="/cronjob">Cronjob</a></li>

                        </ul>
                    </div>
                </div>

            </div>
            <div class="contact-widget">
                <div class="row align-items-center">
                    <div class="col-xl-9">
                        <ul class="location-list">
                            <li>
                                <span><i class="fa-solid fa-phone"></i></span>
                                <div class="location-info">
                                    <h6>Phone</h6>
                                    <p><?= $db->site('hotline') ?></p>
                                </div>
                            </li>
                            <li>
                                <span><i class="fa-regular fa-envelope"></i></span>
                                <div class="location-info">
                                    <h6>Email</h6>
                                    <p><?= $db->site('email') ?></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xl-3 text-xl-end">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="copy-right">
                        <p>Copyright <?= date("Y"); ?>, All Rights Reserved | Software By <?= $db->site('author') ?></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="footer-bottom-links">
                        <ul>
                            <li><a href="/privacy-policy">Chính sách bảo mật</a></li>
                            <li><a href="/terms-condition">Điều khoản & Điều kiện</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="back-to-top">
    <a class="back-to-top-icon align-items-center justify-content-center d-flex" href="#top">
        <img src="/assets/images/arrow-badge-up.svg" alt="img">
    </a>
</div>


<script src="/assets/js/owl.carousel.min.js"></script>
<script src="/assets/plugins/slick/slick.js?v=<?php echo time() ?>"></script>
<script src="/assets/js/script.js?v=<?php echo time() ?>"></script>
<!-- Migrate  -->
<script src="/assets/js/jquery-migrate.min.js"></script>
<!-- CounterUp  -->
<script src="/assets/js/jquery.counterup.min.js"></script>
<!-- Waypoint -->
<script src="/assets/js/waypoints.min.js"></script>
<!-- Nice Select -->
<script src="/assets/js/jquery.nice-select.min.js"></script>
<!-- Isotope -->
<script src="/assets/js/isotope.pkgd.min.js"></script>
<!-- ImgLoaded -->
<script src="/assets/js/imagesloaded.pkgd.min.js"></script>
<!-- AOS -->
<script src="/assets/js/aos.js"></script>
<!-- Quill Editor -->
<script src="/assets/js/quill.js"></script>
<!-- GLightBox -->
<script src="/assets/js/glightbox.min.js"></script>
<!-- Popper -->
<script src="/assets/js/popper.min.js"></script>
<!-- Bootstrap -->
<script src="/assets/js/bootstrap.bundle.min.js"></script>
<!-- Swiper -->
<script src="/assets/js/swiper-bundle.min.js"></script>
<!-- Main -->
<script src="/assets/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
<script>
    var o = new ClipboardJS(".copy");
    o.on("success", function(e) {
        showMessage('Sao Chép Thành Công', 'success');
    });
    o.on("error", function(e) {
        showMessage('Sao Chép Thất Bại', 'error');
    });
</script>
</body>

</html>