<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

// Lấy tiêu đề trang
$title = 'Tin tức - ' . $db->site('title');

require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');


if (isset($_GET["limit"])) {
    $limit = (int) Anti_xss($_GET["limit"]);
} else {
    $limit = 4;
}
if (isset($_GET["page"])) {
    $page = Anti_xss((int) $_GET["page"]);
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `status` = 1 ";
$keyword = "";
$category = "";
if (!empty($_GET["category"])) {
    $category = Anti_xss($_GET["category"]);
    $where .= " AND `category_id` = \"" . $category . "\" ";
}
if (!empty($_GET["keyword"])) {
    $keyword = Anti_xss($_GET["keyword"]);
    $where .= " AND `title` LIKE \"%" . $keyword . "%\" ";
}

$listDatatable = $db->get_list(" SELECT * FROM `posts` WHERE " . $where . " ORDER BY `stt` ASC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `posts` WHERE " . $where . " ");
$listDatatables = $db->get_list("SELECT * FROM `posts` WHERE `status` = 1 ORDER BY `view` DESC LIMIT 3");
$url = "/blogs?keyword=$keyword&category=$category&";
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
                            <li class="breadcrumb-item" aria-current="page">Blog</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                        Blog
                    </h2>
                </div>
            </div>
        </div>
    </div>


    <div class="page-content">
        <div class="container">

            <div class="row">
                <div class="col-lg-8">
                    <div class="blog">
                        <div class="row">
                        <?php foreach ($listDatatable as $row) : ?>
                                                            <div class="col-lg-6">
                                    <div class="blog-grid">
                                        <div class="blog-img">
                                            <a href="/blog/<?= $row['slug'] ?>"><img src="<?= $row['image'] ?>" class="img-fluid" alt="img"></a>
                                        </div>
                                        <div class="blog-content">
                                            <div class="user-head">
                                                <div class="user-info">
                                                    <a href="javascript:void(0);"><img src="/assets/images/avt.png" alt="img"></a>
                                                    <h6><a href="javascript:void(0);"><?= getRowRealTime('users', $row['user_id'], 'name'); ?></a><span><?= $row['created_at'] ?></span></h6>
                                                </div>
                                                <!-- <div class="badge-text">
                                                    <a href="javascript:void(0);" class="badge bg-primary-light"></a>
                                                </div> -->
                                            </div>
                                            <div class="blog-title">
                                                <h3><a href="/blog/<?= $row['slug'] ?>"><?= $row['title'] ?></a></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                                    </div>

                        <div class="d-flex justify-content-center">
                            <div class="pagination">
                                <?= pagination_client($url, $page, $totalDatatable, $limit); ?>                                <!-- <ul>
                                    <li>
                                        <a href="javascript:void(0);" class="previous"><i class="fa-solid fa-chevron-left"></i></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="active">1</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">2</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">3</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">4</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">5</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="next"><i class="fa-solid fa-chevron-right"></i></a>
                                    </li>
                                </ul> -->
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-lg-4 theiaStickySidebar">
                    <div class="blog-sidebar mb-0">

                        <div class="card search-widget">
                            <div class="card-header">
                                <h6><img src="/assets/images/search-icon.svg" alt="icon">Tìm kiếm</h6>
                            </div>
                            <div class="card-body">
                                <form action="" method="GET">
                                    <div class="form-group search-group mb-0">
                                        <span class="search-icon"><i class="feather-search"></i></span>
                                        <input type="text" class="form-control" name="keyword" value="<?= Anti_xss($keyword) ?>" placeholder="Từ khóa">
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="card category-widget">
                            <div class="card-header">
                                <h6><img src="/assets/images/category-icon.svg" alt="icon">Danh mục</h6>
                            </div>
                            <div class="card-body">
                                <ul class="categories">
                                <?php foreach ($db->get_list(" SELECT * FROM `post_category` WHERE `status` = 1 ") as $category) : ?>
                                                                            <li><a href="/blogs?category=<?= $category["id"] ?>"><?= $category["name"] ?> <span><?= $db->get_row(" SELECT COUNT(id) FROM `posts` WHERE `category_id` = '" . $category["id"] . "' ")["COUNT(id)"] ?? 0 ?></span></a></li>
                                                                            <?php endforeach; ?>
                                                                    </ul>
                            </div>
                        </div>
                        <div class="card recent-widget">
                            <div class="card-header">
                                <h6><img src="/assets/images/blog-icon.svg" alt="icon">Bài viết nổi bật</h6>
                            </div>
                            <div class="card-body">
                                <ul class="latest-posts">
                                <?php foreach ($listDatatables as $row) : ?>
                                                                        <li>
                                        <div class="post-thumb">
                                            <a href="/blog/<?= $row['slug'] ?>">
                                                <img class="img-fluid" src="<?= $row['image'] ?>" alt="blog-image">
                                            </a>
                                        </div>
                                        <div class="post-info">
                                            <h6>
                                                <a href="/blog/<?= $row['slug'] ?>"><?= $row['title'] ?></a>
                                            </h6>
                                            <div class="blog-user">
                                                <img src="/assets/images/avt.png" alt="user">
                                                <div class="blog-user-info">
                                                    <p><?= getRowRealTime('users', $row['user_id'], 'name'); ?></p>
                                                    <p class="xs-text"><?= $row['created_at'] ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                                    <?php endforeach; ?>                   
                                                                   </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>