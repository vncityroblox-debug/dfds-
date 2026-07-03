<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `tbl_coupons` WHERE `id` = '" . $id . "'");
    if (!$row) {
        new Redirect("/cpanel/coupons");
    }
} else {
    new Redirect("/cpanel/coupons");
}
if (isset($_POST['SaveCoupon']) && $data_user['level'] == 'admin') {
    $isInsert = $db->update("tbl_coupons", [
        'amount'        => Anti_xss($_POST['amount']),
        'min'           => Anti_xss($_POST['min']),
        'max'           => Anti_xss($_POST['max']),
        'discount'      => Anti_xss($_POST['discount']),
        'updatedate'    => gettime()
    ], " `id` = '".$row['id']."' ");
    if ($isInsert) {
        insert_log($data_user['id'],"Chỉnh sửa mã giảm giá (".$row['code']." ID ".$row['id'].").");
        die('<script type="text/javascript">if(!alert("Lưu thành công!")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Lưu thất bại!")){window.history.back().location.reload();}</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-tags"></i> Chỉnh sửa mã giảm giá '<b style="color:red;"><?=$row['code']?></b>'</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/cpanel/coupons">Coupons</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Coupon</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            CHỈNH SỬA MÃ GIẢM GIÁ
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Số lượng mã giảm giá
                                    (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-3">
                                        <button class="btn btn-primary shadow-primary" type="button" id="button-minus-amount"><i class="fa-solid fa-minus"></i></button>
                                        <input type="number" class="form-control text-center" placeholder="" value="<?=$row['amount']?>" name="amount" required>
                                        <button class="btn btn-primary shadow-primary" type="button" id="button-plus-amount"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                    <script>
                                        document.getElementById('button-plus-amount').addEventListener('click', function() {
                                            incrementValue();
                                        });
                                        document.getElementById('button-minus-amount').addEventListener('click',
                                            function() {
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
                                    <small>Nếu bạn chọn 10, sẽ có 10 lượt sử dụng mã giảm giá cho 10 user khác
                                        nhau.</small>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Chiết khấu giảm (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="discount" value="<?=$row['discount']?>" required>
                                        <span class="input-group-text">
                                            <i class="fa-solid fa-percent"></i>
                                        </span>
                                    </div>
                                    <small>Nhập 10 tức giảm 10% cho đơn hàng áp dụng mã giảm giá này.</small>
                                </div>
                            </div>
                          
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Giá trị đơn hàng tối thiểu
                                    (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?=$row['min']?>" name="min" required>
                                        <span class="input-group-text">
                                            VND </span>
                                    </div>
                                    <small>Giá trị đơn hàng tối thiểu để áp dụng mã giảm giá</small>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email">Giá trị đơn hàng tối đa
                                    (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?=$row['max']?>" name="max" required>
                                        <span class="input-group-text">
                                            VND </span>
                                    </div>
                                    <small>Giá trị đơn hàng tối đa để áp dụng mã giảm giá</small>
                                </div>
                            </div>
                            <a type="button" class="btn btn-danger shadow-danger btn-wave" href="/cpanel/coupons"><i class="fa fa-fw fa-undo me-1"></i>
                                Back</a>
                            <button type="submit" name="SaveCoupon" class="btn btn-primary shadow-primary btn-wave"><i class="fa fa-fw fa-save me-1"></i> Save</button>
                        </form>
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
            text: "Bạn đồng ý thực hiện xóa mốc khuyến mãi " + id,
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
                action: 'removePromotion',
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
                action: 'removePromotion',
                id: id
            },
            success: function(response) {
                if (response.status == 'success') {
                    showMessage('success', 'Mục đã được xóa thành công ' + id);
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