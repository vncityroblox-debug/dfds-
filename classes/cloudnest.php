<?php
function cloudnestExecuteCurl($url, $method, $auth, $data = null)
{

    global $db, $proxyConfigNest;

    $curl = curl_init();

    $headers = array(
        'api-username: ' . decryptAES($db->site('api_username_cloudnest')),
        'api-app: ' . decryptAES($db->site('api_app_cloudnest')),
        'api-secret: ' . decryptAES($db->site('api_secret_cloudnest')),
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
function addonVpsCloudNest()
{
    $auth = getTokenCloudNest();

    $curl = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/get-product', 'GET', $auth);

    return $curl;
}
function osCloudVpsCloudNest()
{
    $auth = getTokenCloudNest();
    $curl = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/get-list-os', 'GET', $auth);

    return $curl;
}

function createOrderCloudNest($productid, $billingcycle, $osid, $cpu, $ram, $disk)
{
    $auth = getTokenCloudNest();
    $data = array(
        "product-id" => $productid,
        "billing-cycle" => $billingcycle,
        "os" => $osid,
        "quantity" => "1",
        "addon-cpu" => $cpu,
        "addon-ram" => $ram,
        "addon-disk" => $disk
    );

    $curl = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/order/create-order', 'POST', $auth, $data);

    return $curl;
}
function infoListVpsCloudNest($vpsid)
{
    global $db;
    $auth = getTokenCloudNest();
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://client.cloudnest.vn/api/agency/vps/get-info-vps',
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
            'api-username: ' . decryptAES($db->site('api_username_cloudnest')),
            'api-app: ' . decryptAES($db->site('api_app_cloudnest')),
            'api-secret: ' . decryptAES($db->site('api_secret_cloudnest')),
            'auth-token: ' . $auth,
            'Content-Type: application/json'
        )
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}
function upgradeCloudNest($vpsid, $cpu, $ram, $disk)
{
    $auth = getTokenCloudNest();
    $data = array(
        "action" => "addon-vps",
        "vps-id" => $vpsid,
        "addon-cpu" => $cpu,
        "addon-ram" => $ram,
        "addon-disk" => $disk
    );

    $curl = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function actionCloudNest($action, $vpsid)
{
    $auth = getTokenCloudNest();
    $data = array(
        "action" => $action,
        "vps-id" => $vpsid
    );

    $curl = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}

function extendCloudCloudNest($vpsid, $billing)
{
    $auth = getTokenCloudNest();
    $data = array(
        "action" => "renew-vps",
        "vps-id" => $vpsid,
        "billing-cycle" => $billing
    );

    $curl = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}
function rebuildCloudNest($vpsid, $osid)
{
    $auth = getTokenCloudNest();
    $data = array(
        "action" => "confirm-rebuild-vps",
        "vps-id" => $vpsid,
        "os-id" => $osid
    );

    $curl = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/vps/action-vps', 'POST', $auth, $data);

    return $curl;
}
function getTokenCloudNest()
{
    global $db;

    $response = cloudnestExecuteCurl('https://client.cloudnest.vn/api/agency/get-info', 'GET', $db->site('auth_token_vps_cloudnest'));

    if (isset($response['error']) && $response['error'] == 0) {
        return $db->site('auth_token_vps_cloudnest');
    } else {
        $data = array(
            "api-username" => decryptAES($db->site('api_username_cloudnest')),
            "api-app" => decryptAES($db->site('api_app_cloudnest')),
            "api-secret" => decryptAES($db->site('api_secret_cloudnest'))
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://client.cloudnest.vn/api/agency/get-token',
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

        $db->update("options", array('value' => $result['auth-token']), " `key` = 'auth_token_vps_cloudnest' ");

        return $result['auth-token'];
    }
}
