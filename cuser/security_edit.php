<?php
/**
 * پردازش تغییر رمز عبور گارسون
 */
error_reporting(0);
ini_set('display_errors', 0);

session_start();

ob_start();

include("../lib/php/lib_include.php");
include("check_admin_session.php");

if (ob_get_length() > 0) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');

$ml = new mobile_input();

$pass = $ml->set_name("pass")
    ->set_title("کلمه عبور پیشین")
    ->set_important(true)
    ->post_str();

$newpass = $ml->set_name("newpass")
    ->set_title("کلمه عبور جدید")
    ->set_important(true)
    ->post_str();

$newpass2 = $ml->set_name("newpass2")
    ->set_title("تکرار کلمه عبور جدید")
    ->set_important(true)
    ->post_str();

/* شماره تماس گارسون از سشن */
$thisuser = $_SESSION['tel'] ?? '';

/* تابع کمکی برای پاسخ */
function reply($status, $msg)
{
    if (ob_get_length() > 0) {
        ob_clean();
    }
    echo json_encode([
        'status' => $status,
        'msg' => $msg
    ], JSON_UNESCAPED_UNICODE);
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    exit;
}

/* ۱) کلمه عبور جدید و تکرارش باید یکسان باشند */
if ($newpass !== $newpass2) {
    reply(0, 'کلمه عبور جدید و تکرار آن با هم یکسان نیستند.');
}

/* ۲) بررسی کلمه عبور پیشین روی جدول waiters */
$db = new database();
$db->connect();

$fm = new makeform();
$pass_s = $fm->sqlstr($pass);
$tel_s = $fm->sqlstr($thisuser);

$db->query("select * from `waiters` where `tel`='$tel_s' and `pass`='$pass_s' limit 1");

if (mysqli_num_rows($db->res) == 0) {
    reply(0, 'کلمه عبور پیشین اشتباه می باشد.');
}

/* ۳) بروزرسانی کلمه عبور */
$newpass_s = $fm->sqlstr($newpass);
$db->query("update `waiters` set `pass`='$newpass_s' where `tel`='$tel_s'");

if (ob_get_length() > 0) {
    ob_clean();
}

if ($db->res) {
    reply(1, 'کلمه عبور با موفقیت تغییر یافت. لطفاً مجدداً وارد شوید.');
} else {
    reply(0, 'اشکال در ثبت اطلاعات');
}
?>