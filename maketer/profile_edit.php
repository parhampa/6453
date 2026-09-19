<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
ob_start();

include("../lib/php/lib_include.php");
include("check_admin_session.php");

if (ob_get_length() > 0) ob_clean();
header('Content-Type: application/json; charset=utf-8');

$ml = new mobile_input();

$name = $ml->set_name("name")->set_title("نام")->set_important(false)->post_str();
$family = $ml->set_name("family")->set_title("نام خانوادگی")->set_important(false)->post_str();
$tel2 = $ml->set_name("tel2")->set_title("شماره تماس 2")->set_important(false)->post_str();


$thisuser = $_SESSION['tel'] ?? '';

function reply($status, $msg)
{
    if (ob_get_length() > 0) ob_clean();
    echo json_encode(['status' => $status, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
    while (ob_get_level() > 0) ob_end_flush();
    exit;
}

$db = new database();
$db->connect();
$fm = new makeform();

$user_safe = $fm->sqlstr($thisuser);

$sql = "UPDATE `marketers` SET
            `name`='$name',
            `family`='$family',
            `tel2`='$tel2'
        WHERE `tel` = '$user_safe'";

$db->query($sql);

if (ob_get_length() > 0) ob_clean();

if ($db->res) {
    reply(1, 'اطلاعات با موفقیت بروزرسانی شد.');
} else {
    reply(0, 'اشکال در ثبت اطلاعات');
}
?>