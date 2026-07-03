<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

$token = getTokenH2();

if ($token) {
    echo '[✓] Token H2Cloud mới đã được lấy và lưu: ' . $token;
} else {
    echo '[x] Lỗi: Không lấy được token từ API H2Cloud';
}
