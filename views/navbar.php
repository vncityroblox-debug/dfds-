<div class="settings-page-lists">
    <ul class="settings-head">
        <li>
            <a href="/user/dashboard" class="menu-item">Dashboard</a>
        </li>
        <?php if($data_user['ctv'] == 1):?>
        <li>
            <a href="/user/product" class="menu-item">Sản Phẩm</a>
        </li>
        <li>
            <a href="/user/withdraw" class="menu-item">Rút Tiền</a>
        </li>
        <?php endif;?>
        <li>
            <a href="/user/profile" class="menu-item">Hồ Sơ</a>
        </li>
        <li>
            <a href="/user/change-password" class="menu-item">Đổi Mật Khẩu</a>
        </li>
        <li>
            <a href="/user/security" class="menu-item">Bảo Mật 2FA</a>
        </li>
        <li>
            <a href="/user/balance" class="menu-item">Biến Động Số Dư</a>
        </li>
        <li>
            <a href="/user/log" class="menu-item">Nhật Ký Hoạt Động</a>
        </li>
        <li>
            <a href="/user/history/product" class="menu-item">Lịch Sử Mua Hàng</a>
        </li>
        <li>
            <a href="/user/history/hosting" class="menu-item">Lịch Sử Mua Hosting</a>
        </li>
        <li>
            <a href="/user/history/vps" class="menu-item">Lịch Sử Mua Vps</a>
        </li>
    </ul>
</div>
<script>
    $(document).ready(function() {
        var url = window.location.pathname;
        var urlRegExp = new RegExp(url.replace(/\/$/, '') + "$");
        $('.menu-item').each(function() {
            if (urlRegExp.test(this.href.replace(/\/$/, ''))) {
                $(this).addClass('active');
            }
        });
    });
</script>