<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function getRowReal($table, $id, $row)
{
    global $db;
    return $db->get_row("SELECT * FROM `$table` WHERE `product_id` = '$id' ")[$row] ?? '';
}
function getRowRealtime($table, $id, $row)
{
    global $db;
    return $db->get_row("SELECT * FROM `$table` WHERE `id` = '$id' ")[$row] ?? '';
}
function isProductFavorited($user_id, $product_id)
{
    global $db;
    if (!$user_id) {
        return false;
    }
    $is_favorite = $db->get_row("SELECT * FROM favorites WHERE user_id = '{$user_id}' AND product_id = '{$product_id}'");
    return $is_favorite;
}
function getRowUser($id, $row)
{
    global $db;
    return $db->get_row("SELECT * FROM `users` WHERE `id` = '$id' ")[$row] ?? 'NULL';
}
function withdrawProductTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) as total FROM product_withdraw WHERE `status` = 2")['total'] ?? 0);
}
function withdrawProductMonth()
{
    global $db;
    $year = date('Y');
    $month = date('m');
    $result = $db->get_row("SELECT SUM(amount) AS total FROM product_withdraw WHERE `status` = 2 AND YEAR(create_gettime) = $year AND MONTH(create_gettime) = $month");
    return format_cash($result['total'] ?? 0);
}
function withdrawProductWeekday()
{
    global $db;
    $startOfWeek = date('Y-m-d', strtotime('monday this week')); // Ngày đầu tuần
    $endOfWeek = date('Y-m-d', strtotime('sunday this week')); // Ngày cuối tuần
    $result = $db->get_row("SELECT SUM(amount) AS total_amount FROM product_withdraw WHERE `status` = 2 AND create_gettime >= '$startOfWeek 00:00:00' AND create_gettime <= '$endOfWeek 23:59:59'");
    return format_cash($result['total_amount'] ?? 0);
}
function withdrawProductDay()
{
    global $db;
    $today = date('Y-m-d');
    $result = $db->get_row("SELECT SUM(amount) AS total_amount FROM product_withdraw WHERE `status` = 2 AND DATE(create_gettime) = '$today'");
    return format_cash($result['total_amount'] ?? 0);
}
function productDay($user_id) 
{
    global $db; // Ensure $db is accessible
    return format_cash($db->get_row("SELECT SUM(seller_earning) AS total_amount FROM order_items WHERE seller_id = '{$user_id}' AND YEAR(created_at) = " . date('Y') . " AND MONTH(created_at) = " . date('m') . " AND DATE(created_at) = CURDATE();")['total_amount'] ?? 0); 
}
function productWeek($user_id) 
{
    global $db; // Ensure $db is accessible
    return format_cash($db->get_row("SELECT SUM(seller_earning) AS total_amount FROM order_items WHERE seller_id = '{$user_id}' AND YEAR(created_at) = " . date('Y') . " AND MONTH(created_at) = " . date('m') . " AND WEEK(created_at, 1) = WEEK(CURDATE(), 1);")['total_amount'] ?? 0);
}
function productMonth($user_id) 
{
    global $db; // Ensure $db is accessible
    return format_cash($db->get_row("SELECT SUM(seller_earning) AS total_amount FROM order_items WHERE seller_id = '{$user_id}' AND YEAR(created_at) = " . date('Y') . " AND MONTH(created_at) = " . date('m') . ";")['total_amount'] ?? 0);
}
function productYear($user_id) 
{
    global $db; // Ensure $db is accessible
    return format_cash($db->get_row("SELECT SUM(seller_earning) AS total_amount FROM order_items WHERE seller_id = '{$user_id}' AND YEAR(created_at) = " . date('Y') . ";")['total_amount'] ?? 0);
}

function rechargeBankMonth()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) as total FROM invoices WHERE YEAR(FROM_UNIXTIME(create_time)) = " . date('Y') . " AND MONTH(FROM_UNIXTIME(create_time)) = " . date('m') . "")['total'] ?? 0);
}
function rechargeBankWeekday()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) AS total_amount FROM invoices WHERE create_time >= UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)) AND create_time < UNIX_TIMESTAMP(DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY))")['total_amount'] ?? 0);
}
function rechargeBankDay()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) AS total_amount FROM invoices WHERE DATE(FROM_UNIXTIME(create_time)) = CURDATE();")['total_amount'] ?? 0);
}
function withdrawTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) as total FROM withdraw_ref WHERE `status` = 2")['total'] ?? 0);
}
function withdrawMonth()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) as total FROM withdraw_ref WHERE `status` = 2 AND YEAR(FROM_UNIXTIME(create_gettime)) = " . date('Y') . " AND MONTH(FROM_UNIXTIME(create_gettime)) = " . date('m') . "")['total'] ?? 0);
}
function withdrawWeekday()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) AS total_amount FROM withdraw_ref WHERE `status` = 2 AND create_gettime >= UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)) AND create_gettime < UNIX_TIMESTAMP(DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY))")['total_amount'] ?? 0);
}
function withdrawDay()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) AS total_amount FROM withdraw_ref WHERE `status` = 2 AND DATE(FROM_UNIXTIME(create_gettime)) = CURDATE();")['total_amount'] ?? 0);
}
function usersTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT COUNT(id) as total FROM users")['total'] ?? 0);
}
function usersMonthTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT COUNT(id) as total FROM users WHERE YEAR(create_date) = " . date('Y') . " AND MONTH(create_date) = " . date('m') . "")['total'] ?? 0);
}
function usersDayTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT COUNT(id) as total FROM users WHERE DATE(create_date) = CURDATE();")['total'] ?? 0);
}
function revenueTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) AS total_amount FROM invoices")['total_amount'] ?? 0);
}
function revenueMonthTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) as total FROM invoices WHERE YEAR(FROM_UNIXTIME(create_time)) = " . date('Y') . " AND MONTH(FROM_UNIXTIME(create_time)) = " . date('m') . "")['total'] ?? 0);
}
function revenueDayTotal()
{
    global $db;
    return format_cash($db->get_row("SELECT SUM(amount) AS total_amount FROM invoices WHERE DATE(FROM_UNIXTIME(create_time)) = CURDATE();")['total_amount'] ?? 0);
}
function getsvcategories($id_server)
{
    global $db;
    $ndk = $db->get_row(" SELECT * FROM `categories` WHERE `category_id` = '$id_server' AND `status` = '1' ");
    if ($ndk) {
        $result = $ndk['name'];
    } else {
        $result = 'Lỗi';
    }
    return $result;
}
function getAddonPrice($site,$type, $quantity, $billingcycle) {
    global $db;
    if ($quantity > 0) {
        $data = $db->get_row("SELECT * FROM `tbl_addon_vps` WHERE `type_addon` = '$type' AND `site` = '{$site}'");
        $detail = json_decode($data['price'], true);
        return $detail['pricing'][$billingcycle]['amount'] * $quantity;
    }
    return 0;
}
function checkCoupon($coupon, $user_id, $total_money)
{
    global $db;
    $coupon = Anti_xss($coupon);
    // check coupon có tồn tại hay không
    if ($coupon = $db->get_row("SELECT * FROM `tbl_coupons` WHERE `code` = '" . $coupon . "' AND `min` <= $total_money AND `max` >= $total_money AND `used` < `amount` ")) {
        // chek số lượng còn hay không
        if ($coupon['used'] < $coupon['amount']) {
            // check đã dùng hay chưa
            if (!$db->get_row("SELECT * FROM `tbl_coupon_used` WHERE `coupon_id` = '" . $coupon['id'] . "' AND `user_id` = '" . $user_id . "' ")) {
                return $coupon['discount'];
            }
            return false;
        }
        return false;
    }
    return false;
}
function checkPromotion($amount)
{
    global $db;
    foreach ($db->get_list("SELECT * FROM `promotions` WHERE `amount` <= '$amount' ORDER by `amount` DESC ") as $promotion) {
        $received = $amount + $amount * $promotion['discount'] / 100;
        return $received;
    }
    return $amount;
}
function count_cronjob_in_server($server_id)
{
    global $db;
    $query = "SELECT COUNT(*) as total FROM `cronjobs` WHERE `server_id` = '{$server_id}'";
    $result = $db->get_row($query);
    return $result['total'] ?? 0;
}
function categories_in_server($server_id)
{
    global $db;
    $query = "
      SELECT COUNT(*) AS total 
      FROM `products` 
      WHERE `category_id` = '{$server_id}' 
        AND `status` = 1
    ";
    $result = $db->get_row($query);
    return $result['total'] ?? 0;
}

function insert_log($user_id, $reason)
{
    global $db;
    $db->insert("logs", [
        'user_id' => $user_id,
        'ip' => myip(),
        'create_time' => time(),
        'device' => $_SERVER['HTTP_USER_AGENT'],
        'create_date' => gettime(),
        'action' => $reason,
    ]);
}
function RemoveCredits($user_id, $amount, $reason)
{
    global $db;
    $db->insert("log_balance", array(
        'money_before' => getRowUser($user_id, 'money'),
        'money_change' => $amount,
        'money_after' => getRowUser($user_id, 'money') - $amount,
        'time' => gettime(),
        'content' => $reason,
        'user_id' => $user_id
    ));
    $isRemove = $db->tru("users", "money", $amount, " `id` = '$user_id' ");
    if ($isRemove) {
        return true;
    } else {
        return false;
    }
}
function PlusCredits($user_id, $amount, $reason)
{
    global $db;
    $db->insert("log_balance", array(
        'money_before' => getRowUser($user_id, 'money'),
        'money_change' => $amount,
        'money_after' => getRowUser($user_id, 'money') + $amount,
        'time' => gettime(),
        'content' => $reason,
        'user_id' => $user_id
    ));
    $isPlus = $db->cong("users", "money", $amount, " `id` = '$user_id' ");
    if ($isPlus) {
        return true;
    } else {
        return false;
    }
}
function Banned($user_id, $reason)
{
    global $db;
    $db->insert("logs", [
        'user_id' => $user_id,
        'ip' => myip(),
        'create_time' => time(),
        'device' => $_SERVER['HTTP_USER_AGENT'],
        'created_at' => gettime(),
        'action' => $reason,
    ]);
    $db->update("users", array(
        'banned' => 1
    ), "id = '" . $user_id . "' ");
}
function addRef($user_id, $price, $note = '')
{
    global $db;
    if ($db->site('status_ref') != 1) {
        return false;
    }
    $getUser = $db->get_row(" SELECT * FROM `users` WHERE `id` = '$user_id' ");
    if ($getUser['ref_id'] != 0) {
        //check ip
        if (getRowUser($getUser['ref_id'], 'ip') == $getUser['ip']) {
            return false;
        }
        $ck = $db->site('ck_ref');
        if (getRowUser($getUser['ref_id'], 'ref_ck') != 0) {
            $ck = getRowUser($getUser['ref_id'], 'ref_ck');
        }
        $price = $price * $ck / 100;
        $db->cong('users', 'ref_money', $price, " `id` = '" . $getUser['ref_id'] . "' ");
        $db->cong('users', 'ref_total_money', $price, " `id` = '" . $getUser['ref_id'] . "' ");
        $db->cong('users', 'ref_amount', $price, " `id` = '" . $getUser['ref_id'] . "' ");
        $db->insert('log_ref', [
            'user_id'       => $getUser['ref_id'],
            'reason'        => $note,
            'sotientruoc'   => getRowUser($getUser['ref_id'], 'ref_money') - $price,
            'sotienthaydoi' => $price,
            'sotienhientai' => getRowUser($getUser['ref_id'], 'ref_money'),
            'created_at'    => gettime()
        ]);
        return true;
    }
    return false;
}
function pusher($username = null, $data_array = null)
{
    global $db;
    $pushdata = $db->get_row("SELECT * FROM `tb_pusher` ORDER BY RAND() LIMIT 1");
    $options = array(
        'cluster' => $pushdata['pusher_cluster'],
        'useTLS' => true
    );
    $pusher = new Pusher\Pusher(
        $pushdata['pusher_key'],
        $pushdata['pusher_secret'],
        (int)$pushdata['pusher_app_id'],
        $options
    );
    return $pusher->trigger($username, 'realtime', $data_array);
}
function whereInvoicePending($payment_method, $amount)
{
    global $db;
    return $db->get_list(
        "SELECT * FROM `invoices` WHERE 
        `status` = 0 AND 
        `payment_method` = '$payment_method' AND 
        `pay` <= '$amount' AND 
        `fake` = 0
        ORDER BY id DESC "
    );
}
function insetLog($user_id, $reason)
{
    global $db;
    $db->insert("logs", [
        'user_id'       => $user_id,
        'ip'            => myip(),
        'create_time' => time(),
        'device'        => $_SERVER['HTTP_USER_AGENT'],
        'create_date'    => gettime(),
        'action'        => $reason
    ]);
}
function sendCSM($mail_nhan, $ten_nhan, $chu_de, $noi_dung, $bcc = '', $path = '')
{
    global $db;
    if ($db->site('pass_email_smtp') != '') {
        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = "html";
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $db->site('email_smtp');
        $mail->Password = $db->site('pass_email_smtp');
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom($db->site('email_smtp'), $bcc);
        $mail->addAddress($mail_nhan, $ten_nhan);
        $mail->addAttachment($path);
        $mail->addReplyTo($db->site('email_smtp'), $bcc);
        $mail->isHTML(true);
        $mail->Subject = $chu_de;
        $mail->Body    = $noi_dung;
        $mail->CharSet = 'UTF-8';
        $send = $mail->send();
        return $send;
    }
    return 'Chưa cấu hình SMTP';
}
function uploadAndSaveOption($inputName, $optionKey)
{
    global $db;
    if (check_img($inputName)) {
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', rand(5, 10));
        $destination_path = realpath($_SERVER["DOCUMENT_ROOT"]);
        $uploads_dir_audio = $destination_path . '/upload/theme/' . $rand . '.png';
        $uploads_dir = '/upload/theme/' . $rand . '.png';
        $tmp_name = $_FILES[$inputName]['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, $uploads_dir_audio);
        if ($addlogo) {
            $db->update('options', [
                'value'  => $uploads_dir
            ], " `key` = '$optionKey' ");
        }
    }
}
function sendMessAdmin($my_text)
{
    if ($my_text != "") {
        return sendMessTelegram($my_text);
    }
    return false;
}
function sendMessUser($my_text, $user_id)
{
    if ($my_text != "") {
        return sendMessTelegramUser($my_text, $user_id);
    }
    return false;
}
function sendMessTelegramUser($my_text, $user_id)
{
    global $db;
    if (!$checkUser = $db->get_row("select * from `users` where `id` = '{$user_id}'")) {
        return false;
    }
    if ($checkUser['telegram_id'] == "") {
        return false;
    }
    if ($checkUser['telegram_token'] == "") {
        return false;
    }
    if ($my_text == "") {
        return false;
    }
    if ($checkUser['status_telegram']  == 2 && $checkUser['telegram_token'] != "" && $checkUser['telegram_id'] != "") {
        $telegram_url = "https://bypass-telegram.cmsnt.workers.dev/bot" . $checkUser['telegram_token'] . "/sendMessage";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["chat_id" => $checkUser['telegram_id'], "text" => $my_text, "parse_mode" => "HTML"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    return false;
}
function sendMessTelegram($my_text, $token = "", $chat_id = "")
{
    global $db;
    if ($chat_id == "") {
        $chat_id = $db->site("telegram_chat_id");
    }
    if ($token == "") {
        $token = $db->site("telegram_token");
    }
    if ($my_text == "") {
        return false;
    }
    if ($db->site("telegram_status") == 1 && $token != "" && $chat_id != "") {
        $telegram_url = "https://bypass-telegram.cmsnt.workers.dev/bot" . $token . "/sendMessage";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["chat_id" => $chat_id, "text" => $my_text, "parse_mode" => "HTML"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    return false;
}
function napthe($telco, $amount, $serial, $pin, $trans_id)
{
    global $db;
    $partner_id = $db->site("card_partner_id");
    $partner_key = $db->site("card_partner_key");
    $url =  $db->site("card_url_api") . "?sign=" . md5($partner_key . $pin . $serial) . "&telco=" . $telco . "&code=" . $pin . "&serial=" . $serial . "&amount=" . $amount . "&request_id=" . $trans_id . "&partner_id=" . $partner_id . "&command=charging";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}
function pauseCronJobs($ids)
{
    global $db;
    $sanitizedIds = implode(',', array_map(function ($id) use ($db) {
        return "'" . Anti_xss($id) . "'";
    }, $ids));
    $query = "UPDATE cronjobs SET status = 'paused' WHERE id IN ($sanitizedIds)";
    $db->query($query);
}
function activateCronJobs($ids)
{
    global $db;
    $sanitizedIds = implode(',', array_map(function ($id) use ($db) {
        return "'" . Anti_xss($id) . "'";
    }, $ids));
    $query = "UPDATE cronjobs SET status = 'active' WHERE id IN ($sanitizedIds)";
    $db->query($query);
}

function numFavorites($user_id)
{
    global $db;
    if (!$user_id) {
        return 0;
    }
    $is_favorite = $db->get_row("SELECT COUNT(*) FROM favorites WHERE user_id = '{$user_id}'");
    return $is_favorite['COUNT(*)'];
}

function updateRank($userId) {
    global $db;

    $user = $db->get_row("SELECT total_money FROM users WHERE id = '{$userId}'");
   
    if (!$user) {
        return;
    }

    $rank = $db->get_row("
        SELECT id FROM ranks 
        WHERE required_amount <= '{$user['total_money']}' 
        ORDER BY required_amount DESC 
        LIMIT 1
    ");
    if ($rank) {
        $db->query("UPDATE users SET rank_id = '{$rank['id']}' WHERE id = '{$userId}'");
    }
}

function canPurchase($userId, $productId)
{
    global $db;

    // Fetch product details
    $product = $db->get_row("SELECT sale_price, rank_id FROM products WHERE product_id = '{$productId}'");
    if (!$product) {
        return false;
    }

    // If no rank is required
    if ((int)$product['rank_id'] === 0) {
        return true;
    }

    // Fetch user rank and required amount
    $userRank = $db->get_row("
        SELECT users.rank_id, ranks.required_amount 
        FROM users 
        JOIN ranks ON users.rank_id = ranks.id 
        WHERE users.id = '{$userId}'
    ");

    if (!$userRank) {
        return false;
    }

    // Compare rank IDs after casting them to integers
    return (int)$userRank['rank_id'] >= (int)$product['rank_id'];
}


function getUserRank($userId)
{
    global $db;

    $userRank = $db->get_row("
        SELECT ranks.name AS rank_name 
        FROM users 
        JOIN ranks ON users.rank_id = ranks.id 
        WHERE users.id = '{$userId}'
    ");

    if ($userRank) {
        return $userRank['rank_name'];
    }
    return 'No rank';
}
function deleteHashtags($productId) {
    global $db;
    $db->query("DELETE FROM product_hashtags WHERE product_id = '{$productId}'");
}

function saveHashtags($productId, $hashtags) {
    global $db;
    foreach ($hashtags as $hashtag) {
        $db->query("INSERT INTO product_hashtags (product_id, hashtag) VALUES ('$productId', '{$hashtag}')");
    }
}

function getRelatedTags($productId) {
    global $db;

    $currentProductHashtags = $db->get_list("
        SELECT hashtag
        FROM product_hashtags
        WHERE product_id = '" . intval($productId) . "'
    ");

    if (empty($currentProductHashtags)) {
        return [];
    }

    $hashtags = array_map(function($tag) {
        return "'" . addslashes($tag['hashtag']) . "'";
    }, $currentProductHashtags);

    $hashtagsList = implode(',', $hashtags);

    $relatedStmt = $db->get_list("
        SELECT DISTINCT hashtag
        FROM product_hashtags
        WHERE hashtag IN ($hashtagsList)
          AND product_id != '" . intval($productId) . "'
    ");
   
    return $relatedStmt;
}
function check_license($license)
{
    global $base_url;
    $apiUrl = "https://license.azviet.net/verify.php";
    $data = array(
        'domain' => $_SERVER['HTTP_HOST'],
        'license' => $license
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Lỗi cURL: ' . curl_error($ch);
    }
    curl_close($ch);
    $response = json_decode($result, true);
    return $response;
}
function active_license()
{
    global $db;
    if ($db->site('license') == '' || check_license($db->site('license'))['status'] == 'error') {
        if (isset($_POST['btnSaveLicense'])) {
            if ($db->site('status_demo') == '1') {
                die('<script type="text/javascript">if(!alert("Chức năng này không khả dụng trên trang web DEMO!")){window.history.back().location.reload();}</script>');
            }
            foreach ($_POST as $key => $value) {
                $db->update("options", array(
                    'value' => $value,
                ), " `key` = '$key' ");
            }
            $response = check_license($db->site('license'));
            if ($response['status'] == 'error') {
                die('<script type="text/javascript">if(!alert("' . $response['msg'] . '")){window.history.back().location.reload();}</script>');
            }
            die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
        }
        ?>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">License</h1>
            <div class="ms-md-1 ms-0">

            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h3 class="card-title">THÔNG TIN BẢN QUYỀN CODE</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group row mb-3">
                                <label class="col-sm-4 col-form-label">Mã bản quyền (license key)</label>
                                <div class="col-sm-8">
                                    <div class="form-line">
                                        <input type="text" name="license"
                                            placeholder="Nhập mã bản quyền của bạn để sử dụng chức năng này"
                                            value="<?=$db->site('license');?>" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <center>
                                <button type="submit" name="btnSaveLicense" class="btn btn-primary btn-block">
                                    <span>Save</span></button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h3 class="card-title">HƯỚNG DẪN</h3>
                    </div>
                    <div class="card-body">
                        <p>Để có thể lấy License key tại đây: <a target="_blank" href="https://t.me/BuiDucThanh">https://t.me/BuiDucThanh</a>
                            </p>
                            <p>Nếu quý khách mua hàng tại AzViet.Net mà chưa có License key, vui lòng liên hệ Facebook
                                <b>https://t.me/BuiDucThanh</b> để được cấp.
                            </p>
                            <p>Chỉ áp dụng cho những ai mua code, không hỗ trợ những trường hợp mua lại hay sử dụng mã nguồn lậu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php';
        die();
    }
}