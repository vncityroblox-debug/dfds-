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
                                    <h3>QUÊN MẬT KHẨU</h3>
                                    <p>Chúng tôi sẽ gửi liên kết để đặt lại mật khẩu của bạn</p>
                                </div>
                                <div class="form-wrap form-focus">
                                    <span class="form-icon">
                                        <i class="feather-mail"></i>
                                    </span>
                                    <input type="email" id="email" class="form-control floating">
                                    <label class="focus-label">Email</label>
                                </div>
                               
                                <div class="d-flex justify-content-center mb-2">
                                    <div class="g-recaptcha" data-sitekey="<?= $db->site('site_key') ?>"></div>
                                </div>
                               
                                <button type="button" class="btn btn-primary w-100" id="btnForgotPassword">Xác Minh</button>
                               
                            </div>
                            
                        </div>
                       
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>

<script type="text/javascript">
    $("#btnForgotPassword").on("click", function() {
        $('#btnForgotPassword').html('<i class="fa fa-spinner fa-spin"></i> Đang kiểm tra...').prop(
            'disabled',
            true);
        $.ajax({
            url: "/model/forgotpassword",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                email: $("#email").val(),
                action: 'forgot',
                captcha: grecaptcha.getResponse()
            },
            success: function(respone) {
                if (respone.status == 'success') {
                    Swal.fire({
                        title: 'Successful !',
                        text: respone.msg,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {

                        }
                    });
                } else {
                    Swal.fire('Failure!', respone.msg, 'error');
                }
                $('#btnForgotPassword').html(
                        'Xác Minh')
                    .prop('disabled', false);
            },
            error: function() {
                showMessage('Không thể xử lý', 'error');
                $('#btnForgotPassword').html(
                        'Xác Minh')
                    .prop('disabled', false);
            }

        });
    });
</script>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>