<?php
/**
 * پردازش ویرایش پروفایل کافه
 */
error_reporting(0);
ini_set('display_errors', 0);

session_start();

ob_start();

include("../lib/php/lib_include.php");
include("check_admin_session.php");

/* هر چیزی که تا اینجا چاپ شده (BOM، warning، HTML) رو دور بریز */
if (ob_get_length() > 0) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');

$ml = new mobile_input();

$title = $ml->set_name("title")
    ->set_title("عنوان کافه")
    ->set_important(true)
    ->post_str();

$slogan = $ml->set_name("slogan")
    ->set_title("شعار کافه")
    ->set_important(false)
    ->post_str();

$tel1 = $ml->set_name("tel1")
    ->set_title("شماره تماس ۱")
    ->set_important(false)
    ->post_str();

$tel2 = $ml->set_name("tel2")
    ->set_title("شماره تماس ۲")
    ->set_important(false)
    ->post_str();

$manager_name = $ml->set_name("manager_name")
    ->set_title("نام مدیر کافه")
    ->set_important(true)
    ->post_str();

$address = $ml->set_name("address")
    ->set_title("آدرس کافه")
    ->set_important(false)
    ->post_str();

$instagram = $ml->set_name("instagram")
    ->set_title("اینستاگرام")
    ->set_important(false)
    ->post_str();

$working_hours = $ml->set_name("working_hours")
    ->set_title("ساعت کاری")
    ->set_important(false)
    ->post_str();

$mob = preg_replace('/[^0-9]/', '', $_SESSION['manager_mobile']);

$sql = "update `cafes` set
            `title`='$title',
            `slogan`='$slogan',
            `tel1`='$tel1',
            `tel2`='$tel2',
            `manager_name`='$manager_name',
            `address`='$address',
            `instagram`='$instagram',
            `working_hours`='$working_hours'
        where `manager_mobile`='$mob'";

$db = new database();
$db->connect()->query($sql);

/* قبل از echo، هر خروجی اضافی از mobile_input رو پاک کن */
if (ob_get_length() > 0) {
    ob_clean();
}

if ($db->res) {
    echo json_encode([
        'status' => 1,
        'msg' => 'عملیات با موفقیت انجام شد.'
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'status' => 0,
        'msg' => 'اشکال در ثبت اطلاعات'
    ], JSON_UNESCAPED_UNICODE);
}

while (ob_get_level() > 0) {
    ob_end_flush();
}
exit;
?>