<?php

function H2executeCurl($url, $method, $auth, $data = null)
{

    global $db;

    $curl = curl_init();

    $headers = array(
        'api-username: ' . decryptAES($db->site('api_username_h2cloud')),
        'api-app: ' . decryptAES($db->site('api_app_h2cloud')),
        'api-secret: ' . decryptAES($db->site('api_secret_h2cloud')),
        'auth-token: ' . $auth,
        'Content-Type: application/json'
    );

    $curlOptions = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    );

    if ($data) {
        $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
    }



    curl_setopt_array($curl, $curlOptions);
    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}


function getTokenH2()
{
    global $db;

    $response = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/get-info', 'GET', $db->site('auth_token_vps_h2cloud'));

    if (isset($response['error']) && $response['error'] == 0) {
        return $db->site('auth_token_vps_h2cloud');
    } else {
        $data = array(
            "api-username" => decryptAES($db->site('api_username_h2cloud')),
            "api-app" => decryptAES($db->site('api_app_h2cloud')),
            "api-secret" => decryptAES($db->site('api_secret_h2cloud'))
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cloudserver.h2cloud.vn/api/agency/get-token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($response, true);

        $db->update("options", array('value' => $result['auth-token']), " `key` = 'auth_token_vps_h2cloud' ");

        return $result['auth-token'];
    }
}

function addonVpsH2()
{
    $auth = getTokenH2();

    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/get-product', 'GET', $auth);

    return $curl;
}

function createOrderCloudH2($productid, $billingcycle, $osid, $cpu, $ram, $disk)
{
    $auth = getTokenH2();
    $data = array(
        "product-id" => $productid,
        "billing-cycle" => $billingcycle,
        "os" => $osid,
        "quantity" => "1",
        "addon-cpu" => $cpu,
        "addon-ram" => $ram,
        "addon-disk" => $disk
    );

    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/order/create-order', 'POST', $auth, $data);

    return $curl;
}

function rebuildCloudVpsH2($vpsid, $osid)
{
    $auth = getTokenH2();
    $data = array(
        "action" => "confirm-rebuild-vps",
        "vps-id" => $vpsid,
        "os-id" => $osid
    );

    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function moneyUserCloudVpsH2()
{
    $auth = getTokenH2();
    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/get-info', 'GET', $auth);

    return $curl['data']['credit'];
}

function upgradeCloudVpsH2($vpsid, $cpu, $ram, $disk)
{
    $auth = getTokenH2();
    $data = array(
        "action" => "addon-vps",
        "vps-id" => $vpsid,
        "addon-cpu" => $cpu,
        "addon-ram" => $ram,
        "addon-disk" => $disk
    );

    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function actionCloudVpsH2($action, $vpsid)
{
    $auth = getTokenH2();
    $data = array(
        "action" => $action,
        "vps-id" => $vpsid
    );

    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function extendCloudVpsH2($vpsid, $billing)
{
    $auth = getTokenH2();
    $data = array(
        "action" => "renew-vps",
        "vps-id" => $vpsid,
        "billing-cycle" => $billing
    );

    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function infoListVpsH2($vpsid)
{
    global $db;
    $auth = getTokenH2();
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cloudserver.h2cloud.vn/api/agency/vps/get-list-vps',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => '{
          "vps-id":' . $vpsid . '
      }',
        CURLOPT_HTTPHEADER => array(
            'api-username: ' . decryptAES($db->site('api_username_h2cloud')),
            'api-app: ' . decryptAES($db->site('api_app_h2cloud')),
            'api-secret: ' . decryptAES($db->site('api_secret_h2cloud')),
            'auth-token: ' . $auth,
            'Content-Type: application/json'
        )

    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}

function osCloudVpsH2()
{
    $auth = getTokenH2();
    $curl = H2executeCurl('https://cloudserver.h2cloud.vn/api/agency/get-list-os', 'GET', $auth);

    return $curl;
}
function getOSNameH2($inputString) {
    // Chuyển đổi chuỗi đầu vào thành chữ thường để dễ dàng so sánh
    $lowercaseString = strtolower($inputString);
    
    // Kiểm tra xem chuỗi đầu vào có chứa từ khóa "ubuntu" không
    if (strpos($lowercaseString, 'ubuntu') !== false) {
        return 'ubuntu';
    }
    
    // Kiểm tra xem chuỗi đầu vào có chứa từ khóa "centos" không
    if (strpos($lowercaseString, 'centos') !== false) {
        return 'centos';
    }

    if (strpos($lowercaseString, 'windows') !== false) {
        return 'windows';
    }
    if (strpos($lowercaseString, 'almalinux') !== false) {
        return 'almalinux';
    }
    if (strpos($lowercaseString, 'debian') !== false) {
        return 'debian';
    }
    
    // Nếu không phát hiện được từ khóa nào, trả về null
    return null;
}
function getImageSourceH2($name) {
    $name = getOSNameH2($name);
    $imagePaths = array(
        'centos' => '/assets/images/centos.png',
        'almalinux' => '/assets/images/almalinux.png',
        'ubuntu' => '/assets/images/ubuntu.png',
        'debian' => '/assets/images/debian.png',
        'windows' => '/assets/images/windows.png',
    );

    $defaultImagePath = '/windows.png';

    $keyword = strtolower(explode(" ", $name)[0]);
    return isset($imagePaths[$keyword]) ? $imagePaths[$keyword] : $defaultImagePath;
}