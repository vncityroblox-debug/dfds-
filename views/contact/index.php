<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
$title = 'Liên hệ - ' . $db->site('title');
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
            <div class="row">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Liên hệ</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                        Liên hệ
                    </h2>
                </div>
            </div>
        </div>
    </div>


    <section class="contact-section">

        <div class="contact-bottom bg-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex">
                        <div class="contact-grid w-100">
                            <div class="contact-content">
                                <div class="contact-icon">
                                    <span>
                                        <img src="/assets/images/contact-mail.svg" alt="Icon">
                                    </span>
                                </div>
                                <div class="contact-details">
                                    <h6>Email Address</h6>
                                    <p><?= $db->site('email') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex">
                        <div class="contact-grid w-100">
                            <div class="contact-content">
                                <div class="contact-icon">
                                    <span>
                                        <img src="/assets/images/contact-phone.svg" alt="Icon">
                                    </span>
                                </div>
                                <div class="contact-details">   
                                    <h6>Phone Number</h6>
                                    <p><?= $db->site('hotline') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>