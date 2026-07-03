<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
$title = 'Thay đổi mật khẩu';
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
                            <h4>Thay đổi mật khẩu</h4>
                        </div>
                        <div class="settings-card-body">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div>
                                        <label for="fname" class="form-label">Mật Khẩu Cũ<span
                                                class="text-lime-300">*</span></label>
                                        <input type="password" class="form-control shadow-none"
                                            id="input_old_password" name="input_old_password" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div>
                                        <label for="fname" class="form-label">Mật Khẩu Mới<span
                                                class="text-lime-300">*</span></label>
                                        <input type="password" class="form-control shadow-none"
                                            id="input_new_password" name="input_new_password" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div>
                                        <label for="fname" class="form-label">Xác Nhận Mật Khẩu<span
                                                class="text-lime-300">*</span></label>
                                        <input type="password" class="form-control shadow-none"
                                            id="input_confirm_password" name="input_confirm_password"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="button" id="btnChangePassword"
                                        class="btn btn-primary">
                                        Thay Đổi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script type="text/javascript">
    $("#btnChangePassword").on("click", function() {
        $('#btnChangePassword').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...')
            .prop('disabled',
                true);
        $.ajax({
            url: "/model/update/password",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                current_password: $("#input_old_password").val(),
                new_password: $("#input_new_password").val(),
                confirm_password: $("#input_confirm_password").val(),
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Successful!', result.msg, 'success');
                    setTimeout("location.href = '/login';", 1000);
                } else {
                    Swal.fire('Failure!', result.msg, 'error');
                }
                $('#btnChangePassword').html(
                    'Thay Đổi'
                ).prop('disabled',
                    false);
            },
            error: function() {
                showMessage('Không thể xử lý', 'error');
                $('#btnChangePassword').html(
                    'Thay Đổi'
                ).prop('disabled',
                    false);
            }

        });
    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>