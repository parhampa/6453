<?php
/**
 * ================================================================
 * ثبت سفارش مشتری — ایجاد فاکتور جدید با آیتم‌ها
 * ================================================================
 * این فایل درخواست POST با فرمت JSON را می‌گیرد و یک فاکتور
 * جدید به همراه آیتم‌های آن در دیتابیس ثبت می‌کند.
 *
 * ورودی JSON:
 * {
 *   "cafe_id": 1,
 *   "table": 5,
 *   "first_name": "سارا",
 *   "last_name": "احمدی",
 *   "phone": "09123456789",
 *   "items": [
 *     { "id": 1, "qty": 2 },
 *     { "id": 3, "qty": 1 }
 *   ]
 * }
 *
 * خروجی JSON:
 * { "status": 1, "msg": "...", "invoice_id": 12, "items": 3, "total": 250000 }
 * ================================================================
 */

error_reporting(0);
ini_set('display_errors', 0);
session_start();
ob_start();

include_once 'lib_include.php';

header('Content-Type: application/json; charset=utf-8');

/* ================================================================
   تابع کمکی برای پاسخ JSON
   ================================================================ */
function reply($status, $msg, $extra = [])
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode(
        array_merge(['status' => $status, 'msg' => $msg], $extra),
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

/* ================================================================
   خواندن ورودی (JSON یا POST معمولی)
   ================================================================ */
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!is_array($input)) {
    $input = $_POST;
}

$cafe_id = (int)($input['cafe_id'] ?? 0);
$table = (int)($input['table'] ?? 0);
$fname = trim($input['first_name'] ?? '');
$lname = trim($input['last_name'] ?? '');
$phone = trim($input['phone'] ?? '');
$items = $input['items'] ?? [];

/* ================================================================
   اعتبارسنجی ورودی‌ها
   ================================================================ */
if ($cafe_id <= 0) {
    reply(0, 'کافه نامعتبر است');
}

if ($fname === '') {
    reply(0, 'نام الزامی است');
}

if ($lname === '') {
    reply(0, 'نام خانوادگی الزامی است');
}

/* نرمال‌سازی شماره تلفن */
$phone = preg_replace('/[^0-9]/', '', $phone);
if (!preg_match('/^09\d{9}$/', $phone)) {
    reply(0, 'شماره تلفن نامعتبر است (باید با ۰۹ شروع شود و ۱۱ رقم باشد)');
}

/* اعتبارسنجی شماره میز */
if ($table < 1 || $table > 99) {
    reply(0, 'شماره میز نامعتبر است (باید بین ۱ تا ۹۹ باشد)');
}

if (!is_array($items) || empty($items)) {
    reply(0, 'سبد خرید خالی است');
}

/* ================================================================
   اتصال به دیتابیس
   ================================================================ */
$db = new database();
$db->connect();

/* ================================================================
   بررسی وجود کافه و فعال بودن آن
   ================================================================ */
$db->query("SELECT id FROM `cafes` WHERE `id` = $cafe_id AND `status` = 1 LIMIT 1");
if (mysqli_num_rows($db->res) === 0) {
    reply(0, 'کافه یافت نشد یا غیرفعال است');
}

/* ================================================================
   امن‌سازی رشته‌ها
   ================================================================ */
$fm = new makeform();
$fname_s = $fm->sqlstr($fname);
$lname_s = $fm->sqlstr($lname);
$phone_s = $fm->sqlstr($phone);

/* ================================================================
   ۱) مشتری: پیدا کردن یا ساخت مشتری جدید
   ================================================================ */
$db->query("SELECT id FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");

if (mysqli_num_rows($db->res) > 0) {
    $row = mysqli_fetch_assoc($db->res);
    $customer_id = (int)$row['id'];
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
   ۲) ایجاد فاکتور جدید
       status = 0      →  مشاهده نشده
       waiter_id = 0   →  ثبت مستقیم توسط مشتری
   ================================================================ */
$today = date('Y-m-d');

$db->query("INSERT INTO `invoices`
            (`invoice_date`, `table_number`, `discount_percent`,
             `customer_id`, `cafe_id`, `waiter_id`, `status`)
            VALUES
            ('$today', $table, 0, $customer_id, $cafe_id, 0, 0)");

$invoice_id = (int)mysqli_insert_id($db->connection);

if ($invoice_id <= 0) {
    reply(0, 'خطا در ایجاد فاکتور');
}

/* ================================================================
   ۳) افزودن آیتم‌ها به فاکتور
       - قیمت از دیتابیس خوانده می‌شود (نه از سمت کلاینت)
       - چک می‌شود که آیتم به همین کافه تعلق دارد
   ================================================================ */
$added_count = 0;
$grand_total = 0;

foreach ($items as $it) {

    $menu_id = (int)($it['id'] ?? 0);
    $qty = (int)($it['qty'] ?? 0);

    /* اعتبارسنجی ساده */
    if ($menu_id <= 0) continue;
    if ($qty <= 0) continue;
    if ($qty > 100) continue;   /* محدودیت حداکثر تعداد */

    /* بررسی تعلق آیتم به کافه و خواندن قیمت واقعی */
    $db->query("SELECT mi.price
                FROM `menu_items` mi
                JOIN `cafe_categories` cc ON cc.id = mi.category_id
                WHERE mi.id = $menu_id
                  AND cc.cafe_id = $cafe_id
                LIMIT 1");

    if (mysqli_num_rows($db->res) === 0) {
        continue;   /* آیتم متعلق به این کافه نیست — نادیده بگیر */
    }

    $row = mysqli_fetch_assoc($db->res);
    $price = (float)$row['price'];

    $db->query("INSERT INTO `invoice_items`
                (`invoice_id`, `menu_item_id`, `unit_price`, `quantity`)
                VALUES
                ($invoice_id, $menu_id, '$price', $qty)");

    $added_count++;
    $grand_total += $price * $qty;
}

/* ================================================================
   ۴) اگر هیچ آیتم معتبری اضافه نشد، فاکتور خالی را حذف کن
   ================================================================ */
if ($added_count === 0) {
    $db->query("DELETE FROM `invoices` WHERE `id` = $invoice_id");
    reply(0, 'هیچ آیتم معتبری یافت نشد');
}

/* ================================================================
   ۵) پاسخ موفق
   ================================================================ */
reply(1, 'سفارش شما با موفقیت ثبت شد', [
    'invoice_id' => $invoice_id,
    'items' => $added_count,
    'total' => $grand_total,
    'table' => $table,
]);

exit;
?>