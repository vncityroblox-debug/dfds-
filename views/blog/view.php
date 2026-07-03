<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if (isset($_GET["slug"])) {
    $slug = Anti_xss($_GET["slug"]);

    // Sửa câu lệnh SQL để đảm bảo cú pháp đúng
    $row = $db->get_row("SELECT * FROM `posts` WHERE `slug` = '" . $slug . "' AND `status` = 1");
    $db->cong("posts", "view", 1, " `slug` = '" . $slug . "' ");
    // Kiểm tra nếu không có kết quả (null hoặc false), chuyển hướng
    if (!$row) {
        new Redirect('/blogs');
    }
} else {
    new Redirect('/blogs');
}

$title = $row['title'] . ' | ' . $db->site('title');

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
                            <li class="breadcrumb-item" aria-current="page">BLog</li>
                            <li class="breadcrumb-item" aria-current="page"><?= $row['title'] ?></li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                    <?= $row['title'] ?>                    </h2>
                </div>
            </div>
        </div>
    </div>


    <div class="page-content">
        <div class="container">
             <?= base64_decode($row['content']) ?>
        </div>
    </div>
</main>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>