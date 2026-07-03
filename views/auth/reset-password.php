<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
$title = $db->site('title');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

 if(empty($_GET['token'])){
    new Redirect('/');
}
$token = Anti_xss($_GET['token']);
$getUser = $db->get_row("SELECT * FROM `users` WHERE `otp` = '".$token."'");
if (!$getUser || $getUser['otp'] == NULL) {
    // Nếu không tìm thấy người dùng hoặc otp là NULL, thực hiện chuyển hướng
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
                                    <h3>ĐẶT LẠI MẬT KHẨU</h3>
                                </div>
                                <div class="form-wrap form-focus">
                                    <span class="form-icon">
                                        <i class="feather-mail"></i>
                                    </span>
                                    <input type="password" id="newpassword" class="form-control floating">
                                    <label class="focus-label">Mật khẩu</label>
                                </div>
                                <div class="form-wrap form-focus">
                                    <span class="form-icon">
                                        <i class="feather-mail"></i>
                                    </span>
                                    <input type="password" id="renewpassword" class="form-control floating">
                                    <label class="focus-label">Xác nhận mật khẩu</label>
                                </div>
                              
                                <button class="btn btn-primary w-100" id="btnReset" type="button">Thay Đổi</button>
                               
                            </div>
                            
                        </div>
                       
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>
<script type="text/javascript">
    $("#btnReset").on("click", function() {
        $('#btnReset').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/forgotpassword",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                newpassword: $("#newpassword").val(),
                renewpassword: $("#renewpassword").val(),
                otp: '<?= isset($getUser['otp']) ? $getUser['otp'] : '' ?>',
                action: 'change'
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
                            location.href = '/login';
                        }
                    });
                    setTimeout("location.href = '/login';", 2000);
                } else {
                    Swal.fire('Failure!', respone.msg, 'error');
                }
                $('#btnReset').html('Thay Đổi').prop('disabled', false);
            },
            error: function() {
                showMessage('Vui lòng liên hệ Developer', 'error');
                $('#btnReset').html('Thay Đổi').prop('disabled', false);
            }

        });
    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>