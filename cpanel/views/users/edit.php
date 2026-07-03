<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_GET['id']) && $data_user['level'] == 'admin') {
    $user = $db->get_row(" SELECT * FROM `users` WHERE `id` = '" . Anti_xss($_GET['id']) . "'  ");
    if (!$user) {
        new Redirect('/cpanel/users/list');
    }
} else {
    new Redirect('/cpanel/users/list');
}
if (isset($_POST['btnCongTien']) && $data_user['level'] == 'admin') {
    $value = Anti_xss($_POST['amount']);
    $ghichu = Anti_xss($_POST['reason']);
    $wallet = Anti_xss($_POST['wallet']);
    if ($value <= 0) {
        die('<script type="text/javascript">if(!alert("Số tiền nhập không hợp lệ!")){window.history.back().location.reload();}</script>');
    }
    if($wallet == 2){
        $db->cong("users", "wallet", $value, " `username` = '" . $user['username'] . "' ");
        PlusCredits($user['id'],$value,$ghichu);
    }else{
        PlusCredits($user['id'],$value,$ghichu);
    }
    die('<script type="text/javascript">if(!alert("Cộng tiền thành công!")){window.history.back().location.reload();}</script>');
}

if (isset($_POST['btnTruTien']) && $data_user['level'] == 'admin') {
    $value = Anti_xss($_POST['amount']);
    $ghichu = Anti_xss($_POST['reason']);
    if ($value <= 0) {
        die('<script type="text/javascript">if(!alert("Số tiền nhập không hợp lệ!")){window.history.back().location.reload();}</script>');
    }
    RemoveCredits($user['id'],$value,$ghichu);
    die('<script type="text/javascript">if(!alert("Trừ tiền thành công!")){window.history.back().location.reload();}</script>');
}
if (isset($_POST['btnSaveUser']) && $data_user['level'] == 'admin') {

    $isUpdate = $db->update("users", array(
        'username' => Anti_xss($_POST['username']),
        'level' => Anti_xss($_POST['level']),
        'ctv' => Anti_xss($_POST['ctv']),
        'banned' => Anti_xss($_POST['banned']),
        'token' => Anti_xss($_POST['token']),
        'email' => Anti_xss($_POST['email']),
        'status_2fa' => Anti_xss($_POST['status_2fa']),
        'chietkhau' => Anti_xss($_POST['chietkhau']),
        'login_attempts' => 0
    ), " `id` = '" . $user['id'] . "' ");
    if ($isUpdate) {
        if (!empty($_POST['password'])) {
            $password = Anti_xss($_POST['password']);
            $db->update("users", array(
                'password' => sha1($password),
            ), " `id` = '" . $user['id'] . "' ");
        }
        die('<script type="text/javascript">if(!alert("Cập nhật thông tin thành công")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("Cập nhật thông tin thất bại")){window.history.back().location.reload();}</script>');
    }
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><a type="button" class="btn btn-dark btn-raised-shadow btn-wave btn-sm me-1" href="/cpanel/users/list"><i class="fa-solid fa-arrow-left"></i></a> Chỉnh sửa thành viên <?= $user['username'] ?></h1>
        </div>
        <div class="row gx-5 mb-5">
            <div class="col-12">
                <div class="mt-4 mt-md-0">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-addCredit" class="btn btn-sm btn-wave btn-success me-1 mb-3 push">
                        <i class="fa fa-fw fa-plus"></i> Cộng số dư
                    </button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-removeCredit" class="btn btn-sm btn-wave btn-danger me-1 mb-3 push">
                        <i class="fa fa-fw fa-minus"></i> Trừ số dư
                    </button>
                    <a type="button" href="/cpanel/logs?user_id=<?=$user['id']?>" target="_blank" class="btn btn-sm btn-wave btn-primary me-1 mb-3 push">
                        <i class="fa fa-fw fa-history"></i> Nhật ký hoạt động
                    </a>
                    <a type="button" href="/cpanel/transactions?user_id=<?=$user['id']?>" target="_blank" class="btn btn-sm btn-wave btn-info me-1 mb-3 push">
                        <i class="fa fa-fw fa-history"></i> Biến động số dư
                    </a>
                </div>
            </div>
            <div class="col-12">
                <div class="card custom-card shadow-none mb-0">
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Username (<span class="text-danger">*</span>)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['username'] ?>" name="username" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Email (<span class="text-danger">*</span>)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control" value="<?= $user['email'] ?>" name="email" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Token (<span class="text-danger">*</span>)</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="text" class="form-control" value="<?= $user['token'] ?>" name="token" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">Mật khẩu (<span class="text-danger">*</span>)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-key"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="**********" name="password">
                                        </div>
                                        <i>Nhập mật khẩu cần thay đổi, hệ thống sẽ tự động mã hóa (bỏ trống nếu không muốn thay đổi)</i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">Secret Key Google 2FA</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class='bx bx-key'></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['secretkey'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">ON/OFF Google 2FA (<span class="text-danger">*</span>)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class='bx bxs-key'></i>
                                            </span>
                                            <select class="form-control select2bs4" name="status_2fa">
                                                <option <?= $user['status_2fa'] == 1 ? 'selected' : '' ?> value="1">
                                                    ON
                                                </option>
                                                <option <?= $user['status_2fa'] == 0 ? 'selected' : '' ?> value="0">
                                                    OFF</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                    <div class="mb-4">
                                        <label class="form-label">Chiết khấu giảm giá (<span class="text-danger">*</span>)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-percent"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['chietkhau'] ?>" name="chietkhau">
                                        </div>
                                    </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">Admin Role (<span class="text-danger">*</span>)</label>
                                        <select class="form-control select2bs4" name="level">
                                            <option <?= $user['level'] == 'member' ? 'selected' : '' ?> value="member">
                                                User (Khách
                                                hàng)
                                            </option>
                                            <option <?= $user['level'] == 'admin' ? 'selected' : '' ?> value="admin">
                                                Super Admin (Admin Role)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">CTV Bán Code (<span class="text-danger">*</span>)</label>
                                        <select class="form-control select2bs4" name="ctv">
                                            <option <?= $user['ctv'] == '0' ? 'selected' : '' ?> value="0">
                                                Không
                                            </option>
                                            <option <?= $user['ctv'] == '1' ? 'selected' : '' ?> value="1">
                                                Có</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <div class="mb-4">
                                            <label class="form-label">Banned (<span class="text-danger">*</span>)</label>
                                            <select class="form-control select2bs4" name="banned">
                                                <option <?= $user['banned'] == '1' ? 'selected' : '' ?> value="1">
                                                    Banned
                                                </option>
                                                <option <?= $user['banned'] == '0' ? 'selected' : '' ?> value="0">
                                                    Live</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">Money</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-wallet"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['money'] ?>đ" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">Total Money</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-money-bill"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['total_money'] ?>đ" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="form-label">Used Money</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class='bx bxs-wallet-alt'></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['total_money'] - $user['money'] ?>đ" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">IP Login</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-wifi"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['ip'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Device Login</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-desktop"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['device'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">First Login</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-calendar-days"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= $user['create_date'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Last Login</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-calendar-days"></i>
                                            </span>
                                            <input type="text" class="form-control" value="<?= date('Y-m-d H:i:s', $user['time_session']) ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a type="button" class="btn btn-danger" href="/cpanel/users/list"><i class="fa fa-fw fa-undo"></i> Back</a>
                            <button type="submit" class="btn btn-primary" name="btnSaveUser"><i class="bi bi-download"></i>
                                Save</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal-addCredit" tabindex="-1" aria-labelledby="modal-block-popout" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel2"><i class="fa fa-plus"></i> CỘNG SỐ DƯ
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="notice_debit" class="alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm" style="display: none;" role="alert">
                        Khi chọn <b>VÍ GHI NỢ</b>, số dư sẽ được cộng trước cho user trong trường hợp auto bank deplay, khi
                        auto bank hoạt động trở lại, hệ thống sẽ tự động trừ lại số tiền đã cộng.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Loại ví:</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="wallet">
                                <option value="1">VÍ CHÍNH</option>
                                <option value="2">VÍ GHI NỢ</option>
                            </select>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var selectWallet = document.querySelector('select[name="wallet"]');
                            var noticeDebit = document.getElementById('notice_debit');

                            selectWallet.addEventListener('change', function() {
                                if (this.value === "2") {
                                    noticeDebit.style.display = 'block';
                                } else {
                                    noticeDebit.style.display = 'none';
                                }
                            });
                        });
                    </script>

                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Amount:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="amount" placeholder="Nhập số tiền cần cộng" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Lý do (nếu có):</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="reason"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hero btn-danger" data-bs-dismiss="modal"><i class="fa fa-fw fa-times me-1"></i> Close</button>
                    <button type="submit" name="btnCongTien" class="btn btn-hero btn-success"><i class="fa fa-fw fa-plus me-1"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-removeCredit" tabindex="-1" aria-labelledby="modal-block-popout" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel2"><i class="fa fa-minus"></i> Balance </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Amount</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="amount" placeholder="Please enter the amount to be deducted" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Reason</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="reason"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hero btn-danger" data-bs-dismiss="modal"><i class="fa fa-fw fa-times me-1"></i> Close</button>
                    <button type="submit" name="btnTruTien" class="btn btn-hero btn-success"><i class="fa fa-fw fa-minus me-1"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>