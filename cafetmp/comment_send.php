<?php
/**
 * ================================================================
 * ثبت / به‌روزرسانی / حذف نظر و امتیاز کاربر
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

/* ---------- خواندن ورودی ---------- */
$raw = file_get_contents('php://input');

if ($raw === false || $raw === '') {
    reply(0, 'بدنه درخواست خالی است');
}

$input = json_decode($raw, true);

if (!is_array($input)) {
    reply(0, 'فرمت درخواست نامعتبر است');
}

$action = isset($input['action']) ? trim((string)$input['action']) : '';
$phone = preg_replace('/[^0-9]/', '', $input['phone'] ?? '');
$menu_item_id = (int)($input['menu_item_id'] ?? 0);
$score = (int)($input['score'] ?? 0);
$comment_text = trim((string)($input['comment_text'] ?? ''));

/* ---------- اعتبارسنجی مشترک ---------- */
if ($action !== 'save' && $action !== 'delete') {
    reply(0, 'درخواست نامعتبر (action)');
}

if (!preg_match('/^09\d{9}$/', $phone)) {
    reply(0, 'شماره تلفن نامعتبر است');
}

if ($menu_item_id <= 0) {
    reply(0, 'آیتم منو نامعتبر است');
}

/* ---------- اعتبارسنجی مخصوص save ---------- */
if ($action === 'save') {
    if ($score < 1 || $score > 5) {
        reply(0, 'امتیاز باید بین ۱ تا ۵ باشد');
    }
    if (mb_strlen($comment_text, 'UTF-8') > 500) {
        reply(0, 'متن نظر نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد');
    }
    if (mb_strlen($comment_text, 'UTF-8') < 1) {
        reply(0, 'متن نظر نمی‌تواند خالی باشد');
    }
}

/* ---------- اتصال ---------- */
$db = new database();
$db->connect();

$phone_s = mysqli_real_escape_string($db->connection, $phone);

/* ---------- پیدا کردن مشتری ---------- */
$db->query("SELECT `id`, `name`, `family` FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");
if (mysqli_num_rows($db->res) === 0) {
    reply(0, 'مشتری یافت نشد. ابتدا اطلاعات خود را ثبت کنید.');
}

$customer = mysqli_fetch_assoc($db->res);
$customer_id = (int)$customer['id'];
$fullname = trim(($customer['name'] ?? '') . ' ' . ($customer['family'] ?? ''));
if ($fullname === '') $fullname = 'کاربر ناشناس';

$fullname_s = mysqli_real_escape_string($db->connection, $fullname);
$comment_s = mysqli_real_escape_string($db->connection, $comment_text);

/* ---------- بررسی وجود آیتم منو ---------- */
$db->query("SELECT `id` FROM `menu_items` WHERE `id` = $menu_item_id LIMIT 1");
if (mysqli_num_rows($db->res) === 0) {
    reply(0, 'آیتم منو یافت نشد');
}

/* ================================================================
   اکشن delete
   ================================================================ */
if ($action === 'delete') {

    $db->query("SELECT `id` FROM `comments`
                WHERE `customer_id` = $customer_id
                  AND `menu_item_id` = $menu_item_id
                LIMIT 1");

    if (mysqli_num_rows($db->res) === 0) {
        reply(0, 'نظری برای حذف پیدا نشد');
    }

    $comment_id = (int)mysqli_fetch_assoc($db->res)['id'];

    $db->query("DELETE FROM `comments` WHERE `id` = $comment_id LIMIT 1");

    /* ---------- آمار جدید ---------- */
    $db->query("SELECT COUNT(*) AS cnt, COALESCE(SUM(`score`),0) AS sm
                FROM `comments`
                WHERE `menu_item_id` = $menu_item_id
                  AND `status` = 1");

    $stats = mysqli_fetch_assoc($db->res);

    reply(1, 'نظر شما حذف شد', [
        'action' => 'delete',
        'comment_id' => $comment_id,
        'new_count' => (int)$stats['cnt'],
        'new_sum' => (int)$stats['sm'],
    ]);
}

/* ================================================================
   اکشن save (insert یا update)
   ================================================================ */
$db->query("SELECT `id` FROM `comments`
            WHERE `customer_id` = $customer_id
              AND `menu_item_id` = $menu_item_id
            LIMIT 1");

$today = date('Y-m-d');

if (mysqli_num_rows($db->res) > 0) {

    $comment_id = (int)mysqli_fetch_assoc($db->res)['id'];

    $db->query("UPDATE `comments`
                SET `score` = $score,
                    `comment_text` = '$comment_s',
                    `comment_date` = '$today',
                    `fullname` = '$fullname_s'
                WHERE `id` = $comment_id
                LIMIT 1");

    $do_action = 'update';
} else {

    $db->query("INSERT INTO `comments`
                (`fullname`, `score`, `comment_text`, `comment_date`,
                 `menu_item_id`, `customer_id`, `status`)
                VALUES
                ('$fullname_s', $score, '$comment_s', '$today',
                 $menu_item_id, $customer_id, 1)");

    $comment_id = (int)mysqli_insert_id($db->connection);

    if ($comment_id <= 0) {
        reply(0, 'خطا در ثبت نظر');
    }

    $do_action = 'insert';
}

/* ---------- آمار جدید ---------- */
$db->query("SELECT COUNT(*) AS cnt, COALESCE(SUM(`score`),0) AS sm
            FROM `comments`
            WHERE `menu_item_id` = $menu_item_id
              AND `status` = 1");

$stats = mysqli_fetch_assoc($db->res);

reply(1, ($do_action === 'update' ? 'نظر شما به‌روزرسانی شد' : 'نظر شما ثبت شد'), [
    'action' => $do_action,
    'comment_id' => $comment_id,
    'comment' => [
        'fullname' => $fullname,
        'score' => $score,
        'comment_text' => $comment_text,
        'comment_date' => $today,
    ],
    'new_count' => (int)$stats['cnt'],
    'new_sum' => (int)$stats['sm'],
]);