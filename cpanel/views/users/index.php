<?php
$title = "Dashboard";
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');
use PragmaRX\Google2FA\Google2FA;
$google2fa = new Google2FA();
if (isset($_POST['AddUser']) && $data_user['level'] == 'admin') {
    $user = Anti_xss($_POST['username']);
    $email = Anti_xss($_POST['email']);
    if ($db->num_rows("SELECT * FROM `users` WHERE `username` = '{$user}'") > 0){
        die('<script type="text/javascript">if(!alert("Tài khoản đã tồn tại !")){window.history.back().location.reload();}</script>');
    }
    if ($db->num_rows("SELECT * FROM `users` WHERE `email` = '{$email}'") > 0){
        die('<script type="text/javascript">if(!alert("Email đã tồn tại !")){window.history.back().location.reload();}</script>');
    }
    
    $isInsert = $db->insert("users", array(
        'username'         => Anti_xss($_POST['username']),
        'password'         => sha1(Anti_xss($_POST['password'])),
        'email'         => Anti_xss($_POST['email']),
        'device'=>$_SERVER['HTTP_USER_AGENT'],
        'ip' => myip(),
        'ref_id'        => !empty($_SESSION['ref']) ? $_SESSION['ref'] : 0,
        'token' => md5(random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 6).time()),
        'secretkey' => $google2fa->generateSecretKey(),
        'time_all_bank' => time(),
        'time_session' => time(),
        'create_date' => gettime()
    ));
    if($isInsert){
        die('<script type="text/javascript">if(!alert("Thêm thành công !")){window.history.back().location.reload();}</script>');
    }else{
        die('<script type="text/javascript">if(!alert("Thêm thất bại !")){window.history.back().location.reload();}</script>');
    }
}

$sotin1trang = 10;
if (isset($_GET['page'])) {
    $page = Anti_xss(intval($_GET['page']));
} else {
    $page = 1;
}

$from = ($page - 1) * $sotin1trang;
$where = ' `id` > 0 ';
$order_by = 'ORDER BY id DESC';
$username = '';
$email = '';
$content = '';
$money = '';
$limit = '';
$ip = '';
$userid = '';
$device = '';
$createdate = '';
$money = '';
$status = '';
if (!empty($_GET['user_id'])) {
    $userid = Anti_xss($_GET['user_id']);
    $where .= ' AND `id` = ' . $userid . ' ';
}
if (!empty($_GET['username'])) {
    $username = Anti_xss($_GET['username']);
    $dataUser = $db->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
    $where .= ' AND `id` LIKE "' . $dataUser['id'] . '" ';
}
if (!empty($_GET['email'])) {
    $email = Anti_xss($_GET['email']);
    $where .= ' AND `email` LIKE "%' . $email . '%" ';
}
if (!empty($_GET['ip'])) {
    $ip = Anti_xss($_GET['ip']);
    $where .= ' AND `ip` LIKE "%' . $ip . '%" ';
}
if (!empty($_GET['content'])) {
    $content = Anti_xss($_GET['content']);
    $where .= ' AND `action` LIKE "%' . $content . '%" ';
}
if (!empty($_GET['device'])) {
    $device = Anti_xss($_GET['device']);
    $where .= ' AND `device` LIKE "%' . $content . '%" ';
}
if (!empty($_GET['limit'])) {
    $limit = Anti_xss($_GET['limit']);
    $sotin1trang = $limit;
}

if (!empty($_GET['createdate'])) {
    $createdate = Anti_xss($_GET['createdate']);
    $create_date_1 = $createdate;
    $create_date_1 = explode(' to ', $create_date_1);
    if ($create_date_1[0] != $create_date_1[1]) {
        $create_date_1 = [$create_date_1[0] . ' 00:00:00', $create_date_1[1] . ' 23:59:59'];
        $where .= " AND `create_date` >= '" . $create_date_1[0] . "' AND `create_date` <= '" . $create_date_1[1] . "' ";
    }
}


if (!empty($_GET['status'])) {
    $status = Anti_xss($_GET['status']);
    if ($status == 1) {
        $where .= ' AND `banned` = 0 ';
    } else if ($status == 2) {
        $where .= ' AND `banned` = 1 ';
    }
}

if (!empty($_GET['money'])) {
    $money = Anti_xss($_GET['money']);
    if ($money == 1) {
        $order_by = ' ORDER BY `money` ASC ';
    } else if ($money == 2) {
        $order_by = ' ORDER BY `money` DESC ';
    }
}

$listUsers = $db->get_list("SELECT * FROM `users` WHERE $where $order_by LIMIT $from,$sotin1trang ");
active_license()
?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-users"></i> Users</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DANH SÁCH THÀNH VIÊN
                        </div>
                        <div class="d-flex">
                            <button data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2" class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light"><i class="ri-add-line fw-semibold align-middle"></i> Thêm thành viên</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" type="number" value="<?= $userid ?>" name="user_id" placeholder="ID Khách hàng">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" type="text" value="<?= $username ?>" name="username" placeholder="Username">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $email ?>" name="email" placeholder="Email">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input class="form-control" value="<?= $ip ?>" name="ip" placeholder="Địa chỉ IP">
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <select name="status" class="form-control">
                                        <option value="">Trạng thái
                                        </option>
                                        <option <?= $status == 2 ? 'selected' : '' ?> value="2">Banned
                                        </option>
                                        <option <?= $status == 1 ? 'selected' : '' ?> value="1">Active
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg col-md-4 col-6">
                                    <select name="money" class="form-control">
                                        <option value="">Sắp xếp số dư
                                        </option>
                                        <option <?= $money == 1 ? 'selected' : '' ?> value="1">Tăng dần
                                        </option>
                                        <option <?= $money == 2 ? 'selected' : '' ?> value="2">Giảm dần
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hero btn-primary"><i class="fa fa-search"></i>
                                        Search </button>
                                    <a class="btn btn-hero btn-danger" href="/cpanel/users/list"><i class="fa fa-trash"></i>
                                        Clear filter </a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="5">5</option>
                                        <option selected value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label">Short by Date:</label>
                                    <select name="shortByDate" onchange="this.form.submit()" class="form-select filter-select">
                                        <option value="">Tất cả</option>
                                        <option value="1">Hôm nay </option>
                                        <option value="2">Tuần này </option>
                                        <option value="3">
                                            Tháng này </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive table-wrapper mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input" name="check_all" id="check_all_checkbox_users" value="option1">
                                            </div>
                                        </th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Email</th>
                                        <th scope="col" class="text-center">Ví</th>
                                        <th scope="col" class="text-center">Admin</th>
                                        <th scope="col" class="text-center">Trạng thái</th>
                                        <th scope="col" class="text-center">Loại TK</th>
                                        <th scope="col">Thời gian</th>
                                        <th scope="col" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    foreach ($listUsers as $row) : ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check form-check-md d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input checkbox_users" data-id="<?= $row['id'] ?>" name="checkbox_users" value="<?= $row['id'] ?>" />
                                                </div>
                                            </td>
                                            <td><a class="text-primary" href="/cpanel/user/edit/<?= $row['id'] ?>"><?= $row['username'] ?> [ID <?= $row['id'] ?>]</a>
                                            </td>
                                            <td>
                                                <i class="fa fa-envelope" aria-hidden="true"></i> <?= $row['email'] ?>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge bg-primary-gradient"><?= format_cash($row['money']) ?>đ</span>
                                            </td>
                                            <td class="text-center"><?=$row['level'] == 'admin' ? '<span class="badge bg-success">Admin</span>':'<span class="badge bg-danger">Không</span>'?></td>
                                            <td class="text-center"><?=$row['banned'] == 1 ?'<span class="badge bg-danger">Banned</span>':'<span class="badge bg-success">Active</span>'?></td>
                                            <td class="text-center"><?= $row['provider'] == "google" ? 'Google' : 'Tài khoản' ?></td>
                                            <td><span><?= $row['create_date'] ?></span></td>
                                            <td class="text-center fs-base">
                                                <a href="/cpanel/user/edit/<?= $row['id'] ?>" class="btn btn-sm btn-primary shadow-primary btn-wave" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-fw fa-edit"></i> Edit
                                                </a>
                                                <a type="button" onclick="confirmAction(<?= $row['id'] ?>)" class="btn btn-sm btn-danger shadow-danger btn-wave" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <td colspan="9">
                                        <div class="btn-list">
                                            <button type="button" onclick="confirmDeleteAccount()" class="btn btn-outline-danger shadow-danger btn-wave btn-sm"><i class="fa-solid fa-trash"></i> XÓA THÀNH VIÊN</button>
                                        </div>
                                    </td>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">

                            <div class="col-sm-12 col-md-12 mb-3">
                                <div class="pagination-style-1">
                                    <div class="d-flex justify-content-center">
                                        <?php
                                        $total = $db->num_rows("SELECT * FROM `users` WHERE $where");
                                        if ($total > $sotin1trang) {
                                            echo '<center>' . pagination("/cpanel/users/list?user_id=$userid&username=$username&email=$email&ip=$ip&status=$status&money=$money&limit=$limit&shortByDate=&", $from, $total, $sotin1trang) . '</center>';
                                        } ?>
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


<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2"
    data-bs-keyboard="false" aria-hidden="true">
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2">Thêm thành viên mới</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Username</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="username"
                                placeholder="Please enter your username" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Password</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="password"
                                placeholder="Please enter your password" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Email</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" name="email"
                                placeholder="Please enter your email address" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="AddUser" class="btn btn-primary btn-sm"><i
                            class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    function logoutALL() {
        cuteAlert({
            type: "question",
            title: "WARNING",
            message: "The system will exit the login of all accounts, except for the Admin account, do you agree?",
            confirmText: "Agree",
            cancelText: "Close"
        }).then((e) => {
            if (e) {
                $('#logoutALL').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
                $.ajax({
                    url: "",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: "logoutALL"
                    },
                    success: function(respone) {
                        if (respone.status == 'success') {
                            showMessage('error', respone.msg);
                            $('#logoutALL').html(
                                '<i class="fas fa-right-from-bracket mr-1"></i>THOÁT TẤT CẢ').prop(
                                'disabled', false);
                        } else {
                            showMessage('error', respone.msg);
                        }
                    },
                    error: function() {
                        alert(html(response));
                        location.reload();
                    }
                });
            }
        })
    }
    const confirmAction = (id) => {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện xóa thành viên "+ id,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await Item(id);
            }
        });
    }

    const Item = async (id) => {
        Swal.fire({
            icon: "info",
            title: "Đang xử lý!",
            html: "Không được tắt trang này, vui lòng đợi trong giây lát!",
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
            willClose: () => {},
        });

        $.ajax({
            url: '/model/admin/delete',
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'removeUser',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Thất Bại', result.msg, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Thất Bại', xhr.responseText, 'error');
            }
        });
    }


    function postRemoveAccount(id) {
        $.ajax({
            url: "/model/admin/delete",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'removeUser',
                id: id
            },
            success: function(response) {
                if (response.status == 'success') {
                    showMessage('success', 'Mục đã được xóa thành công ' + id);
                } else {
                    showMessage('error', 'Đã xảy ra lỗi khi xóa mục ' + id);
                }
            }
        });
    }

    function confirmDeleteAccount() {
        var checkbox = document.getElementsByName('checkbox_users');
        var isAnyCheckboxChecked = false;
        for (var i = 0; i < checkbox.length; i++) {
            if (checkbox[i].checked === true) {
                isAnyCheckboxChecked = true;
                break;
            }
        }
        if (!isAnyCheckboxChecked) {
            alert('Lỗi: Vui lòng chọn ít nhất một bản ghi.');
            return;
        }
        var result = confirm('Bạn có đồng ý xóa các bản ghi đã chọn không?');
        if (result) {
            function postUpdatesSequentially(index) {
                if (index < checkbox.length) {
                    if (checkbox[index].checked === true) {
                        postRemoveAccount(checkbox[index].value);
                    }
                    setTimeout(function() {
                        postUpdatesSequentially(index + 1);
                    }, 100);
                } else {
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            }
            postUpdatesSequentially(0);
        }
    }

    $(function() {
        $('#check_all_checkbox_users').on('click', function() {
            $('.checkbox_users').prop('checked', this.checked);
        });
        $('.checkbox_users').on('click', function() {
            $('#check_all_checkbox_users').prop('checked', $('.checkbox_users:checked')
                .length === $('.checkbox_users').length);
        });
    });
</script>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');

?>