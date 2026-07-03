<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if ($db->site('cron_status') != 1) {
    new Redirect('/');
    exit;
}

$title = 'Thuê dịch vụ cronjob';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>
<section class="py-110 bg-offWhite">
        <div class="container">
            <div class="rounded-3">

                <section class="space-y-6">
                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <div class="profile-info-card">
                                <!-- Header -->
                                <div class="profile-info-header">
                                    <h4 class="text-18 fw-semibold text-dark-300">
                                        THÊM LINK CRON
                                    </h4>
                                </div>
                                <div class="profile-info-body bg-white">
                                    <div class="mb-3">
                                        <label for="url" class="form-label">URL Cronjob</label>
                                        <input type="text" class="form-control shadow-none" id="url" name="url" placeholder="https://example.com/api" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="method" class="form-label">Method</label>
                                        <select class="form-select shadow-none" id="method" name="method" required>
                                            <option value="GET">GET</option>
                                            <option value="POST">POST</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="cron_expression" class="form-label">Cron Expression</label>
                                        <input type="text" class="form-control shadow-none" id="cron_expression" name="cron_expression" placeholder="*/1 * * * * *" required>
                                        <div class="form-text">Nhập biểu thức cron (ví dụ: "*/1 * * * * *" chạy mỗi 1 giây)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="server" class="form-label">Chọn Máy Chủ</label>
                                        <select class="form-select shadow-none" id="server" name="server" required>
                                            <option value="">-- Chọn Máy Chủ --</option>
                                            <?php 
foreach ($db->get_list("SELECT * FROM `server_cronjobs` WHERE `status` = '1'") as $row) {
    $current_time = new DateTime();
    $discount_valid_until = new DateTime($row['discount_valid_until']);
    $discount_percent = ($current_time < $discount_valid_until) ? $row['discount_percent'] : 0;
    $price = $row['price'] * (1 - $discount_percent / 100);
?>
    <option value="<?=$row['id'];?>">
        <?=$row['name'];?> - Giá: <?=format_cash($price, 2);?> VND
        <?php if ($discount_percent): ?> Giảm <?=$discount_percent;?>%, hết hạn <?=$row['discount_valid_until'];?><?php endif; ?> (Giá gốc: <?=format_cash($row['price']);?> VND)
    </option>
<?php } ?>


                                                                                    </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="months" class="form-label">Thời Gian Sử Dụng (Số Tháng)</label>
                                        <select class="form-select shadow-none" id="months" name="months" required>
                                            <option value="">-- Chọn Thời Gian --</option>
                                            <option value="1">1 Tháng</option>
                                            <option value="3">3 Tháng</option>
                                            <option value="6">6 Tháng</option>
                                            <option value="12">12 Tháng</option>
                                        </select>
                                        <div class="form-text">Chọn số tháng để sử dụng cronjob</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="headers" class="form-label">Headers (Tùy chọn)</label>
                                        <textarea class="form-control shadow-none" id="headers" name="headers" rows="3" placeholder='{"Content-Type": "application/json"}'></textarea>
                                        <div class="form-text">Headers phải ở định dạng JSON (ví dụ: {"Content-Type": "application/json"})</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="body" class="form-label">Body (Tùy chọn)</label>
                                        <textarea class="form-control shadow-none" id="body" name="body" rows="3" placeholder='{"key": "value"}'></textarea>
                                        <div class="form-text">Body cần nhập khi chọn phương thức POST</div>
                                    </div>

                                    <button type="button" class="btn btn-primary" id="btnBuy" onclick="buyCron()">Thêm Cronjob</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="profile-info-card">
                                <div class="profile-info-header">
                                    <h4 class="text-18 fw-semibold text-dark-300">
                                        LƯU Ý
                                    </h4>
                                </div>
                                <div class="profile-info-body bg-white">
                                    <?= $db->site('cron_notice') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</main>

<script type="text/javascript">
    function isValidURL(url) {
        const pattern = new RegExp('^(https?:\\/\\/)?' +
            '((([a-zA-Z0-9\\-]+\\.)+[a-zA-Z]{2,})|' +
            '((\\d{1,3}\\.){3}\\d{1,3}))' +
            '(\\:\\d+)?(\\/[-a-zA-Z0-9@:%_\\+.~#?&//=]*)?$', 'i');
        return !!pattern.test(url);
    }

    function buyCron() {
        const linkCron = document.getElementById('url').value;
        if (!isValidURL(linkCron)) {
            Swal.fire('Failure!', 'Liên kết CRON không hợp lệ', 'error');
            return;
        }
        Swal.fire({
            title: 'Xác nhận thanh toán',
            text: "Bạn có chắc chắn muốn thanh toán không?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Huỷ bỏ',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                processBuyCron();
            }
        });
    }

    function processBuyCron() {
        const btnBuy = document.getElementById('btnBuy');
        const btnContent = btnBuy.innerHTML;
        $('#btnBuy').html('<i class="fa fa-spinner fa-spin"></i>')
            .prop('disabled',
                true);
        $.ajax({
            url: "/model/order/cronjob",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                url: $("#url").val(),
                cron_expression: $("#cron_expression").val(),
                method: $("#method").val(),
                headers: $("#headers").val(),
                body: $("#body").val(),
                server: $("#server").val(),
                months: $("#months").val(),
            },
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        title: 'Successful!',
                        text: response.msg,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Xem đơn hàng', // Nút xem đơn hàng
                        cancelButtonText: 'Thêm liên kết mới', // Nút tạo tiếp
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '';
                        }
                    });
                } else {
                    Swal.fire('Failure!', response.msg, 'error');
                }
                $('#btnBuy').html(btnContent).prop('disabled', false);
            },
            error: function() {
                showMessage('Không thể xử lý', 'error');
                $('#btnBuy').html(
                    btnContent
                ).prop('disabled',
                    false);
            }
        });
    }
</script>
<style>
/* Card container */
.profile-info-card {
    background: #ffffff;
    border: 1.5px solid #d1e9ff;
    border-radius: 16px;
    box-shadow: 0 6px 14px rgba(30, 136, 229, 0.05);
    overflow: hidden;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.profile-info-card:hover {
    border-color: #1e88e5;
    box-shadow: 0 8px 18px rgba(30, 136, 229, 0.1);
}

/* Header title */
.profile-info-header {
    background: #e3f2fd;
    padding: 16px 20px;
    border-bottom: 1px solid #bbdefb;
}
.profile-info-header h4 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 600;
    color: #0d47a1;
}

/* Form body */
.profile-info-body {
    padding: 20px;
    font-size: 0.95rem;
}
.form-label {
    font-weight: 600;
    color: #1a237e;
}
.form-control,
.form-select {
    border-radius: 10px;
    border: 1px solid #cfd8dc;
    font-size: 0.95rem;
    padding: 10px 12px;
    transition: border-color 0.3s ease;
}
.form-control:focus,
.form-select:focus {
    border-color: #1e88e5;
    box-shadow: 0 0 0 0.15rem rgba(30, 136, 229, 0.2);
}
.form-text {
    font-size: 0.85rem;
    color: #546e7a;
}

/* Submit button */
#btnBuy.btn-primary {
    background: linear-gradient(90deg, #1e88e5, #42a5f5);
    border: none;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    padding: 12px 0;
    border-radius: 30px;
    width: 100%;
    transition: background 0.3s ease;
    text-transform: uppercase;
}
#btnBuy.btn-primary:hover {
    background: linear-gradient(90deg, #1565c0, #1976d2);
    color: white;
}

/* Notice section */
.profile-info-body.bg-white {
    background: #ffffff;
    font-size: 0.95rem;
    color: #37474f;
}

</style>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>