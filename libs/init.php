<?php
// Require các thư viện PHP
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/config.php';
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/db.php'; // Database
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/session.php'; // Session
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/classdb.php'; // Classdb
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/functions.php';     // Function cơ bản 
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/hosting.php';
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/cloudvps.php';
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/cloudnest.php';
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/classes/h2cloud.php';

error_reporting(0);
$db = new DB();

   
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'];
define('DOMAIN', $base_url);
define('GOOGLE_APP_ID',$db->site('google_app_id'));
define('GOOGLE_APP_SECRET',$db->site('google_app_secret'));
define('GOOGLE_APP_CALLBACK_URL',DOMAIN.'/login/google');


$date = date('Y/m/d H:i:s', time());

$config = [
    "project" => "SOURCECODE_ENCRYPTION", 
    "version" => "3.5.8"
];
$list_api_stc = [
    'MBBANK' => 'https://api.sieuthicode.net/historyapimbbankv2',
    'BIDV' => 'https://api.sieuthicode.net/historyapibidvv2',
    'VCB' => 'https://api.sieuthicode.net/historyapivcbv2',
    'VIETTIN' => 'https://api.sieuthicode.net/historyapiviettinv2',
    'ACB' => 'https://api.sieuthicode.net/historyapiacbv2',
    'VIETTEL' => 'https://api.sieuthicode.net/historyapiviettelv2',
];
$list_api_azviet = [
    'MBBANK' => 'https://api.azviet.net/historyapimbbankv2',
    'BIDV' => 'https://api.azviet.net/historyapibidvv2',
    'VCB' => 'https://api.azviet.net/historyapivcbv2',
    'VIETTIN' => 'https://api.azviet.net/historyapiviettinv2',
    'ACB' => 'https://api.azviet.net/historyapiacbv2',
    'VIETTEL' => 'https://api.azviet.net/historyapiviettelv2',
];
$config_listbank = [
    'THESIEURE'      => 'Ví THESIEURE.COM',
    'MOMO'      => 'Ví điện tử MOMO',
    'Zalo Pay'      => 'Ví điện tử Zalo Pay',
    'VietinBank' => 'Ngân hàng TMCP Công thương Việt Nam VietinBank',
    'Vietcombank' => 'Ngân hàng TMCP Ngoại Thương Việt Nam Vietcombank',
    'BIDV' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam BIDV',
    'Agribank' => 'Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam Agribank',
    'OCB' => 'Ngân hàng TMCP Phương Đông OCB',
    'MBBank' => 'Ngân hàng TMCP Quân đội MBBank',
    'Techcombank' => 'Ngân hàng TMCP Kỹ thương Việt Nam Techcombank',
    'ACB' => 'Ngân hàng TMCP Á Châu ACB',
    'VPBank' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng VPBank',
    'TPBank' => 'Ngân hàng TMCP Tiên Phong TPBank',
    'Sacombank' => 'Ngân hàng TMCP Sài Gòn Thương Tín Sacombank',
    'HDBank' => 'Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh HDBank',
    'VietCapitalBank' => 'Ngân hàng TMCP Bản Việt VietCapitalBank',
    'SCB' => 'Ngân hàng TMCP Sài Gòn SCB',
    'VIB' => 'Ngân hàng TMCP Quốc tế Việt Nam VIB',
    'SHB' => 'Ngân hàng TMCP Sài Gòn - Hà Nội SHB',
    'Eximbank' => 'Ngân hàng TMCP Xuất Nhập khẩu Việt Nam Eximbank',
    'MSB' => 'Ngân hàng TMCP Hàng Hải MSB',
    'CAKE' => 'TMCP Việt Nam Thịnh Vượng - Ngân hàng số CAKE by VPBank CAKE',
    'Ubank' => 'TMCP Việt Nam Thịnh Vượng - Ngân hàng số Ubank by VPBank Ubank',
    'SaigonBank' => 'Ngân hàng TMCP Sài Gòn Công Thương SaigonBank',
    'BacABank' => 'Ngân hàng TMCP Bắc Á BacABank',
    'PVcomBank' => 'Ngân hàng TMCP Đại Chúng Việt Nam PVcomBank',
    'Oceanbank' => 'Ngân hàng Thương mại TNHH MTV Đại Dương Oceanbank',
    'NCB' => 'Ngân hàng TMCP Quốc Dân NCB',
    'ShinhanBank' => 'Ngân hàng TNHH MTV Shinhan Việt Nam ShinhanBank',
    'ABBANK' => 'Ngân hàng TMCP An Bình ABBANK',
    'VietABank' => 'Ngân hàng TMCP Việt Á VietABank',
    'NamABank' => 'Ngân hàng TMCP Nam Á NamABank',
    'PGBank' => 'Ngân hàng TMCP Xăng dầu Petrolimex PGBank',
    'VietBank' => 'Ngân hàng TMCP Việt Nam Thương Tín VietBank',
    'BaoVietBank' => 'Ngân hàng TMCP Bảo Việt BaoVietBank',
    'SeABank' => 'Ngân hàng TMCP Đông Nam Á SeABank',
    'COOPBANK' => 'Ngân hàng Hợp tác xã Việt Nam COOPBANK',
    'LienVietPostBank' => 'Ngân hàng TMCP Bưu Điện Liên Việt LienVietPostBank',
    'KienLongBank' => 'Ngân hàng TMCP Kiên Long KienLongBank',
    'KBank' => 'Ngân hàng Đại chúng TNHH Kasikornbank KBank',
    'GPBank' => 'Ngân hàng Thương mại TNHH MTV Dầu Khí Toàn Cầu GPBank',
    'CBBank' => 'Ngân hàng Thương mại TNHH MTV Xây dựng Việt Nam CBBank',
    'CIMB' => 'Ngân hàng TNHH MTV CIMB Việt Nam CIMB',
    'DBSBank' => 'DBS Bank Ltd - Chi nhánh Thành phố Hồ Chí Minh DBSBank',
    'DongABank' => 'Ngân hàng TMCP Đông Á DongABank',
    'KookminHCM' => 'Ngân hàng Kookmin - Chi nhánh Thành phố Hồ Chí Minh KookminHCM',
    'KookminHN' => 'Ngân hàng Kookmin - Chi nhánh Hà Nội KookminHN',
    'Woori' => 'Ngân hàng TNHH MTV Woori Việt Nam Woori',
    'VRB' => 'Ngân hàng Liên doanh Việt - Nga VRB',
    'StandardChartered' => 'Ngân hàng TNHH MTV Standard Chartered Bank Việt Nam StandardChartered',
    'HongLeong' => 'Ngân hàng TNHH MTV Hong Leong Việt Nam HongLeong',
    'HSBC' => 'Ngân hàng TNHH MTV HSBC (Việt Nam) HSBC',
    'IBKHN' => 'Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh Hà Nội IBKHN',
    'IBKHCM' => 'Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh TP. Hồ Chí Minh IBKHCM',
    'IndovinaBank' => 'Ngân hàng TNHH Indovina IndovinaBank',
    'Nonghyup' => 'Ngân hàng Nonghyup - Chi nhánh Hà Nội Nonghyup',
    'UnitedOverseas' => 'Ngân hàng United Overseas - Chi nhánh TP. Hồ Chí Minh UnitedOverseas',
    'PublicBank' => 'Ngân hàng TNHH MTV Public Việt Nam PublicBank',
    'Kasikorn Bank' => 'Kasikorn Bank',
    'Siam Commercial Bank'  => 'Siam Commercial Bank',
    'Bank of Ayudthya'  => 'Bank of Ayudthya',
    'Krungthai Bank'    => 'Krungthai Bank',
    'Bangkok Bank'      => 'Bangkok Bank',
    'ICICI Bank'        => 'ICICI Bank',
    'HDFC Bank'         => 'HDFC Bank',
    'State Bank of India'   => 'State Bank of India',
    'ABA Bank'     => 'ABA Bank Cambodia',
    'Wing Bank' => 'Wing Bank',
    'Maybank'   => 'Maybank',
    'CIMB Clicks Malaysia' => 'CIMB Clicks Malaysia',
    'United Bank for Africa (UBA)'  => 'United Bank for Africa (UBA)',
    'Wise.com'  => 'Wise.com',
    'Binance'   => 'Binance',
    'Bitcoin'   => 'Bitcoin',
    'USDT'      => 'USDT',
    'Payoneer'  => 'Payoneer',
    'Algérie Poste' => 'Algérie Poste',
    'Paysera'       => 'Paysera',
    'Mercado Pago'  => 'Mercado Pago',
    'Banco Inter'   => 'Banco Inter'
    
];
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
); 
//tạo session
$session = new Session();
$session->start();
// Kiểm tra session
// Check session
if ($session->get() != '') {
    $user = $session->get();
} else {
    $user = '';
}
$onlineUsers = [];
if ($user) {
    // Lấy dữ liệu tài khoản
    $sql_get_data_user = "SELECT * FROM `users` WHERE `username` = '{$user}'";
    if ($db->num_rows($sql_get_data_user)) {
        $data_user = $db->get_row($sql_get_data_user);
        if (empty($data_user)) {
            $data_user = "";
        } else {
            $data_user = $data_user;
        }
    }
}