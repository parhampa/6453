<?php
/**
 * پردازشگر AJAX برای فاکتور
 * شامل: آیتم‌ها، تخفیف، شماره میز، وضعیت
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start();

$response = ['success' => false, 'message' => 'خطای نامشخص'];
$json_sent = false;

register_shutdown_function(function () use (&$response, &$json_sent) {
    if ($json_sent) return;
    $buffer = '';
    while (ob_get_level() > 0) {
        $buffer = ob_get_clean() . $buffer;
    }
    if (!empty($buffer)) {
        $clean = trim(strip_tags($buffer));
        if (!empty($clean)) {
            $response['success'] = false;
            $response['message'] = $clean;
            $response['debug_output'] = $buffer;
        }
    }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
});

try {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include("../lib/php/lib_include.php");
    include("check_admin_session.php");

    $fm = new makeform();
    $db = new database();
    $db->connect();
    $ml = new mobile_input();

    /* ═══════════════════════════════════════════════════
       گارسون جاری + کافه‌ی مربوط به گارسون
       ═══════════════════════════════════════════════════ */
    $wid = (int)get_waiters_id();
    if ($wid <= 0) throw new Exception('گارسن فعال یافت نشد.');

    $db->query("SELECT cafe_id FROM `waiters` WHERE id = $wid LIMIT 1");
    $wrow = mysqli_fetch_assoc($db->res);
    $cfid = (int)($wrow['cafe_id'] ?? 0);

    $action = $ml->set_name("action")->set_title("عملیات")->set_important(false)->post_str();
    $invoice_id = $ml->set_name("invoice_id")->set_title("شناسه فاکتور")->set_important(false)->post_int();

    if ($action === '' || $action === null) {
        throw new Exception('پارامتر action دریافت نشد.');
    }
    if ($invoice_id <= 0) {
        throw new Exception('شناسه فاکتور نامعتبر است.');
    }

    /* ⭐ فقط فاکتورهای خود گارسون */
    $db->query("SELECT id FROM `invoices` WHERE id = $invoice_id AND waiter_id = $wid LIMIT 1");
    if (mysqli_num_rows($db->res) == 0) {
        throw new Exception('فاکتور مورد نظر یافت نشد.');
    }

    switch ($action) {

        /* ═══════ افزودن آیتم ═══════ */
        case 'add':
            $menu_item_id = $ml->set_name("menu_item_id")->set_title("آیتم منو")->set_important(true)->post_int();
            $quantity = $ml->set_name("quantity")->set_title("تعداد")->set_important(true)->post_int();
            $unit_price = $ml->set_name("unit_price")->set_title("قیمت واحد")->set_important(true)->post_int();

            if ($menu_item_id <= 0) throw new Exception('آیتم منو انتخاب نشده است.');
            if ($quantity <= 0) throw new Exception('تعداد باید بیشتر از صفر باشد.');

            /* ⭐ فقط آیتم‌های منوی کافه‌ی گارسون */
            $db->query("SELECT mi.id, mi.title, mi.recipe 
                        FROM `menu_items` mi
                        JOIN `cafe_categories` cc ON cc.id = mi.category_id
                        WHERE mi.id = $menu_item_id AND cc.cafe_id = $cfid LIMIT 1");
            if (mysqli_num_rows($db->res) == 0) throw new Exception('آیتم منو یافت نشد.');
            $mi = mysqli_fetch_assoc($db->res);

            $db->query("INSERT INTO `invoice_items` 
                        (`invoice_id`, `menu_item_id`, `unit_price`, `quantity`) 
                        VALUES ($invoice_id, $menu_item_id, $unit_price, $quantity)");
            $new_id = mysqli_insert_id($db->connection);

            $response = [
                'success' => true,
                'message' => 'آیتم با موفقیت اضافه شد.',
                'item' => [
                    'id' => $new_id,
                    'menu_item_id' => $menu_item_id,
                    'item_title' => $mi['title'],
                    'item_recipe' => $mi['recipe'],
                    'unit_price' => $unit_price,
                    'quantity' => $quantity,
                    'line_total' => $unit_price * $quantity
                ]
            ];
            break;

        /* ═══════ ویرایش آیتم ═══════ */
        case 'update':
            $id = $ml->set_name("id")->set_title("شناسه آیتم")->set_important(true)->post_int();
            $quantity = $ml->set_name("quantity")->set_title("تعداد")->set_important(true)->post_int();
            $unit_price = $ml->set_name("unit_price")->set_title("قیمت واحد")->set_important(true)->post_int();

            if ($id <= 0) throw new Exception('شناسه آیتم نامعتبر است.');
            if ($quantity <= 0) throw new Exception('تعداد باید بیشتر از صفر باشد.');

            $db->query("SELECT id FROM `invoice_items` WHERE id = $id AND invoice_id = $invoice_id LIMIT 1");
            if (mysqli_num_rows($db->res) == 0) throw new Exception('آیتم یافت نشد.');

            $db->query("UPDATE `invoice_items` SET `quantity` = $quantity, `unit_price` = $unit_price WHERE `id` = $id");

            $response = ['success' => true, 'message' => 'آیتم ویرایش شد.', 'line_total' => $unit_price * $quantity];
            break;

        /* ═══════ حذف آیتم ═══════ */
        case 'delete':
            $id = $ml->set_name("id")->set_title("شناسه آیتم")->set_important(true)->post_int();
            if ($id <= 0) throw new Exception('شناسه آیتم نامعتبر است.');

            $db->query("SELECT id FROM `invoice_items` WHERE id = $id AND invoice_id = $invoice_id LIMIT 1");
            if (mysqli_num_rows($db->res) == 0) throw new Exception('آیتم یافت نشد.');

            $db->query("DELETE FROM `invoice_items` WHERE `id` = $id");
            $response = ['success' => true, 'message' => 'آیتم حذف شد.'];
            break;

        /* ═══════ ویرایش تخفیف ═══════ */
        case 'update_discount':
            $discount_percent = $ml->set_name("discount_percent")->set_title("درصد تخفیف")->set_important(false)->post_int();
            if ($discount_percent < 0) $discount_percent = 0;
            if ($discount_percent > 100) $discount_percent = 100;

            $db->query("SELECT COALESCE(SUM(unit_price * quantity), 0) AS total FROM `invoice_items` WHERE invoice_id = $invoice_id");
            $row = mysqli_fetch_assoc($db->res);
            $grand_total = (float)$row['total'];

            $discount_amount = ($grand_total * $discount_percent) / 100;
            $final_total = $grand_total - $discount_amount;

            $db->query("UPDATE `invoices` SET `discount_percent` = $discount_percent WHERE `id` = $invoice_id");

            $response = [
                'success' => true,
                'message' => 'درصد تخفیف ذخیره شد.',
                'discount_percent' => $discount_percent,
                'discount_amount' => $discount_amount,
                'grand_total' => $grand_total,
                'final_total' => $final_total
            ];
            break;

        /* ═══════ ویرایش شماره میز ═══════ */
        case 'update_table':
            $table_number = $ml->set_name("table_number")->set_title("شماره میز")->set_important(false)->post_int();
            if ($table_number < 0) $table_number = 0;

            $db->query("UPDATE `invoices` SET `table_number` = $table_number WHERE `id` = $invoice_id");

            $response = ['success' => true, 'message' => 'شماره میز ذخیره شد.', 'table_number' => $table_number];
            break;

        /* ═══════ ویرایش وضعیت فاکتور ═══════ */
        case 'update_status':
            $status = $ml->set_name("status")->set_title("وضعیت")->set_important(false)->post_int();
            if ($status < 0 || $status > 6) {
                throw new Exception('وضعیت نامعتبر است.');
            }

            $db->query("UPDATE `invoices` SET `status` = $status WHERE `id` = $invoice_id");

            $status_texts = [
                0 => 'مشاهده نشده',
                1 => 'مشاهده شده',
                2 => 'در حال انجام',
                3 => 'غیر قابل انجام',
                4 => 'انجام شده',
                5 => 'در انتظار پرداخت',
                6 => 'پرداخت شده'
            ];

            $response = [
                'success' => true,
                'message' => 'وضعیت فاکتور به «' . $status_texts[$status] . '» تغییر یافت.',
                'status' => $status,
                'status_text' => $status_texts[$status]
            ];
            break;

        default:
            throw new Exception('عملیات نامعتبر است: ' . htmlspecialchars($action));
    }

} catch (Throwable $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

$json_sent = true;

while (ob_get_level() > 0) {
    ob_end_clean();
}

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;