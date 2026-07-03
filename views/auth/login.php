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
                                    <h3>Đăng Nhập Tài Khoản</h3>
                                    <p>Điền vào các trường để vào tài khoản của bạn</p>
                                </div>
                                <div class="form-wrap form-focus">
                                    <span class="form-icon">
                                        <i class="feather-mail"></i>
                                    </span>
                                    <input type="text" id="username" class="form-control floating">
                                    <label class="focus-label">Tài khoản</label>
                                </div>
                                <div class="form-wrap form-focus pass-group">
                                    <span class="form-icon">
                                        <i class="toggle-password feather-eye-off"></i>
                                    </span>
                                    <input type="password" id="password" class="pass-input form-control  floating">
                                    <label class="focus-label">Mật khẩu</label>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="">
                                        <div class="form-wrap">
                                            <label class="custom_check mb-0">Lưu phiên đăng nhập
                                                <input type="checkbox" id="remember" name="remember">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="form-wrap text-md-end">
                                            <a href="/forgot-password" class="forgot-link">Quên mật khẩu?</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mb-2">
                                    <div class="g-recaptcha" data-sitekey="<?= $db->site('site_key') ?>"></div>
                                </div>
                                <div class="form-wrap mantadory-info d-none">
                                    <p><i class="feather-alert-triangle"></i>Fill all the fields to submit</p>
                                </div>
                                <button type="button" class="btn btn-primary w-100" id="btnLoginPage">Đăng Nhập</button>
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
                                <p>Không có tài khoản ?
                                <a href="/register"> Tạo tài khoản </a></p>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>
<script type="text/javascript">
    $("#btnLoginPage").on("click", function() {
        $('#btnLoginPage').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/login",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                username: $("#username").val(),
                password: $("#password").val(),
                remember: $('#remember').is(':checked'),
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
                            location.href = '/';
                        }
                    });
                    setTimeout("location.href = '/';", 2000);
                } else if (respone.status == 'verify') {
                    Swal.fire('Warning!', respone.msg, 'warning');
                    setTimeout("location.href = '" + respone.url + "';", 2000);
                } else {
                    Swal.fire('Failure!', respone.msg, 'error');
                }
                $('#btnLoginPage').html('Đăng Nhập').prop('disabled', false);
            },
            error: function() {
                showMessage('Vui lòng liên hệ Developer', 'error');
                $('#btnLoginPage').html('Đăng Nhập').prop('disabled', false);
            }

        });
    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>