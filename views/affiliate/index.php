<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
$title = 'Tiếp thị liên kết';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
if ($db->site('status_ref') != 1) {
    new Redirect('/');
    exit;
}
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$conditions = ["`user_id` = '{$data_user['id']}'"];

$where = implode(" AND ", $conditions);
$total_withdraw = $db->get_row("SELECT COUNT(*) as total FROM withdraw_ref WHERE $where ORDER BY `id` DESC")['total'] ?? 0;
$withdraws = $db->get_list("SELECT * FROM withdraw_ref WHERE $where ORDER BY `id` DESC LIMIT $limit OFFSET $offset");
$total_log = $db->get_row("SELECT COUNT(*) as total FROM log_ref WHERE $where ORDER BY `id` DESC")['total'] ?? 0;
$logs = $db->get_list("SELECT * FROM log_ref WHERE $where ORDER BY `id` DESC LIMIT $limit OFFSET $offset");
$url = "/affiliates?&";
?>
<section class="py-110 bg-offWhite">
        <div class="container">
            <div class="rounded-3">
                <div class="row mb-3">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div
                            class="p-3 d-flex align-items-center dashobard-widget justify-content-between bg-white rounded shadow-sm">
                            <div>
                                <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                    <?= format_cash($data_user['ref_money']); ?>đ
                                </h3>
                                <p class="text-18 text-dark-200">Hoa hồng khả dụng</p>
                            </div>
                            <div class="dashboard-widget-icon">
                                <img src="/assets/images/total.png" width="75" height="71" />
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div
                            class="p-3 d-flex align-items-center dashobard-widget justify-content-between bg-white rounded shadow-sm">
                            <div>
                                <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                    <?= format_cash($data_user['ref_total_money']); ?>đ
                                </h3>
                                <p class="text-18 text-dark-200">Hoa hồng đã nhận</p>
                            </div>
                            <div class="dashboard-widget-icon">
                                <img src="/assets/images/total.png" width="75" height="71" />
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div
                            class="p-3 d-flex align-items-center dashobard-widget justify-content-between bg-white rounded shadow-sm">
                            <div>
                                <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                    <?= format_cash($db->get_row(" SELECT COUNT(id) FROM `users` WHERE `ref_id` = '" . $data_user['id'] . "' ")['COUNT(id)']); ?>                                </h3>
                                <p class="text-18 text-dark-200">Đã giới thiệu</p>
                            </div>
                            <div class="dashboard-widget-icon">
                                <img src="/assets/images/total_active.png" width="75" height="71" />
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div
                            class="p-3 d-flex align-items-center dashobard-widget justify-content-between bg-white rounded shadow-sm">
                            <div>
                                <h3 class="dashboard-widget-title fw-bold text-dark-300">
                                   <?= format_cash($data_user['ref_click']); ?>                               </h3>
                                <p class="text-18 text-dark-200">Lượt click</p>
                            </div>
                            <div class="dashboard-widget-icon">
                                <img src="/assets/images/total_pause.png" width="75" height="71" />
                            </div>
                        </div>
                    </div>
                </div>
                <section class="space-y-6">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="profile-info-card">
                                <!-- Header -->
                                <div class="profile-info-header">
                                    <h4 class="text-18 fw-semibold text-dark-300">
                                        HƯỚNG DẪN SỬ DỤNG
                                    </h4>
                                </div>
                                <div class="profile-info-body bg-white">
                                    <?= $db->site('notice_ref') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="profile-info-card">
                                <!-- Header -->
                                <div class="profile-info-header">
                                    <h4 class="text-18 fw-semibold text-dark-300">
                                        THÔNG TIN GIỚI THIỆU
                                    </h4>
                                </div>
                                <div class="profile-info-body bg-white">
                                    <div class="text-danger fw-bold mb-3">Bạn sẽ nhận được <?= $db->site('ck_ref') ?>%
                                        hoa hồng khi người dùng
                                        bạn giới thiệu nạp tiền vào tài khoản.</div>
                                    <div>
                                        <label for="referral_code" class="form-label">Liên Kết Giới Thiệu:</label>
                                        <div class="input-group">
                                            <input type="text" id="referral_code" name="referral_code"
                                                class="form-control form-control-sm shadow-none"
                                                value="<?= DOMAIN ?>/reffer/<?= $data_user['id'] ?>"
                                                style="border-radius: 5px 0 0 5px" readonly="">
                                            <button class="btn btn-primary copy" data-clipboard-target="#referral_code"
                                                style="border-radius: 0 5px 5px 0" type="button"><i
                                                    class="fas fa-copy"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="profile-info-card">
                                <!-- Header -->
                                <div class="profile-info-header">
                                    <h4 class="text-18 fw-semibold text-dark-300">
                                        YÊU CẦU RÚT TIỀN
                                    </h4>
                                </div>
                                <div class="profile-info-body bg-white">
                                    <form id="form-withdraw" method="POST">
                                        <div class="mb-5">
                                            <div class="text-danger fw-bold">
                                                <i>
                                                    Số tiền có thể rút: từ <span class="text-success"><?= format_cash($db->site('minrut_ref')) ?>đ</span></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="amount" class="form-label">Số Tiền Rút</label>
                                            <input type="number" class="form-control shadow-none" id="amount" name="amount"
                                                value="<?= $db->site('minrut_ref')?>" required="">
                                        </div>
                                       
                                        <div class="mb-4 group_banking">
                                            <label for="bank" class="form-label">Ngân Hàng</label>
                                            <select name="bank" id="bank" class="form-control shadow-none">
                                                <option value="">Chọn Ngân Hàng Rút</option>
                                                 <?php $listbank = explode(PHP_EOL, $db->site('listbank_ref')); ?>
                                                                        <?php foreach($listbank as $value):?>
                                                                        <option value="<?=$value;?>"><?=$value;?></option>
                                                                        <?php endforeach?>
                                                                                            </select>
                                        </div>
                                        <div class="row mb-4 group_banking">
                                            <div class="col-md-6">
                                                <label for="account_number" class="form-label">Số Tài Khoản</label>
                                                <input type="text" class="form-control shadow-none" id="stk"
                                                    name="stk" value="" placeholder="Nhập số tài khoản">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Chủ Tài Khoản</label>
                                                <input type="text" class="form-control shadow-none" id="name"
                                                    name="name" value="" placeholder="Nhập tên chủ tài khoản">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <button class="btn btn-primary" type="button" id="btnWithdraw">Rút
                                                Tiền Ngay</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <aside class="d-flex flex-column gap-4">
                                <div>
                                    <nav>
                                        <div class="nav package-tabs d-flex gap-4 align-items-center" id="nav-tab"
                                            role="tablist">
                                            <button class="package-tab-btn active" id="nav-basic-tab"
                                                data-bs-toggle="tab" data-bs-target="#nav-basic" type="button"
                                                role="tab" aria-controls="nav-basic" aria-selected="true">
                                                Lịch sử nhận hoa hồng
                                            </button>
                                            <button class="package-tab-btn" id="nav-standard-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-standard" type="button" role="tab"
                                                aria-controls="nav-standard" aria-selected="false"
                                                tabindex="-1">
                                                Lịch sử rút tiền
                                            </button>
                                        </div>
                                    </nav>
                                    <div class="package-tab-content bg-white">
                                        <div class="tab-content" id="nav-tabContent">
                                            <!-- Basic -->
                                            <div class="tab-pane fade active show" id="nav-basic" role="tabpanel"
                                                aria-labelledby="nav-basic-tab" tabindex="0">
                                                <div class="overflow-x-auto">
                                                    <div class="w-100">
                                                        <table id="example" class="w-100 dashboard-table">
                                                            <thead class="pb-3">
                                                                <tr>
                                                                    <th scope="col">
                                                                        ID
                                                                    </th>
                                                                    <th scope="col">
                                                                        Hoa hồng ban đầu
                                                                    </th>
                                                                    <th scope="col">
                                                                        Hoa hồng thay đổi
                                                                    </th>
                                                                    <th scope="col">
                                                                        Hoa hồng hiện tại
                                                                    </th>
                                                                    <th scope="col">
                                                                        Thời gian
                                                                    </th>

                                                                    <th scope="col">
                                                                        Lý do
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                           <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $aff): ?>
                                                                <tr>
                                                                <td>
                                                                <?= $aff['id'] ?>
                                                            </td>
                                                            <td class="text-dark">
                                                                <?= format_cash($aff['sotientruoc']) ?> ₫
                                                            </td>
                                                            <td>
                                                                <span class="text-green-600"><?= format_cash($aff['sotienthaydoi']) ?>
                                                                    ₫</span>
                                                            </td>
                                                            <td>
                                                                <?= format_cash($aff['sotienhientai']) ?> ₫
                                                            </td>

                                                            <td>
                                                                <?= $aff['created_at'] ?>
                                                            </td>
                                                            <td>
                                                                <?= $aff['reason'] ?>
                                                            </td>


                                                        </tr>
                                                            </tr>
                                        <?php endforeach; ?>
                                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
                        <div class="empty-state">
                                <svg width="184" height="152" viewBox="0 0 184 152" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd">
                                        <g transform="translate(24 31.67)">
                                            <ellipse fill-opacity=".8" fill="#F5F5F7" cx="67.797" cy="106.89" rx="67.797" ry="12.668"></ellipse>
                                            <path d="M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z" fill="#AEB8C2"></path>
                                            <path d="M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z" fill="url(#linearGradient-1)" transform="translate(13.56)"></path>
                                            <path d="M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z" fill="#F5F5F7"></path>
                                            <path d="M42.678 9.953h50.237a2 2 0 0 1 2 2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z" fill="#DCE0E6"></path>
                                        </g>
                                        <path d="M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z" fill="#DCE0E6"></path>
                                        <g transform="translate(149.65 15.383)" fill="#FFF">
                                            <ellipse cx="20.654" cy="3.167" rx="2.849" ry="2.815"></ellipse>
                                            <path d="M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z"></path>
                                        </g>
                                    </g>
                                </svg>
                                <p>Không có dữ liệu</p>
                            </div>
                            </td>
                    </tr>
                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                            <?= pagination_client($url, $page, $total_log, $limit); ?>
                        </div>
                                                </div>
                                            </div>
                                            <!-- Standard -->
                                            <div class="tab-pane fade" id="nav-standard" role="tabpanel"
                                                aria-labelledby="nav-standard-tab" tabindex="0">
                                                <div class="overflow-x-auto">
                                                    <div class="w-100">
                                                        <table class="w-100 dashboard-table">
                                                            <thead class="pb-3">
                                                                <tr>
                                                                    <th scope="col">Mã giao dịch</th>
                                                                    <th scope="col">Thông tin</th>
                                                                    <th scope="col">Số tiền</th>
                                                                    <th scope="col">Nội dung</th>
                                                                    <th scope="col">Trạng thái</th>
                                                                    <th scope="col">Thời Gian</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php if (!empty($withdraws)): ?>
                    <?php foreach ($withdraws as $row): ?>
                                                                <tr>
                                                                <td class="text-dark"><?= $row['trans_id']; ?></td>
                                                                <td class="text-dark">
                                                                    <ul>
                                                                        <li>Ngân hàng: <?= $row['bank'] ?></li>
                                                                        <li>Số tài khoản: <?= $row['stk'] ?></li>
                                                                        <li>Chủ tài khoản: <?= $row['name'] ?></li>
                                                                    </ul>
                                                                </td>
                                                                <td class="text-dark"><b style="color:red"><?= format_cash($row['amount']); ?></b></td>
                                                                <td class="text-dark">
                                                                        <?= $row['reason']; ?>
                                                                </td>
                                                                <td class="text-dark"><?= status_withdraw_orders($row['status']); ?></td>
                                                                <td class="text-dark"><?= $row['update_gettime']; ?></td>
                                                            </tr>
                                        <?php endforeach; ?>
                                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
                        <div class="empty-state">
                                <svg width="184" height="152" viewBox="0 0 184 152" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd">
                                        <g transform="translate(24 31.67)">
                                            <ellipse fill-opacity=".8" fill="#F5F5F7" cx="67.797" cy="106.89" rx="67.797" ry="12.668"></ellipse>
                                            <path d="M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z" fill="#AEB8C2"></path>
                                            <path d="M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z" fill="url(#linearGradient-1)" transform="translate(13.56)"></path>
                                            <path d="M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z" fill="#F5F5F7"></path>
                                            <path d="M42.678 9.953h50.237a2 2 0 0 1 2 2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z" fill="#DCE0E6"></path>
                                        </g>
                                        <path d="M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z" fill="#DCE0E6"></path>
                                        <g transform="translate(149.65 15.383)" fill="#FFF">
                                            <ellipse cx="20.654" cy="3.167" rx="2.849" ry="2.815"></ellipse>
                                            <path d="M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z"></path>
                                        </g>
                                    </g>
                                </svg>
                                <p>Không có dữ liệu</p>
                            </div>
                            </td>
                    </tr>
                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                            <?= pagination_client($url, $page, $total_withdraw, $limit); ?>
                        </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </aside>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

</main>
<script type="text/javascript">
    $("#btnWithdraw").on("click", function() {
        $('#btnWithdraw').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/withdraw",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                bank: $('#bank').val(),
                stk: $('#stk').val(),
                name: $('#name').val(),
                amount: $('#amount').val()
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: result.msg,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire(
                        'Failure',
                        result.msg,
                        'error'
                    );
                }
                $('#btnWithdraw').html('Rút Tiền Ngay')
                    .prop('disabled', false);
            }
        })
    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>