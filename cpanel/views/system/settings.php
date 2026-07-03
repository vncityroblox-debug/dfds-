<?php
$title = "Cấu hình website";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
if (isset($_POST['SaveSettings']) && $data_user['level'] == 'admin') {
    foreach ($_POST as $key => $value) {
        $db->update("options", array(
            'value' => $value
        ), " `key` = '$key' ");
    }
    die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
}
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-gear"></i> Cài đặt</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-2">
                                <nav class="nav nav-tabs flex-column nav-style-5 mb-3" role="tablist">
                                    <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#cai-dat-chung" aria-selected="false"><i class="bx bx-cog me-2 align-middle d-inline-block"></i>Cài đặt chung</a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#ket-noi" aria-selected="false"><i class="bx bx-plug me-2 align-middle d-inline-block"></i>Kết nối</a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#telegram-template" aria-selected="true"><i class="fa-brands fa-telegram me-2 align-middle d-inline-block"></i>Telegram
                                        Template</a>

                                </nav>
                            </div>
                            <div class="col-xl-10">
                                <div class="tab-content">
                                    <div class="tab-pane text-muted show active" id="cai-dat-chung" role="tabpanel">
                                        <h4>Cài đặt chung</h4>
                                        <form action="" method="POST">
                                            <div class="row push mb-3">
                                                <div class="col-md-12">
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td>Title</td>
                                                                <td>
                                                                    <input type="text" name="title" value="<?= $db->site('title') ?>" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Description</td>
                                                                <td>
                                                                    <textarea name="description" class="form-control"><?= $db->site('description') ?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Keywords</td>
                                                                <td>
                                                                    <textarea name="keywords" class="form-control"><?= $db->site('keywords') ?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Author</td>
                                                                <td>
                                                                    <input type="text" name="author" value="<?= $db->site('author') ?>" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Timezone</td>
                                                                <td>
                                                                    <input type="text" name="timezone" value="Asia/Ho_Chi_Minh" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email</td>
                                                                <td>
                                                                    <input type="text" name="email" value="<?= $db->site('email') ?>" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Hotline</td>
                                                                <td>
                                                                    <input type="text" name="hotline" value="<?= $db->site('hotline') ?>" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Telegram</td>
                                                                <td>
                                                                    <input type="text" name="telegram" value="<?= $db->site('telegram') ?>" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Zalo</td>
                                                                <td>
                                                                    <input type="text" name="zalo" value="<?= $db->site('zalo') ?>" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Facebook</td>
                                                                <td>
                                                                    <input type="text" name="facebook" value="<?= $db->site('facebook') ?>" class="form-control">
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="col-md-12">
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <tbody>

                                                            <tr>
                                                                <td>Điều khoản & Điều kiện</td>
                                                                <td>
                                                                    <textarea name="terms" class="form-control" rows="5"><?= $db->site('terms') ?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Thông báo nổi</td>
                                                                <td>
                                                                    <textarea id="popup_home" name="popup_home"><?= $db->site('popup_home') ?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Nội dung trang chính sách</td>
                                                                <td>
                                                                    <textarea id="page_policy" name="page_policy"><?= $db->site('page_policy') ?></textarea>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <button type="submit" name="SaveSettings" class="btn btn-primary w-100 mb-3">
                                                <i class="fa fa-fw fa-save me-1"></i> Save </button>
                                        </form>
                                    </div>
                                    <div class="tab-pane text-muted" id="ket-noi" role="tabpanel">
                                        <h4>Kết nối</h4>
                                        <form action="" method="POST">
                                            <div class="row push mb-3">
                                                <div class="col-md-6">
                                                    <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th colspan="2" class="text-center">
                                                                    <img src="/cpanel/assets/images/icon-smtp.png" width="20px"> SMTP
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>SMTP Email</td>
                                                                <td>
                                                                    <input type="text" name="email_smtp" value="<?= $db->site('email_smtp') ?>" class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>SMTP Password</td>
                                                                <td>
                                                                    <input type="text" name="pass_email_smtp" value="<?= $db->site('pass_email_smtp') ?>" class="form-control">
                                                                    <small><a href="" target="_blank" class="text-primary">Hướng
                                                                            dẫn tích hợp SMTP</a></small>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th colspan="2" class="text-center">
                                                                    <img src="/cpanel/assets/images/google.png"
                                                                        width="20px"> Login Google                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Trạng thái</td>
                                                                <td>
                                                                     <select class="form-control" name="status_login_google">
                                                                        <option <?= $db->site('status_login_google') == 1 ? 'selected' : '' ?> value="1">ON
                                                                        </option>
                                                                        <option <?= $db->site('status_login_google') == 0 ? 'selected' : '' ?> value="0">OFF
                                                                        </option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Google App ID</td>
                                                                <td>
                                                                    <input type="text" name="google_app_id"
                                                                        placeholder="VD: G-XXXXXXXX"
                                                                        value="<?= $db->site('google_app_id') ?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Google App Secre</td>
                                                                <td>
                                                                    <input type="text" name="google_app_secret"
                                                                        placeholder="VD: G-XXXXXXXX"
                                                                        value="<?= $db->site('google_app_secret') ?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th colspan="2" class="text-center">
                                                                    <img src="/cpanel/assets/images/icon-bot-telegram.avif" width="25px"> Bot Telegram
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>BOT Telegram</td>
                                                                <td>
                                                                    <select class="form-control" name="telegram_status">
                                                                        <option <?= $db->site('telegram_status') == 1 ? 'selected' : '' ?> value="1">ON
                                                                        </option>
                                                                        <option <?= $db->site('telegram_status') == 0 ? 'selected' : '' ?> value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Telegram Token</td>
                                                                <td>
                                                                    <input type="text" name="telegram_token" value="<?= $db->site('telegram_token') ?>" class="form-control">
                                                                    <small><a class="text-primary" href="" target="_blank">Xem hướng dẫn</a></small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Telegram Chat ID</td>
                                                                <td>
                                                                    <input type="text" name="telegram_chat_id" value="<?= $db->site('telegram_chat_id') ?>" class="form-control">
                                                                    <small><a class="text-primary" href="" target="_blank">Xem hướng dẫn</a></small>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th colspan="2" class="text-center">
                                                                    <img src="/cpanel/assets/images/google_recaptcha.png" width="20px"> reCAPTCHA
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>reCAPTCHA</td>
                                                                <td>
                                                                    <select class="form-control" name="status_captcha">
                                                                        <option <?= $db->site('status_captcha') == 1 ? 'selected' : '' ?> value="1">ON
                                                                        </option>
                                                                        <option <?= $db->site('status_captcha') == 0 ? 'selected' : '' ?> value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>reCAPTCHA Site Key</td>
                                                                <td>
                                                                    <input type="text" name="site_key" value="<?= $db->site('site_key') ?>" class="form-control">
                                                                    <small><a href="" target="_blank">Xem hướng dẫn</a></small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>reCAPTCHA Secret Key</td>
                                                                <td>
                                                                    <input type="text" name="secret_key" value="<?= $db->site('secret_key') ?>" class="form-control">
                                                                    <small><a href="" target="_blank">Xem hướng dẫn</a></small>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <button type="submit" name="SaveSettings" class="btn btn-primary w-100 mb-3">
                                                <i class="fa fa-fw fa-save me-1"></i> Save </button>
                                        </form>
                                    </div>
                                    <div class="tab-pane text-muted" id="telegram-template" role="tabpanel">
                                        <h4>Nội dung thông báo Telegram</h4>
                                        <form action="" method="POST">
                                            <div class="row push mb-3">
                                                <div class="col-md-12">
                                                    <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th colspan="2" class="text-center">
                                                                    Bỏ trống để tắt thông báo </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Thông báo nạp tiền</td>
                                                                <td>
                                                                    <textarea class="form-control mb-2" rows="3" name="noti_recharge"><?= $db->site('noti_recharge') ?></textarea>
                                                                    <ul>
                                                                        <li><b>{domain}</b> => Tên website của quý
                                                                            khách.</li>
                                                                        <li><b>{username}</b> => Tên khách hàng nạp.
                                                                        </li>
                                                                        <li><b>{method}</b> => Phương thức nạp.</li>
                                                                        <li><b>{amount}</b> => Số tiền nạp.</li>
                                                                        <li><b>{price}</b> => Thực nhận.</li>
                                                                        <li><b>{time}</b> => Thời gian.</li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Thông báo hành động</td>
                                                                <td>
                                                                    <textarea class="form-control mb-2" rows="3" name="noti_action"><?= $db->site('noti_action') ?></textarea>
                                                                    <ul>
                                                                        <li><b>{domain}</b> => Tên website của quý
                                                                            khách.</li>
                                                                        <li><b>{username}</b> => Tên thành viên.</li>
                                                                        <li><b>{action}</b> => Hành động của thành viên.
                                                                        </li>
                                                                        <li><b>{ip}</b> => Địa chỉ IP của thành viên.
                                                                        </li>
                                                                        <li><b>{time}</b> => Thời gian.</li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Thông báo rút số dư hoa hồng</td>
                                                                <td>
                                                                    <textarea class="form-control mb-2" rows="3" name="noti_affiliate_withdraw"><?= $db->site('noti_affiliate_withdraw') ?></textarea>
                                                                    <ul>
                                                                        <li><b>{domain}</b> => Tên website của quý
                                                                            khách.</li>
                                                                        <li><b>{username}</b> => Tên thành viên rút.
                                                                        </li>
                                                                        <li><b>{bank}</b> => Tên ngân hàng nhận tiền.
                                                                        </li>
                                                                        <li><b>{account_number}</b> => Số tài khoản nhận
                                                                            tiền.</li>
                                                                        <li><b>{account_name}</b> => Tên chủ tài khoản.
                                                                        </li>
                                                                        <li><b>{amount}</b> => Số dư cần rút.</li>
                                                                        <li><b>{ip}</b> => Địa chỉ IP của thành viên.
                                                                        </li>
                                                                        <li><b>{time}</b> => Thời gian.</li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <button type="submit" name="SaveSettings" class="btn btn-primary w-100 mb-3">
                                                <i class="fa fa-fw fa-save me-1"></i> Save </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    CKEDITOR.replace("page_policy");
    CKEDITOR.replace("popup_home");
    CKEDITOR.replace("terms");
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>