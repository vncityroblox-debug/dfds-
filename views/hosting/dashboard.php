<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

// Kiểm tra đăng nhập
if (!@$user) {
    new Redirect('/login');
    exit;
}

$title = "Quản Lý Hosting - ". $db->site('title');

require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

if (isset($_GET['id'])) {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `purchased_hosting` WHERE `id` = '{$id}' AND `user_id` = '{$data_user['id']}' ");
    if (!$row) {
        new Redirect('/user/history/hosting');
    }
    $whm_info = json_decode($row['server_whm'], true);
    if (!$whm = $db->get_row("SELECT * FROM `whm_info` WHERE `username` = '{$whm_info['username']}'")) {
    }
    $info_package = json_decode($row['info_package'], true);
    $url_cpanel = "";
    $result = loginCpanelHostingViaAPI($whm['ip'], $whm['username'], $whm['password'], $row['username'], "cpaneld");
    if (isset($result['metadata']['result'])) {
        if ($result['metadata']['result'] == 1) {
            $url_cpanel = $result['data']['url'];
        }
    }
} else {
    new Redirect('/user/history/hosting');
}
?>

<main>
    <section class="py-110">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm p-3">
                        <div class="pb-4 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <h3 class="h5 fw-bold text-dark mb-0">STC 1</h3>
                                <span class="inline-block bg-green-200 text-green-700 text-xs font-semibold px-2 py-1 uppercase rounded"><?= status_hosting($row['status']) ?></span>                            </div>
                            <a href="https://<?= $info_package['name'] ?>" class="text-primary text-decoration-underline"><?= $info_package['name'] ?></a>
                        </div>

                        <div class="border-top pt-4 row row-cols-1 row-cols-md-2 gy-4 text-muted mb-6">
                            <div>
                                <div class="text-secondary">Thanh toán lần đầu</div>
                                <div class="fw-medium"><?= format_cash($row['price']) ?> VND</div>
                            </div>
                            <div>
                                <div class="text-secondary">Chu kỳ thanh toán</div>
                                <div class="fw-medium"><?= $row['month'] ?> Tháng</div>
                            </div>
                            <div>
                                <div class="text-secondary">Ngày đăng ký</div>
                                <div class="fw-medium"><?= date('H:i:s d-m-Y', $row['start_date']) ?></div>
                            </div>
                            <div>
                                <div class="text-secondary">Ngày hết hạn</div>
                                <div class="fw-medium"><?= date('H:i:s d-m-Y', $row['end_date']) ?></div>
                            </div>
                            <div>
                                <div class="text-secondary">Số tiền thanh toán định kỳ</div>
                                <div class="fw-medium"><?= format_cash($row['price']) ?> VND</div>
                            </div>
                            <div>
                                <div class="text-secondary">Hình thức thanh toán</div>
                                <div class="fw-medium">Số dư tài khoản</div>
                            </div>
                        </div>

                        <div class="row gy-4">
                            <div class="col-md-6">
                                <label for="username" class="form-label fw-medium">Link Cpanel</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="http://<?= $row['ip'] ?>:2082" readonly="">
                                    <button class="btn btn-outline-secondary copy" data-clipboard-text="http://<?= $row['ip'] ?>:2083">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="ip" class="form-label fw-medium">Địa chỉ IP</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" value="<?= $row['ip'] ?>" readonly="">
                                    <button class="btn btn-outline-secondary copy" data-clipboard-text="<?= $row['ip'] ?>">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="username" class="form-label fw-medium">Tài Khoản</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" value="<?= $row['username'] ?>" readonly="">
                                    <button class="btn btn-outline-secondary copy" data-clipboard-text="<?= $row['username'] ?>">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium">Mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" value="<?= $row['password'] ?>" readonly="">
                                    <button class="btn btn-outline-secondary copy" data-clipboard-text="<?= $row['password'] ?>">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">

                    <div class="alert alert-danger mb-1" role="alert">
                        <span>Lưu ý: Chức năng cài lại hosting sẽ đưa hosting về ban đầu và sẽ mất dữ liệu cũ</span>
                    </div>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 card-title mb-4">Liên kết với cPanel</h2>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div role="button" class="text-center" data-bs-toggle="modal" data-bs-target="#cronJobsModal">
                                        <img src="/assets/images/cron_jobs.png" alt="Cron Job" class="mb-2 img-fluid">
                                        <p>Cron Jobs</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div role="button" class="text-center" data-bs-toggle="modal" data-bs-target="#addonDomainsModal">
                                        <img src="/assets/images/addon_domains.png" alt="Addon Domains" class="mb-2 img-fluid">
                                        <p>Addon Domains</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div role="button" class="text-center" data-bs-toggle="modal" data-bs-target="#subdomainsModal">
                                        <img src="/assets/images/subdomains.png" alt="Subdomains" class="mb-2 img-fluid">
                                        <p>Subdomains</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div role="button" class="text-center" onclick="performAction(<?= $row['id'] ?>, 3)">
                                        <img src="/assets/images/dash_reinstall.svg" alt="Cài đặt lại hosting" class="mb-2 img-fluid">
                                        <p>Cài đặt lại hosting</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div role="button" class="text-center" data-bs-toggle="modal" data-bs-target="#changeDomainModal">
                                        <img src="/assets/images/dash_change_domain.svg" alt="Đổi miền chính" class="mb-2 img-fluid">
                                        <p>Đổi miền chính</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 card-title mb-4">Công cụ</h2>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div role="button" class="text-center" data-bs-toggle="modal" data-bs-target="#blockIPModal">
                                        <img src="/assets/images/dash_allow_ip.svg" alt="Block IP" class="mb-2 img-fluid">
                                        <p>Chặn IP</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div role="button" class="text-center" data-bs-toggle="modal" data-bs-target="#unlockIPModal">
                                        <img src="/assets/images/dash_unblock_ip.svg" alt="Unblock IP" class="mb-2 img-fluid">
                                        <p>Bỏ chặn IP</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h2 class="h5 card-title mb-4">Tài nguyên sử dụng</h2>
                                    <div class="mb-3">
                                        <h6>Disk Usage</h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
                                        </div>
                                        <span id="description-disk" class="text-gray-700 mt-2 block">0 MB / 0 MB (0%)</span>
                                    </div>
                                    <div class="mb-3">
                                        <h6>Bandwidth</h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" id="progress-bar-bandwidth" style="width: 0%"></div>
                                        </div>
                                        <span id="description-bandwidth" class="text-gray-700 mt-2 block">0 / 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h2 class="h5 card-title mb-4">Liên kết nhanh</h2>
                                    <div class="list-group">
                                        <a target="_blank" href="<?= $url_cpanel ?>" class="list-group-item list-group-item-action bg-light-hover mb-1 rounded-lg">
                                            Đăng nhập vào cPanel
                                        </a>
                                        <a href="javascript:" onclick="performAction(<?= $row['id'] ?>,2)" class="list-group-item list-group-item-actio bg-light-hover mb-1">
                                            Thay đổi mật khẩu
                                        </a>
                                        <a href="javascript:" onclick="performAction(<?= $row['id'] ?>,4)" class="list-group-item list-group-item-action bg-light-hover mb-1">
                                            Gia hạn
                                        </a>
                                        <a href="javascript:" class="list-group-item list-group-item-action bg-light-hover mb-1" data-bs-toggle="modal" data-bs-target="#upgradeHostingModal">
                                            Nâng cấp
                                        </a>
                                        <a href="javascript:" class="list-group-item list-group-item-action bg-light-hover" data-bs-toggle="modal" data-bs-target="#tradeEmailModal">
                                            Đổi quyền quản trị
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Cron Jobs Modal -->
<div class="modal fade" id="cronJobsModal" tabindex="-1" aria-labelledby="cronJobsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cronJobsModalLabel">Cron Jobs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="cronLink" class="form-label">Link Cron</label>
                        <input type="text" class="form-control" id="cronLink">
                    </div>
                    <div class="mb-3">
                        <label for="cronTime" class="form-label">Thời Gian Chạy (phút)</label>
                        <input type="number" class="form-control" id="cronTime">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 10)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Addon Domains Modal -->
<div class="modal fade" id="addonDomainsModal" tabindex="-1" aria-labelledby="addonDomainsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addonDomainsModalLabel">Addon Domains</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="domain" class="form-label">Tên miền</label>
                        <input type="text" class="form-control" id="domain">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 8)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Subdomains Modal -->
<div class="modal fade" id="subdomainsModal" tabindex="-1" aria-labelledby="subdomainsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subdomainsModalLabel">Subdomains</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="subdomain" class="form-label">Nhập subdomain</label>
                        <input type="text" class="form-control" id="subdomain" placeholder="VD: test">
                    </div>
                    <div class="mb-3">
                        <label for="rootdomain" class="form-label">Nhập root domain</label>
                        <input type="text" class="form-control" id="rootdomain" placeholder="VD: example.vn">
                    </div>
                    <div class="mb-3">
                        <label for="result" class="form-label">Kết quả sẽ nhận được là test.example.vn</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 9)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Hosting Modal -->
<div class="modal fade" id="upgradeHostingModal" tabindex="-1" aria-labelledby="upgradeHostingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upgradeHostingModalLabel">Nâng Cấp Hosting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="selectedPackage" class="form-label">Chọn gói</label>
                        <select class="form-select" id="selectedPackage" v-model="selectedPackage" size="large">
                            <?php foreach ($db->get_list("SELECT * FROM `hosting_packages` WHERE `whm_id` = '" . $whm['id'] . "' AND `id` != '" . $row['package_id'] . "' AND `status` = '1' AND `id` > '" . $row['package_id'] . "' AND `disk_quota` > " . $info_package['disk_quota'] . " ORDER BY `id` ASC") as $package) : ?>
                            <option label="<?= $package['name'] ?> - <?= $package['disk_quota'] ?>MB" value="<?= $package['id'] ?>"></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="upgradeFee" class="form-label">Phí nâng cấp = Giá gói mới - Giá gói cũ</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 5)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Trade Email Modal -->
<div class="modal fade" id="tradeEmailModal" tabindex="-1" aria-labelledby="tradeEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tradeEmailModalLabel">Addon Domains</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email người dùng</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 6)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Block IP Modal -->
<div class="modal fade" id="blockIPModal" tabindex="-1" aria-labelledby="blockIPModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blockIPModalLabel">Chặn IP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="ip" class="form-label">IP</label>
                        <input type="text" class="form-control" id="ip" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 13)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Unlock IP Modal -->
<div class="modal fade" id="unlockIPModal" tabindex="-1" aria-labelledby="unlockIPModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unlockIPModalLabel">Bỏ Chặn IP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="ip" class="form-label">IP</label>
                        <input type="text" class="form-control" id="ip" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 14)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Domain Modal -->
<div class="modal fade" id="changeDomainModal" tabindex="-1" aria-labelledby="changeDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeDomainModalLabel">Đổi Miền Chính</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="changedomain" class="form-label">Tên miền</label>
                        <input type="text" class="form-control" id="changedomain" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performAction(<?= $row['id'] ?>, 11)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    <?php
    if (time() > $row['end_date']) : ?>
        Swal.fire('Thất Bại', 'Gói hosting đã hết hạn sử dụng, vui lòng gia hạn để tiếp tục thực hiện các chức năng', 'error');
    <?php endif; ?>
        $.ajax({
        url: '/model/action/hosting',
        method: "POST",
        dataType: "JSON",
        data: {
            csrf_token: csrf_token,
            param: <?= $row['id'] ?>,
            action: 12
        },
        success: function(response) {
            if (response.status == 'success') {
                var megabytesLimit = response.disk.megabyte_limit;
                var megabytesUsed = response.disk.megabytes_used;
                var percentUsed = ((megabytesUsed / megabytesLimit) * 100).toFixed(0);

                var html = '<span id="description-disk">' + megabytesUsed.toFixed(2) + ' MB / ' + megabytesLimit
                    .toFixed(2) + ' MB (' + percentUsed + '%)</span>';

                $('#description-disk').html(html);

                $('#progress-bar').css('width', percentUsed + '%');

                var byte_limit = response.bandwidth.byte_limit;
                var bytes_used = response.bandwidth.bytes_used;

                var percentBandwidth = ((bytes_used / byte_limit) * 100).toFixed(0);

                var html2 = '<span id="description-bandwidth">' + bytes_used + ' / ' + byte_limit +
                    ' (' + percentBandwidth + '%)</span>';

                $('#description-bandwidth').html(html2);

                $('#progress-bar-bandwidth').css('width', percentBandwidth + '%');

            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Thất Bại', xhr.responseText, 'error');
        }
    });
</script>

<script type="text/javascript">
    function performAction(param, action) {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện chức năng này chứ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await handleAction(param, action);
            }
        });
    }

    const handleAction = async (param, action) => {
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
            url: '/model/action/hosting',
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                param: param,
                action: action,
                email: $('#email').val(),
                cronLink: $('#cronLink').val(),
                cronTime: $('#cronTime').val(),
                package: $('#selectedPackage').val(),
                subdomain: $('#subdomain').val(),
                rootdomain: $('#rootdomain').val(),
                domain: $('#domain').val(),
                changedomain: $('#changedomain').val(),
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công', result.msg, 'success').then(() => {
                        window.location.href = '';
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
</script>


<?php require realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>