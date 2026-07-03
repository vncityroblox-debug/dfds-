<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

// Kiểm tra đăng nhập
if (!@$user) {
    new Redirect('/login');
    exit;
}

$title = "Quản Lý VPS - ". $db->site('title');

require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

if (isset($_GET['id'])) {
    $id = Anti_xss($_GET['id']);
    $check = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `id` = '{$id}' AND `user_id` = '{$data_user['id']}' AND `site` IN ('VNCLOUD', 'H2CLOUD')");
    if (!$check) {
        new Redirect('/user/history/vps');
    }

    $vpsId = (int)$check['vps_id'];
    $array = [$vpsId];

    if (!empty($array)) {
        $jsonString = json_encode($array);
        $result = infoListVps($jsonString);

        if (isset($result['error']) && $result['error'] == 0) {
            foreach ($result['data'] as $infovps) {
                $vpsInfo = json_encode($infovps);
                $encryptedInfo = encryptAES($vpsInfo);
                $db->update("tbl_purchased_cloudvps", ['info' => $encryptedInfo, 'status' => $infovps['vps-status']], " `vps_id` = '" . $infovps['vps-id'] . "' ");
            }
        }
    }
    $row = $db->get_row("SELECT * FROM `tbl_purchased_cloudvps` WHERE `id` = '{$id}' AND `user_id` = '{$data_user['id']}' AND `site` IN ('VNCLOUD', 'H2CLOUD')");
    if (!$row) {
        new Redirect('/user/history/vps');
    }

    $detail = json_decode(decryptAES($row['info']), true);
    $os = json_decode($db->site('os_vps'), true);
} else {
    new Redirect('/user/history/vps');
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
                                <h3 class="h5 fw-bold text-dark mb-0"><?= $detail['text-config'] ?></h3>
                                <span class="inline-block bg-green-200 text-green-700 text-xs font-semibold px-2 py-1 uppercase rounded"><?= status_order_cloud($detail['vps-status']) ?></span>
                            </div>
                        </div>

                        <div class="border-top pt-4 row row-cols-1 row-cols-md-2 gy-4 text-muted mb-6">
                            <div>
                                <div class="text-secondary">Thanh toán lần đầu</div>
                                <div class="fw-medium"><?= format_cash($row['price']) ?> VND</div>
                            </div>
                            <div>
                                <div class="text-secondary">Chu kỳ thanh toán</div>
                                <div class="fw-medium"><?= $detail['billing-cycle'] ?></div>
                            </div>
                            <div>
                                <div class="text-secondary">Ngày đăng ký</div>
                                <div class="fw-medium"><?= $detail['date_create'] ?></div>
                            </div>
                            <div>
                                <div class="text-secondary">Ngày hết hạn</div>
                                <div class="fw-medium"><?= $detail['next_due_date'] ?></div>
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
        <label for="port" class="form-label fw-medium"><?= $detail['username'] == "root" ? "SSH Port" : "Remote Port" ?></label>
        <div class="input-group">
            <input type="text" class="form-control" value="<?= $detail['username'] == "root" ? "22" : "3389" ?>" readonly>
            <button class="btn btn-outline-secondary copy" data-clipboard-text="<?= $detail['username'] == "root" ? "22" : "3389" ?>">
                <i class="bx bx-copy"></i>
            </button>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">Địa chỉ IP</label>
        <div class="input-group">
            <input
                type="password"
                class="form-control copy"
                id="ipInput"
                value="<?= $detail['ip'] ?>"
                readonly
                data-clipboard-text="<?= $detail['ip'] ?>"
            >
            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('ipInput', 'toggleIP')">
                <i class="bx bx-show" id="toggleIP"></i>
            </button>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">Tài Khoản</label>
        <div class="input-group">
            <input
                type="password"
                class="form-control copy"
                id="usernameInput"
                value="<?= $detail['username'] ?>"
                readonly
                data-clipboard-text="<?= $detail['username'] ?>"
            >
            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('usernameInput', 'toggleUsername')">
                <i class="bx bx-show" id="toggleUsername"></i>
            </button>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">Mật Khẩu</label>
        <div class="input-group">
            <input
                type="password"
                class="form-control copy"
                id="passwordInput"
                value="<?= $detail['password'] ?>"
                readonly
                data-clipboard-text="<?= $detail['password'] ?>"
            >
            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('passwordInput', 'togglePassword')">
                <i class="bx bx-show" id="togglePassword"></i>
            </button>
        </div>
    </div>
</div>
                    </div>
                </div>
                
<div class="col-md-6">

    <div class="alert alert-danger mb-1" role="alert">
        <span>Lưu ý: Chức năng cài lại hệ điều hành VPS sẽ đưa vps về ban đầu và sẽ mất dữ liệu cũ</span>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="vpsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="function-tab" data-bs-toggle="tab" data-bs-target="#function" type="button" role="tab" aria-controls="function" aria-selected="true">
                        <i class="bx bx-download"></i> Chức Năng
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reinstall-tab" data-bs-toggle="tab" data-bs-target="#reinstall" type="button" role="tab" aria-controls="reinstall" aria-selected="false">
                        <i class="bx bx-cog"></i> Cài Lại Hệ Điều Hành
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="vpsTabContent">
                <div class="tab-pane fade show active" id="function" role="tabpanel" aria-labelledby="function-tab">
                    <div class="row g-3">
                        <?php if ($detail['vps-status'] != 'expire') : ?>
                        <div class="col-6 col-md-3 text-center">
                            <div role="button" class="cursor-pointer" onclick="confirmAction(<?= $row['id'] ?>,1)">
                                <img src="/assets/images/startButton.png" alt="Start" class="mb-2 img-fluid" style="max-width: 50px;">
                                <p>Start</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div role="button" class="cursor-pointer" onclick="confirmAction(<?= $row['id'] ?>,3)">
                                <img src="/assets/images/rebootButton.png" alt="Reboot" class="mb-2 img-fluid" style="max-width: 50px;">
                                <p>Reboot</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div role="button" class="cursor-pointer" onclick="confirmAction(<?= $row['id'] ?>,2)">
                                <img src="/assets/images/shutdownButton.png" alt="Shut Down" class="mb-2 img-fluid" style="max-width: 50px;">
                                <p>Shut Down</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div role="button" class="cursor-pointer" onclick="openModal('modalUpgrade')">
                                <img src="/assets/images/upgrade.png" alt="Nâng Cấp" class="mb-2 img-fluid" style="max-width: 50px;">
                                <p>Nâng Cấp</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-6 col-md-3 text-center">
                            <div role="button" class="cursor-pointer" onclick="confirmAction(<?= $row['id'] ?>,5)">
                                <img src="/assets/images/extend.png" alt="Gia Hạn" class="mb-2 img-fluid" style="max-width: 50px;">
                                <p>Gia Hạn</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div role="button" class="cursor-pointer" onclick="openModal('modalTrade')">
                                <img src="/assets/images/trade.png" alt="Đổi Quản Trị" class="mb-2 img-fluid" style="max-width: 50px;">
                                <p>Đổi Quản Trị</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="reinstall" role="tabpanel" aria-labelledby="reinstall-tab">
                    <div class="mb-2 mt-2">Select OS:</div>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        <?php foreach ($os['os-vps'] as $osEntry) :
                            $imageSrc = getImageSource($osEntry['os-name']);
                        ?>
                            <div class="col">
                                <div role="button" class="card-wrapper border rounded os p-3 text-center" id="item<?= $osEntry['os-name'] ?>" data-os="<?= $osEntry['os-id'] ?>" onclick="updateOs('<?= $osEntry['os-id'] ?>')">
                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                        <img width="50px" src="<?= $imageSrc; ?>" alt="<?= $osEntry['os-name'] ?>" />
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 distro_name"><?= $osEntry['os-name'] ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn btn-primary mt-4" onclick="confirmAction(<?= $row['id'] ?>, 4)">
                        <i class="bx bx-download me-1"></i> Cài Lại Ngay
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


<div class="modal fade" id="modalUpgrade" tabindex="-1" aria-labelledby="modalUpgradeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUpgradeLabel">Nâng Cấp VPS</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="cpu" class="form-label">CPU Thêm (CORE):</label>
            <input type="number" class="form-control" id="cpu" value="0" onchange="totalPayment()">
          </div>
          <div class="mb-3">
            <label for="ram" class="form-label">RAM Thêm (GB):</label>
            <input type="number" class="form-control" id="ram" value="0" onchange="totalPayment()">
          </div>
          <div class="mb-3">
            <label for="disk" class="form-label">DISK Thêm (1 đơn vị = 10GB):</label>
            <input type="number" class="form-control" id="disk" value="0" onchange="totalPayment()">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold text-danger">Thanh toán:</label>
            <div id="total" class="fs-5 fw-bold text-danger">0</div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Thoát</button>
        <button type="button" class="btn btn-primary" onclick="confirmAction(<?= $row['id'] ?>,10)">Xác Nhận</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modalTrade" tabindex="-1" aria-labelledby="modalTradeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTradeLabel">Chuyển Quyền Quản Trị VPS</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Nhập email cần chuyển">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Thoát</button>
        <button type="button" class="btn btn-primary" onclick="confirmAction(<?= $row['id'] ?>,7)">Xác Nhận</button>
      </div>
    </div>
  </div>
</div>

<script>
function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bx-show");
        icon.classList.add("bx-hide");
    } else {
        input.type = "password";
        icon.classList.remove("bx-hide");
        icon.classList.add("bx-show");
    }
}
</script>


<script type="text/javascript">
    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
    }

function openModal(modalId) {
  var myModal = new bootstrap.Modal(document.getElementById(modalId));
  myModal.show();
}

    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-tab-target]');
        const tabContents = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = document.querySelector(tab.getAttribute('data-tab-target'));

                tabContents.forEach(tc => {
                    tc.classList.add('hidden');

                });

                tabs.forEach(t => {
                    t.classList.remove('border-b-2', 'border-blue-500');
                    t.classList.add('border-transparent');
                    t.setAttribute('aria-selected', 'false');
                });

                tab.classList.add('border-b-2', 'border-blue-500');
                tab.classList.remove('border-transparent');
                tab.setAttribute('aria-selected', 'true');
                target.classList.remove('hidden');

            });
        });
    });
</script>
<script type="text/javascript">
const items = document.querySelectorAll('.os');
items.forEach(item => {
    item.addEventListener('click', () => {
        const os = item.dataset.os;
        items.forEach(i => {
            i.classList.remove('border-2', 'active-select');
        });
        item.classList.add('border-2', 'active-select');
        updateOs(os);
    });
});

let osdata = "";
function updateOs(os) {
    osdata = os;
}

    const confirmAction = (param, action) => {
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
                await Item(param, action);
            }
        });
    }

    const Item = async (param, action) => {
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
            url: '/model/action/vps',
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                param: param,
                action: action,
                osid: osdata,
                email: $('#email').val(),
                cpu: $('#cpu').val(),
                ram: $('#ram').val(),
                disk: $('#disk').val()
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
</script>

<script type="text/javascript">
    function totalPayment() {
        $('#total').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');
        $.ajax({
            url: "/model/total/cash",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                id: <?= $id ?>,
                cpu: $("#cpu").val(),
                ram: $("#ram").val(),
                disk: $("#disk").val(),
                action: 'upgrade'
            },
            success: function(respone) {
                $("#total").html(respone.total);
            },
            error: function() {
                showMessage('Không thể tính kết quả thanh toán', 'error');
            }
        });
    }
</script>
<?php require realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>