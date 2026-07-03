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
    <input type="text" class="form-control shadow-none" id="urls" value="<?= $row['url'] ?>">

    <div class="mb-1">
        <label for="method" class="form-label">Method</label>
        <select class="form-select" id="method" name="method" required="">
            <option value="GET" <?= $row['method'] == 'GET' ? 'selected' : '' ?>>GET</option>
            <option value="POST" <?= $row['method'] == 'POST' ? 'selected' : '' ?>>POST</option>
        </select>
    </div>

    <div class="mb-1">
        <label for="cron_expression" class="form-label">Cron Expression</label>
        <input type="text" class="form-control" id="cron_expressions" name="cron_expressions" placeholder="*/5 * * * *" value="<?= $row['cron_expression'] ?>">
        <div class="form-text">Nhập biểu thức cron (ví dụ: "*/5 * * * *" chạy mỗi 5 phút)</div>
    </div>

    <div class="mb-1">
        <label for="server" class="form-label">Chọn Máy Chủ</label>
        <select class="form-select" id="servers" name="servers" required="">
            <option value="">-- Chọn Máy Chủ --</option>
                            <?php foreach ($db->get_list("SELECT * FROM `server_cronjobs` WHERE `status` = 1") as $server): ?>
                                            <option value="<?= $server['id'] ?>" <?= $row['server_id'] == $server['id'] ? 'selected' : '' ?>>
                                                <?= $server['name'] ?></option>
                                        <?php endforeach; ?>
                    </select>
    </div>
 
    <div class="mb-1">
        <label for="headers" class="form-label">Headers (Tùy chọn)</label>
        <textarea class="form-control" id="headers" name="headers" rows="3" placeholder="{&quot;Content-Type&quot;: &quot;application/json&quot;}"><?= $row['headers'] ?></textarea>
        <div class="form-text">Headers phải ở định dạng JSON (ví dụ: {"Content-Type": "application/json"})</div>
    </div>

    <div class="mb-1">
        <label for="body" class="form-label">Body (Tùy chọn)</label>
        <textarea class="form-control" id="body" name="body" rows="3" placeholder="{&quot;key&quot;: &quot;value&quot;}"><?= $row['body'] ?></textarea>
        <div class="form-text">Body cần nhập khi chọn phương thức POST</div>
    </div>

    <div class="d-flex gap-4 align-items-center justify-content-end">
        <button type="button" class="w-btn-black-lg" data-bs-dismiss="modal">
            Đóng
        </button>
        <button class="w-btn-secondary-sm" id="edit" onclick="editCron(<?= $row['id'] ?>)">
            Cập Nhật
        </button>
    </div>
</div>

<script>
    function editCron(id) {
        $('#edit').html('Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "/model/update/cron",
            method: "POST",
            dataType: "JSON",
            data: {
                id: id,
                action: "edit",
                csrf_token: csrf_token,
                url: $("#urls").val(),
                cron_expression: $("#cron_expressions").val(),
                method: $("#method").val(),
                headers: $("#headers").val(),
                body: $("#body").val(),
                server: $("#servers").val()
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
                $('#edit').html(
                        'Cập Nhật')
                    .prop('disabled', false);
            }
        });
    }
</script></div>
        </div>
    </div>
</div>