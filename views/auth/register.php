<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
$title = $db->site('title');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>
<section class="py-5 bg-offWhite">
        <div class="container">
            <div class="rounded-3">
                <div class="row">
                    <div class="col-lg-6 p-3 p-lg-5 m-auto">
                        <div class="login-userset">
                            <div class="login-card">
                                <div class="login-heading">
                                    <h3>Đăng Ký Tài Khoản</h3>
                                    
                                </div>
                                <div class="form-wrap form-focus">
                                    <span class="form-icon">
                                        <i class="feather-user"></i>
                                    </span>
                                    <input type="text" class="form-control floating" id="username">
                                    <label class="focus-label">Tài khoản *</label>
                                </div>
                                <div class="form-wrap form-focus">
                                    <span class="form-icon">
                                        <i class="feather-mail"></i>
                                    </span>
                                    <input type="email" class="form-control floating" id="email">
                                    <label class="focus-label">Email</label>
                                </div>
                                <div class="form-wrap form-focus pass-group">
                                    <span class="form-icon">
                                        <i class="toggle-password feather-eye-off"></i>
                                    </span>
                                    <input type="password" class="pass-input form-control  floating" id="password">
                                    <label class="focus-label">Mật khẩu</label>
                                </div>
                                
                                <div class="form-wrap">
                                    <label class="custom_check mb-0">By login you agree to our <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a>
                                        <input type="checkbox" name="remeber">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="d-flex justify-content-center mb-2">
                                    <div class="g-recaptcha" data-sitekey="<?= $db->site('site_key') ?>"></div>
                                </div>
                                <button type="button" id="btnRegister" class="btn btn-primary w-100">Đăng Ký</button>
                                <?php if ($db->site('status_login_google') == 1) : ?>
                                <div class="login-or">
                                    <span class="span-or">or sign up with</span>
                                </div>
                                <ul class="login-social-link d-flex justify-content-center">
                                    <li>
                                        <a href="/login/google">
                                            <img src="/assets/images/google-icon.svg" alt="Facebook"> Google
                                        </a>
                                    </li>
                                  
                                </ul>
                                <?php endif ?>
                            </div>
                            <div class="acc-in">
                                <p>Bạn đã có tài khoản? <a href="/login">Đăng Nhập</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script type="text/javascript">
    $("#btnRegister").on("click", function() {
        $('#btnRegister').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/register",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                username: $("#username").val(),
                password: $("#password").val(),
                email: $("#email").val(),
                captcha: (typeof grecaptcha !== 'undefined' ? grecaptcha.getResponse() : '')
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
                $('#btnRegister').html('Đăng Ký').prop('disabled', false);
            },
            error: function() {
                showMessage('Không thể xử lý', 'error');
                $('#btnRegister').html('Đăng Ký').prop('disabled', false);
            }

        });
    });
</script>


<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>