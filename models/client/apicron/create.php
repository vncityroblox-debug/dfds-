<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Kiểm tra token API
$token = $_POST['token'] ?? '';
$user = $db->get_row("SELECT * FROM users WHERE token = '$token' AND banned = 0");

if (!$user) {
    die(json_encode(['status' => 'error', 'msg' => 'Token không hợp lệ']));
}

// Lấy dữ liệu từ form
$link = Anti_xss($_POST['linkcron'] ?? '');
$second = intval($_POST['timeloop'] ?? 0);
$server_id = intval($_POST['maychucron'] ?? 0);
$months = intval($_POST['thoigiangiahan'] ?? 0);

if (!$link || !$second || !$server_id || !$months) {
    die(json_encode(['status' => 'error', 'msg' => 'Vui lòng điền đầy đủ thông tin']));
}

// Kiểm tra server
$server = $db->get_row("SELECT * FROM server_cronjobs WHERE id = $server_id");
if (!$server || $server['usage_limit'] <= 0) {
    die(json_encode(['status' => 'error', 'msg' => 'Máy chủ không khả dụng']));
}

// Tính giá
$price_per_month = $server['price'];
$discount = $server['discount_percent'];
$valid_until = $server['discount_valid_until'];

$final_price = ($discount > 0 && strtotime($valid_until) > time()) ?
    $price_per_month - ($price_per_month * $discount / 100) :
    $price_per_month;

$total_price = $final_price * $months;
$total_price -= ($total_price * $user['chietkhau'] / 100);

// Kiểm tra tiền
if ($user['money'] < $total_price) {
    die(json_encode(['status' => 'error', 'msg' => 'Không đủ số dư: cần ' . format_cash($total_price)]));
}

// Trừ tiền
$isCharged = RemoveCredits($user['id'], $total_price, "Thuê cronjob $link trong $months tháng");

if (!$isCharged) {
    die(json_encode(['status' => 'error', 'msg' => 'Không thể trừ tiền']));
}
$random = random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 11);
// Tạo đơn hàng
$expires_at = date('Y-m-d H:i:s', strtotime("+$months months"));
$data = [
    'user_id' => $user['id'],
    'trans_id' => $random,
    'url' => $link,
    'method' => 'GET',
    'cron_expression' => "*/$second * * * * *",
    'server_id' => $server_id,
    'expires_at' => $expires_at,
    'body' => '',
    'headers' => '',
    'payment' => $total_price
];

if ($db->insert('cronjobs', $data)) {
    $db->tru("server_cronjobs", 'usage_limit', 1, " `id` = '$server_id' ");
    echo json_encode([
        'status' => 'success',
        'msg' => 'Thanh toán đơn hàng thành công',
        'data' => [
            'trans_id' => $random,
            'url' => $link,
            'second' => $second,
            'price' => $total_price,
            'status' => 'hoatdong',
            'created_at' => date('Y/m/d H:i:s'),
            'expired_date' => date('Y/m/d H:i:s', strtotime($expires_at)),
            'expired_timestamp' => strtotime($expires_at)
        ]
    ]);
} else {
    die(json_encode(['status' => 'error', 'msg' => 'Tạo cronjob thất bại']));
}
}