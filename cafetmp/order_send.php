<?php
/**
 * ================================================================
 * ثبت سفارش مشتری — ایجاد فاکتور جدید با آیتم‌ها
 * ================================================================
 * ورودی JSON:
 * {
 *   "cafe_id": 1,
 *   "table": 5,
 *   "first_name": "سارا",
 *   "last_name": "احمدی",
 *   "phone": "09123456789",
 *   "description": "بدون شکر",
 *   "items": [
 *     { "id": 1, "qty": 2 },
 *     { "id": 3, "qty": 1 }
 *   ]
 * }
 * ================================================================
 */

error_reporting(0);
ini_set('display_errors', 0);
session_start();
ob_start();

/* ✅ اصلاح شد: مسیر include مانند سایر فایل‌های پروژه */
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

$cafe_id = (int)($input['cafe_id'] ?? 0);
$table = (int)($input['table'] ?? 0);
$fname = trim($input['first_name'] ?? '');
$lname = trim($input['last_name'] ?? '');
$phone = trim($input['phone'] ?? '');
$description = trim($input['description'] ?? '');
$items = $input['items'] ?? [];

/* ---------- اعتبارسنجی ---------- */
if ($cafe_id <= 0) reply(0, 'کافه نامعتبر است');
if ($fname === '') reply(0, 'نام الزامی است');
if ($lname === '') reply(0, 'نام خانوادگی الزامی است');

$phone = preg_replace('/[^0-9]/', '', $phone);
if (!preg_match('/^09\d{9}$/', $phone)) {
    reply(0, 'شماره تلفن نامعتبر است (باید با ۰۹ شروع شود و ۱۱ رقم باشد)');
}

if ($table < 1 || $table > 99) {
    reply(0, 'شماره میز نامعتبر است (باید بین ۱ تا ۹۹ باشد)');
}

if (!is_array($items) || empty($items)) {
    reply(0, 'سبد خرید خالی است');
}

/* ---------- اعتبارسنجی توضیحات ---------- */
if (mb_strlen($description, 'UTF-8') > 2000) {
    reply(0, 'توضیحات نمی‌تواند بیشتر از ۲۰۰۰ کاراکتر باشد');
}

/* ---------- اتصال ---------- */
$db = new database();
$db->connect();

/* ---------- بررسی کافه ---------- */
$db->query("SELECT `id` FROM `cafes` WHERE `id` = $cafe_id AND `status` = 1 LIMIT 1");
if (mysqli_num_rows($db->res) === 0) {
    reply(0, 'کافه یافت نشد یا غیرفعال است');
}

/* ---------- امن‌سازی ---------- */
$fm = new makeform();
$fname_s = $fm->sqlstr($fname);
$lname_s = $fm->sqlstr($lname);
$phone_s = $fm->sqlstr($phone);
$desc_s = $fm->sqlstr($description);

/* ================================================================
   ۱) مشتری: پیدا کردن یا ساخت
   ================================================================ */
$db->query("SELECT `id` FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");

if (mysqli_num_rows($db->res) > 0) {
    $customer_id = (int)mysqli_fetch_assoc($db->res)['id'];
} else {
    $mili = time() . rand(100, 999);
    $db->query("INSERT INTO `customers` (`name`, `family`, `tel`, `mili`)
                VALUES ('$fname_s', '$lname_s', '$phone_s', '$mili')");
    $customer_id = (int)mysqli_insert_id($db->connection);
}

if ($customer_id <= 0) {
    reply(0, 'خطا در ثبت اطلاعات مشتری');
}

/* ================================================================
   ۲) تخفیف فعلی مشتری
   ================================================================ */
$db->query("SELECT `discount_percent`
            FROM `invoices`
            WHERE `customer_id` = $customer_id
              AND `cafe_id` = $cafe_id
              AND `status` != 3
            ORDER BY `id` DESC
            LIMIT 1");

$user_discount = 0;
if (mysqli_num_rows($db->res) > 0) {
    $user_discount = (int)mysqli_fetch_assoc($db->res)['discount_percent'];
    if ($user_discount < 0) $user_discount = 0;
    if ($user_discount > 100) $user_discount = 100;
}

/* ================================================================
   ۳) ایجاد فاکتور جدید — با description
   ================================================================ */
$today = date('Y-m-d');

/* ⭐ ساخت مقدار SQL برای توضیحات */
$desc_sql = !empty($description) ? "'$desc_s'" : "NULL";

$db->query("INSERT INTO `invoices`
            (`invoice_date`, `table_number`, `discount_percent`,
             `customer_id`, `cafe_id`, `waiter_id`, `status`, `description`)
            VALUES
            ('$today', $table, $user_discount,
             $customer_id, $cafe_id, 0, 0, $desc_sql)");

$invoice_id = (int)mysqli_insert_id($db->connection);

if ($invoice_id <= 0) {
    reply(0, 'خطا در ایجاد فاکتور');
}

/* ================================================================
   ۴) افزودن آیتم‌ها
   ================================================================ */
$added_count = 0;
$grand_total = 0;

foreach ($items as $it) {

    $menu_id = (int)($it['id'] ?? 0);
    $qty = (int)($it['qty'] ?? 0);

    if ($menu_id <= 0) continue;
    if ($qty <= 0) continue;
    if ($qty > 100) continue;

    $db->query("SELECT mi.`price`
                FROM `menu_items` mi
                JOIN `cafe_categories` cc ON cc.`id` = mi.`category_id`
                WHERE mi.`id` = $menu_id
                  AND cc.`cafe_id` = $cafe_id
                LIMIT 1");

    if (mysqli_num_rows($db->res) === 0) continue;

    $price = (float)mysqli_fetch_assoc($db->res)['price'];

    $db->query("INSERT INTO `invoice_items`
                (`invoice_id`, `menu_item_id`, `unit_price`, `quantity`)
                VALUES
                ($invoice_id, $menu_id, '$price', $qty)");

    $added_count++;
    $grand_total += $price * $qty;
}

/* ---------- اگر هیچ آیتم معتبری نبود ---------- */
if ($added_count === 0) {
    $db->query("DELETE FROM `invoices` WHERE `id` = $invoice_id");
    reply(0, 'هیچ آیتم معتبری یافت نشد');
}

/* ---------- مبلغ نهایی ---------- */
$final_total = $grand_total * (100 - $user_discount) / 100;

reply(1, 'سفارش شما با موفقیت ثبت شد', [
    'invoice_id' => $invoice_id,
    'items' => $added_count,
    'subtotal' => $grand_total,
    'discount_percent' => $user_discount,
    'total' => $final_total,
    'table' => $table,
    'description' => $description,
]);

exit;
?>