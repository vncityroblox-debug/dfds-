<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if ($db->site('hosting_status') != 1) {
    new Redirect('/');
    exit;
}

$title = 'Thuê dịch vụ hosting';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');


if (isset($_GET["id"])) {
    $id = Anti_xss($_GET["id"]);
    if (!($row = $db->get_row("SELECT * FROM `hosting_packages` WHERE `id` = '" . $id . "' AND `status` = 1 "))) {
        new Redirect('/hosting');
    }
} else {
    new Redirect('/hosting');
}
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
                        <li class="breadcrumb-item" aria-current="page">Hosting</li>
                    </ol>
                </nav>
                <h2 class="breadcrumb-title">
                    Đăng ký dịch vụ <?= strtoupper($row['name']) ?>
                </h2>
            </div>
        </div>
    </div>
</div>
<div class="container py-5" id="order">
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">Sản phẩm đã chọn</h3>
                    <div class="bg-light p-3 rounded mb-3">
                        <p class="fw-bold text-uppercase"><?=$row['name']?></p>
                                <ul class="list-unstyled">
                                    <li><span><i class="bx bx-check-double"></i></span>Dung Lượng: <?= format_cash($row['disk_quota']) ?> MB</li>
                                    <li><span><i class="bx bx-check-double"></i></span>Băng Thông: Không giới hạn</li>
                                    <li><span><i class="bx bx-check-double"></i></span>Miễn Phí: Chứng Chỉ SSL</li>
                                    <li><span><i class="bx bx-check-double"></i></span>Miền Khác: không giới hạn</li>
                                    <li><span><i class="bx bx-check-double"></i></span>Miền Bí Danh: không giới hạn</li>
                                    <li><span><i class="bx bx-check-double"></i></span>Các Thông Số Khác: không giới hạn</li>
                                    <li><span><i class="bx bx-check-double"></i></span>Vị Trị Máy Chủ: Việt Nam</li>
                                    <li><span><i class="bx bx-check-double"></i></span>Backup: Không có</li>
                                    <?= base64_decode($row['description']) ?>
                                </ul>
                    </div>
<h4 class="card-title">Chu kỳ thanh toán</h4>
<?php $price = $row['price']; ?>
<div class="row g-2 mb-3" id="paymentCycles">
    <div class="col-6 col-md-4">
        <button class="payment-cycle w-100 border rounded-lg p-3 text-center active"
                data-month="1"
                data-price="<?= $price ?>"
                onclick="selectCycle(1, <?= $price ?>)">
            <div class="fw-semibold">1 Tháng</div>
            <div class="text-danger"><?= format_cash($price) ?> đ</div>
        </button>
    </div>
    <div class="col-6 col-md-4">
        <button class="payment-cycle w-100 border rounded-lg p-3 text-center"
                data-month="2"
                data-price="<?= $price * 2 ?>"
                onclick="selectCycle(2, <?= $price * 2 ?>)">
            <div class="fw-semibold">2 Tháng</div>
            <div class="text-danger"><?= format_cash($price * 2) ?> đ</div>
        </button>
    </div>
    <div class="col-6 col-md-4">
        <button class="payment-cycle w-100 border rounded-lg p-3 text-center"
                data-month="3"
                data-price="<?= $price * 3 ?>"
                onclick="selectCycle(3, <?= $price * 3 ?>)">
            <div class="fw-semibold">3 Tháng</div>
            <div class="text-danger"><?= format_cash($price * 3) ?> đ</div>
        </button>
    </div>
    <div class="col-6 col-md-4">
        <button class="payment-cycle w-100 border rounded-lg p-3 text-center"
                data-month="6"
                data-price="<?= $price * 6 ?>"
                onclick="selectCycle(6, <?= $price * 6 ?>)">
            <div class="fw-semibold">6 Tháng</div>
            <div class="text-danger"><?= format_cash($price * 6) ?> đ</div>
        </button>
    </div>
                        <div class="col-6 col-md-4">
                            <button class="payment-cycle w-100 border rounded-lg p-3 text-center"
                                    data-month="9"
                                    data-price="<?= $price * 9 ?>"
                                    onclick="selectCycle(9, <?= $price * 9 ?>)">
                                <div class="fw-semibold">9 Tháng</div>
                                <div class="text-danger"><?= format_cash($price * 9) ?> đ</div>
                            </button>
                        </div>
                        <div class="col-6 col-md-4">
                            <button class="payment-cycle w-100 border rounded-lg p-3 text-center"
                                    data-month="12"
                                    data-price="<?= $price * 12 ?>"
                                    onclick="selectCycle(12, <?= $price * 12 ?>)">
                                <div class="fw-semibold">12 Tháng</div>
                                <div class="text-danger"><?= format_cash($price * 12) ?> đ</div>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="domain" class="form-label fw-bold">Đặt tên miền máy chủ</label>
                        <input type="text" id="domain" class="form-control" placeholder="Nhập tên miền của bạn">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm api-sidebar-menu">
                <div class="card-body">
                    <h3 class="card-title">Thống kê đơn hàng</h3>
                    <div class="bg-light p-3 rounded mb-3">
                        <p id="priceDetails"><?=$row['name']?>: <?= format_cash($row['price']); ?> đ</p>
                        <p id="monthsDetails">1 Tháng: <?= format_cash($row['price']); ?> đ</p>
                        <p id="totalAmount" class="fw-bold text-danger">Tổng tiền thanh toán: <?= format_cash($row['price']); ?> đ</p>
                    </div>
                    <div class="mb-3">
                        <input type="text" id="coupon" class="form-control" placeholder="Nhập mã giảm giá nếu có">
                        <button class="btn btn-danger mt-2 w-100" id="applyCouponBtn">Áp dụng</button>
                    </div>
                    <button onclick="confirmAction(<?=$row['id']?>)" class="btn btn-primary w-100">Thanh Toán</button>
                    <a href="/hosting" class="btn btn-link d-block mt-2">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const price = <?=$row['price']?>;
    let selectedMonths = 1;
    let selectedPrice = price;
    var id = <?=$row['id']?>;

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.payment-cycle').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.payment-cycle').forEach(btn => btn.classList.remove('active-select'));
                button.classList.add('active-select');
                selectedMonths = button.dataset.month;
                selectedPrice = button.dataset.price;
                updateOrderSummary();
            });
        });

        const firstBtn = document.querySelector('.payment-cycle');
        if (firstBtn) {
            firstBtn.classList.add('active-select');
        }
    });

    function updateOrderSummary() {
        const formatter = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        });

        document.getElementById('priceDetails').textContent = `<?=$row['name']?>: ${formatter.format(selectedPrice)}`;
        document.getElementById('monthsDetails').textContent = `${selectedMonths} Tháng: ${formatter.format(selectedPrice)}`;
        document.getElementById('totalAmount').textContent = `Tổng tiền thanh toán: ${formatter.format(selectedPrice)}`;
    }

    const confirmAction = (id) => {
        const domain = document.getElementById('domain').value;
        if (!domain) {
            alert('Vui lòng nhập tên miền!');
            return;
        }

        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện thanh toán hosting?",
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
    };

    const Item = async (id) => {
        Swal.fire({
            icon: "info",
            title: "Đang khởi tạo hosting!",
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
            url: '/model/order/hosting',
            method: 'POST',
            dataType: 'JSON',
            data: {
                id: id,
                csrf_token: csrf_token,
                domain: $('#domain').val(),
                coupon: $('#coupon').val(),
                selectedMonths: selectedMonths
            },
            success: function(result) {
                Swal.close();

                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tạo hosting thành công!',
                        html: `${result.msg}`,
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                        allowEnterKey: true,
                        showConfirmButton: true,
                        confirmButtonText: 'Xem lịch sử'
                    }).then((confirm) => {
                        if (confirm.isConfirmed) {
                            window.location.href = '/user/history/hosting';
                        }
                    });
                } else {
                    Swal.fire('Thất bại', result.msg, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Thất bại', xhr.responseText, 'error');
            }
        });
    };
</script>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>