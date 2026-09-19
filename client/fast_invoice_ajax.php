<?php
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
    if (session_status() === PHP_SESSION_NONE) session_start();

    include("../lib/php/lib_include.php");
    include("check_admin_session.php");
    include("calhead.php");

    $db = new database();
    $db->connect();
    $ml = new mobile_input();
    $fm = new makeform();

    $cfid = (int)get_cafe_id();
    if ($cfid <= 0) throw new Exception('کافه فعال یافت نشد.');

    $action = $ml->set_name("action")->set_title("عملیات")->set_important(false)->post_str();
    if (empty($action)) throw new Exception('عملیات نامعتبر است.');

    /* ═══════════ 1) ایجاد فاکتور + مشتری ═══════════ */
    if ($action === 'create_invoice') {

        $table_number = $ml->set_name("table_number")->set_title("شماره میز")->set_important(true)->post_int();
        $customer_tel = $ml->set_name("customer_tel")->set_title("شماره تماس")->set_important(true)->post_str();
        $customer_name = $ml->set_name("customer_name")->set_title("نام")->set_important(true)->post_str();
        $customer_family = $ml->set_name("customer_family")->set_title("نام خانوادگی")->set_important(true)->post_str();
        $customer_birth = $ml->set_name("customer_birth")->set_title("تاریخ تولد")->set_important(false)->post_str();

        if ($table_number <= 0) throw new Exception('شماره میز را وارد کنید.');
        if (empty($customer_tel)) throw new Exception('شماره تماس را وارد کنید.');

        $tel_s = $fm->sqlstr($customer_tel);

        // ---- اعتبارسنجی تاریخ تولد (اگر وارد شده باشد) ----
        $birth_sql = "NULL";
        if (!empty($customer_birth)) {
            // فرمت مورد انتظار: YYYY-MM-DD (میلادی)
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $customer_birth)) {
                throw new Exception('فرمت تاریخ تولد نامعتبر است.');
            }
            // بررسی صحت تاریخ
            $parts = explode('-', $customer_birth);
            if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                throw new Exception('تاریخ تولد وارد شده معتبر نیست.');
            }
            $birth_s = $fm->sqlstr($customer_birth);
            $birth_sql = "'$birth_s'";
        }

        $db->query("SELECT id FROM `customers` WHERE tel = '$tel_s' LIMIT 1");
        if (mysqli_num_rows($db->res) > 0) {
            $c = mysqli_fetch_assoc($db->res);
            $customer_id = (int)$c['id'];

            // اگر مشتری وجود دارد ولی تاریخ تولدش خالی است، به‌روزرسانی کن
            if ($birth_sql !== "NULL") {
                $db->query("UPDATE `customers` 
                            SET birth_date = $birth_sql 
                            WHERE id = $customer_id 
                              AND (birth_date IS NULL OR birth_date = '0000-00-00')");
            }
        } else {
            $name_s = $fm->sqlstr($customer_name);
            $family_s = $fm->sqlstr($customer_family);
            $mili = time() . rand(100, 999);

            $db->query("INSERT INTO `customers` (`name`, `family`, `tel`, `birth_date`, `mili`) 
                        VALUES ('$name_s', '$family_s', '$tel_s', $birth_sql, '$mili')");
            $customer_id = mysqli_insert_id($db->connection);
        }

        $today = date('Y-m-d');
        $waiter_id = isset($_SESSION['waiter_id']) ? (int)$_SESSION['waiter_id'] : 0;

        $db->query("INSERT INTO `invoices` 
                    (`invoice_date`, `table_number`, `discount_percent`, `customer_id`, `cafe_id`, `waiter_id`, `status`) 
                    VALUES ('$today', $table_number, 0, $customer_id, $cfid, $waiter_id, 0)");
        $invoice_id = mysqli_insert_id($db->connection);

        $response = [
            'success' => true,
            'message' => 'فاکتور با موفقیت ایجاد شد.',
            'invoice_id' => $invoice_id,
            'customer_id' => $customer_id,
            'customer_name' => $customer_name,
            'customer_family' => $customer_family,
            'customer_tel' => $customer_tel,
            'table_number' => $table_number,
            'birth_date' => $customer_birth
        ];
    } /* ═══════════ 2) افزودن آیتم ═══════════ */
    elseif ($action === 'add_item') {

        $invoice_id = $ml->set_name("invoice_id")->set_title("شناسه فاکتور")->set_important(true)->post_int();
        $menu_item_id = $ml->set_name("menu_item_id")->set_title("آیتم منو")->set_important(true)->post_int();
        $quantity = $ml->set_name("quantity")->set_title("تعداد")->set_important(true)->post_int();

        if ($quantity <= 0) $quantity = 1;

        $db->query("SELECT id FROM `invoices` WHERE id = $invoice_id AND cafe_id = $cfid LIMIT 1");
        if (mysqli_num_rows($db->res) == 0) throw new Exception('فاکتور معتبر نیست.');

        $db->query("SELECT mi.id, mi.title, mi.recipe, mi.price 
                    FROM `menu_items` mi
                    JOIN `cafe_categories` cc ON cc.id = mi.category_id
                    WHERE mi.id = $menu_item_id AND cc.cafe_id = $cfid LIMIT 1");
        if (mysqli_num_rows($db->res) == 0) throw new Exception('آیتم یافت نشد.');
        $mi = mysqli_fetch_assoc($db->res);

        $unit_price = (float)$mi['price'];

        $db->query("SELECT id, quantity FROM `invoice_items` 
                    WHERE invoice_id = $invoice_id AND menu_item_id = $menu_item_id LIMIT 1");

        if (mysqli_num_rows($db->res) > 0) {
            $existing = mysqli_fetch_assoc($db->res);
            $new_qty = (int)$existing['quantity'] + $quantity;
            $item_id = (int)$existing['id'];

            $db->query("UPDATE `invoice_items` SET quantity = $new_qty WHERE id = $item_id");
            $is_new = false;
            $final_qty = $new_qty;
        } else {
            $db->query("INSERT INTO `invoice_items` (`invoice_id`, `menu_item_id`, `unit_price`, `quantity`) 
                        VALUES ($invoice_id, $menu_item_id, $unit_price, $quantity)");
            $item_id = mysqli_insert_id($db->connection);
            $is_new = true;
            $final_qty = $quantity;
        }

        $response = [
            'success' => true,
            'is_new' => $is_new,
            'item' => [
                'id' => $item_id,
                'menu_item_id' => $menu_item_id,
                'item_title' => $mi['title'],
                'item_recipe' => $mi['recipe'],
                'unit_price' => $unit_price,
                'quantity' => $final_qty,
                'line_total' => $unit_price * $final_qty
            ]
        ];
    } /* ═══════════ 3) حذف آیتم ═══════════ */
    elseif ($action === 'delete_item') {

        $invoice_id = $ml->set_name("invoice_id")->set_title("شناسه فاکتور")->set_important(true)->post_int();
        $item_id = $ml->set_name("item_id")->set_title("شناسه آیتم")->set_important(true)->post_int();

        $db->query("SELECT ii.id FROM `invoice_items` ii 
                    JOIN `invoices` i ON i.id = ii.invoice_id 
                    WHERE ii.id = $item_id AND ii.invoice_id = $invoice_id AND i.cafe_id = $cfid LIMIT 1");
        if (mysqli_num_rows($db->res) == 0) throw new Exception('آیتم یافت نشد.');

        $db->query("DELETE FROM `invoice_items` WHERE id = $item_id");

        $response = ['success' => true, 'message' => 'آیتم حذف شد.'];
    } /* ═══════════ 4) تغییر تعداد ═══════════ */
    elseif ($action === 'update_quantity') {

        $invoice_id = $ml->set_name("invoice_id")->set_title("شناسه فاکتور")->set_important(true)->post_int();
        $item_id = $ml->set_name("item_id")->set_title("شناسه آیتم")->set_important(true)->post_int();
        $quantity = $ml->set_name("quantity")->set_title("تعداد")->set_important(true)->post_int();

        if ($quantity <= 0) throw new Exception('تعداد باید بیشتر از صفر باشد.');

        $db->query("SELECT ii.id, ii.unit_price FROM `invoice_items` ii 
                    JOIN `invoices` i ON i.id = ii.invoice_id 
                    WHERE ii.id = $item_id AND ii.invoice_id = $invoice_id AND i.cafe_id = $cfid LIMIT 1");
        if (mysqli_num_rows($db->res) == 0) throw new Exception('آیتم یافت نشد.');
        $row = mysqli_fetch_assoc($db->res);

        $db->query("UPDATE `invoice_items` SET quantity = $quantity WHERE id = $item_id");

        $response = [
            'success' => true,
            'quantity' => $quantity,
            'line_total' => (float)$row['unit_price'] * $quantity
        ];
    } /* ═══════════ 5) ثبت نهایی ═══════════ */
    elseif ($action === 'finalize') {

        $invoice_id = $ml->set_name("invoice_id")->set_title("شناسه فاکتور")->set_important(true)->post_int();
        $status = $ml->set_name("status")->set_title("وضعیت")->set_important(true)->post_int();

        if ($status < 0 || $status > 6) throw new Exception('وضعیت نامعتبر است.');

        $db->query("SELECT id FROM `invoices` WHERE id = $invoice_id AND cafe_id = $cfid LIMIT 1");
        if (mysqli_num_rows($db->res) == 0) throw new Exception('فاکتور معتبر نیست.');

        $db->query("SELECT COUNT(*) AS c FROM `invoice_items` WHERE invoice_id = $invoice_id");
        $row = mysqli_fetch_assoc($db->res);
        if ((int)$row['c'] == 0) throw new Exception('حداقل یک آیتم به فاکتور اضافه کنید.');

        $db->query("UPDATE `invoices` SET status = $status WHERE id = $invoice_id");

        $response = [
            'success' => true,
            'message' => 'فاکتور با موفقیت ثبت شد.',
            'invoice_id' => $invoice_id
        ];
    } else {
        throw new Exception('عملیات نامعتبر: ' . htmlspecialchars($action));
    }

} catch (Throwable $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

$json_sent = true;
while (ob_get_level() > 0) ob_end_clean();
if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;