<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
ob_start();

include("../lib/php/lib_include.php");
include("check_admin_session.php");

if (ob_get_length() > 0) ob_clean();
header('Content-Type: application/json; charset=utf-8');

function reply($status, $msg)
{
    if (ob_get_length() > 0) ob_clean();
    echo json_encode(['status' => $status, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
    while (ob_get_level() > 0) ob_end_flush();
    exit;
}

$ml = new mobile_input();

$pass     = $ml->set_name("pass")->set_title("کلمه عبور پیشین")->set_important(true)->post_str();
$newpass  = $ml->set_name("newpass")->set_title("کلمه عبور جدید")->set_important(true)->post_str();
$newpass2 = $ml->set_name("newpass2")->set_title("تکرار کلمه عبور جدید")->set_important(true)->post_str();

$thisuser = $_SESSION['tel'] ?? '';

if ($newpass !== $newpass2) {
    reply(0, 'کلمه عبور جدید و تکرار آن یکسان نیستند.');
}

$db = new database();
$db->connect();
$fm = new makeform();

$user_safe = $fm->sqlstr($thisuser);
$pass_safe = $fm->sqlstr($pass);

$db->query("SELECT * FROM `marketers` WHERE `tel`='$user_safe' AND `pass`='$pass_safe' LIMIT 1");

if (mysqli_num_rows($db->res) == 0) {
    reply(0, 'کلمه عبور پیشین اشتباه است.');
}

$new_safe = $fm->sqlstr($newpass);
$db->query("UPDATE `marketers` SET `pass`='$new_safe' WHERE `tel`='$user_safe'");

if (ob_get_length() > 0) ob_clean();

if ($db->res) {
    reply(1, 'کلمه عبور با موفقیت تغییر یافت. لطفاً مجدداً وارد شوید.');
} else {
    reply(0, 'اشکال در ثبت اطلاعات');
}
?>