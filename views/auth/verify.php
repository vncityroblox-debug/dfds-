<?php
 require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
 $title = "Xác minh 2 bước ".$db->site('title');
 require_once(realpath($_SERVER['DOCUMENT_ROOT'].'/views/header.php'));
 require_once(realpath($_SERVER['DOCUMENT_ROOT'].'/views/sidebar.php'));
 if (isset($_GET['token'])) {
    if (!$row = $db->get_row("SELECT * FROM `users` WHERE `token` = '".Anti_xss($_GET['token'])."' ")) {
        new Redirect('/');
    }
} else {
    new Redirect('/');
    }
?>
<section class="py-5 bg-offWhite">
        <div class="container">
            <div class="rounded-3">
                <div class="row">
                    <div class="col-lg-6 p-3 p-lg-5 m-auto">
                        <div class="login-userset">
                            <div class="login-card">
                                <div class="login-heading">
                                    <h3>XÁC THỰC 2 BƯỚC</h3>
                                </div>
                                <div class="form-wrap form-focus">
                                    <span class="form-icon">
                                        <i class="feather-mail"></i>
                                    </span>
                                    <input type="text" id="secret" class="form-control floating">
                                    <label class="focus-label">Mã xác minh</label>
                                </div>
                                <div class="d-flex justify-content-center mb-2">
                                    <div class="g-recaptcha" data-sitekey="<?= $db->site('site_key') ?>"></div>
                                </div>
                                <button class="btn btn-primary w-100" id="btnVerify" type="button">Xác Thực</button>
                            </div>
                            <div class="acc-in">
                                <p>Không có tài khoản ?
                                    <a href="/register"> Tạo tài khoản </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script type="text/javascript">
    var token = '<?=$row['token'];?>';
    $("#btnVerify").on("click", function() {
        $('#btnVerify').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/authenticator",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                secret: $("#secret").val(),
                token: token,
                type: 'VerifyGoogle2FA',
                captcha: grecaptcha.getResponse()
            },
            success: function(respone) {
                if (respone.status == 'success') {
                    Swal.fire({
                        title: 'Successful!',
                        text: respone.msg,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {

                        }
                    });
                    setTimeout("location.href = '/';", 1000);
                } else {
                    Swal.fire('Failure!', respone.msg, 'error');
                }
                $('#btnVerify').html('Xác Thực').prop('disabled', false);
            },
            error: function() {
                showMessage('Không thể xử lý', 'error');
                $('#btnVerify').html('Xác Thực').prop('disabled', false);
            }

        });
    });
</script>
