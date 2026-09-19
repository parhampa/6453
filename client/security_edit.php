<?php
/**
 * پردازش تغییر رمز عبور کافه
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

/* موبایل مدیر از سشن */
$mob = preg_replace('/[^0-9]/', '', $_SESSION['manager_mobile']);

/* پاسخ خطا و پایان */
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

/* ۲) بررسی کلمه عبور پیشین */
$db = new database();
$db->connect()->query(
    "select * from `cafes` where `manager_mobile`='$mob' and `pass`='$pass' limit 1"
);

if (mysqli_num_rows($db->res) == 0) {
    reply(0, 'کلمه عبور پیشین اشتباه می باشد.');
}

/* ۳) بروزرسانی کلمه عبور */
$db->connect()->query(
    "update `cafes` set `pass`='$newpass' where `manager_mobile`='$mob'"
);

if ($db->res) {
    reply(1, 'کلمه عبور با موفقیت تغییر یافت. لطفاً مجدداً وارد شوید.');
} else {
    reply(0, 'اشکال در ثبت اطلاعات');
}
?>