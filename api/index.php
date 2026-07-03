<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/..');

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

$routes = [
    '/' => '/index.php',
    '/home' => '/index.php',
    '/id' => '/id.php',
    '/login' => '/views/auth/login.php',
    '/forgot-password' => '/views/auth/forgot-password.php',
    '/logout' => '/views/auth/logout.php',
    '/register' => '/views/auth/register.php',
    '/bot.php' => '/bot.php',
    '/doilacloitoken' => '/models/cloudvps/token.php',
    '/ioncube' => '/views/ioncube/index.php',
    '/cronjob' => '/views/cronjob/index.php',
    '/user/history/cronjob' => '/views/cronjob/history.php',
    '/cloudvps' => '/views/cloudvps/index.php',
    '/user/history/vps' => '/views/cloudvps/history.php',
    '/user/history/platinum' => '/views/cloudvps/historyplatinum.php',
    '/user/history/cheap' => '/views/cloudvps/historycheap.php',
    '/vps/order' => '/views/vps/order.php',
    '/api/v1/cloudvps/plan' => '/models/cloudvps/plan.php',
    '/api/v1/cloudvps/addon' => '/models/cloudvps/addon.php',
    '/api/v1/cloudvps/os' => '/models/cloudvps/os.php',
    '/hosting' => '/views/hosting/index.php',
    '/user/history/hosting' => '/views/hosting/history.php',
    '/cron/hosting/expired' => '/models/cron/hosting/expired.php',
    '/cron/hosting/remove' => '/models/cron/hosting/remove.php',
    '/cron/hosting/extend' => '/models/cron/hosting/extend.php',
    '/design' => '/views/design/index.php',
    '/blogs' => '/views/blog/index.php',
    '/contact' => '/views/contact/index.php',
    '/privacy-policy' => '/views/contact/privacy.php',
    '/terms-condition' => '/views/contact/terms.php',
    '/user/favorites' => '/views/product/favorites.php',
    '/user/product' => '/views/profile/product.php',
    '/user/product/upload' => '/views/profile/product-upload.php',
    '/services' => '/views/product/services.php',
    '/user/history/product' => '/views/product/history.php',
    '/user/author-form' => '/views/profile/from.php',
    '/user/profile' => '/views/profile/index.php',
    '/user/withdraw' => '/views/profile/withdraw.php',
    '/user/balance' => '/views/profile/balance.php',
    '/user/log' => '/views/profile/log.php',
    '/user/dashboard' => '/views/profile/dashboard.php',
    '/user/change-password' => '/views/profile/change-password.php',
    '/user/security' => '/views/profile/security.php',
    '/bank' => '/views/deposit/index.php',
    '/card' => '/views/deposit/card.php',
    '/api-document' => '/views/apidocs/index.php',
    '/cron/deposit/check' => '/models/cron/bank.php',
    '/api/deposit/card' => '/models/callback/card.php',
    '/api/v1/order/cron' => '/handle/cronjob/order.php',
    '/api/v1/update/cron' => '/handle/cronjob/update.php',
    '/affiliates' => '/views/affiliate/index.php',
    '/model/login' => '/models/client/login.php',
    '/model/ioncube' => '/models/client/ioncube.php',
    '/model/seller/withdraw' => '/models/client/sellerwithdraw.php',
    '/model/register' => '/models/client/register.php',
    '/model/favorite' => '/models/client/favorite.php',
    '/model/reviews' => '/models/client/reviews.php',
    '/model/total/cash' => '/models/client/totalcash.php',
    '/model/order/product' => '/models/client/product.php',
    '/model/forgotpassword' => '/models/client/forgotpasword.php',
    '/model/update/info' => '/models/client/updateinfo.php',
    '/model/update/captcha' => '/models/client/updatecaptcha.php',
    '/model/update/features' => '/models/client/features.php',
    '/model/update/password' => '/models/client/updatepassword.php',
    '/model/withdraw' => '/models/client/withdraw.php',
    '/model/authenticator' => '/models/client/authenticator.php',
    '/model/card' => '/models/client/card.php',
    '/model/action/hosting' => '/models/client/actionhosting.php',
    '/model/action/vps' => '/models/client/actionvps.php',
    '/api/license' => '/models/client/license.php',
    '/libs/mails/notification' => '/libs/mails/notification.php',
    '/model/order/cloudvps' => '/models/client/buycloudvps.php',
    '/model/order/hosting' => '/models/client/buyhosting.php',
    '/model/order/cronjob' => '/models/client/buycronjob.php',
    '/model/update/cron' => '/models/client/updatecron.php',
    '/model/modal/cron/edit' => '/models/client/modal/cronedit.php',
    '/model/modal/cron/extend' => '/models/client/modal/cronextend.php',
    '/model/admin/delete' => '/models/admin/delete.php',
    '/model/admin/vps' => '/models/admin/vps.php',
    '/model/admin/update' => '/models/admin/update.php',
    '/model/admin/updates' => '/models/admin/updates.php',
    '/model/admin/withdraw' => '/models/admin/withdraw.php',
    '/model/admin/withdraw-product' => '/models/admin/withdraw-product.php',
    '/model/admin/host' => '/models/admin/host.php',
    '/cpanel/blog/category' => '/cpanel/views/blog/category.php',
    '/cpanel/blog/add' => '/cpanel/views/blog/blog-add.php',
    '/cpanel/blog/list' => '/cpanel/views/blog/blog-list.php',
    '/cpanel/home' => '/cpanel/index.php',
    '/cpanel/logs' => '/cpanel/views/history/logs.php',
    '/cpanel/transactions' => '/cpanel/views/history/transactions.php',
    '/cpanel/recharge/card' => '/cpanel/views/recharge/card.php',
    '/cpanel/recharge/card/config' => '/cpanel/views/recharge/card-config.php',
    '/cpanel/recharge' => '/cpanel/views/recharge/bank.php',
    '/cpanel/recharge/bank/config' => '/cpanel/views/recharge/config.php',
    '/cpanel/users/list' => '/cpanel/views/users/index.php',
    '/cpanel/promotions' => '/cpanel/views/recharge/promotions.php',
    '/cpanel/coupons' => '/cpanel/views/recharge/coupons.php',
    '/cpanel/affiliate/history' => '/cpanel/views/affiliate/history.php',
    '/cpanel/affiliate/withdraw' => '/cpanel/views/affiliate/withdraw.php',
    '/cpanel/affiliate/config' => '/cpanel/views/affiliate/config.php',
    '/cpanel/cron/config' => '/cpanel/views/cronjob/config.php',
    '/cpanel/cron/server' => '/cpanel/views/cronjob/server.php',
    '/cpanel/vps/config' => '/cpanel/views/vps/config.php',
    '/cpanel/vps/plan' => '/cpanel/views/vps/plan.php',
    '/cpanel/vps/addon' => '/cpanel/views/vps/addon.php',
    '/cpanel/vps/history' => '/cpanel/views/vps/history.php',
    '/cpanel/vps/platinum/history' => '/cpanel/views/vps/historyplatinum.php',
    '/cpanel/design/config' => '/cpanel/views/design/config.php',
    '/cpanel/hosting/add-host' => '/cpanel/views/hosting/add-hosting.php',
    '/cpanel/hosting/server' => '/cpanel/views/hosting/whm-list.php',
    '/cpanel/hosting/package' => '/cpanel/views/hosting/package.php',
    '/cpanel/hosting/history' => '/cpanel/views/hosting/history.php',
    '/cpanel/cron/order' => '/cpanel/views/cronjob/order.php',
    '/cpanel/product/categories' => '/cpanel/views/product/list-categories.php',
    '/cpanel/product/config' => '/cpanel/views/product/config.php',
    '/cpanel/product/withdraw' => '/cpanel/views/product/withdraw.php',
    '/cpanel/product/orders' => '/cpanel/views/product/orders.php',
    '/cpanel/product/list' => '/cpanel/views/product/list-products.php',
    '/cpanel/theme' => '/cpanel/views/system/theme.php',
    '/cpanel/settings' => '/cpanel/views/system/settings.php',
    '/api/cron/profile' => '/models/client/apicron/profile.php',
    '/api/cron/server' => '/models/client/apicron/server.php',
    '/api/cron/create' => '/models/client/apicron/create.php',
    '/api/cron/history' => '/models/client/apicron/history.php',
    '/api/cron/checkhistory' => '/models/client/apicron/checkhistory.php',
    '/api/cron/action' => '/models/client/apicron/action.php',
];

if (isset($routes[$path])) {
    $target = $routes[$path];
} elseif (preg_match('#^/reffer/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['ref'] = $matches[1];
    $target = '/views/auth/reffer.php';
} elseif (preg_match('#^/verify/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['token'] = $matches[1];
    $target = '/views/auth/verify.php';
} elseif (preg_match('#^/reset-password/([a-zA-Z0-9_-]+)#', $path, $matches)) {
    $_GET['token'] = $matches[1];
    $target = '/views/auth/reset-password.php';
} elseif (preg_match('#^/cloudvps/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/cloudvps/dangky.php';
} elseif (preg_match('#^/user/history/vps/dashboard/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/cloudvps/dashboard.php';
} elseif (preg_match('#^/user/history/vps/platinum/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/cloudvps/dashboardplatinum.php';
} elseif (preg_match('#^/user/history/vps/cheap/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/cloudvps/dashboardcheap.php';
} elseif (preg_match('#^/hosting/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/hosting/hostings.php';
} elseif (preg_match('#^/user/history/hosting/dashboard/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/hosting/dashboard.php';
} elseif (preg_match('#^/blog/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    $target = '/views/blog/view.php';
} elseif (preg_match('#^/user/product/edit/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/profile/product-edit.php';
} elseif (preg_match('#^/product/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/product/product.php';
} elseif (preg_match('#^/seller/([A-Za-z0-9-]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/views/product/seller.php';
} elseif (preg_match('#^/cpanel/blog/category/update/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/blog/update-category.php';
} elseif (preg_match('#^/cpanel/blog/list/update/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/blog/blog-update.php';
} elseif (preg_match('#^/cpanel/recharge/bank/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/recharge/edit.php';
} elseif (preg_match('#^/cpanel/user/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/users/edit.php';
} elseif (preg_match('#^/cpanel/coupon/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/recharge/edit-coupon.php';
} elseif (preg_match('#^/cpanel/cron/server/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/cronjob/edit-server.php';
} elseif (preg_match('#^/cpanel/vps/plan/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/vps/edit-plan.php';
} elseif (preg_match('#^/cpanel/vps/addon/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/vps/edit-addon.php';
} elseif (preg_match('#^/cpanel/vps/history/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/vps/edit-history.php';
} elseif (preg_match('#^/cpanel/vps/platinum/history/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/vps/edit-history-platinum.php';
} elseif (preg_match('#^/cpanel/hosting/server/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/hosting/whm-edit.php';
} elseif (preg_match('#^/cpanel/hosting/package/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/hosting/package-edit.php';
} elseif (preg_match('#^/cpanel/hosting/history/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/hosting/history-edit.php';
} elseif (preg_match('#^/cpanel/cron/order/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/cronjob/edit-order.php';
} elseif (preg_match('#^/cpanel/product/categories/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/product/edit-categories.php';
} elseif (preg_match('#^/cpanel/product/list/edit/([0-9]+)#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    $target = '/cpanel/views/product/edit-products.php';
} else {
    $target = $path;
}

$full_path = realpath(__DIR__ . '/..' . $target);
if ($full_path && is_file($full_path) && strpos($full_path, realpath(__DIR__ . '/..')) === 0) {
    $_SERVER['SCRIPT_FILENAME'] = $full_path;
    $_SERVER['SCRIPT_NAME'] = $target;
    $_SERVER['PHP_SELF'] = $target;
    
    require $full_path;
} else {
    http_response_code(404);
    $error404_path = realpath(__DIR__ . '/../error-404.html');
    if ($error404_path) {
        readfile($error404_path);
    } else {
        echo "404 Not Found";
    }
}
