<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
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
$listDatatable = $db->get_list(" SELECT * FROM `tbl_cloudvps` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `tbl_cloudvps` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("/cpanel/vps/plan?limit=" . $limit . "&shortByDate=" . $shortByDate . "&"), $from, $totalDatatable, $limit);
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Gói VPS</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Gói VPS</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH GÓI
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteAll()" id="delete-all-btn" disabled>Xóa tất cả</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="top-filter">
                                <div class="filter-show"> 
                                    <label class="filter-label">Show :</label> 
                                    <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option <?= $limit == 5 ? "selected" : "" ?> value="5">5</option>
                                        <option <?= $limit == 10 ? "selected" : "" ?> value="10">10</option>
                                        <option <?= $limit == 20 ? "selected" : "" ?> value="20">20</option>
                                        <option <?= $limit == 50 ? "selected" : "" ?> value="50">50</option>
                                        <option <?= $limit == 100 ? "selected" : "" ?> value="100">100</option>
                                        <option <?= $limit == 500 ? "selected" : "" ?> value="500">500</option>
                                        <option <?= $limit == 1000 ? "selected" : "" ?> value="1000">1000</option>
                                    </select> 
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label">ShortbyDate:</label> 
                                    <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option <?= $shortByDate == 1 ? "selected" : "" ?> value="1">Hôm nay</option>
                                        <option <?= $shortByDate == 2 ? "selected" : "" ?> value="2">Tuần này</option>
                                        <option <?= $shortByDate == 3 ? "selected" : "" ?> value="3">Tháng này</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5px;"><input type="checkbox" id="select-all"></th>
                                        <th style="width: 5px;">#</th>
                                        <th>Tên gói</th>
                                        <th>Giá gốc</th>
                                        <th>Giá bán</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) {
                                        $pricing = json_decode($row['pricing'], true);
                                        $price = json_decode($row['price'], true);
                                    ?>
                                        <tr onchange="updateForm('<?= $row['id']; ?>')">
                                            <td><input type="checkbox" class="select-item" value="<?= $row['id']; ?>"></td>
                                            <td><?= $row['id']; ?></td>
                                            <td><b><?= $row['name']; ?></b></td>
                                            <td><span class="badge bg-danger"><?= format_cash($pricing['monthly']['amount']); ?></span></td>
                                            <td><span class="badge bg-dark"><?= format_cash($price['monthly']['amount']); ?></span></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-lg">
                                                    <input class="form-check-input" type="checkbox" id="status<?php echo $row["id"]; ?>" value="1" <?php echo $row["status"] == 1 ? "checked=\"\"" : ""; ?>>
                                                </div>
                                            </td>
                                            <td><?= $row['created_at']; ?></td>
                                            <td>
                                                <a href="/cpanel/vps/plan/edit/<?= $row['id'] ?>">
                                                    <button data-toggle="tooltip" type="button" class="btn btn-info btn-outline btn-xs m-r-5 tooltip-info"><i class="ri-edit-line align-bottom"></i></button>
                                                </a>
                                                <button data-toggle="tooltip" type="button" class="btn btn-danger btn-outline btn-xs m-r-5 tooltip-danger" onclick="deleteForm('<?= $row['id']; ?>')"><i class="ri-delete-bin-line align-bottom"></i></button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?= $limit ?> of <?= format_cash($totalDatatable) ?> Results</p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3"> <?= $limit < $totalDatatable ? $urlDatatable : "" ?> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function updateForm(id) {
        $.ajax({
            url: "/model/admin/updates",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'updateTablePlan',
                id: id,
                status: $('#status' + id).is(':checked') ? 1 : 0
            },
            success: function(result) {
                showMessage(result.msg, result.status);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showMessage('Đã xảy ra lỗi khi cập nhật trạng thái', 'error');
                console.error('Update error:', textStatus, errorThrown);
                location.reload();
            }
        });
    }

    function deleteForm(id) {
        if (confirm('Bạn có chắc chắn muốn xóa gói VPS này?')) {
            $.ajax({
                url: "/model/admin/delete",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'deleteTablePlan',
                    id: id
                },
                success: function(result) {
                    showMessage(result.msg, result.status);
                    if (result.status === 'success') {
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    showMessage('Đã xảy ra lỗi khi xóa gói VPS', 'error');
                    console.error('Delete error:', textStatus, errorThrown);
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    }

    function deleteAll() {
        const selectedIds = $('.select-item:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            showMessage('Vui lòng chọn ít nhất một gói VPS để xóa', 'error');
            return;
        }

        if (confirm(`Bạn có chắc chắn muốn xóa ${selectedIds.length} gói VPS đã chọn?`)) {
            let successCount = 0;
            let errorCount = 0;
            let completed = 0;
            const total = selectedIds.length;

            selectedIds.forEach(id => {
                $.ajax({
                    url: "/model/admin/delete",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'deleteTablePlan',
                        id: id
                    },
                    success: function(result) {
                        if (result.status === 'success') {
                            successCount++;
                        } else {
                            errorCount++;
                        }
                        completed++;
                        if (completed === total) {
                            showMessage(`Xóa thành công ${successCount} gói, thất bại ${errorCount} gói`, successCount > 0 ? 'success' : 'error');
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        errorCount++;
                        completed++;
                        console.error('Bulk delete error for ID ' + id + ':', textStatus, errorThrown);
                        if (completed === total) {
                            showMessage(`Xóa thành công ${successCount} gói, thất bại ${errorCount} gói`, successCount > 0 ? 'success' : 'error');
                            setTimeout(() => location.reload(), 1000);
                        }
                    }
                });
            });
        }
    }

    $(document).ready(function() {
        $('#select-all').on('change', function() {
            $('.select-item').prop('checked', $(this).is(':checked'));
            $('#delete-all-btn').prop('disabled', $('.select-item:checked').length === 0);
        });

        $('.select-item').on('change', function() {
            $('#delete-all-btn').prop('disabled', $('.select-item:checked').length === 0);
            $('#select-all').prop('checked', $('.select-item').length === $('.select-item:checked').length);
        });
    });
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');
?>