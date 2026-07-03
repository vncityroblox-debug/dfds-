<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

if (isset($_POST["submit"]) && $data_user['level'] == "admin") {

    $data = [
        'name' => Anti_xss($_POST['name']),
        'price' => Anti_xss($_POST['price']),
        'usage_limit' => Anti_xss($_POST['usage_limit']),
        'discount_percent' => Anti_xss($_POST['discount_percent']),
        'discount_valid_until' => Anti_xss($_POST['discount_valid_until'])
    ];

    if ($db->insert('server_cronjobs', $data)) {
        die('<script type="text/javascript">alert("Máy chủ đã được thêm thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Thêm máy chủ thất bại"); window.history.back();</script>');
    }
}

if (isset($_GET["limit"])) {
    $limit = (int) Anti_xss($_GET["limit"]);
} else {
    $limit = 10;
}

if (isset($_GET["page"])) {
    $page = Anti_xss((int) $_GET["page"]);
} else {
    $page = 1;
}

$from = ($page - 1) * $limit;
$where = " `id` > 0 ";
$shortByDate = "";

if (isset($_GET["shortByDate"])) {
    $shortByDate = Anti_xss($_GET["shortByDate"]);
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date("m");
    $currentYear = date("Y");
    $currentDate = date("Y-m-d");
    if ($shortByDate == 1) {
        $where .= " AND `created_at` LIKE '%" . $currentDate . "%' ";
    }
    if ($shortByDate == 2) {
        $where .= " AND YEAR(created_at) = " . $currentYear . " AND WEEK(created_at, 1) = " . $currentWeek . " ";
    }
    if ($shortByDate == 3) {
        $where .= " AND MONTH(created_at) = '" . $currentMonth . "' AND YEAR(created_at) = '" . $currentYear . "' ";
    }
}

$listDatatable = $db->get_list(" SELECT * FROM `server_cronjobs` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `server_cronjobs` WHERE " . $where . " ORDER BY `id` DESC ");
$urlDatatable = pagination(("server?limit=" . $limit . "&shortByDate=" . $shortByDate . "&"), $from, $totalDatatable, $limit);
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0"><i class="fa-solid fa-layer-group"></i> Quản lý máy chủ CRON</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" id="open-card-hide" class="btn btn-primary btn-sm mb-3">
                        <i class="fa-solid fa-plus"></i> Thêm mới
                    </button>
                </div>
            </div>
            <div class="col-xl-12" id="card-hide" style="display: none;">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên máy chủ</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Giá</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>

                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">Giới hạn sử dụng</label>
                                <input type="number" class="form-control" id="usage_limit" name="usage_limit" required>
                            </div>

                            <div class="mb-3">
                                <label for="discount_percent" class="form-label">Phần trăm chiết khấu giảm giá</label>
                                <input type="number" class="form-control" id="discount_percent" name="discount_percent" value="0">
                            </div>

                            <div class="mb-3">
                                <label for="discount_valid_until" class="form-label">Giảm giá có giá trị đến</label>
                                <input type="datetime-local" class="form-control" id="discount_valid_until" name="discount_valid_until">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Thêm Danh Mục</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH SERVER CRON
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
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
                                        <th>Server</th>
                                        <th>Giá thuê</th>
                                        <th>Chi tiết</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) { ?>
                                        <tr onchange="updateForm('<?= $row['id']; ?>')">

                                            <td><?= $row['name'] ?></td>
                                            <td><strong style="color:red;"><?= format_cash($row['price']); ?>đ</strong> / 1
                                                Tháng</td>
                                            <td>
                                                <i class="fa-solid fa-link"></i> Link đang chạy: <strong style="color:blue;"><?=count_cronjob_in_server($row['id'])?></strong><br>
                                                <i class="fa-solid fa-link"></i> Link tối đa: <strong style="color:red;"><?= $row['usage_limit']; ?></strong><br>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?= $row["id"]; ?>" value="1" <?= $row["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <a type="button" href="/cpanel/cron/server/edit/<?= $row["id"] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                    <i class="fa fa-pencil-alt"></i> Edit
                                                </a>
                                                <a type="button" onclick="RemoveRow('<?= $row['id']; ?>')" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?php echo $limit; ?> of <?php echo format_cash($totalDatatable); ?> Results</p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3">
                                <?php echo $limit < $totalDatatable ? $urlDatatable : ""; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateForm(id) {
        $.ajax({
            url: "/model/admin/update",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'updateTableServerCron',
                id: id,
                status: $('#status' + id + ':checked').val()
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function() {
                alert(html(result));
                location.reload();
            }
        });
    }
</script>

<script type="text/javascript">
    function postRemove(id) {
        $.ajax({
            url: "/model/admin/delete",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'removeServerCron',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: result.msg
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: result.msg
                    })
                }
            }
        });
    }



    function RemoveRow(id) {

        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn có đồng ý xóa mục ID " + id + " này không ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                postRemove(id);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var button = document.getElementById('open-card-hide');
        var card = document.getElementById('card-hide');

        // Thêm sự kiện click cho nút button
        button.addEventListener('click', function() {
            // Kiểm tra nếu card đang hiển thị thì ẩn đi, ngược lại hiển thị
            if (card.style.display === 'none' || card.style.display === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>