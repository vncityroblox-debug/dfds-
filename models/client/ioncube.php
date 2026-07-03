<?php
header('Content-Type: application/json');
$url = 'https://dvd.vn/ajaxs/client/iconcube.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_default = isset($_POST['code_default']) ? $_POST['code_default'] : null;
    $php = isset($_POST['php']) ? $_POST['php'] : null;
    $ioncube = isset($_POST['ioncube']) ? $_POST['ioncube'] : null;
    if ($code_default && $php && $ioncube) {
        $encoded_code_default = base64_encode($code_default);
        $data = array(
            'code_default' => $encoded_code_default,
            'php' => $php,
            'ioncube' => $ioncube
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $api_response = curl_exec($ch);
        if (curl_errno($ch)) {
            $response = array(
                'status' => 'error',
                'message' => 'CURL Error: ' . curl_error($ch)
            );
        } else {
            $response = json_decode($api_response, true);
        }
        curl_close($ch);
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Thiếu tham số.'
        );
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Yêu cầu không hợp lệ.'
    );
}
echo json_encode($response);
?>