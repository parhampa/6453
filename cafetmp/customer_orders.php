<?php
/**
 * ================================================================
 * دریافت سفارشات کاربر + تخفیف فعلی
 * ----------------------------------------------------------------
 * action = "list"      →  ۵ سفارش اخیر کاربر از این کافه
 * action = "discount"  →  تخفیف فعلی مشتری (از آخرین فاکتور غیرلغو)
 * ================================================================
 */

error_reporting(0);
ini_set('display_errors', 0);
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

$action = trim($input['action'] ?? '');
$phone = preg_replace('/[^0-9]/', '', $input['phone'] ?? '');
$cafe_id = (int)($input['cafe_id'] ?? 0);

if (!preg_match('/^09\d{9}$/', $phone)) {
    reply(0, 'شماره تلفن نامعتبر است');
}

/* ---------- اتصال و یافتن مشتری ---------- */
$db = new database();
$db->connect();

$fm = new makeform();
$phone_s = $fm->sqlstr($phone);

$db->query("SELECT `id` FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");
if (mysqli_num_rows($db->res) === 0) {
    reply(0, 'مشتری یافت نشد');
}
$customer_id = (int)mysqli_fetch_assoc($db->res)['id'];

/* ================================================================
   نقشه‌ی وضعیت‌های فاکتور
   ================================================================ */
$status_map = [
    0 => ['label' => 'مشاهده نشده', 'color' => 'status-pending', 'icon' => 'fa-hourglass-half'],
    1 => ['label' => 'مشاهده شده', 'color' => 'status-seen', 'icon' => 'fa-eye'],
    2 => ['label' => 'در حال انجام', 'color' => 'status-progress', 'icon' => 'fa-fire'],
    3 => ['label' => 'غیر قابل انجام', 'color' => 'status-cancel', 'icon' => 'fa-circle-xmark'],
    4 => ['label' => 'انجام شده', 'color' => 'status-ready', 'icon' => 'fa-circle-check'],
    5 => ['label' => 'در انتظار پرداخت', 'color' => 'status-waitpay', 'icon' => 'fa-credit-card'],
    6 => ['label' => 'پرداخت شده', 'color' => 'status-paid', 'icon' => 'fa-check-double'],
];

/* ================================================================
   action = list  →  ۵ سفارش اخیر از این کافه
   ================================================================ */
if ($action === 'list') {

    $where = "i.`customer_id` = $customer_id";
    if ($cafe_id > 0) $where .= " AND i.`cafe_id` = $cafe_id";

    /* ---------- مرحله ۱: گرفتن فاکتورها و ذخیره در آرایه ----------
       ⚠️ نکته حیاتی: نتیجه رو قبل از کوئری‌های بعدی در آرایه می‌ریزیم
       تا $db->res بازنویسی نشه.
    */
    $db->query("
        SELECT i.`id`, i.`invoice_date`, i.`table_number`,
               i.`discount_percent`, i.`status`,
               c.`title` AS cafe_title
        FROM `invoices` i
        LEFT JOIN `cafes` c ON c.`id` = i.`cafe_id`
        WHERE $where
        ORDER BY i.`id` DESC
        LIMIT 5
    ");

    $invoice_rows = [];
    while ($row = mysqli_fetch_assoc($db->res)) {
        $invoice_rows[] = $row;
    }

    /* ---------- مرحله ۲: برای هر فاکتور، آیتم‌ها رو بگیر ---------- */
    $orders = [];

    foreach ($invoice_rows as $row) {

        $inv_id = (int)$row['id'];
        $st = (int)$row['status'];
        $discount = (int)$row['discount_percent'];
        $info = $status_map[$st] ?? ['label' => 'نامشخص', 'color' => 'status-pending', 'icon' => 'fa-question'];

        /* آیتم‌های این فاکتور */
        $items = [];
        $subtotal = 0;

        $db->query("
            SELECT ii.`quantity`, ii.`unit_price`, mi.`title`
            FROM `invoice_items` ii
            LEFT JOIN `menu_items` mi ON mi.`id` = ii.`menu_item_id`
            WHERE ii.`invoice_id` = $inv_id
        ");
        while ($it = mysqli_fetch_assoc($db->res)) {
            $qty = (int)$it['quantity'];
            $price = (float)$it['unit_price'];
            $subtotal += $price * $qty;
            $items[] = [
                'title' => $it['title'] !== null ? $it['title'] : '—',
                'qty' => $qty,
                'price' => $price,
            ];
        }

        $total = $subtotal * (100 - $discount) / 100;

        $orders[] = [
            'id' => $inv_id,
            'date' => $row['invoice_date'],
            'table' => (int)$row['table_number'],
            'cafe_title' => $row['cafe_title'] ?? '',
            'status' => $st,
            'status_label' => $info['label'],
            'status_color' => $info['color'],
            'status_icon' => $info['icon'],
            'items_count' => count($items),
            'subtotal' => $subtotal,
            'discount_percent' => $discount,
            'total' => $total,
            'items' => $items,
        ];
    }

    reply(1, 'ok', ['orders' => $orders]);
}

/* ================================================================
   action = discount
   ================================================================ */
if ($action === 'discount') {

    $where = "`customer_id` = $customer_id AND `status` != 3";
    if ($cafe_id > 0) $where .= " AND `cafe_id` = $cafe_id";

    $db->query("SELECT `discount_percent` FROM `invoices`
                WHERE $where
                ORDER BY `id` DESC
                LIMIT 1");

    $discount = 0;
    if (mysqli_num_rows($db->res) > 0) {
        $discount = (int)mysqli_fetch_assoc($db->res)['discount_percent'];
    }

    reply(1, 'ok', ['discount_percent' => $discount]);
}

reply(0, 'درخواست نامعتبر');