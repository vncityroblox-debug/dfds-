<?php
class Redirect
{
    public function __construct($url = null)
    {
        if ($url) {
            echo '<script>location.href="' . $url . '";</script>';
        }
    }
}
function getBase64QR($url) {
    // Lấy mã QR từ URL
    $image = file_get_contents($url);
    // Chuyển đổi ảnh thành chuỗi base64
    return 'data:image/png;base64,' . base64_encode($image);
}
function online($time)
{
    if (time() - $time <= 300) {
        return '<span class="badge badge-success"><i class="fa-solid fa-circle"></i>Online</span>';
    } else {
        return '<span class="badge bg-soft-danger"><i class="fa-solid fa-circle"></i>Offline</span>';
    }
}
function status_hosting($status)
{
    $statusMapping = [
        'active' => '<span class="badge bg-success">Hoạt động</span>',
        'expired' => '<span class="badge bg-danger">Hết hạn</span>',
        'suspended' => '<span class="badge bg-warning">Tạm khóa</span>',
    ];
    return $statusMapping[$status] ?? '<span class="badge bg-orange">Khác</span>';
}
function status_cron($status)
{
    $statusMapping = [
        'active' => '<span class="badge bg-success">Đang chạy</span>',
        'paused' => '<span class="badge bg-danger">Tạm dừng</span>',
        'expired' => '<span class="badge bg-danger">Hết hạn</span>'
    ];
    return $statusMapping[$status] ?? '<span class="badge bg-warning">Khác</span>';
}
function status_license($status)
{
    $statusMapping = [
        '2' => '<span class="badge bg-success">Công khai</span>',
        '1' => '<span class="badge bg-info">Riêng tư</span>'
    ];
    return $statusMapping[$status] ?? '<span class="badge bg-warning">Khác</span>';
}
function status_refund($status)
{
    $statusMapping = [
        '0' => '<span class="badge bg-success">KHÔNG</span>',
        '1' => '<span class="badge bg-danger">CÓ</span>'
    ];
    return $statusMapping[$status] ?? '<span class="badge bg-warning">Khác</span>';
}
function xss($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
function Anti_xss($data)
{
    // Fix &entity\n;
    $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
    $query_string = $_SERVER['QUERY_STRING'];
    $sql_injection = array(
        "union",
        "coockie",
        "concat",
        "alter",
        "exec",
        "shell",
        "wget",
        "**/",
        "/**",
        "0x3a",
        "null",
        "DR/**/OP/",
        "drop",
        "/*",
        "*/",
        "*",
        "--",
        ";",
        "||",
        "' #",
        "or 1=1",
        "'1'='1",
        "BUN",
        "S@BUN",
        "char",
        "OR%",
        "`",
        "[",
        "]",
        "<",
        ">",
        "++",
        "script",
        "1,1",
        "substring",
        "ascii",
        "sleep(",
        "insert",
        "between",
        "values",
        "truncate",
        "benchmark",
        "sql",
        "mysql",
        "%27",
        "%22",
        "(",
        ")",
        "<?",
        "<?php",
        "?>",
        "../",
        "/localhost",
        "127.0.0.1",
        "loopback",
        ":",
        "%0A",
        "%0D",
        "%3C",
        "%3E",
        "%00",
        "%2e%2e",
        "input_file",
        "execute",
        "mosconfig",
        "environ",
        "scanner",
        "path=.",
        "mod=.",
        "eval\(",
        "javascript:",
        "base64_",
        "boot.ini",
        "etc/passwd",
        "self/environ",
        "md5",
        "echo.*kae",
        "=%27$",
        "'",
        '"'
    );
    foreach ($sql_injection as $key) {
        if (strlen($query_string) > 255 or strpos(strtolower($query_string), strtolower($key)) !== false) {
            new Redirect("/");
        }
    }
    $data = addslashes(trim($data));
    do {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);
    // we are done...
    return $data;
}
function Anti_xsss($data)
{
    $data = html_entity_decode($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $data = preg_replace('#(<[^>]+?)(on\w+|xmlns)[^>]*>#iu', '$1>', $data);

    $data = preg_replace('#(href|src)\s*=\s*["\']?\s*javascript:[^"\'>]*["\']?#i', '$1="#"', $data);
    $data = preg_replace('#(href|src)\s*=\s*["\']?\s*vbscript:[^"\'>]*["\']?#i', '$1="#"', $data);
    $data = preg_replace('#(href|src)\s*=\s*["\']?\s*data:[^"\'>]*["\']?#i', '$1="#"', $data);

    do {
        $old_data = $data;
        $data = preg_replace('#</?(script|iframe|object|embed|applet|meta|style|form|input|textarea|button|link|frame|frameset)[^>]*>#i', '', $data);
    } while ($old_data !== $data);

    $data = strip_tags($data, '<a><p><br><b><strong><i><em><ul><ol><li>');

    return trim($data);
}
function format_cash($price)
{
    return str_replace(",", ".", number_format($price));
}
function custom_cal_days_in_month($month, $year)
{
    if ($month < 1 || $month > 12 || $year < 0) {
        return false;
    }
    $nextMonth = $month % 12 + 1;
    $nextYear = ($month == 12) ? $year + 1 : $year;
    $lastDayOfNextMonth = mktime(0, 0, 0, $nextMonth, 0, $nextYear);
    $numberOfDays = date('d', $lastDayOfNextMonth);

    return $numberOfDays;
}
function qr_bank($type, $stk, $accountname, $amount, $comment)
{
    if ($type == 'MOMO') {
        $result = 'data:image/png;base64,' . base64_encode(file_get_contents("https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=2|99|$stk|||0|0|$amount|$comment|transfer_myqr"));
    } else {
        $result = "https://api.vietqr.io/$type/$stk/$amount/$comment/qronly2.jpg?accountName=$accountname";
    }
    return $result;
}
function status_card($status)
{
    $status_labels = [
        'completed' => '<span class="badge bg-success">Thành công</span>',
        'error' => '<button class="status-badge in-active">Thẻ lỗi</button>',
        'pending' => '<span class="status-badge pending">Đang xử lý</button>'
    ];
    return $status_labels[$status] ?? '<span class="badge bg-danger">Khác</span>';
}
function pagination_client($url, $page, $total_items, $limit)
{
    $total_pages = ceil($total_items / $limit);
    if ($total_pages <= 1) return ''; // Nếu chỉ có 1 trang thì không hiển thị phân trang.

    $out = ['<div class="pagination">'];
    $neighbors = 2;

    // Nút "Trước"
    if ($page > 1) {
        $out[] = '<a href="' . $url . 'page=' . ($page - 1) . '">&laquo;</a>';
    }

    // Hiển thị trang đầu tiên nếu không nằm trong khoảng lân cận
    if ($page > $neighbors + 1) {
        $out[] = '<a href="' . $url . 'page=1">1</a>';
        if ($page > $neighbors + 2) $out[] = '<a role="button">...</a>';
    }

    // Hiển thị các trang lân cận
    for ($i = max(1, $page - $neighbors); $i <= min($total_pages, $page + $neighbors); $i++) {
        if ($i == $page) {
            $out[] = '<a class="active">' . $i . '</a>';
        } else {
            $out[] = '<a href="' . $url . 'page=' . $i . '">' . $i . '</a>';
        }
    }

    // Hiển thị trang cuối nếu không nằm trong khoảng lân cận
    if ($page < $total_pages - $neighbors) {
        if ($page < $total_pages - $neighbors - 1) $out[] = '<a role="button">...</a>';
        $out[] = '<a href="' . $url . 'page=' . $total_pages . '">' . $total_pages . '</a>';
    }

    // Nút "Tiếp"
    if ($page < $total_pages) {
        $out[] = '<a href="' . $url . 'page=' . ($page + 1) . '">&raquo;</a>';
    }

    $out[] = '</div>';
    return implode(' ', $out);
}


function pagination($url, $start, $total, $kmess)
{
    $out[] = ' <div class="paging_simple_numbers"><ul class="pagination">';
    $neighbors = 2;
    if ($start >= $total) $start = max(0, $total - (($total % $kmess) == 0 ? $kmess : ($total % $kmess)));
    else $start = max(0, (int)$start - ((int)$start % (int)$kmess));
    $base_link = '<li class="paginate_button page-item previous "><a class="page-link" href="' . strtr($url, array('%' => '%%')) . 'page=%d' . '">%s</a></li>';
    $out[] = $start == 0 ? '' : sprintf($base_link, $start / $kmess, 'Previous');
    if ($start > $kmess * $neighbors) $out[] = sprintf($base_link, 1, '1');
    if ($start > $kmess * ($neighbors + 1)) $out[] = '<li class="paginate_button page-item previous disabled"><a class="page-link">...</a></li>';
    for ($nCont = $neighbors; $nCont >= 1; $nCont--) if ($start >= $kmess * $nCont) {
        $tmpStart = $start - $kmess * $nCont;
        $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
    }
    $out[] = '<li class="paginate_button page-item previous active"><a class="page-link">' . ($start / $kmess + 1) . '</a></li>';
    $tmpMaxPages = (int)(($total - 1) / $kmess) * $kmess;
    for ($nCont = 1; $nCont <= $neighbors; $nCont++) if ($start + $kmess * $nCont <= $tmpMaxPages) {
        $tmpStart = $start + $kmess * $nCont;
        $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
    }
    if ($start + $kmess * ($neighbors + 1) < $tmpMaxPages) $out[] = '<li class="paginate_button page-item previous disabled"><a class="page-link">...</a></li>';
    if ($start + $kmess * $neighbors < $tmpMaxPages) $out[] = sprintf($base_link, $tmpMaxPages / $kmess + 1, $tmpMaxPages / $kmess + 1);
    if ($start + $kmess < $total) {
        $display_page = ($start + $kmess) > $total ? $total : ($start / $kmess + 2);
        $out[] = sprintf($base_link, $display_page, 'Next');
    }
    $out[] = '</ul></div>';
    return implode('', $out);
}
function myip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);

    curl_close($ch);
    return $data;
}
function check_email($data)
{
    if (preg_match('/^.+@.+$/', $data, $matches)) {
        return true;
    } else {
        return false;
    }
}
function gettime()
{
    return date('Y/m/d H:i:s', time());
}
function random($string, $int)
{
    return substr(str_shuffle($string), 0, $int);
}
function JsonMsg($status, $msg)
{
    return json_encode(array("status" => $status, "msg" => $msg));
}
function display_service_admin($status)
{
    $statusMapping = [
        'pending' => '<span class="badge bg-warning">Đang chờ</span>',
        'completed' => '<span class="badge bg-success">Hoàn thành</span>',
        'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
        'cancelled_refund' => '<span class="badge bg-danger">Hủy đơn hoàn tiền</span>',
        'error_refund' => '<span class="badge bg-danger">Lỗi đơn hoàn tiền</span>',
        'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
        'progress' => '<span class="badge bg-warning">Đang chạy</span>',
        'partial' => '<span class="badge bg-danger">Chạy thiếu (Đã hoàn tiền)</span>',
        'processing' => '<span class="badge bg-warning">Đang xử lý</span>',
    ];
    return $statusMapping[$status] ?? '<span class="badge bg-warning">Khác</span>';
}
function status_withdraw_orders($status)
{
    $statusMapping = [
        '2' => '<span class="badge bg-success rounded-lg text-white" style="background-color: #1A5D1A">Đã thanh toán</span>',
        '1' => '<span class="badge bg-danger rounded-lg text-white" style="background-color: #FF6666">Bị hủy</span>',
        '0' => '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Đang chờ</span>',
    ];
    return $statusMapping[$status] ?? '<span class="ant-tag css-eq3tly ant-tag-red">Khác</span>';
}
function display($status)
{
    $statusMapping = [
        '1' => '<span class="badge bg-success">Hiển thị</span>',
        '0' => '<span class="badge bg-danger">Đã ẩn</span>'
    ];
    return $statusMapping[$status] ?? '<span class="badge bg-warning">Khác</span>';
}

function status_order_cloud($status){
    $statusMapping = [
        'on' => '<span class="badge bg-success">Đang bật</span>',
        'off' => '<span class="badge bg-danger">Đã tắt</span>',
        'progressing' => '<span class="badge bg-warning">Đang tạo</span>',
        'waiting' => '<span class="badge bg-warning">Đang chờ tạo</span>',
        'rebuild' => '<span class="badge bg-warning">Đang cài lại</span>',
        'expire' => '<span class="badge bg-danger">Hết hạn</span>',
        'suspend' => '<span class="badge bg-danger">Đã khóa</span>',
        'delete_vps' => '<span class="badge bg-danger">Đã xóa</span>',
        'cancel' => '<span class="badge bg-danger">Đã hủy</span>',
    ];
    return $statusMapping[$status] ?? '<span class="badge badge-danger">Khác</span>';
}
function getDurationMappingValue($duration)
{
    $durationMapping = array(
        'monthly' => '1 Tháng',
        'twomonthly' => '2 Tháng',
        'quarterly' => '3 Tháng',
        'semi_annually' => '6 Tháng',
        'annually' => '1 Năm',
        'biennially' => '2 Năm',
        'triennially' => '3 Năm'
    );

    return isset($durationMapping[$duration]) ? $durationMapping[$duration] : '';
}
function isValidPassword($password)
{
    // Kiểm tra xem mật khẩu có chứa các ký tự cụ thể (#, &, /) hay không
    if (preg_match('/[#&\/]/', $password)) {
        return false; // Mật khẩu không hợp lệ
    }
    return true; // Mật khẩu hợp lệ
}
function custom_round($number, $intnumbeintnumber)
{
    if ($number > $intnumbeintnumber + 0.2 && $number <= $intnumbeintnumber + 0.5) {
        return $intnumbeintnumber + 0.5;
    } elseif ($number > $intnumbeintnumber + 0.5 && $number < $intnumbeintnumber + 1) {
        return $intnumbeintnumber + 1;
    } elseif ($number > $intnumbeintnumber && $number <= $intnumbeintnumber + 0.2) {
        return $intnumbeintnumber;
    } elseif ($number >= $intnumbeintnumber && $number <= $intnumbeintnumber + 0.2) {
        return $intnumbeintnumber;
    }
}
function check_img($img)
{
    $filename = $_FILES[$img]['name'];
    $ext = explode(".", $filename);
    $ext = end($ext);
    $arr_type = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($ext, $arr_type)) {
        return true;
    }
}
function parse_order_id($des, $memo)
{
    $re = '/' . $memo . '\d+/im';
    preg_match_all($re, $des, $matches, PREG_SET_ORDER, 0);
    if (count($matches) == 0)
        return null;
    $orderCode = $matches[0][0];
    $prefixLength = strlen($memo);
    $orderId = intval(substr($orderCode, $prefixLength));
    return $orderId;
}
function create_slug($string)
{
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
        '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
        '#(Đ)#',
        "/[^a-zA-Z0-9\-\_]/",
    );
    $replace = array(
        'a',
        'e',
        'i',
        'o',
        'u',
        'y',
        'd',
        'A',
        'E',
        'I',
        'O',
        'U',
        'Y',
        'D',
        '-',
    );
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    $string = strtolower($string);
    return $string;
}
function timeAgo($time_ago)
{
    $time_ago   = date("Y-m-d H:i:s", $time_ago);
    $time_ago   = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed;
    $minutes    = round($time_elapsed / 60);
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400);
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640);
    $years      = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return "$seconds giây trước";
    }
    //Minutes
    else if ($minutes <= 60) {
        return "$minutes phút trước";
    }
    //Hours
    else if ($hours <= 24) {
        return "$hours tiếng trước";
    }
    //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return "Hôm qua";
        } else {
            return "$days ngày trước";
        }
    }
    //Weeks
    else if ($weeks <= 4.3) {
        return "$weeks tuần trước";
    }
    //Months
    else if ($months <= 12) {
        return "$months tháng trước";
    }
    //Years
    else {
        return "$years năm trước";
    }
}

function generate_csrf_token()
{
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = base64_encode(openssl_random_pseudo_bytes(32));
    }
    return $_SESSION["csrf_token"];
}
function checkFormatCard($type, $seri, $pin)
{
    $seri = strlen($seri);
    $pin = strlen($pin);
    $data = [];
    if ($type == "Viettel" || $type == "viettel" || $type == "VT" || $type == "VIETTEL") {
        if ($seri != 11 && $seri != 14) {
            $data = ["status" => false, "msg" => "Độ dài seri không phù hợp"];
            return $data;
        }
        if ($pin != 13 && $pin != 15) {
            $data = ["status" => false, "msg" => "Độ dài mã thẻ không phù hợp"];
            return $data;
        }
    }
    if ($type == "Mobifone" || $type == "mobifone" || $type == "Mobi" || $type == "MOBIFONE") {
        if ($seri != 15) {
            $data = ["status" => false, "msg" => "Độ dài seri không phù hợp"];
            return $data;
        }
        if ($pin != 12) {
            $data = ["status" => false, "msg" => "Độ dài mã thẻ không phù hợp"];
            return $data;
        }
    }
    if ($type == "VNMB" || $type == "Vnmb" || $type == "VNM" || $type == "VNMOBI") {
        if ($seri != 16) {
            $data = ["status" => false, "msg" => "Độ dài seri không phù hợp"];
            return $data;
        }
        if ($pin != 12) {
            $data = ["status" => false, "msg" => "Độ dài mã thẻ không phù hợp"];
            return $data;
        }
    }
    if ($type == "Vinaphone" || $type == "vinaphone" || $type == "Vina" || $type == "VINAPHONE") {
        if ($seri != 14) {
            $data = ["status" => false, "msg" => "Độ dài seri không phù hợp"];
            return $data;
        }
        if ($pin != 14) {
            $data = ["status" => false, "msg" => "Độ dài mã thẻ không phù hợp"];
            return $data;
        }
    }
    if ($type == "Garena" || $type == "garena") {
        if ($seri != 9) {
            $data = ["status" => false, "msg" => "Độ dài seri không phù hợp"];
            return $data;
        }
        if ($pin != 16) {
            $data = ["status" => false, "msg" => "Độ dài mã thẻ không phù hợp"];
            return $data;
        }
    }
    if ($type == "Zing" || $type == "zing" || $type == "ZING") {
        if ($seri != 12) {
            $data = ["status" => false, "msg" => "Độ dài seri không phù hợp"];
            return $data;
        }
        if ($pin != 9) {
            $data = ["status" => false, "msg" => "Độ dài mã thẻ không phù hợp"];
            return $data;
        }
    }
    if ($type == "Vcoin" || $type == "VTC") {
        if ($seri != 12) {
            $data = ["status" => false, "msg" => "Độ dài seri không phù hợp"];
            return $data;
        }
        if ($pin != 12) {
            $data = ["status" => false, "msg" => "Độ dài mã thẻ không phù hợp"];
            return $data;
        }
    }
    $data = ["status" => true, "msg" => "Success"];
    return $data;
}
// Hàm mã hóa AES
function encryptAES($data)
{
    $cipherText = openssl_encrypt($data, 'aes-256-cbc', 'ZtO1Y64ORITfYYW5an5/+0i0n416LbQsZYBAFGo6P0=', OPENSSL_RAW_DATA, '/iQv7qORIT3VsJn5');
    return base64_encode($cipherText);
}

// Hàm giải mã AES
function decryptAES($encryptedData)
{
    $decodedData = base64_decode($encryptedData);
    return openssl_decrypt($decodedData, 'aes-256-cbc', 'ZtO1Y64ORITfYYW5an5/+0i0n416LbQsZYBAFGo6P0=', OPENSSL_RAW_DATA, '/iQv7qORIT3VsJn5');
}

function getDomainExtension($domain)
{
    $parts = explode('.', $domain);
    // Remove the first element and join the remaining elements
    $domainExtension = implode('', array_slice($parts, 1));
    return $domainExtension;
}
function uploadDemoImages($files, $upload_dir = "/upload/demos/")
{
    $uploaded_files = [];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Các định dạng ảnh cho phép

    // Kiểm tra và tạo thư mục nếu chưa có
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Lặp qua từng file và xử lý upload
    for ($i = 0; $i < count($files['name']); $i++) {
        $file_extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);

        // Kiểm tra định dạng file
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            continue; // Bỏ qua file không hợp lệ
        }

        // Tạo tên file mã hóa bằng md5 và time
        $hashed_name = md5(time() . $files['name'][$i] . $i) . '.' . $file_extension;
        $target_file = $upload_dir . $hashed_name;

        // Di chuyển file đến thư mục đích
        if (move_uploaded_file($files['tmp_name'][$i], realpath($_SERVER["DOCUMENT_ROOT"]) . $target_file)) {
            $uploaded_files[] = $target_file; // Lưu lại đường dẫn các file đã upload thành công
        }
    }

    return $uploaded_files; // Trả về mảng chứa các đường dẫn file đã upload
}

function uploadDemoImage($file)
{
    $target_dir = "/upload/demos/"; // Đường dẫn lưu ảnh

    // Kiểm tra và tạo thư mục nếu chưa có
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Lấy phần mở rộng file và kiểm tra loại ảnh
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
    if (!in_array($imageFileType, $allowed_types)) {
        return false;
    }

    // Tạo tên file mã hóa bằng md5 và time
    $hashed_name = md5(time() . $file["name"]) . '.' . $imageFileType;
    $target_file = $target_dir . $hashed_name;

    // Di chuyển ảnh từ thư mục tạm vào thư mục đích
    if (move_uploaded_file($file["tmp_name"], realpath($_SERVER["DOCUMENT_ROOT"]) . $target_file)) {
        return $target_file; // Trả về đường dẫn ảnh đã upload
    } else {
        return false; // Nếu upload thất bại
    }
}
function uploadThumbnail($file, $upload_dir = "/upload/thumbnails/")
{
    // Kiểm tra và tạo thư mục nếu chưa có
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Lấy phần mở rộng file
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Các định dạng ảnh cho phép

    // Kiểm tra định dạng file
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        return false; // Không hợp lệ, trả về false
    }

    // Tạo tên file mã hóa bằng md5 và time
    $hashed_name = md5(time() . $file['name']) . '.' . $file_extension;
    $target_file = $upload_dir . $hashed_name;

    // Di chuyển file đến thư mục đích
    if (move_uploaded_file($file['tmp_name'], realpath($_SERVER["DOCUMENT_ROOT"]) . $target_file)) {
        return $target_file; // Trả về đường dẫn file đã upload
    } else {
        return false; // Upload thất bại
    }
}
function tinhPhanTramGiam($giaGoc, $giaDaGiam)
{
    if ($giaGoc <= 0) {
        return 0; // Giá gốc không hợp lệ
    }
    $phanTramGiam = (($giaGoc - $giaDaGiam) / $giaGoc) * 100;
    return round($phanTramGiam, 2); // Làm tròn 2 chữ số thập phân
}
function generateHashtags($productName)
{
    $productName = strtolower($productName);
    $hashtags = explode(" ", preg_replace("/[^a-z0-9\s]/", "", $productName));
    $hashtags = array_map(function ($word) {
        return '#' . $word;
    }, $hashtags);
    return array_filter($hashtags);
}
function getPurchaseCode()
{
    $part1 = rand(100000, 999999);
    $part2 = rand(10, 99);
    $part3 = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
    $part4 = rand(100000, 999999);
    return "{$part1}-{$part2}-{$part3}-{$part4}";
}

function generateApiKey()
{
    return md5(random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 6) . time());
}
function handleResponse($user_id, $success, $logMessage, $successMessage, $errorMessage)
{
    if ($success) {
        insert_log($user_id, $logMessage);
        alertAndRedirect($successMessage);
    } else {
        alertAndRedirect($errorMessage);
    }
}

// Outputs a JavaScript alert and redirects the user back
function alertAndRedirect($message)
{
    echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
    exit;
}
