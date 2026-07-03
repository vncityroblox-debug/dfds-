   <!-- Start::app-sidebar -->
   <aside class="app-sidebar sticky" id="sidebar">

       <!-- Start::main-sidebar-header -->
       <div class="main-sidebar-header">
           <a href="/cpanel/home" class="header-logo">
               <img src="<?= $db->site('logo') ?>" alt="logo" class="desktop-logo">
               <img src="<?= $db->site('favicon') ?>" alt="logo" class="toggle-logo">
               <img src="<?= $db->site('logo') ?>" alt="logo" class="desktop-dark">
               <img src="<?= $db->site('favicon') ?>" alt="logo" class="toggle-dark">
               <img src="<?= $db->site('logo') ?>" alt="logo" class="desktop-white">
               <img src="<?= $db->site('favicon') ?>" alt="logo" class="toggle-white">
           </a>
       </div>
       <!-- End::main-sidebar-header -->

       <!-- Start::main-sidebar -->
       <div class="main-sidebar" id="sidebar-scroll">

           <!-- Start::nav -->
           <nav class="main-menu-container nav nav-pills flex-column sub-open">
               <div class="slide-left" id="slide-left">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                       <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                   </svg>
               </div>
               <ul class="main-menu">
                   <li class="slide__category"><span class="category-name">Main</span></li>
                   <li class="slide">
                       <a href="/cpanel/home" class="side-menu__item">
                           <i class="bx bxs-dashboard side-menu__icon"></i>
                           <span class="side-menu__label">Dashboard</span>
                       </a>
                   </li>
                   <li class="slide has-sub ">
                       <a href="javascript:void(0);" class="side-menu__item ">
                           <i class='bx bx-history side-menu__icon'></i>
                           <span class="side-menu__label">Lịch sử</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/logs" class="side-menu__item ">Nhật ký hoạt
                                   động</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/transactions" class="side-menu__item ">Biến động
                                   số dư</a>
                           </li>
                       </ul>
                   </li>
                   <li class="slide__category"><span class="category-name">Dịch vụ</span></li>
                   <li class="slide has-sub ">
                       <a href="javascript:void(0);" class="side-menu__item ">
                           <i class='bx bx-cart side-menu__icon'></i>
                           <span class="side-menu__label">Sản phẩm</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/product/categories" class="side-menu__item ">Danh mục</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/product/list" class="side-menu__item ">Sản phẩm</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/product/orders" class="side-menu__item ">Đơn hàng</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/product/config" class="side-menu__item ">Cấu hình</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/product/withdraw" class="side-menu__item ">Rút
                                   tiền
                               </a>
                           </li>
                       </ul>
                   </li>
                   <li class="slide has-sub">
                       <a href="javascript:void(0);"
                           class="side-menu__item">
                           <i class='bx bx-task side-menu__icon'></i>
                           <span class="side-menu__label">Cron Jobs</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/cron/server"
                                   class="side-menu__item">Quản lý Server CRON</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/cron/order"
                                   class="side-menu__item">Quản lý Link CRON</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/cron/config"
                                   class="side-menu__item">Cấu hình</a>
                           </li>
                       </ul>
                       <!-- MỤC CLOUD VPS -->
                   <li class="slide has-sub ">
                       <a href="javascript:void(0);" class="side-menu__item ">
                           <i class='bx bx-cart side-menu__icon'></i>
                           <span class="side-menu__label">Cloud</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/vps/plan" class="side-menu__item ">Gói VPS</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/vps/addon" class="side-menu__item ">Addon VPS</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/vps/history" class="side-menu__item ">Đơn hàng</a>
                           </li>
                            <li class="slide">
                               <a href="/cpanel/vps/platinum/history" class="side-menu__item ">Đơn hàng Platinum</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/vps/config" class="side-menu__item ">Cấu hình</a>
                           </li>

                       </ul>
                   </li>
                   <!-- MỤC HOSTING -->
                   <li class="slide has-sub ">
                       <a href="javascript:void(0);" class="side-menu__item ">
                           <i class='bx bx-hdd side-menu__icon'></i>
                           <span class="side-menu__label">Hosting</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/hosting/server" class="side-menu__item ">Máy chủ</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/hosting/package" class="side-menu__item ">Tất cả gói</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/hosting/history" class="side-menu__item ">Đơn hàng</a>
                           </li>

                       </ul>
                   </li>

                   <li class="slide__category"><span class="category-name">Quản lý</span></li>
                   <li class="slide">
                       <a href="/cpanel/users/list" class="side-menu__item ">
                           <i class="bx bxs-user side-menu__icon"></i>
                           <span class="side-menu__label">Thành viên</span>
                       </a>
                   </li>

                   <li class="slide has-sub ">
                       <a href="javascript:void(0);" class="side-menu__item ">
                           <i class='bx bxs-wallet-alt side-menu__icon'></i>
                           <span class="side-menu__label">Nạp tiền</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/recharge/card" class="side-menu__item ">Thẻ cào</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/recharge" class="side-menu__item ">Ngân
                                   hàng</a>
                           </li>

                       </ul>
                   </li>
                   <li class="slide has-sub ">
                       <a href="javascript:void(0);" class="side-menu__item ">
                           <i class='bx bx-group side-menu__icon'></i>
                           <span class="side-menu__label">Affiliate Program</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/affiliate/history" class="side-menu__item ">Nhật
                                   ký hoa hồng</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/affiliate/withdraw" class="side-menu__item ">Rút
                                   tiền
                               </a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/affiliate/config" class="side-menu__item ">Cấu
                                   hình</a>
                           </li>
                       </ul>
                   </li>

                   <li class="slide">
                       <a href="/cpanel/coupons" class="side-menu__item ">
                           <i class="bx bxs-discount side-menu__icon"></i>
                           <span class="side-menu__label">Mã giảm giá</span>
                       </a>
                   </li>
                   <li class="slide">
                       <a href="/cpanel/promotions" class="side-menu__item ">
                           <i class="fa-solid fa-percent side-menu__icon"></i>
                           <span class="side-menu__label">Khuyến mãi nạp tiền</span>
                       </a>
                   </li>
                   <li class="slide has-sub ">
                       <a href="javascript:void(0);" class="side-menu__item ">
                           <i class='bx bxl-blogger side-menu__icon'></i>
                           <span class="side-menu__label">Bài viết</span>
                           <i class="fe fe-chevron-right side-menu__angle"></i>
                       </a>
                       <ul class="slide-menu child1">
                           <li class="slide">
                               <a href="/cpanel/blog/add" class="side-menu__item ">Viết bài mới</a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/blog/list" class="side-menu__item ">Tất cả bài viết
                               </a>
                           </li>
                           <li class="slide">
                               <a href="/cpanel/blog/category" class="side-menu__item ">Chuyên mục</a>
                           </li>
                       </ul>
                   </li>
                   <li class="slide__category"><span class="category-name">Cài đặt hệ thống</span></li>

                   <li class="slide">
                       <a href="/cpanel/theme" class="side-menu__item ">
                           <i class="bx bxs-image side-menu__icon"></i>
                           <span class="side-menu__label">Giao diện</span>
                       </a>
                   </li>
                   <li class="slide mb-5">
                       <a href="/cpanel/settings" class="side-menu__item ">
                           <i class="bx bx-cog side-menu__icon"></i>
                           <span class="side-menu__label">Cài đặt</span>
                       </a>
                   </li>
               </ul>
               <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                       <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                   </svg></div>
           </nav>
           <!-- End::nav -->

       </div>
       <!-- End::main-sidebar -->

   </aside>
   <!-- End::app-sidebar -->