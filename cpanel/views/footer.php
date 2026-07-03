<!-- Footer Start -->
<!-- Footer Start -->
<footer class="footer mt-auto py-3 bg-white text-center">
    <div class="container">
        <span class="text-muted"> Copyright © <span id="year"></span> <a href="#" class="text-dark fw-semibold"><?= $db->site("title");?></a>.
            Software by <a href="https://t.me/BuiDucThanh">
                <span class="fw-semibold text-primary text-decoration-underline">BuiDucThanh</span> 🇻🇳
            </a> All rights reserved
        </span>
        <div class="gtranslate_wrapper"></div>
        <script>
        window.gtranslateSettings = {
            "default_language": "vi",
            "languages": ["vi", "en", "th", "ms", "zh-CN", "tl", "de", "km", "ru", "my", "lo", "tr", "uk", "ko",
                "zh-TW", "it", "fr", "ar"
            ],
            "wrapper_selector": ".gtranslate_wrapper"
        }
        </script>
        <script src="https://cdn.gtranslate.net/widgets/latest/flags.js" defer></script>
    </div>
</footer>
<!-- Footer End -->

</div>

<!-- Scroll To Top -->
<div class="scrollToTop">
    <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
</div>
<div id="responsive-overlay"></div>
<script src="<?= DOMAIN ?>/cpanel/assets/libs/@popperjs/core/umd/popper.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/js/defaultmenu.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/libs/node-waves/waves.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/js/sticky.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/libs/simplebar/simplebar.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/js/simplebar.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/js/date&time_pickers.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/libs/chart.js/chart.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/js/custom-switcher.min.js"></script>
<script src="<?= DOMAIN ?>/cpanel/assets/js/custom.js"></script>
</body>

</html>
<script>
    $(function() {
        var url = window.location.pathname,
            urlRegExp = new RegExp(url.replace(/\/$/, '') + "$");
        $('ul li a').each(function() {
            if (urlRegExp.test(this.href.replace(/\/$/, ''))) {
                var href = $(this).parents().eq(0).attr('id');
                $(this).addClass('side-menu__item active');
                $('#' + href).addClass('side-menu__item active');
                Checkhref(href);
            }
        });

        function Checkhref(href) {
            $('ul li a').each(function() {
                if ($(this).attr('href') == "#" + href) {
                    $(this).addClass('side-menu__item active');
                }
            });
        }
    });
</script>
<script type="text/javascript">
    new ClipboardJS(".copy");

    function copy() {
        cuteToast({
            type: "success",
            message: "Đã sao chép vào bộ nhớ tạm",
            timer: 5000
        });
    }
</script>