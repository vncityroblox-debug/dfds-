<?php
function extractDomain($domain)
{
    $dotPosition = strpos($domain, '.');
    if ($dotPosition !== false) {
        return substr($domain, 0, $dotPosition);
    } else {
        return $domain;
    }
}
function getDiskViaAPI($whmHost, $whmUser, $whmPass)
{
    $apiUrl = "http://$whmHost:2082/execute/Quota/get_quota_info";
    $addCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $addCronResult = json_decode($addCronResponse, true);
    return $addCronResult;
}
function blockIP($whmHost, $whmUser, $whmPass, $ip)
{
    $apiUrl = "http://$whmHost:2082/execute/BlockIP/add_ip?ip=$ip";
    $addCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $addCronResult = json_decode($addCronResponse, true);
    return $addCronResult;
}
function unBlockIP($whmHost, $whmUser, $whmPass, $ip)
{
    $apiUrl = "http://$whmHost:2082/execute/BlockIP/remove_ip?ip=$ip";
    $addCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $addCronResult = json_decode($addCronResponse, true);
    return $addCronResult;
}
function getBandWidthViaAPI($whmHost, $whmUser, $whmPass)
{
    $apiUrl = "http://$whmHost:2082/execute/Quota/get_local_quota_info";
    $addCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $addCronResult = json_decode($addCronResponse, true);
    return $addCronResult;
}
function changeDomainViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $domain)
{
    $data = array(
        'api.version' => 1,
        'user' => $cpanelUser,
        'domain' => $domain,
    );
    $apiUrl = "http://$whmHost:2086/json-api/modifyacct?" . http_build_query($data);
    $addCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $addCronResult = json_decode($addCronResponse, true);
    return $addCronResult;
}
function addCronJobViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $minute, $command)
{
    $data = array(
        'cpanel_jsonapi_apiversion' => 2,
        'cpanel_jsonapi_module' => 'Cron',
        'cpanel_jsonapi_func' => 'add_line',
        'cpanel_jsonapi_user' => $cpanelUser,
        'command' => $command,
        'day' => '*',
        'hour' => '*',
        'minute' => "*/$minute",
        'month' => '*',
        'weekday' => '*',
    );
    $apiUrl = "http://$whmHost:2086/json-api/cpanel?" . http_build_query($data);
    $addCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $addCronResult = json_decode($addCronResponse, true);
    return $addCronResult;
}
function addSubDomainViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $subdomain, $rootdomain)
{
    $result = $subdomain . '.' . $rootdomain;
    $data = array(
        'cpanel_jsonapi_apiversion' => 2,
        'cpanel_jsonapi_module' => 'SubDomain',
        'cpanel_jsonapi_func' => 'addsubdomain',
        'cpanel_jsonapi_user' => $cpanelUser,
        'domain' => $subdomain,
        'rootdomain' => $rootdomain,
        'dir' => "$result/public_html",
        'disallowdot' => 1,
    );
    $apiUrl = "http://$whmHost:2086/json-api/cpanel?" . http_build_query($data);
    $listCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $listCronResult = json_decode($listCronResponse, true);
    return $listCronResult;
}
function addAddonDomainViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $domain)
{
    $data = array(
        'cpanel_jsonapi_apiversion' => 2,
        'cpanel_jsonapi_module' => 'AddonDomain',
        'cpanel_jsonapi_func' => 'addaddondomain',
        'cpanel_jsonapi_user' => $cpanelUser,
        'subdomain' => $domain,
        'newdomain' => $domain,
        'dir' => "$domain/public_html",
    );
    $apiUrl = "http://$whmHost:2086/json-api/cpanel?" . http_build_query($data);
    $listCronResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $listCronResult = json_decode($listCronResponse, true);
    return $listCronResult;
}
function unsuspendacctHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser)
{
    $data = array(
        'api.version' => 1,
        'user' => $cpanelUser,
    );
    $apiUrl = "http://$whmHost:2086/json-api/unsuspendacct?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}
function loginCpanelHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $service)
{
    $data = array(
        'api.version' => 1,
        'user' => $cpanelUser,
        'service' => $service,
        'locale' => "vi",
        'app' => "awstats",
    );
    $apiUrl = "http://$whmHost:2086/json-api/create_user_session?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}
function suspendacctHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $reason)
{
    $data = array(
        'api.version' => 1,
        'user' => $cpanelUser,
        'reason' => $reason,
    );
    $apiUrl = "http://$whmHost:2086/json-api/suspendacct?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}
function changePasswordHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $password)
{


    $data = array(
        'api.version' => 1,
        'user' => $cpanelUser,
        'password' => $password,
        'enabledigest' => 0,
        'db_pass_update' => 1,
    );
    $apiUrl = "http://" . $whmHost . ":2086/json-api/passwd?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}
function changePackageHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $package)
{
    $data = array(
        'api.version' => 1,
        'user' => $cpanelUser,
        'pkg' => $package,
    );
    $apiUrl = "http://$whmHost:2086/json-api/changepackage?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}
function removeHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $domain)
{
    $data = array(
        'api.version' => 1,
        'username' => $cpanelUser,
        'keepdns' => 0
    );
    $data2 = array(
        'api.version' => 1,
        'domain' => $domain,
    );
    $apiUrl = "http://$whmHost:2086/json-api/removeacct?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    $apiUrl2 = "http://$whmHost:2086/json-api/killdns?" . http_build_query($data2);
    $createResponse2 = callWHMAPI($apiUrl2, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}
function createHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $domain, $email, $packageName, $password)
{
    $data = array(
        'api.version' => 1,
        'username' => $cpanelUser,
        'domain' => $domain,
        'contactemail' => $email,
        'pkgname' => $packageName,
        'password' => $password,
    );
    $apiUrl = "http://$whmHost:2086/json-api/createacct?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}

function createResellerHostingViaAPI($whmHost, $whmUser, $whmPass, $cpanelUser, $domain, $email, $packageName, $password)
{
    $data = array(
        'api.version' => 1,
        'username' => $cpanelUser,
        'domain' => $domain,
        'contactemail' => $email,
        'pkgname' => $packageName,
        'password' => $password,
        'reseller' => 1,
    );
    $apiUrl = "http://$whmHost:2086/json-api/createacct?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}

function setResellerAccount($whmHost, $whmUser, $whmPass, $cpanelUser, $accountLimit)
{
    $data_accountlimit = array(
        'api.version' => 1,
        'user' => $cpanelUser,
        'account_limit' => $accountLimit,
        'enable_account_limit' => 1
    );
    $apiUrl_accountlimit = "http://$whmHost:2086/json-api/setresellerlimits?" . http_build_query($data_accountlimit);
    $createResponse_accountlimit = callWHMAPI($apiUrl_accountlimit, $whmUser, $whmPass);

    if (json_decode($createResponse_accountlimit, true)['metadata']['result'] == 1) {
        $data_permissions = array(
            'api.version' => 1,
            'reseller' => $cpanelUser,
            'acllist' => 'RSL'
        );
        $apiUrl_permissions = "http://$whmHost:2086/json-api/setacls?" . http_build_query($data_permissions);
        $createResponse_permissions = callWHMAPI($apiUrl_permissions, $whmUser, $whmPass);
        return json_decode($createResponse_permissions, true);
    } else {
        return json_decode($createResponse_accountlimit, true);
    }
}

function setResellerAccountLimit($whmHost, $whmUser, $whmPass, $cpanelUser, $AccountLimit)
{
    $data = array(
        'api.version' => 1,
        'user' => $cpanelUser,
        'account_limit' => $AccountLimit
    );
    $apiUrl = "http://$whmHost:2086/json-api/setresellerlimits?" . http_build_query($data);
    $createResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($createResponse, true);
}

function getHostingPackageViaAPI($whmHost, $whmUser, $whmPass)
{
    $data = array(
        'api.version' => 1,
    );
    $queryString = http_build_query($data);
    $apiUrl = "http://$whmHost:2086/json-api/listpkgs?$queryString";
    $packageResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($packageResponse, true);
}

function createHostingPackageViaAPI($whmHost, $whmUser, $whmPass, $packageData)
{
    $queryString = http_build_query($packageData);
    $apiUrl = "http://$whmHost:2086/json-api/addpkg?$queryString";
    $packageResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($packageResponse, true);
}
function updateHostingPackageViaAPI($whmHost, $whmUser, $whmPass, $packageData)
{
    $queryString = http_build_query($packageData);
    $apiUrl = "http://$whmHost:2086/json-api/editpkg?$queryString";
    $packageResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($packageResponse, true);
}
function checkDomainHostingPackageViaAPI($whmHost, $whmUser, $whmPass)
{
    $apiUrl = "http://$whmHost:2086/json-api/get_domain_info?api.version=1";
    $packageResponse = callWHMAPI($apiUrl, $whmUser, $whmPass);
    return json_decode($packageResponse, true);
}
function callWHMAPI($apiUrl, $whmUser, $whmPass)
{
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
    $userAgentArray[] = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36 OPR/49.0.2725.47";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36 Edge/15.15063";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 OPR/49.0.2725.64";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/62.0.3202.94 Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0;  Trident/5.0)";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/63.0.3239.84 Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0;  Trident/5.0)";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0";
    $userAgentArray[] = "Mozilla/5.0 (iPad; CPU OS 11_1_2 like Mac OS X) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0 Mobile/15B202 Safari/604.1";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Safari/604.1.38";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; CrOS x86_64 9901.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.97 Safari/537.36";

    $getArrayKey = array_rand($userAgentArray);
    $randomUserAgent = $userAgentArray[$getArrayKey];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$whmUser:$whmPass");
   
    curl_setopt($ch, CURLOPT_USERAGENT, $randomUserAgent);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
function checkHetHan($timestamp)
{
    $expiryDate = date('Y-m-d', $timestamp);
    $currentDate = date('Y-m-d');
    if ($expiryDate <= $currentDate) {
        return true;
    } else {
        return false;
    }
}
function blockHost($timestamp, $lockDays)
{
    $lockTimestamp = $timestamp + ($lockDays * 24 * 60 * 60);
    $currentTimestamp = time();
    if ($currentTimestamp >= $lockTimestamp) {
        return true;
    } else {
        return false;
    }
}
function notiExpkHost($expiryTimestamp, $reminderDays)
{
    $reminderTimestamp = $expiryTimestamp - ($reminderDays * 24 * 60 * 60);
    $currentTimestamp = time();
    if ($currentTimestamp >= $reminderTimestamp) {
        return true;
    } else {
        return false;
    }
}
