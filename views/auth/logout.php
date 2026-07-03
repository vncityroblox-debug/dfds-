<?php
// Require database & thông tin chung
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
// Xoá session
$session->destroy();
new Redirect(DOMAIN); // Trở về trang index
?>