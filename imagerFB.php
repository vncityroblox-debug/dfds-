<?php
header('Content-Type: application/json');

if (!isset($_GET['uid']) || empty($_GET['uid'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu UID cần kiểm tra.'
    ]);
    exit;
}

$uid = trim($_GET['uid']);
$api = "https://graph.facebook.com/" . $uid . "/picture?redirect=false";
$response = json_decode(fetch($api), true);

$status = (empty($response['data']['width'])) ? 'die' : 'live';

$result = [
    'result' => [
        'uid' => $uid,
        'status' => $status
    ]
];

echo json_encode($result);

function fetch($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>
