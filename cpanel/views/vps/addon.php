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
$listDatatable = $db->get_list(" SELECT * FROM `tbl_addon_vps` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM `tbl_addon_vps` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("/cpanel/vps/addon?limit=" . $limit . "&shortByDate=" . $shortByDate . "&"), $from, $totalDatatable, $limit);
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Addon VPS</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Addon VPS</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH ADDON
                        </div>

                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">

                            <div class="top-filter">
                                <div class="filter-show"> <label class="filter-label">Show :</label> <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option <?= $limit == 5 ? "selected" : "" ?> value="5">5</option>
                                        <option <?= $limit == 10 ? "selected" : "" ?> value="10">10</option>
                                        <option <?= $limit == 20 ? "selected" : "" ?> value="20">20</option>
                                        <option <?= $limit == 50 ? "selected" : "" ?> value="50">50</option>
                                        <option <?= $limit == 100 ? "selected" : "" ?> value="100">100</option>
                                        <option <?= $limit == 500 ? "selected" : "" ?> value="500">500</option>
                                        <option <?= $limit == 1000 ? "selected" : "" ?> value="1000">1000</option>
                                    </select> </div>
                                <div class="filter-short">
                                    <label class="filter-label">ShortbyDate:</label> <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tấtcả</option>
                                        <option <?= $shortByDate == 1 ? "selected" : "" ?> value="1">Hôm nay
                                        </option>
                                        <option <?= $shortByDate == 2 ? "selected" : "" ?> value="2">Tuầ này
                                        </option>
                                        <option <?= $shortByDate == 3 ? "selected" : "" ?> value="3"> Tháng này
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <thead>
                                        <tr>
                                            <th style="width: 5px;">#</th>
                                            <th>Tên gói</th>
                                            <th>Giá gốc</th>
                                            <th>Giá bán</th>
                                            <th>Thời gian tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $row) {
                                         $pricing = json_decode($row['detail'], true)['pricing'];
                                         $price = json_decode($row['price'], true)['pricing'];
                                     ?>
                                         <tr>
                                             <td><?= $row['id']; ?></td>
                                             <td><b><?= $row['name']; ?>
                                             </td>
                                             <td><span class="badge bg-danger"><?= format_cash($pricing['monthly']['amount']); ?></span></span>
                                             </td>
                                             <td><span class="badge bg-dark"><?= format_cash($price['monthly']['amount']); ?></span></span>
                                             </td>
                                             <td><?= $row['created_at']; ?></td>
                                             <td>
                                                 <a href="/cpanel/vps/addon/edit/<?= $row['id'] ?>">
                                                     <button data-toggle="tooltip" type="button" class="btn btn-info btn-outline btn-xs m-r-5 tooltip-info"><i class="ri-edit-line align-bottom"></i></button>
                                                 </a>
                                             </td>
                                         </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?= $limit ?> of <?= format_cash($totalDatatable) ?> Results
                                </p>
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
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>