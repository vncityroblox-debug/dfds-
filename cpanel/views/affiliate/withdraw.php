<?php
$title = "Nhật ký hoa hồng";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

$sotin1trang = 10;
if (isset($_GET['page']) && $data_user['level'] == 'admin') {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}

$from = ($page - 1) * $sotin1trang;
$where = ' `id` > 0 ';
$order_by = 'ORDER BY id DESC';
$username = '';
$content = '';
$limit = '';
$userid = '';
$trans_id = '';
$stk = '';
if (!empty($_GET['user_id'])) {
    $userid = Anti_xss($_GET['user_id']);
    $where .= ' AND `user_id` = ' . $userid . ' ';
}
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `user_id` LIKE "' . $dataUser['id'] . '" ';
}

if (!empty($_GET['content'])) {
    $content = Anti_xss($_GET['content']);
    $where .= ' AND `reason` LIKE "%' . $content . '%" ';
}

if (!empty($_GET['limit'])) {
    $limit = Anti_xss($_GET['limit']);
    $sotin1trang = $limit;
}
if (!empty($_GET['trans_id'])) {
    $trans_id = Anti_xss($_GET['trans_id']);
    $where .= ' AND `trans_id` LIKE "%' . $trans_id . '%" ';
}
if (!empty($_GET['stk'])) {
    $stk = Anti_xss($_GET['stk']);
    $where .= ' AND `stk` LIKE "%' . $stk . '%" ';
}
$createdate = '';
if (!empty($_GET['createdate'])) {
    $createdate = Anti_xss($_GET['createdate']);
    $create_date_1 = $createdate;
    $create_date_1 = explode(' to ', $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . ' 00:00:00', $create_date_1[1] . ' 23:59:59'];
        $where .= " AND `created_at` >= '" . $create_date_1[0] . "' AND `created_at` <= '" . $create_date_1[1] . "' ";
    }
}

$listLogs = $db->get_list("SELECT * FROM `withdraw_ref` WHERE $where $order_by LIMIT $from,$sotin1trang ");
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Affiliate Withdraw</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Affiliate Program</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Affiliate Withdraw</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <p class="mb-1 fs-5 fw-semibold text-default">
                                    <?=withdrawTotal()?>đ </p>
                                <p class="mb-0 text-muted">Tổng số tiền đã rút</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-danger rounded-circle fs-20"><i class="bx bxs-wallet-alt"></i></span>
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
                                    <?=withdrawMonth()?>đ </p>
                                <p class="mb-0 text-muted">Tiền rút trong tháng <?=date('m')?></p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-info rounded-circle fs-20"><i class="bx bxs-wallet-alt"></i></span>
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
                                <?=withdrawWeekDay()?>đ </p>
                                <p class="mb-0 text-muted">Tiền rút trong tuần</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-warning rounded-circle fs-20"><i class="bx bxs-wallet-alt"></i></span>
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
                                <?=withdrawDay()?>đ </p>
                                <p class="mb-0 text-muted">Tiền rút hôm nay</p>
                            </div>
                            <div class="ms-2">
                                <span class="avatar text-bg-primary rounded-circle fs-20"><i class="bx bxs-wallet-alt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            ĐƠN RÚT TIỀN HOA HỒNG
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $userid ?>" name="user_id" placeholder="ID User">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $username ?>" name="username" placeholder="Username">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $trans_id ?>" name="trans_id" placeholder="Mã giao dịch">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $stk ?>" name="stk" placeholder="Số tài khoản">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?= $content ?>" name="content" placeholder="Lý do">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="createdate" class="form-control form-control-sm" id="daterange" value="<?= $createdate ?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Search </button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/affiliate/withdraw"><i class="fa fa-trash"></i>
                                        Clear filter </a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="5">5</option>
                                        <option selected value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1.000</option>
                                        <option value="5000">5.000</option>
                                        <option value="10000">10.000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label">Short by Date:</label>
                                    <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option value="1">Hôm nay </option>
                                        <option value="2">Tuần này </option>
                                        <option value="3">
                                            Tháng này </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive table-wrapper mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mã giao dịch</th>
                                        <th>Khách hàng</th>
                                        <th>Thông tin</th>
                                        <th>Số tiền</th>
                                        <th>Nội dung</th>
                                        <th>Trạng thái</th>
                                        <th>Thời Gian</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listLogs as $row) : ?>
                                        <tr>
                                            <td><?= $row['trans_id']; ?></td>
                                            <td><?= getRowRealTime('users', $row['user_id'], 'username'); ?></td>
                                            <td>
                                                <ul>
                                                    <li>Ngân hàng: <?= $row['bank'] ?></li>
                                                    <li>Số tài khoản: <?= $row['stk'] ?></li>
                                                    <li>Chủ tài khoản: <?= $row['name'] ?></li>
                                                </ul>
                                            </td>
                                            <td><b style="color:red"><?= format_cash($row['amount']); ?></b></td>
                                            <td><textarea class="form-control"><?= $row['reason']; ?></textarea></td>
                                            <td><?= status_withdraw_orders($row['status']); ?></td>
                                            <td><?= $row['update_gettime']; ?></td>
                                            <td><button class="btn btn-info" onclick="show(<?= $row['id']; ?>,<?= $row['status']; ?>,`<?= $row['reason']; ?>`)"><i class="ri-edit-line align-bottom"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">

                            <div class="col-sm-12 col-md-12 mb-3">
                                <div class="pagination-style-1">
                                    <div class="d-flex justify-content-center">
                                        <?php
                                        $total = $db->num_rows("SELECT * FROM `withdraw_ref` WHERE $where");
                                        if ($total > $sotin1trang) {
                                            echo '<center>' . pagination("/cpanel/affiliate/withdraw?user_id=$userid&username=$username&content=$content&createdate=$createdate&limit=$limit&shortByDate=&", $from, $total, $sotin1trang) . '</center>';
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-diamond" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Thông tin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Trạng thái</label>
                    <input type="hidden" id="iddiamond">
                    <select class="form-select mb-3" id="statuss">
                        <option value="0">Chờ duyệt</option>
                        <option value="2">Đã thanh toán</option>
                        <option value="1">Đã hủy</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="note"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="change" onclick="change()">Lưu ngay</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
function show(id, status, note) {
    $('#iddiamond').val(id);
    $('#note').val(note);
    var selectElement = document.getElementById("statuss");
    var options = selectElement.options;
    for (var i = 0; i < options.length; i++) {
        if (options[i].value == status) {
            options[i].selected = true;
        }
    }
    $('#modal-diamond').modal('show');
}

function change() {
    $('#change').html('Đang xử lý...').prop('disabled',
        true);
    $.ajax({
        url: "/model/admin/withdraw",
        method: "POST",
        dataType: "JSON",
        data: {
            id: $("#iddiamond").val(),
            note: $("#note").val(),
            status: $("#statuss").val()
        },
        success: function(response) {
            if (response.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: response.msg
                })
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Có lỗi',
                    text: response.msg
                })
            }
            $('#change').html(
                    'Lưu ngay')
                .prop('disabled', false);
        }
    });
}
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>