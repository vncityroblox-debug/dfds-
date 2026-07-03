<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
$title = 'Điều khoản & Điều kiện - ' . $db->site('title');
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
                            <li class="breadcrumb-item" aria-current="page">Điều khoản & Điều kiện</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                        Điều khoản & Điều kiện
                    </h2>
                </div>
            </div>
        </div>
    </div>


    <section class="contact-section">
        <div class="contact-bottom bg-white">
            <div class="container">
                <div class="row justify-content-center">
                    <?=Anti_xss($db->site('terms'));?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>