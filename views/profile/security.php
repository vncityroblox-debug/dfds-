<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
$title = "Thiết lập bảo mật";
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>
<section class="py-110">
        <div class="container">
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php');?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-card-head">
                            <h4>Thêm tài khoản của bạn</h4>
                        </div>
                        <div class="settings-card-body">
                            <p class="mb-3">Sử dụng mã QR hoặc khóa thiết lập trên ứng dụng Google Authenticator để thêm tài khoản của bạn.</p>
                            <div class="form-group mx-auto text-center">
                                                    <?php
                                    use PragmaRX\Google2FAQRCode\Google2FA;
   // Tạo đối tượng Google2FA
   $google2fa = new Google2FA();
  // tạo mã mới
 $code2fa = $data_user['secretkey'];
// lấy tên miền 
 $tenungdung = $db->site('title');
// Tạo mã QR cho use 
$qrCodeUrl = $google2fa->getQRCodeUrl(
   $tenungdung,
   $data_user['email'],
    $code2fa
);

// get url
$chartUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);
$base64QRCode = getBase64QR($chartUrl);
?>
                                    <img class="qr-code" src="<?= $base64QRCode; ?>">



                            </div>
                            <div class="form-group">
                                <label class="form-label">Setup Key</label>
                                <div class="input-group">
                                    <input type="text" name="key" value="<?=$data_user['secretkey'];?>" id="key" class="form-control form--control"
                                        readonly>
                                    <button type="button" class="input-group-text copy" data-clipboard-target="#key"> <i class="fa fa-copy"></i> </button>
                                </div>
                            </div>

                            <label><i class="fa fa-info-circle"></i> Help</label>
                            <p>Google Authenticator là ứng dụng đa yếu tố dành cho thiết bị di động. Ứng dụng này tạo ra các mã có thời gian được sử dụng trong quá trình xác minh 2 bước. Để sử dụng Google Authenticator, hãy cài đặt ứng dụng Google Authenticator trên thiết bị di động của bạn. <a class="text--base"
                                    href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en"
                                    target="_blank">Tải xuống</a></p>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-card-head">
                            <h4>Bật bảo mật 2FA</h4>
                        </div>
                        <div class="settings-card-body">
                        <div class="mb-3">
                                <label for="code" class="form-label">Google Authenticatior OTP </label>
                                <input type="text" class="form-control shadow-none" id="secret" name="secret" placeholder="Nhập mã xác thực" required="">
                            </div>
                            <div class="mb-3">
                <?php if ($data_user['status_2fa'] == 1): ?>
                    <button id="btnChange" class="w-btn-black-lg w-100" onclick="change2fa()">Tắt 2FA</button>
                <?php else: ?>
                    <button id="btnChange" class="btn btn-primary w-100" onclick="change2fa()">Bật 2FA</button>
                <?php endif; ?>
                        </div>
                       
                    </div>
                </div>
              
            </div>

        </div>
    </section>
</main>
<script>
    function change2fa() {
        $('#btnChange').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/authenticator",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                secret: $("#secret").val(),
                type: 'ChangeGoogle2FA'
            },
            success: function(respone) {
                if (respone.status == 'success') {
                    showMessage(respone.msg, 'success');
                    setTimeout("location.href = '';", 1000);
                } else {
                    showMessage(respone.msg, 'error');
                }
                <?php if ($data_user['status_2fa'] == 1): ?>
                $('#btnChange').html('Tắt 2FA').prop('disabled', false);
            },
            error: function() {
                showMessage('Không thể xử lý', 'error');
                $('#btnChange').html('Tắt 2FA').prop('disabled', false);
            }
            <?php else: ?>
            $('#btnChange').html('Bật 2FA').prop('disabled', false);
            },
            error: function() {
                showMessage('Không thể xử lý', 'error');
                $('#btnChange').html('Bật 2FA').prop('disabled', false);
            }
            <?php endif; ?>
        });
    }
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>