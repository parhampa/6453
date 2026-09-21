<?php
/**
 * ================================================================
 * مدیریت تاریخ تولد مشتری
 * ================================================================
 */

error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

include_once '../lib/php/lib_include.php';

header('Content-Type: application/json; charset=utf-8');

function reply($status, $msg, $extra = [])
{
    while (ob_get_level() > 0) ob_end_clean();
    echo json_encode(
        array_merge(['status' => $status, 'msg' => $msg], $extra),
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

/* ---------- تبدیل تاریخ شمسی به میلادی ----------
   ⚠️ اسم این تابع رو ct_ گذاشتیم چون jalali_to_gregorian
   از قبل در lib/php/date_and_time.php وجود داره.
*/
if (!function_exists('ct_jalali_to_gregorian')) {
    function ct_jalali_to_gregorian($jy, $jm, $jd)
    {
        $jy += 1595;
        $days = -355668 + (365 * $jy) + (((int)($jy / 33)) * 8) + ((int)((($jy % 33) + 3) / 4))
            + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
        $gy = 400 * ((int)($days / 146097));
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * ((int)(--$days / 36524));
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        $gy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $gy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        $gd = $days + 1;
        $leap = (($gy % 4 == 0) && ($gy % 100 != 0)) || ($gy % 400 == 0);
        $sal_a = [0, 31, $leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for ($gm = 1; $gm <= 12 && $gd > $sal_a[$gm]; $gm++) {
            $gd -= $sal_a[$gm];
        }
        return [$gy, $gm, $gd];
    }
}

/* ---------- خواندن ورودی ---------- */
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) $input = $_POST;

$action = isset($input['action']) ? trim($input['action']) : '';
$phone = preg_replace('/[^0-9]/', '', isset($input['phone']) ? $input['phone'] : '');
$fname = isset($input['first_name']) ? trim($input['first_name']) : '';
$lname = isset($input['last_name']) ? trim($input['last_name']) : '';

if (!preg_match('/^09\d{9}$/', $phone)) {
    reply(0, 'شماره تلفن نامعتبر است');
}

/* ---------- اتصال ---------- */
$db = new database();
$db->connect();

/* ---------- escape ---------- */
$phone_s = mysqli_real_escape_string($db->connection, $phone);
$fname_s = mysqli_real_escape_string($db->connection, $fname);
$lname_s = mysqli_real_escape_string($db->connection, $lname);

/* ---------- پیدا کردن مشتری ---------- */
$db->query("SELECT `id`, `birth_date` FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");

if (mysqli_num_rows($db->res) > 0) {
    $row = mysqli_fetch_assoc($db->res);
    $customer_id = (int)$row['id'];
    $has_birth = !empty($row['birth_date']) && $row['birth_date'] !== '0000-00-00';
} else {
    $mili = time() . rand(100, 999);
    $db->query("INSERT INTO `customers` (`name`, `family`, `tel`, `mili`)
                VALUES ('$fname_s', '$lname_s', '$phone_s', '$mili')");
    $customer_id = (int)mysqli_insert_id($db->connection);
    $has_birth = false;

    if ($customer_id <= 0) {
        reply(0, 'خطا در ایجاد پروفایل مشتری');
    }
}

/* ---------- action = check ---------- */
if ($action === 'check') {
    reply(1, 'ok', [
        'exists' => 1,
        'customer_id' => $customer_id,
        'has_birthdate' => $has_birth ? 1 : 0,
    ]);
}

/* ---------- action = save ---------- */
if ($action === 'save') {

    if ($has_birth) {
        reply(1, 'تاریخ تولد قبلاً ثبت شده', ['already' => 1]);
    }

    $jy = isset($input['jy']) ? (int)$input['jy'] : 0;
    $jm = isset($input['jm']) ? (int)$input['jm'] : 0;
    $jd = isset($input['jd']) ? (int)$input['jd'] : 0;

    if ($jy < 1300 || $jy > 1420) reply(0, 'سال نامعتبر است');
    if ($jm < 1 || $jm > 12) reply(0, 'ماه نامعتبر است');
    if ($jd < 1) reply(0, 'روز نامعتبر است');
    if ($jm <= 6 && $jd > 31) reply(0, 'روز نامعتبر برای این ماه');
    if ($jm >= 7 && $jm <= 11 && $jd > 30) reply(0, 'روز نامعتبر برای این ماه');
    if ($jm == 12 && $jd > 30) reply(0, 'روز نامعتبر برای این ماه');

    list($gy2, $gm2, $gd2) = ct_jalali_to_gregorian($jy, $jm, $jd);
    $greg = sprintf('%04d-%02d-%02d', $gy2, $gm2, $gd2);

    $db->query("UPDATE `customers` SET `birth_date` = '$greg' WHERE `id` = $customer_id LIMIT 1");

    reply(1, 'تاریخ تولد با موفقیت ثبت شد', ['birth_date' => $greg]);
}

reply(0, 'درخواست نامعتبر');