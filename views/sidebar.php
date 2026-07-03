<body>
    <div class="loader-wrapper">
        <span class="site-loader"> </span>
    </div>
    <script>
        window.addEventListener('load', function() {
            var loadingOverlay = document.querySelector('.loader-wrapper');
            loadingOverlay.style.display = 'none';
        });
    </script>
    <!-- Menu Start -->
    <header class="header-primary">
        
        <div class="container">
            <nav class="navbar navbar-expand-xl justify-content-between">
                <a href="/">
                    <img src="<?= $db->site('logo') ?>" width="150" alt="" />
                </a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="d-block d-xl-none">
                            <div class="logo">
                                <a href="/"><img src="<?= $db->site('logo') ?>" width="150" alt="" /></a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="/"
                                role="button"
                                aria-expanded="false">Trang Chủ</a>
                        </li>
                         <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-expanded="false">Dịch Vụ</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/service" class="dropdown-item"><span>Mua Mã Nguồn</span></a>
                                </li>
                                <li>
                                    <a href="/design" class="dropdown-item"><span>Thuê Thiết Kế Riêng</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-expanded="false">Nạp Tiền</a>
                            <ul class="dropdown-menu">
                            <?php if ($db->site('card_status') == 1) : ?>
                                <li>
                                    <a href="/card" class="dropdown-item"><span>Thẻ cào tự động</span></a>
                                </li>
                                <?php endif; ?>
                                <li>
                                    <a href="/bank" class="dropdown-item"><span>Ngân hàng và Ví </span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-expanded="false">Lịch Sử</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/user/history/product" class="dropdown-item"><span>Lịch Sử Đơn Hàng</span></a>
                                    <a href="/user/history/vps" class="dropdown-item"><span>Lịch Sử Cloud Vps </span></a>
                                    <a href="/user/history/hosting" class="dropdown-item"><span>Lịch Sử Hosting</span></a>
                                    <a href="/user/history/cronjob" class="dropdown-item"><span>Lịch Sử Cronjob</span></a>
                                </li>
                            </ul>
                        </li>
                       
                        <?php if ($db->site('status_ref') == 1) : ?>
                        <li class="nav-item">
                                <a class="nav-link" href="/affiliates">Tiếp Thị Liên Kết</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/blogs">Tin Tức</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contact">Liên Hệ</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="/api-document">Tài Liệu API</a>
                        </li>
                        
                    </ul>

                </div>
                <div class="navbar-right d-flex align-items-center gap-2">
                    <a href="/user/favorites" class="header-widget" title="Sản phẩm yêu thích">
                        <i class="fas fa-heart"></i>
                        <sup id="numFavorites"><?= isset($data_user['id']) ? ($db->get_row("SELECT COUNT(id) as total FROM `favorites` WHERE `user_id` = '{$data_user['id']}'")['total'] ?? 0) : 0; ?></sup>

                    </a>
                    <div class="align-items-center">
                   <?php if(@$user):?>
                   <div class="dropdown">
                                <button type="button" class="d-flex header-widget" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?= !empty($data_user['profile_picture']) ? $data_user['profile_picture'] : '/assets/images/avt.png'; ?>" class="rounded-circle w-40 me-1" alt="">
                                    <span>
                                        <p class="text-uppercase"><?=$data_user['provider'] == 'google' ? $data_user['name'] : $data_user['username']?></p>
                                        <p style="color:red;"><?=format_cash($data_user['money'])?>đ</p>
                                    </span>
                                </button>
                                <ul class="dashboard-profile dropdown-menu" style="position: absolute;inset: 0px 0px auto auto;margin: 0px;transform: translate3d(0px, 58.4px, 0px);">
                                    <?php if(isset($data_user['username']) && $data_user['level'] == 'admin') { ?>   
                                    <li>
                                        <a class="dashboard-profile-item dropdown-item" href="/cpanel/home"><i class="fa-solid fa-user-tie me-1 fs-10"></i>Admin Dashboard</a>
                                    </li>
                                    <?php }?>
                                    <li>
                                        <a class="dashboard-profile-item dropdown-item" href="/user/dashboard"><i class="fa fa-home me-1 fs-10"></i>Dashboard</a>
                                    </li>
                                    <li>
                                        <a class="dashboard-profile-item dropdown-item" href="/user/profile"><i class="fa fa-user me-1 fs-10"></i>Tài Khoản</a>
                                    </li>
                                    <?php if($data_user['ctv'] == 1):?>
                                    <li>
                                        <a class="dashboard-profile-item dropdown-item" href="/user/product/upload"><i class="fa fa-upload me-1 fs-10"></i>Đăng Code</a>
                                    </li>
                                    <?php endif;?>
                                    <li>
                                        <a class="dashboard-profile-item dropdown-item" href="/user/security">
                                            <i class="fa-solid fa-shield-halved me-1 fs-10"></i>Bảo Mật
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dashboard-profile-item dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                            <i class="fa-solid fa-right-from-bracket me-1 fs-10"></i>Đăng Xuất
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php else:?>
                            <a href="/login" class="btn-secondary me-1">
                                Đăng Nhập
                            </a>
                        <?php endif;?>
                    </div>
                    <button
                        class="navbar-toggler d-block d-xl-none"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNav"
                        aria-controls="navbarNav"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span></span>
                    </button>
                </div>
            </nav>
        </div>
    </header>
    <main>