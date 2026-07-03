<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}

if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
    die(jsonMsg('error', 'Invalid CSRF Protection Token'));
}
if(check_license($db->site('license'))['status'] == 'error'){
    die(JsonMsg('error', 'Website này chưa kích hoạt bản quyền'));
}
        if ($db->site('status_demo') == 1) {
            die(JsonMsg('error', 'Đây là trang web demo, bạn không thể thực hiện chức năng này'));
        }
if (!$user) {
    die(JsonMsg('error', 'Vui lòng đăng nhập để thực hiện'));
}
if (!isset($_POST["id"]) || empty($_POST["id"])) {
    die(jsonMsg('error', 'Vui lòng chọn đơn'));
}
$id = Anti_xss($_POST["id"]);
if (!($row = $db->get_row(" SELECT * FROM `cronjobs` WHERE `id` = '" . Anti_xss($_POST["id"]) . "'  "))) {
}
?>
<div class="d-flex flex-column gap-4">

    <label for="product-name" class="proposal-form-label">Link cron*</label>
    <input type="text" class="form-control shadow-none" value="<?= $row['url'] ?>" readonly="">
    <input type="hidden" value="<?= $row['payment'] ?>" id="price">

    <label for="months" class="form-label">Thời Gian Sử Dụng (Số Tháng)</label>
    <select class="form-select shadow-none" onchange="totalPrice()" id="month" name="month" required="">
        <option value="">-- Chọn Thời Gian --</option>
        <option value="1">1 Tháng</option>
        <option value="3">3 Tháng</option>
        <option value="6">6 Tháng</option>
        <option value="12">12 Tháng</option>
    </select>
    <div class="form-text">Chọn số tháng để sử dụng cronjob</div>

    <div class="text-center mb-3">
        <h3 class="real_amount"><b id="total" style="color: red;"><?= format_cash($row['payment']) ?></b></h3>
        <span class="">Tổng tiền
        </span>
    </div>

    <div class="d-flex gap-4 align-items-center justify-content-end">
        <button type="button" class="w-btn-black-lg" data-bs-dismiss="modal">
            Đóng
        </button>
        <button class="w-btn-secondary-sm" id="extend" onclick="extendCron(<?= $row['id'] ?>)">Gia Hạn</button>
    </div>
</div>

<script>
   function totalPrice() {
        var total = 0;
        var price = parseInt($("#price").val());
        var months = parseInt($("#month").val());

        if (!isNaN(months)) {
            total = price * months;
            $('#total').html(total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'));
        } else {
            $('#total').html('0');
        }
    }
  
    function extendCron(id) {
        $('#extend').html('Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/update/cron",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: "extend",
                month: $('#month').val()
            },
            success: function(response) {
                if (response.status == 'success') {
                    showMessage(response.msg, response.status);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showMessage(response.msg, response.status);
                }
                $('#extend').html(
                        'Gia Hạn')
                    .prop('disabled', false);
            }
        });
    }
</script></div>
        </div>
    </div>
</div>