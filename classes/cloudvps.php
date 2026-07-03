<?php

function executeCurl($url, $method, $auth, $data = null)
{

    global $db;

    $curl = curl_init();

    $headers = array(
        'api-username: ' . decryptAES($db->site('api_username')),
        'api-app: ' . decryptAES($db->site('api_app')),
        'api-secret: ' . decryptAES($db->site('api_secret')),
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


function getToken()
{
    global $db;

    $response = executeCurl('https://portal.vncloud.net/api/agency/get-info', 'GET', $db->site('auth_token_vps'));

    if (isset($response['error']) && $response['error'] == 0) {
        return $db->site('auth_token_vps');
    } else {
        $data = array(
            "api-username" => decryptAES($db->site('api_username')),
            "api-app" => decryptAES($db->site('api_app')),
            "api-secret" => decryptAES($db->site('api_secret'))
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://portal.vncloud.net/api/agency/get-token',
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

        $db->update("options", array('value' => $result['auth-token']), " `key` = 'auth_token_vps' ");

        return $result['auth-token'];
    }
}

function addonVps()
{
    $auth = getToken();

    $curl = executeCurl('https://portal.vncloud.net/api/agency/get-product', 'GET', $auth);

    return $curl;
}

function createOrderCloud($productid, $billingcycle, $osid, $cpu, $ram, $disk)
{
    $auth = getToken();
    $data = array(
        "product-id" => $productid,
        "billing-cycle" => $billingcycle,
        "os" => $osid,
        "quantity" => "1",
        "addon-cpu" => $cpu,
        "addon-ram" => $ram,
        "addon-disk" => $disk
    );

    $curl = executeCurl('https://portal.vncloud.net/api/agency/order/create-order', 'POST', $auth, $data);

    return $curl;
}

function rebuildCloudVps($vpsid, $osid)
{
    $auth = getToken();
    $data = array(
        "action" => "confirm-rebuild-vps",
        "vps-id" => $vpsid,
        "os-id" => $osid
    );

    $curl = executeCurl('https://portal.vncloud.net/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function moneyUserCloudVps()
{
    $auth = getToken();
    $curl = executeCurl('https://portal.vncloud.net/api/agency/get-info', 'GET', $auth);

    return $curl['data']['credit'];
}

function upgradeCloudVps($vpsid, $cpu, $ram, $disk)
{
    $auth = getToken();
    $data = array(
        "action" => "addon-vps",
        "vps-id" => $vpsid,
        "addon-cpu" => $cpu,
        "addon-ram" => $ram,
        "addon-disk" => $disk
    );

    $curl = executeCurl('https://portal.vncloud.net/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function actionCloudVps($action, $vpsid)
{
    $auth = getToken();
    $data = array(
        "action" => $action,
        "vps-id" => $vpsid
    );

    $curl = executeCurl('https://portal.vncloud.net/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function extendCloudVps($vpsid, $billing)
{
    $auth = getToken();
    $data = array(
        "action" => "renew-vps",
        "vps-id" => $vpsid,
        "billing-cycle" => $billing
    );

    $curl = executeCurl('https://portal.vncloud.net/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function infoListVps($vpsid)
{
    global $db;
    $auth = getToken();
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://portal.vncloud.net/api/agency/vps/get-info-vps',
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
            'api-username: ' . decryptAES($db->site('api_username')),
            'api-app: ' . decryptAES($db->site('api_app')),
            'api-secret: ' . decryptAES($db->site('api_secret')),
            'auth-token: ' . $auth,
            'Content-Type: application/json'
        )

    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}

function osCloudVps()
{
    $auth = getToken();
    $curl = executeCurl('https://portal.vncloud.net/api/agency/get-list-os', 'GET', $auth);

    return $curl;
}
function getOSName($inputString) {
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
function getImageSource($name) {
    $name = getOSName($name);
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