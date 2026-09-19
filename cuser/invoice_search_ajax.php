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

    /* ═══════════════════════════════════════════════════
       شناسه گارسون جاری
       ═══════════════════════════════════════════════════ */
    $wid = (int)get_waiters_id();
    if ($wid <= 0) throw new Exception('گارسن فعال یافت نشد.');

    $action = $ml->set_name("action")->set_title("عملیات")->set_important(false)->post_str();
    if (empty($action)) throw new Exception('عملیات نامعتبر است.');

    if ($action === 'search_invoices') {

        $q_table = trim($ml->set_name("q_table")->set_title("شماره میز")->set_important(false)->post_str());
        $q_tel = trim($ml->set_name("q_tel")->set_title("شماره تماس")->set_important(false)->post_str());

        if ($q_table === '' && $q_tel === '') {
            throw new Exception('شماره میز یا شماره تماس مشتری را وارد کنید.');
        }

        $cond_table = '';
        if ($q_table !== '') {
            $t = (int)$q_table;
            if ($t > 0) {
                $cond_table = " AND i.table_number = $t ";
            }
        }

        $cond_tel = '';
        if ($q_tel !== '') {
            $tels = $fm->sqlstr($q_tel);
            $cond_tel = " AND c.tel LIKE '%$tels%' ";
        }

        /* ═══════════════════════════════════════════════════
           فقط فاکتورهای همین گارسون
           ═══════════════════════════════════════════════════ */
        $sql = "SELECT 
                    i.id AS invoice_id,
                    i.invoice_date,
                    i.table_number,
                    i.status,
                    i.discount_percent,
                    c.id AS customer_id,
                    c.name AS customer_name,
                    c.family AS customer_family,
                    c.tel AS customer_tel,
                    (SELECT COALESCE(SUM(unit_price * quantity), 0) 
                     FROM `invoice_items` 
                     WHERE invoice_id = i.id) AS items_total
                FROM `invoices` i
                LEFT JOIN `customers` c ON c.id = i.customer_id
                WHERE i.waiter_id = $wid
                  $cond_table
                  $cond_tel
                ORDER BY i.id DESC
                LIMIT 5";

        $db->query($sql);

        $results = [];
        $status_texts = [
            0 => 'مشاهده نشده',
            1 => 'مشاهده شده',
            2 => 'در حال انجام',
            3 => 'غیر قابل انجام',
            4 => 'انجام شده',
            5 => 'در انتظار پرداخت',
            6 => 'پرداخت شده'
        ];
        $status_classes = [
            0 => 'st-notseen',
            1 => 'st-seen',
            2 => 'st-inprogress',
            3 => 'st-failed',
            4 => 'st-done',
            5 => 'st-waitpay',
            6 => 'st-paid'
        ];

        while ($row = mysqli_fetch_assoc($db->res)) {

            $year = substr($row['invoice_date'], 0, 4);
            $month = substr($row['invoice_date'], 5, 2);
            $day = substr($row['invoice_date'], 8, 2);
            $jdate = gregorian_to_jalali($year, $month, $day);
            $jfdate = $jdate[0] . "/" . $jdate[1] . "/" . $jdate[2];

            $name = trim(($row['customer_name'] ?? '') . ' ' . ($row['customer_family'] ?? ''));
            if ($name === '') $name = 'مشتری #' . $row['customer_id'];

            $items_total = (float)$row['items_total'];
            $discount_percent = (float)$row['discount_percent'];
            $discount_amount = ($items_total * $discount_percent) / 100;
            $final_total = $items_total - $discount_amount;

            $status = (int)$row['status'];

            $results[] = [
                'invoice_id' => (int)$row['invoice_id'],
                'invoice_date' => $jfdate,
                'table_number' => (int)$row['table_number'],
                'full_name' => $name,
                'customer_tel' => $row['customer_tel'] ?? '—',
                'status' => $status,
                'status_text' => $status_texts[$status] ?? 'نامشخص',
                'status_class' => $status_classes[$status] ?? '',
                'final_total' => $final_total
            ];
        }

        $response = [
            'success' => true,
            'waiter_id' => $wid,
            'count' => count($results),
            'results' => $results
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