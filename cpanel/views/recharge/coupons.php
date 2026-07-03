<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_POST['AddCoupon']) && $data_user['level'] == 'admin') {
    $isInsert = $db->insert("tbl_coupons", [
        'code'          => Anti_xss($_POST['code']),
        'amount'        => Anti_xss($_POST['amount']),
        'min'           => Anti_xss($_POST['min']),
        'max'           => Anti_xss($_POST['max']),
        'discount'      => Anti_xss($_POST['discount']),
        'createdate'    => gettime(),
        'updatedate'    => gettime(),
        'used'          => 0
    ]);
    if ($isInsert) {
        insert_log($data_user['id'],"Thêm mã giảm giá (".Anti_xss($_POST['code']).") vào hệ thống.");
        die('<script type="text/javascript">if(!alert("Thêm thành công !")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Thêm thất bại !")){window.history.back().location.reload();}</script>');
    }
}
$sotin1trang = 10;
if (isset($_GET['page'])) {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}

$from = ($page - 1) * $sotin1trang;
$where = ' `id` > 0 ';
$order_by = 'ORDER BY id DESC';
$limit = '';
$code = '';
if (!empty($_GET['limit'])) {
    $limit = Anti_xss($_GET['limit']);
    $sotin1trang = $limit;
}
if (!empty($_GET['code'])) {
    $code = Anti_xss($_GET['code']);
    $where .= ' AND `code` LIKE "%' . $code . '%" ';
}
$createdate = '';
if (!empty($_GET['createdate'])) {
    $createdate = Anti_xss($_GET['createdate']);
    $create_date_1 = $createdate;
    $create_date_1 = explode(' to ', $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . ' 00:00:00', $create_date_1[1] . ' 23:59:59'];
        $where .= " AND `createdate` >= '" . $create_date_1[0] . "' AND `createdate` <= '" . $create_date_1[1] . "' ";
    }
}
$coupons = $db->get_list("SELECT * FROM `tbl_coupons` WHERE $where $order_by LIMIT $from,$sotin1trang ");
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-tags"></i> Mã giảm giá</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Coupons</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH MÃ GIẢM GIÁ
                        </div>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2" class="btn btn-sm btn-primary shadow-primary"><i class="ri-add-line fw-semibold align-middle"></i> Tạo mã giảm giá mới</button>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control form-control-sm" value="<?=$code?>" name="code" placeholder="Mã giảm giá">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="createdate" class="form-control form-control-sm" id="daterange" value="<?=$createdate?>" placeholder="Chọn thời gian">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-sm btn-primary"><i class="fa fa-search"></i>
                                        Search </button>
                                    <a class="btn btn-hero btn-sm btn-danger" href="/cpanel/coupons"><i class="fa fa-trash"></i>
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
                                        <option value="1000">1000</option>
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
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mã giảm giá</th>
                                        <th>Sản phẩm áp dụng</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-center">Đã sử dụng</th>
                                        <th>Giảm</th>
                                        <th>Thời gian</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($coupons as $row) : ?>
                                        <tr>
                                            <td><b><?=$row['code']?></b><br>
                                            (<?=$row['used'] >= $row['amount'] ? '<span style="color:red">Đã sử dụng hết</span>' : '<span style="color:green">Còn '.$row['amount'] - $row['used'].' lượt sử dụng</span>';?>)
                                            </td>
                                            <td>
                                                <span class="badge bg-info-transparent">Áp dụng cho toàn bộ</span>
                                            </td>
                                            <td class="text-center"><span style="font-size: 15px;" class="badge bg-info"><?=format_cash($row['amount']);?></span>
                                            </td>
                                            <td class="text-center"><span style="font-size: 15px;" class="badge bg-danger"><?=format_cash($row['used']);?></span>
                                            </td>
                                            <td><span style="font-size: 15px;" class="badge bg-primary"><?=$row['discount'];?>%</span></td>
                                            <td><?=$row['createdate'];?></td>
                                            <td class="text-center">
                                                <a type="button" href="/cpanel/coupon/edit/<?=$row['id'];?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <a type="button" onclick="confirmAction(<?=$row['id'];?>)" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
                                        $total = $db->num_rows("SELECT * FROM `tbl_coupons` WHERE $where");
                                        if ($total > $sotin1trang) {
                                            echo '<center>' . pagination("/cpanel/coupons?code=$code&createdate=$createdate&limit=$limit&shortByDate=&", $from, $total, $sotin1trang) . '</center>';
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
<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2" data-bs-keyboard="false" aria-hidden="true">
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-centered modal-lg dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2"><i class="fa-solid fa-plus"></i> Tạo mã giảm giá mới
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Mã giảm giá (<span class="text-danger">*</span>)</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="code" name="code" placeholder="Nhập mã giảm giá cần tạo" required>
                                <button class="btn btn-danger" type="button" onclick="randomCode()"><i class="fa-solid fa-shuffle"></i> Tạo mã ngẫu
                                    nhiên</button>
                            </div>

                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Số lượng mã giảm giá (<span class="text-danger">*</span>)</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <button class="btn btn-primary shadow-primary" type="button" id="button-minus-amount"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" class="form-control text-center" placeholder="" value="1" name="amount" required>
                                <button class="btn btn-primary shadow-primary" type="button" id="button-plus-amount"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <script>
                                document.getElementById('button-plus-amount').addEventListener('click', function() {
                                    incrementValue();
                                });
                                document.getElementById('button-minus-amount').addEventListener('click', function() {
                                    decrementValue();
                                });

                                function incrementValue() {
                                    var inputElement = document.getElementsByName('amount')[0];
                                    var currentValue = parseInt(inputElement.value, 10);
                                    inputElement.value = currentValue + 1;
                                }

                                function decrementValue() {
                                    var inputElement = document.getElementsByName('amount')[0];
                                    var currentValue = parseInt(inputElement.value, 10);
                                    if (currentValue > 1) {
                                        inputElement.value = currentValue - 1;
                                    }
                                }
                            </script>
                            <small>Nếu bạn chọn 10, sẽ có 10 lượt sử dụng mã giảm giá cho 10 user khác nhau.</small>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Chiết khấu giảm (<span class="text-danger">*</span>)</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" class="form-control" name="discount" required>
                                <span class="input-group-text">
                                    <i class="fa-solid fa-percent"></i>
                                </span>
                            </div>
                            <small>Nhập 10 tức giảm 10% cho đơn hàng áp dụng mã giảm giá này.</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Giá trị đơn hàng tối thiểu (<span class="text-danger">*</span>)</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" class="form-control" value="100000" name="min" required>
                                <span class="input-group-text">
                                    VND </span>
                            </div>
                            <small>Giá trị đơn hàng tối thiểu để áp dụng mã giảm giá</small>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Giá trị đơn hàng tối đa (<span class="text-danger">*</span>)</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" class="form-control" value="100000000" name="max" required>
                                <span class="input-group-text">
                                    VND </span>
                            </div>
                            <small>Giá trị đơn hàng tối đa để áp dụng mã giảm giá</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light " data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="AddCoupon" class="btn btn-primary shadow-primary btn-wave"><i class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function random(length) {
        var result = '';
        var characters = 'QWERTYUPASDFGHJKZXCVBNM123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() *
                charactersLength));
        }
        return result;
    }

    function randomCode() {
        document.getElementById('code').value = random(8);
    }
    const confirmAction = (id) => {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện xóa mã giảm giá " + id,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await Item(id);
            }
        });
    }

    const Item = async (id) => {
        Swal.fire({
            icon: "info",
            title: "Đang xử lý!",
            html: "Không được tắt trang này, vui lòng đợi trong giây lát!",
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
            willClose: () => {},
        });

        $.ajax({
            url: '/model/admin/delete',
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'removeCoupon',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Thất Bại', result.msg, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Thất Bại', xhr.responseText, 'error');
            }
        });
    }

    function postRemoveAccount(id) {
        $.ajax({
            url: "/model/admin/delete",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'removeCoupon',
                id: id
            },
            success: function(response) {
                if (response.status == 'success') {
                    showMessage('success', 'Mã giảm giá xóa thành công ' + id);
                } else {
                    showMessage('error', 'Đã xảy ra lỗi khi xóa mục ' + id);
                }
            }
        });
    }

    function confirmDeleteAccount() {
        var checkbox = document.getElementsByName('checkbox');
        var isAnyCheckboxChecked = false;
        for (var i = 0; i < checkbox.length; i++) {
            if (checkbox[i].checked === true) {
                isAnyCheckboxChecked = true;
                break;
            }
        }
        if (!isAnyCheckboxChecked) {
            alert('Lỗi: Vui lòng chọn ít nhất một bản ghi.');
            return;
        }
        var result = confirm('Bạn có đồng ý xóa các bản ghi đã chọn không?');
        if (result) {
            function postUpdatesSequentially(index) {
                if (index < checkbox.length) {
                    if (checkbox[index].checked === true) {
                        postRemoveAccount(checkbox[index].value);
                    }
                    setTimeout(function() {
                        postUpdatesSequentially(index + 1);
                    }, 100);
                } else {
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            }
            postUpdatesSequentially(0);
        }
    }

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
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>