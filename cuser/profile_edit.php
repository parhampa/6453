<?php
/**
 * پردازش ویرایش پروفایل گارسون
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

$fullname = $ml->set_name("fullname")
    ->set_title("نام و نام خانوادگی")
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

if (empty($fullname)) {
    reply(0, 'نام و نام خانوادگی را وارد کنید.');
}

/* بروزرسانی روی جدول waiters */
$db = new database();
$db->connect();

$fm = new makeform();
$name_s = $fm->sqlstr($fullname);
$tel_s  = $fm->sqlstr($thisuser);

$db->query("update `waiters` set `fullname`='$name_s' where `tel`='$tel_s'");

if (ob_get_length() > 0) {
    ob_clean();
}

if ($db->res) {
    reply(1, 'اطلاعات پروفایل با موفقیت بروزرسانی شد.');
} else {
    reply(0, 'اشکال در ثبت اطلاعات');
}
?>