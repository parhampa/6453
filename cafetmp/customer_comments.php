<?php
/**
 * ================================================================
 * گرفتن نظرات خود کاربر برای آیتم‌های منو
 * ----------------------------------------------------------------
 * ورودی JSON:
 *   { "phone": "09123456789", "cafe_id": 1 }
 *
 * خروجی JSON:
 *   {
 *     "status": 1,
 *     "comments": {
 *       "1": { "my_score": 5, "my_comment_id": 12, "my_comment_text": "..." }
 *     }
 *   }
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
$input = json_decode($raw, true);
if (!is_array($input)) $input = $_POST;

$phone = preg_replace('/[^0-9]/', '', $input['phone'] ?? '');
$cafe_id = (int)($input['cafe_id'] ?? 0);

if (!preg_match('/^09\d{9}$/', $phone)) {
    reply(0, 'شماره تلفن نامعتبر است');
}

$db = new database();
$db->connect();

$phone_s = mysqli_real_escape_string($db->connection, $phone);

/* ---------- پیدا کردن مشتری ---------- */
$db->query("SELECT `id` FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");
if (mysqli_num_rows($db->res) === 0) {
    reply(0, 'مشتری یافت نشد', ['comments' => new stdClass()]);
}

$customer_id = (int)mysqli_fetch_assoc($db->res)['id'];

/* ---------- گرفتن نظرات خود کاربر ---------- */
if ($cafe_id > 0) {
    $sql = "SELECT cm.`id`, cm.`menu_item_id`, cm.`score`, cm.`comment_text`
            FROM `comments` cm
            JOIN `menu_items` mi ON mi.`id` = cm.`menu_item_id`
            JOIN `cafe_categories` cc ON cc.`id` = mi.`category_id`
            WHERE cm.`customer_id` = $customer_id
              AND cc.`cafe_id` = $cafe_id";
} else {
    $sql = "SELECT cm.`id`, cm.`menu_item_id`, cm.`score`, cm.`comment_text`
            FROM `comments` cm
            WHERE cm.`customer_id` = $customer_id";
}

$db->query($sql);

$comments = new stdClass();
while ($row = mysqli_fetch_assoc($db->res)) {
    $item_id = (string)(int)$row['menu_item_id'];
    $comments->$item_id = [
        'my_score' => (int)$row['score'],
        'my_comment_id' => (int)$row['id'],
        'my_comment_text' => (string)$row['comment_text'],
    ];
}

reply(1, 'ok', ['comments' => $comments]);