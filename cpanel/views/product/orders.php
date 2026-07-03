<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

$limit = 10;
if (isset($_GET['page']) && $data_user['level'] == "admin") {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = ' `order_id` > 0 ';
$username = '';
$shortByDate = "";
$create_gettime = "";
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}

if (!empty($_GET["create_gettime"])) {
    $create_date = Anti_xss($_GET["create_gettime"]);
    $create_gettime = $create_date;
    $create_date_1 = str_replace("-", "/", $create_date);
    $create_date_1 = explode(" to ", $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . " 00:00:01", $create_date_1[1] . " 23:59:59"];
        $where .= " AND `order_date` >= '" . $create_date_1[0] . "' AND `order_date` <= '" . $create_date_1[1] . "' ";
    }
}
if (isset($_GET["shortByDate"])) {
    $shortByDate = Anti_xss($_GET["shortByDate"]);
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date("m");
    $currentYear = date("Y");
    $currentDate = date("Y-m-d");
    if ($shortByDate == 1) {
        $where .= " AND `order_date` LIKE '%" . $currentDate . "%' ";
    }
    if ($shortByDate == 2) {
        $where .= " AND YEAR(order_date) = " . $currentYear . " AND WEEK(order_date, 1) = " . $currentWeek . " ";
    }
    if ($shortByDate == 3) {
        $where .= " AND MONTH(order_date) = '" . $currentMonth . "' AND YEAR(order_date) = '" . $currentYear . "' ";
    }
}
$query = "
    SELECT o.*, p.title,p.thumbnail, p.file_url, p.slug
    FROM orders o 
    JOIN products p ON o.product_id = p.product_id 
    WHERE $where
    ORDER BY o.order_id DESC 
    LIMIT $from, $limit
";
$listOrder = $db->get_list($query);
$invoice = $db->get_row("
    SELECT 
        COUNT(*) AS total,
        SUM(total_price) AS total_revenue
    FROM orders
");

$totalOrder = $cronStats['total'] ?? 0;
$totalRevenue = $invoice['total_revenue'];
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-shopping-cart"></i> Lịch sử mua hàng</h1>
        </div>
        <div class="row">
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= $totalOrder ?> </p>
                                <p class="mb-0 text-muted">Tổng đơn</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-success rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?= format_cash($totalRevenue ?? 0) ?>đ</p>
                                <p class="mb-0 text-muted">Doanh thu

                                </p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-primary rounded-circle fs-20"><i class='bx bxs-wallet-alt'></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH ĐƠN HÀNG
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $username ?>" name="username" placeholder="Khách hàng">
                                </div>
                                
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="create_gettime" class="form-control" id="daterange" value="<?= $create_gettime ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Tìm kiếm</button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/product/orders"><i class="fa fa-trash"></i>
                                        Bỏ lọc</a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option <?php echo $limit == 5 ? "selected" : ""; ?> value="5">5</option>
                                        <option <?php echo $limit == 10 ? "selected" : ""; ?> value="10">10</option>
                                        <option <?php echo $limit == 20 ? "selected" : ""; ?> value="20">20</option>
                                        <option <?php echo $limit == 50 ? "selected" : ""; ?> value="50">50</option>
                                        <option <?php echo $limit == 100 ? "selected" : ""; ?> value="100">100</option>
                                        <option <?php echo $limit == 500 ? "selected" : ""; ?> value="500">500</option>
                                        <option <?php echo $limit == 1000 ? "selected" : ""; ?> value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label">Short by Date:</label>
                                    <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option <?php echo $shortByDate == 1 ? "selected" : ""; ?> value="1">Hôm nay</option>
                                        <option <?php echo $shortByDate == 2 ? "selected" : ""; ?> value="2">Tuần này</option>
                                        <option <?php echo $shortByDate == 3 ? "selected" : ""; ?> value="3">Tháng này</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input" name="check_all" id="check_all_checkbox" value="option1">
                                            </div>
                                        </th>
                                        <th>Khách hàng</th>
                                        <th class="text-center">Sản phẩm</th>
                                        <th class="text-center">Thanh toán</th>
                                        <th class="text-center">Thời gian</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($listOrder as $row) :
                                    ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check form-check-md d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input checkbox" data-id="<?= $row['order_id'] ?>" name="checkbox" value="<?= $row['order_id'] ?>">
                                                </div>
                                            </td>
                                            <td class="text-center"><a class="text-primary" href="/cpanel/user/edit/<?= $row['user_id'] ?>"><b style="color:blue"><?= getRowRealtime('users', $row['user_id'], 'username'); ?></b>
                                                    [<b><?= getRowRealtime('users', $row['user_id'], 'id'); ?></b>]</a>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-3 align-items-center project-name">
                                                    <div class="rounded-3 admin-job-icon">
                                                        <img src=" <?= $row['thumbnail'] ?>" alt="" width="100">
                                                    </div>
                                                    <div>
                                                        <p class="text-dark-200" role="button" onclick="location.href='/product/<?= $row['slug'] ?>';">
                                                            <?= $row['title'] ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?= format_cash($row["total_price"]) ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $row["order_date"] ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" id="btnDelete<?= $row['order_id'] ?>" onclick="deleteOrder(`<?= $row['order_id'] ?>`)" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="btn-list">
                                                    <button type="button" id="btn_delete" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" data-bs-original-title="Xóa các link đã chọn">
                                                        <i class="fa-solid fa-trash"></i> Xóa </button>
                                                </div>
                                               
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-center">
                                <?php
                                $total = $db->num_rows("SELECT * FROM `orders` WHERE $where");
                                if ($total > $limit) {
                                    echo '<center>' . pagination("/cpanel/product/orders?limit=" . $limit . "&shortByDate=" . $shortByDate . "&username=" . $username . "&create_gettime=" . $create_gettime . "&email=" . $email . "&", $from, $total, $limit) . '</center>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#check_all_checkbox').on('click', function() {
            $('.checkbox').prop('checked', this.checked);
        });
        $('.checkbox').on('click', function() {
            $('#check_all_checkbox').prop('checked', $('.checkbox:checked')
                .length === $('.checkbox').length);
        });
    });
</script>
<script>
    $("#btn_delete").click(function() {
        var checkboxes = document.querySelectorAll('input[name="checkbox"]:checked');
        if (checkboxes.length === 0) {
            showMessage('Vui lòng tích vào đơn cần xóa.', 'error');
            return;
        }
        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ XÓA " + checkboxes.length +
                " link bạn chọn khi nhấn Đồng Ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                delete_records();
            }
        });
    });

    function delete_records() {
        var checkbox = document.getElementsByName('checkbox');

        function postUpdatesSequentially(index) {
            if (index < checkbox.length) {
                if (checkbox[index].checked === true) {
                    post_delete(checkbox[index].value);
                }
                setTimeout(function() {
                    postUpdatesSequentially(index + 1);
                }, 100);
            } else {
                Swal.fire({
                    title: "Thành công!",
                    text: "Xóa link thành công",
                    icon: "success"
                });
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        }
        postUpdatesSequentially(0);
    }

    function post_delete(id) {
        $.ajax({
            url: "/model/admin/delete",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: 'deleteOrderProduct'
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function() {
                alert(html(response));
                location.reload();
            }
        });
    }

    function deleteOrder(id) {
        const originalContent = $('#btnDelete' + id).html();
        $('#btnDelete' + id).html('<span><i class="fa fa-spinner fa-spin"></i></span>')
            .prop('disabled', true);

        Swal.fire({
            title: "Bạn có chắc không?",
            text: "Hệ thống sẽ xóa đơn hàng này nếu bạn nhấn Đồng ý",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Đóng"
        }).then((result) => {
            if (result.isConfirmed) {
                post_delete(id);
                setTimeout(() => {
                    location.reload();
                }, 500);
            }
        }).finally(() => {
            $('#btnDelete' + id).html(originalContent)
                .prop('disabled', false);
        });
    }
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>