<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
if (!@$user || $data_user['ctv'] != '1') {
    new Redirect('/');
    exit;
}
$title = 'Rút tiền';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
// Initialize variables
$transaction_id = $_GET['tid'] ?? '';
$purchase_date = $_GET['purchase_date'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build SQL query based on filters
$conditions = ["`user_id` = '{$data_user['id']}'"];
if ($transaction_id) {
    $conditions[] = "trans_id LIKE '%$transaction_id%'";
}
if ($purchase_date) {
    $dates = explode(" to ", $purchase_date);
    if (count($dates) == 2) {
        $conditions[] = "create_gettime BETWEEN '{$dates[0]} 00:00:00' AND '{$dates[1]} 23:59:59'";
    }
}

$where = implode(" AND ", $conditions);

// Get the total count of records
$total_items = $db->get_row("SELECT COUNT(*) as total FROM product_withdraw WHERE $where")['total'] ?? 0;

// Get the withdrawal records
$withdrawals = $db->get_list("SELECT * FROM product_withdraw WHERE $where LIMIT $limit OFFSET $offset");
$url = "/user/withdraw?tid=$transaction_id&purchase_date=$purchase_date&";
?>
<section class="py-110">
        <div class="container">
        <?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php');?>
        <div class="row">
                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-card-head">
                            <h4>Rút tiền bán hàng - Khả dụng <span class="text-danger"><?= format_cash($data_user['product_amount']) ?>đ</span></h4>
                        </div>
                        <div class="settings-card-body">
                            <div class="mb-2">
                                <div class="text-danger fw-bold">
                                    <i>
                                        Số tiền có thể rút: từ <span class="text-success"><?= format_cash($db->site('minrut_product')) ?> ₫</span></span>
                                    </i>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="amount" class="form-label">Số Tiền Rút</label>
                                <input type="number" class="form-control shadow-none" id="amount" name="amount" required="">
                            </div>

                            <div class="mb-4 group_banking">
                                <label for="bank" class="form-label">Ngân Hàng</label>
                                <select name="bank" id="bank" class="form-control shadow-none">
                                    <option value="">-- Chọn ngân hàng cần rút --
                                                                        </option>
                                                                        <?php $listbank = explode(PHP_EOL, $db->site('listbank_product')); ?>
                                                                        <?php foreach($listbank as $value):?>
                                                                        <option value="<?=$value;?>"><?=$value;?>
                                                                        </option>
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
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-card-head">
                            <h4>Lưu ý</h4>
                        </div>
                        <div class="settings-card-body">
                            <p><?= Anti_xss($db->site('notice_product')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="GET" action="" class="row">
                        <div class="col-lg col-md-4 col-6">
                            <input class="form-control shadow-none col-sm-2 mb-2" name="tid" type="text" value="<?= Anti_xss($transaction_id) ?>" placeholder="Mã giao dịch">
                        </div>

                        <div class="col-lg col-md-4 col-6">
                            <input type="text" class="form-control shadow-none mb-2" name="purchase_date" id="purchase_date" type="text" value="<?= Anti_xss($purchase_date) ?>" placeholder="Chọn khoảng thời gian">
                        </div>
                        <div class="col-lg col-md-4 col-6">
                            <button class="shop-widget-btn mb-2"><i class="fas fa-search"></i><span>Tìm kiếm</span></button>
                        </div>
                        <div class="col-lg col-md-4 col-6">
                            <a href="/user/withdraw" class="shop-widget-btn mb-2"><i class="far fa-trash-alt"></i><span>Bỏ lọc</span></a>
                        </div>
                    </form>
                    <div class="overflow-x-auto">
    <table class="w-100 dashboard-table table text-dark">
        <thead>
            <tr>
                <th scope="col" class="py-2 px-4">Mã giao dịch</th>
                <th scope="col" class="py-2 px-4">Thông tin</th>
                <th scope="col" class="py-2 px-4">Số tiền</th>
                <th scope="col" class="py-2 px-4">Trạng thái</th>
                <th scope="col" class="py-2 px-4">Thời gian</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($withdrawals)): ?>
                <?php foreach ($withdrawals as $withdrawal): ?>
                    <tr>
                        <td class="text-dark"><?= $withdrawal['trans_id'] ?></td>
                        <td class="text-dark"><ul>
                                                    <li>Ngân hàng: <?= $withdrawal['bank'] ?></li>
                                                    <li>Số tài khoản: <?= $withdrawal['stk'] ?></li>
                                                    <li>Chủ tài khoản: <?= $withdrawal['name'] ?></li>
                                                </ul></td>
                        <td class="text-dark"><?= format_cash($withdrawal['amount']) ?>đ</td>
                        <td class="text-dark"><?= status_withdraw_orders($withdrawal['status']) ?></td>
                        <td class="text-dark"><span class="status-badge in-active"><?= $withdrawal['create_gettime'] ?></span></td>
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

<!-- Pagination -->
<div class="d-flex justify-content-end">
    <?= pagination_client($url, $page, $total_items, $limit); ?>
</div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<script type="text/javascript">
    $("#btnWithdraw").on("click", function() {
        $('#btnWithdraw').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/seller/withdraw",
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#purchase_date", {
            mode: "range",
            dateFormat: "Y-m-d"
        });
    });
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>