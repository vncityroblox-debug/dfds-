<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if(isset($_GET['ref'])){
    $user_id = Anti_xss($_GET['ref']);
    if($row = $db->get_row("SELECT * FROM `users` WHERE `id` = '".$user_id."' AND `banned` = 0")){
        $_SESSION['ref'] = $row['id'];
        $db->cong('users', 'ref_click', 1, " `id` = '".$row['id']."' ");
        new Redirect('/');
    }
    new Redirect('/');
}
new Redirect('/');
 
?>
 