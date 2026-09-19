<?php
/**
 * نمایش و ویرایش فاکتور
 * 🔒 فقط فاکتورهای کافه فعلی قابل مشاهده هستند
 */
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
include("calhead.php");

$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ═══════════════════════════════════════════════════
// کد کافه فعلی از تابع get_cafe_id()
// ═══════════════════════════════════════════════════
$current_cafe_id = (int)get_cafe_id();

$db = new database();
$db->connect();

$invoice = null;
$items = [];
$all_menu_items = [];
$grand_total = 0;
$discount_amount = 0;
$final_total = 0;
$discount_percent = 0;
$inv_status = ['text' => 'نامشخص', 'class' => ''];
$jfdate = '—';
$customer_fullname = '—';
$has_logo = false;
$logo_path = '';

if ($invoice_id > 0) {

    // ═══════════════════════════════════════════════════
    // کوئری با فیلتر cafe_id
    // فقط فاکتوری که هم id آن با invoice_id برابر است
    // و هم cafe_id آن برابر کافه فعلی باشد
    // ═══════════════════════════════════════════════════
    $db->query("SELECT id, invoice_date, table_number, discount_percent, status, customer_id, cafe_id, waiter_id 
                FROM `invoices` 
                WHERE id = $invoice_id 
                  AND cafe_id = $current_cafe_id");

    if (mysqli_num_rows($db->res) > 0) {
        $invoice = mysqli_fetch_assoc($db->res);

        // مشتری
        $invoice['customer_name'] = '';
        $invoice['customer_family'] = '';
        $invoice['customer_tel'] = '';
        $invoice['customer_mili'] = '';

        if (!empty($invoice['customer_id'])) {
            $cid = (int)$invoice['customer_id'];
            $db->query("SELECT * FROM `customers` WHERE id = $cid");
            if (mysqli_num_rows($db->res) > 0) {
                $c = mysqli_fetch_assoc($db->res);
                $invoice['customer_name'] = $c['name'] ?? '';
                $invoice['customer_family'] = $c['family'] ?? '';
                $invoice['customer_tel'] = $c['tel'] ?? '';
                $invoice['customer_mili'] = $c['mili'] ?? '';
            }
        }

        // کافه
        $invoice['cafe_title'] = '';
        $invoice['cafe_slogan'] = '';
        $invoice['cafe_tel1'] = '';
        $invoice['cafe_tel2'] = '';
        $invoice['cafe_address'] = '';
        $invoice['cafe_instagram'] = '';
        $invoice['cafe_hours'] = '';
        $invoice['cafe_logo'] = '';

        if (!empty($invoice['cafe_id'])) {
            $cfid = (int)$invoice['cafe_id'];
            $db->query("SELECT * FROM `cafes` WHERE id = $cfid");
            if (mysqli_num_rows($db->res) > 0) {
                $cf = mysqli_fetch_assoc($db->res);
                $invoice['cafe_title'] = $cf['title'] ?? '';
                $invoice['cafe_slogan'] = $cf['slogan'] ?? '';
                $invoice['cafe_tel1'] = $cf['tel1'] ?? '';
                $invoice['cafe_tel2'] = $cf['tel2'] ?? '';
                $invoice['cafe_address'] = $cf['address'] ?? '';
                $invoice['cafe_instagram'] = $cf['instagram'] ?? '';
                $invoice['cafe_hours'] = $cf['working_hours'] ?? '';
                $invoice['cafe_logo'] = $cf['logo'] ?? '';
            }
        }

        // گارسون
        $invoice['waiter_name'] = '';
        if (!empty($invoice['waiter_id']) && $invoice['waiter_id'] > 0) {
            $wid = (int)$invoice['waiter_id'];
            $db->query("SELECT fullname FROM `waiters` WHERE id = $wid");
            if (mysqli_num_rows($db->res) > 0) {
                $w = mysqli_fetch_assoc($db->res);
                $invoice['waiter_name'] = $w['fullname'] ?? '';
            }
        }

        // اقلام فاکتور
        $db->query("SELECT 
                        ii.id, ii.menu_item_id, ii.unit_price, ii.quantity,
                        mi.title AS item_title, mi.recipe AS item_recipe
                    FROM `invoice_items` ii
                    LEFT JOIN `menu_items` mi ON mi.id = ii.menu_item_id
                    WHERE ii.invoice_id = $invoice_id
                    ORDER BY ii.id ASC");

        while ($row = mysqli_fetch_assoc($db->res)) {
            $row['line_total'] = $row['unit_price'] * $row['quantity'];
            $grand_total += $row['line_total'];
            $items[] = $row;
        }

        // محاسبه تخفیف
        $discount_percent = isset($invoice['discount_percent']) ? (float)$invoice['discount_percent'] : 0;
        if ($discount_percent < 0) $discount_percent = 0;
        if ($discount_percent > 100) $discount_percent = 100;

        $discount_amount = ($grand_total * $discount_percent) / 100;
        $final_total = $grand_total - $discount_amount;

        // لیست آیتم‌های منو (فقط از کافه فعلی)
        $db->query("SELECT mi.id, mi.title, mi.price 
                    FROM `menu_items` mi 
                    JOIN `cafe_categories` cc ON cc.id = mi.category_id 
                    WHERE cc.cafe_id = $current_cafe_id 
                    ORDER BY mi.title");
        while ($row = mysqli_fetch_assoc($db->res)) {
            $all_menu_items[] = $row;
        }

        // تاریخ شمسی
        $year = substr($invoice['invoice_date'], 0, 4);
        $month = substr($invoice['invoice_date'], 5, 2);
        $day = substr($invoice['invoice_date'], 8, 2);
        $jdate = gregorian_to_jalali($year, $month, $day);
        $jfdate = $jdate[0] . "/" . $jdate[1] . "/" . $jdate[2];

        // وضعیت
        $status_map = [
            0 => ['text' => 'مشاهده نشده', 'class' => 'status-notseen'],
            1 => ['text' => 'مشاهده شده', 'class' => 'status-seen'],
            2 => ['text' => 'در حال انجام', 'class' => 'status-inprogress'],
            3 => ['text' => 'غیر قابل انجام', 'class' => 'status-failed'],
            4 => ['text' => 'انجام شده', 'class' => 'status-done'],
            5 => ['text' => 'در انتظار پرداخت', 'class' => 'status-waitpay'],
            6 => ['text' => 'پرداخت شده', 'class' => 'status-paid'],
        ];
        $inv_status = $status_map[$invoice['status']] ?? ['text' => 'نامشخص', 'class' => ''];

        // لوگو
        $logo_path = $invoice['cafe_logo'];
        $has_logo = !empty($logo_path) && file_exists("../" . $logo_path);

        // نام مشتری
        $customer_fullname = trim($invoice['customer_name'] . ' ' . $invoice['customer_family']);
        if ($customer_fullname === '') {
            $customer_fullname = 'مشتری #' . $invoice['customer_id'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<title>مشاهده فاکتور<?php echo $invoice ? ' #' . $invoice_id : ''; ?></title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/new.css">
<style>
    :root {
        --panel-primary: #2c3e50;
        --panel-secondary: #34495e;
        --panel-accent: #16a085;
        --panel-bg: #f4f6f9;
        --panel-text: #2c3e50;
        --border-color: #e8ecef;
    }

    body {
        background-color: var(--panel-bg);
        font-family: Tahoma, "Segoe UI", sans-serif;
        color: var(--panel-text);
    }

    .invoice-actions {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        padding: 10px 14px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    .invoice-actions .page-title {
        font-size: 13.5px;
        font-weight: bold;
        color: var(--panel-primary);
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .invoice-actions .page-title i {
        color: var(--panel-accent);
        font-size: 14px;
    }

    .invoice-actions .buttons {
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-print {
        background: var(--panel-accent);
        color: #fff;
    }

    .btn-print:hover {
        background: #12876f;
        color: #fff;
    }

    .btn-back {
        background: #f4f6f9;
        color: var(--panel-primary);
        border: 1px solid var(--border-color);
    }

    .btn-back:hover {
        background: #e8edf2;
        color: var(--panel-primary);
    }

    .btn-edit-toggle {
        background: #f39c12;
        color: #fff;
    }

    .btn-edit-toggle:hover {
        background: #d68910;
        color: #fff;
    }

    .btn-edit-toggle.active {
        background: var(--panel-primary);
        color: #fff;
    }

    .btn-edit-toggle.active:hover {
        background: #1a252f;
    }

    .invoice-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.07);
        overflow: hidden;
        margin-bottom: 16px;
    }

    .invoice-header {
        background: linear-gradient(135deg, var(--panel-primary) 0%, #3d5468 100%);
        color: #fff;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        position: relative;
        overflow: hidden;
    }

    .invoice-header::before {
        content: '';
        position: absolute;
        top: -80%;
        left: -10%;
        width: 260px;
        height: 300%;
        background: rgba(255, 255, 255, 0.04);
        transform: rotate(25deg);
        pointer-events: none;
    }

    .invoice-header .cafe-info {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .cafe-logo {
        width: 62px;
        height: 62px;
        border-radius: 10px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, 0.25);
    }

    .cafe-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cafe-logo .no-logo {
        font-size: 26px;
        color: var(--panel-accent);
    }

    .cafe-details h1 {
        font-size: 16px;
        font-weight: bold;
        margin: 0 0 2px;
        letter-spacing: 0.3px;
    }

    .cafe-details .slogan {
        font-size: 10.5px;
        opacity: 0.85;
        margin-bottom: 4px;
        font-style: italic;
    }

    .cafe-details .contact-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 10.5px;
        opacity: 0.9;
    }

    .cafe-details .contact-row span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .cafe-details .contact-row i {
        color: #1abc9c;
        font-size: 10px;
    }

    .invoice-number-box {
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .invoice-number-box .label {
        font-size: 9.5px;
        opacity: 0.8;
        margin-bottom: 2px;
    }

    .invoice-number-box .number {
        font-size: 20px;
        font-weight: bold;
        letter-spacing: 0.8px;
        direction: ltr;
    }

    .status-select {
        margin-top: 6px;
        padding: 4px 10px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: bold;
        font-family: Tahoma;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        direction: rtl;
        transition: all 0.2s ease;
        min-width: 140px;
        text-align: center;
    }

    .status-select:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .status-select:focus {
        outline: none;
        background: #ffffff;
        color: var(--panel-primary);
        border-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
    }

    .status-select option {
        color: var(--panel-primary);
        background: #ffffff;
        padding: 4px;
    }

    .status-select.saving {
        background: #fef5e7 !important;
        color: #d68910 !important;
        border-color: #f39c12 !important;
    }

    .status-select.saved {
        background: #e8f5e9 !important;
        color: #27ae60 !important;
        border-color: #27ae60 !important;
    }

    .status-select.save-error {
        background: #fdecea !important;
        color: #c0392b !important;
        border-color: #c0392b !important;
    }

    .status-notseen {
        background: #ecf0f1;
        color: #7f8c8d;
    }

    .status-seen {
        background: #e3f2fd;
        color: #2980b9;
    }

    .status-inprogress {
        background: #fef5e7;
        color: #d68910;
    }

    .status-failed {
        background: #fdecea;
        color: #c0392b;
    }

    .status-done {
        background: #e8f5e9;
        color: #27ae60;
    }

    .status-waitpay {
        background: #fef0e6;
        color: #d35400;
    }

    .status-paid {
        background: #d5f4ea;
        color: #16a085;
    }

    .invoice-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: #fafbfc;
        border-bottom: 1px solid var(--border-color);
        padding: 12px 20px;
        gap: 20px;
    }

    .meta-block .block-title {
        font-size: 10.5px;
        font-weight: bold;
        color: var(--panel-accent);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .meta-block .block-title i {
        font-size: 10px;
    }

    .meta-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        font-size: 12px;
    }

    .meta-inline .item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .meta-inline .item .k {
        color: #95a5a6;
        font-size: 11px;
    }

    .meta-inline .item .v {
        color: var(--panel-text);
        font-weight: 600;
    }

    .items-section {
        padding: 14px 20px 6px;
    }

    .items-section .section-title {
        font-size: 12px;
        font-weight: bold;
        color: var(--panel-primary);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .items-section .section-title i {
        color: var(--panel-accent);
        font-size: 11px;
    }

    .items-section .section-title .count {
        font-size: 10.5px;
        color: #95a5a6;
        font-weight: normal;
        margin-right: auto;
    }

    .items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .items-table thead {
        background: var(--panel-primary);
        color: #fff;
    }

    .items-table thead th {
        padding: 8px 12px;
        font-size: 11px;
        font-weight: 600;
        text-align: right;
        white-space: nowrap;
    }

    .items-table thead th:first-child {
        text-align: center;
        width: 40px;
    }

    .items-table thead th.num {
        text-align: center;
        width: 100px;
    }

    .items-table tbody tr {
        background: #ffffff;
        transition: background 0.15s ease;
    }

    .items-table tbody tr:nth-child(even) {
        background: #fbfcfd;
    }

    .items-table tbody tr:hover {
        background: #f0f8f5;
    }

    .items-table tbody td {
        padding: 8px 12px;
        font-size: 12px;
        border-top: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .items-table tbody td:first-child {
        text-align: center;
        font-weight: bold;
        color: var(--panel-accent);
        font-size: 11px;
    }

    .items-table tbody td.num {
        text-align: center;
        font-family: Tahoma;
        font-weight: 600;
        direction: ltr;
        font-size: 11.5px;
    }

    .items-table tbody td.line-total {
        color: var(--panel-accent);
        font-weight: bold;
    }

    .item-title-cell {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .item-title-cell .title {
        font-weight: 600;
        font-size: 12px;
    }

    .item-title-cell .recipe {
        font-size: 10px;
        color: #95a5a6;
        font-weight: normal;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .empty-items {
        text-align: center;
        padding: 30px 20px;
        color: #95a5a6;
        font-size: 12px;
    }

    .empty-items i {
        font-size: 28px;
        color: #d5dde3;
        display: block;
        margin-bottom: 8px;
    }

    .edit-inline-input {
        width: 100%;
        padding: 5px 7px;
        border: 1px solid #d6dbe1;
        border-radius: 5px;
        font-size: 11.5px;
        text-align: center;
        font-family: Tahoma;
        background: #ffffff;
        transition: all 0.3s ease;
        direction: ltr;
        color: var(--panel-text);
        font-weight: 600;
        cursor: text;
    }

    .edit-inline-input:hover {
        border-color: #b3bcc5;
        background: #fafbfc;
    }

    .edit-inline-input:focus {
        border-color: var(--panel-accent);
        box-shadow: 0 0 0 2px rgba(22, 160, 133, 0.15);
        outline: none;
        background: #ffffff;
    }

    .edit-inline-input.saving {
        background: #fef5e7 !important;
        border-color: #f39c12 !important;
        color: #d68910 !important;
    }

    .edit-inline-input.saved {
        background: #e8f5e9 !important;
        border-color: #27ae60 !important;
        color: #27ae60 !important;
    }

    .edit-inline-input.save-error {
        background: #fdecea !important;
        border-color: #c0392b !important;
        color: #c0392b !important;
    }

    .add-select {
        width: 100%;
        padding: 5px 7px;
        border: 1px solid #d6dbe1;
        border-radius: 5px;
        font-size: 11.5px;
        background: #ffffff;
        color: var(--panel-text);
    }

    .add-select:focus {
        border-color: var(--panel-accent);
        outline: none;
    }

    .btn-row-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-width: 30px;
        height: 30px;
        padding: 0 8px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 11.5px;
        vertical-align: middle;
        font-family: Tahoma;
    }

    .btn-del {
        background: #fdecea;
        color: #c0392b;
    }

    .btn-del:hover {
        background: #c0392b;
        color: #fff;
    }

    .btn-add {
        background: var(--panel-accent);
        color: #fff;
        font-weight: 600;
        padding: 0 12px;
    }

    .btn-add:hover {
        background: #12876f;
        color: #fff;
    }

    .add-row td {
        background: #f0f8f5 !important;
        border-top: 2px dashed var(--panel-accent) !important;
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }

    .total-section {
        padding: 8px 20px 16px;
    }

    .total-wrapper {
        display: flex;
        justify-content: flex-end;
    }

    .total-panel {
        min-width: 340px;
        max-width: 420px;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 16px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 12.5px;
    }

    .total-row .label {
        color: #5a6b7a;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .total-row .label i {
        color: #95a5a6;
        font-size: 11px;
    }

    .total-row .value {
        font-weight: bold;
        color: var(--panel-text);
        direction: ltr;
    }

    .total-row .value small {
        font-size: 10px;
        font-weight: normal;
        color: #95a5a6;
        margin-right: 3px;
    }

    .total-row.discount-row {
        background: #fff5f5;
        border-color: #fadbd8;
    }

    .total-row.discount-row .label i {
        color: #e74c3c;
    }

    .total-row.discount-row .value {
        color: #e74c3c;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .discount-percent-input {
        width: 55px;
        padding: 3px 6px;
        border: 1px solid #f5b7b1;
        border-radius: 5px;
        font-size: 12px;
        text-align: center;
        font-family: Tahoma;
        font-weight: bold;
        background: #ffffff;
        color: #c0392b;
        direction: ltr;
        transition: all 0.3s ease;
    }

    .discount-percent-input:hover {
        border-color: #e74c3c;
        background: #fff;
    }

    .discount-percent-input:focus {
        border-color: #c0392b;
        outline: none;
        box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.15);
        background: #ffffff;
    }

    .discount-percent-input.saving {
        background: #fef5e7 !important;
        border-color: #f39c12 !important;
        color: #d68910 !important;
    }

    .discount-percent-input.saved {
        background: #e8f5e9 !important;
        border-color: #27ae60 !important;
        color: #27ae60 !important;
    }

    .discount-percent-input.save-error {
        background: #fdecea !important;
        border-color: #c0392b !important;
        color: #c0392b !important;
    }

    .table-number-input {
        width: 60px;
        padding: 2px 6px;
        border: 1px solid #a8d5c4;
        border-radius: 8px;
        font-size: 11px;
        text-align: center;
        font-family: Tahoma;
        font-weight: bold;
        background: #e8f5e9;
        color: var(--panel-accent);
        direction: ltr;
        transition: all 0.3s ease;
    }

    .table-number-input:hover {
        border-color: var(--panel-accent);
        background: #d5f4ea;
    }

    .table-number-input:focus {
        border-color: var(--panel-accent);
        outline: none;
        box-shadow: 0 0 0 2px rgba(22, 160, 133, 0.15);
        background: #ffffff;
    }

    .table-number-input.saving {
        background: #fef5e7 !important;
        border-color: #f39c12 !important;
        color: #d68910 !important;
    }

    .table-number-input.saved {
        background: #e8f5e9 !important;
        border-color: #27ae60 !important;
        color: #27ae60 !important;
    }

    .table-number-input.save-error {
        background: #fdecea !important;
        border-color: #c0392b !important;
        color: #c0392b !important;
    }

    .total-row.final-row {
        background: linear-gradient(135deg, var(--panel-primary) 0%, #3d5468 100%);
        color: #fff;
        border: none;
        padding: 12px 18px;
        border-radius: 10px;
        box-shadow: 0 5px 14px rgba(44, 62, 80, 0.22);
        margin-top: 4px;
    }

    .total-row.final-row .label {
        color: #fff;
        opacity: 0.9;
        font-size: 13px;
    }

    .total-row.final-row .label i {
        color: #1abc9c;
        font-size: 12px;
    }

    .total-row.final-row .value {
        color: #fff;
        font-size: 19px;
        letter-spacing: 0.3px;
    }

    .total-row.final-row .value small {
        color: #bdc3c7;
    }

    .invoice-footer {
        background: #f8fafb;
        padding: 10px 20px;
        text-align: center;
        font-size: 10.5px;
        color: #7f8c9b;
        border-top: 1px solid var(--border-color);
    }

    .invoice-footer .footer-brand {
        color: var(--panel-accent);
        font-weight: bold;
    }

    .empty-state {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.07);
        padding: 50px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 50px;
        color: #d5dde3;
        display: block;
        margin-bottom: 14px;
    }

    .empty-state h3 {
        font-size: 15px;
        color: var(--panel-primary);
        margin-bottom: 6px;
    }

    .empty-state p {
        font-size: 12px;
        color: #95a5a6;
        margin-bottom: 16px;
    }

    .flash-success {
        animation: flashSuccess 0.8s ease;
    }

    @keyframes flashSuccess {
        0% {
            background-color: #d5f4ea;
        }
        100% {
            background-color: transparent;
        }
    }

    .custom-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(44, 62, 80, 0.55);
        backdrop-filter: blur(3px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 20px;
        animation: fadeIn 0.2s ease;
    }

    .custom-modal-backdrop.show {
        display: flex;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .custom-modal {
        background: #ffffff;
        border-radius: 14px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        direction: rtl;
    }

    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .custom-modal .modal-icon-area {
        padding: 24px 20px 8px;
        text-align: center;
    }

    .custom-modal .modal-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 4px;
        animation: iconPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes iconPop {
        0% {
            transform: scale(0);
        }
        70% {
            transform: scale(1.15);
        }
        100% {
            transform: scale(1);
        }
    }

    .custom-modal.modal-success .modal-icon {
        background: linear-gradient(135deg, #d5f4ea, #a8e6cf);
        color: #16a085;
        box-shadow: 0 8px 24px rgba(22, 160, 133, 0.25);
    }

    .custom-modal.modal-error .modal-icon {
        background: linear-gradient(135deg, #fdecea, #f5b7b1);
        color: #c0392b;
        box-shadow: 0 8px 24px rgba(192, 57, 43, 0.25);
    }

    .custom-modal.modal-warning .modal-icon {
        background: linear-gradient(135deg, #fef5e7, #fad7a0);
        color: #d68910;
        box-shadow: 0 8px 24px rgba(214, 137, 16, 0.25);
    }

    .custom-modal.modal-info .modal-icon {
        background: linear-gradient(135deg, #e3f2fd, #aed6f1);
        color: #2980b9;
        box-shadow: 0 8px 24px rgba(41, 128, 185, 0.25);
    }

    .custom-modal.modal-confirm .modal-icon {
        background: linear-gradient(135deg, #fef0e6, #f5cba7);
        color: #d35400;
        box-shadow: 0 8px 24px rgba(211, 84, 0, 0.25);
    }

    .custom-modal .modal-body-content {
        padding: 8px 24px 20px;
        text-align: center;
    }

    .custom-modal .modal-title-text {
        font-size: 16px;
        font-weight: bold;
        color: var(--panel-primary);
        margin-bottom: 8px;
    }

    .custom-modal .modal-message {
        font-size: 13px;
        color: #5a6b7a;
        line-height: 1.7;
        word-wrap: break-word;
    }

    .custom-modal .modal-actions {
        padding: 14px 20px 20px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .custom-modal .modal-btn {
        flex: 1;
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-family: Tahoma;
        max-width: 160px;
    }

    .custom-modal .modal-btn:hover {
        transform: translateY(-1px);
    }

    .custom-modal .modal-btn:active {
        transform: translateY(0);
    }

    .custom-modal .modal-btn-ok {
        background: var(--panel-accent);
        color: #fff;
        box-shadow: 0 4px 12px rgba(22, 160, 133, 0.3);
    }

    .custom-modal .modal-btn-ok:hover {
        background: #12876f;
    }

    .custom-modal.modal-error .modal-btn-ok {
        background: #c0392b;
        box-shadow: 0 4px 12px rgba(192, 57, 43, 0.3);
    }

    .custom-modal .modal-btn-cancel {
        background: #f4f6f9;
        color: var(--panel-primary);
        border: 1px solid var(--border-color);
    }

    .custom-modal .modal-btn-cancel:hover {
        background: #e8edf2;
    }

    @media print {
        body {
            background: #fff;
            padding: 0;
        }

        .sidebar, .top-bar, .dashboard-header, .invoice-actions,
        nav, header, footer, .custom-modal-backdrop {
            display: none !important;
        }

        .main-content, .content-area {
            margin: 0 !important;
            padding: 0 !important;
        }

        .invoice-card {
            box-shadow: none;
            border-radius: 0;
            margin: 0;
        }

        .discount-percent-input, .table-number-input, .status-select {
            border: none;
            background: transparent !important;
            padding: 0;
            box-shadow: none !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            color: inherit !important;
        }

        .invoice-header, .total-row.final-row, .items-table thead {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    @media (max-width: 768px) {
        .invoice-header {
            flex-direction: column;
            text-align: center;
            padding: 14px 16px;
        }

        .invoice-header .cafe-info {
            flex-direction: column;
            gap: 10px;
        }

        .cafe-details .contact-row {
            justify-content: center;
        }

        .invoice-meta {
            grid-template-columns: 1fr;
            padding: 12px 16px;
            gap: 12px;
        }

        .meta-inline {
            flex-direction: column;
            gap: 4px;
        }

        .items-section {
            padding: 12px 10px 4px;
        }

        .items-table thead th, .items-table tbody td {
            padding: 6px 6px;
            font-size: 10.5px;
        }

        .items-table thead th.num, .items-table tbody td.num {
            width: 60px;
        }

        .total-section {
            padding: 6px 10px 14px;
        }

        .total-panel {
            min-width: auto;
            max-width: 100%;
        }
    }
</style>
<body style="direction: rtl;">

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container-fluid px-3 py-3">

    <?php if (!$invoice) { ?>

        <div class="invoice-actions">
            <div class="page-title">
                <i class="fa fa-file-invoice"></i>
                <span>مشاهده فاکتور</span>
            </div>
            <div class="buttons">
                <a href="invoices.php?action=show" class="btn-action btn-back">
                    <i class="fa fa-arrow-right"></i>
                    بازگشت به لیست
                </a>
            </div>
        </div>

        <div class="empty-state">
            <i class="fa fa-file-circle-xmark"></i>
            <h3>فاکتور مورد نظر یافت نشد</h3>
            <p>این فاکتور یا وجود ندارد یا متعلق به کافه شما نیست.</p>
        </div>

    <?php } else { ?>

        <div class="invoice-actions">
            <div class="page-title">
                <i class="fa fa-file-invoice"></i>
                <span>فاکتور #<?php echo $invoice_id; ?></span>
            </div>
            <div class="buttons">
                <button onclick="toggleEditMode()" id="toggle-edit-btn" class="btn-action btn-edit-toggle">
                    <i class="fa fa-edit"></i>
                    ویرایش اقلام
                </button>
                <button onclick="window.print()" class="btn-action btn-print">
                    <i class="fa fa-print"></i>
                    چاپ
                </button>
                <a href="invoices.php?action=show" class="btn-action btn-back">
                    <i class="fa fa-arrow-right"></i>
                    بازگشت
                </a>
            </div>
        </div>

        <div class="invoice-card">

            <div class="invoice-header">
                <div class="cafe-info">
                    <div class="cafe-logo">
                        <?php if ($has_logo) { ?>
                            <img src="../<?php echo htmlspecialchars($logo_path); ?>"
                                 alt="<?php echo htmlspecialchars($invoice['cafe_title']); ?>">
                        <?php } else { ?>
                            <i class="fa fa-mug-hot no-logo"></i>
                        <?php } ?>
                    </div>
                    <div class="cafe-details">
                        <h1><?php echo htmlspecialchars($invoice['cafe_title'] ?: 'کافه حذف شده'); ?></h1>
                        <?php if (!empty($invoice['cafe_slogan'])) { ?>
                            <div class="slogan">« <?php echo htmlspecialchars($invoice['cafe_slogan']); ?> »</div>
                        <?php } ?>
                        <div class="contact-row">
                            <?php if (!empty($invoice['cafe_tel1'])) { ?>
                                <span><i class="fa fa-phone"></i> <?php echo htmlspecialchars($invoice['cafe_tel1']); ?></span>
                            <?php } ?>
                            <?php if (!empty($invoice['cafe_instagram'])) { ?>
                                <span><i class="fa-brands fa-instagram"></i> <?php echo htmlspecialchars($invoice['cafe_instagram']); ?></span>
                            <?php } ?>
                            <?php if (!empty($invoice['cafe_hours'])) { ?>
                                <span><i class="fa fa-clock"></i> <?php echo htmlspecialchars($invoice['cafe_hours']); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="invoice-number-box">
                    <div class="label">شماره فاکتور</div>
                    <div class="number">#<?php echo $invoice_id; ?></div>

                    <select id="status-select" class="status-select" onchange="autoSaveStatus()">
                        <option value="0" <?php echo ($invoice['status'] == 0) ? 'selected' : ''; ?>>👁 مشاهده نشده
                        </option>
                        <option value="1" <?php echo ($invoice['status'] == 1) ? 'selected' : ''; ?>>✓ مشاهده شده
                        </option>
                        <option value="2" <?php echo ($invoice['status'] == 2) ? 'selected' : ''; ?>>⏳ در حال انجام
                        </option>
                        <option value="3" <?php echo ($invoice['status'] == 3) ? 'selected' : ''; ?>>✕ غیر قابل انجام
                        </option>
                        <option value="4" <?php echo ($invoice['status'] == 4) ? 'selected' : ''; ?>>✔ انجام شده
                        </option>
                        <option value="5" <?php echo ($invoice['status'] == 5) ? 'selected' : ''; ?>>⏰ در انتظار
                            پرداخت
                        </option>
                        <option value="6" <?php echo ($invoice['status'] == 6) ? 'selected' : ''; ?>>💰 پرداخت شده
                        </option>
                    </select>
                </div>
            </div>

            <div class="invoice-meta">
                <div class="meta-block">
                    <div class="block-title">
                        <i class="fa fa-user"></i>
                        مشتری
                    </div>
                    <div class="meta-inline">
                        <div class="item">
                            <span class="k">نام:</span>
                            <span class="v"><?php echo htmlspecialchars($customer_fullname); ?></span>
                        </div>
                        <div class="item">
                            <span class="k">تلفن:</span>
                            <span class="v"><?php echo htmlspecialchars($invoice['customer_tel'] ?: '—'); ?></span>
                        </div>
                        <div class="item">
                            <span class="k">شناسه:</span>
                            <span class="v"><?php echo htmlspecialchars($invoice['customer_mili'] ?: '—'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="meta-block">
                    <div class="block-title">
                        <i class="fa fa-circle-info"></i>
                        فاکتور
                    </div>
                    <div class="meta-inline">
                        <div class="item">
                            <span class="k">تاریخ:</span>
                            <span class="v"><?php echo $jfdate; ?></span>
                        </div>
                        <div class="item">
                            <span class="k">شماره میز:</span>
                            <span class="v" style="display:inline-flex;align-items:center;">
                                <input type="number"
                                       id="table-number-input"
                                       class="table-number-input"
                                       value="<?php echo (int)$invoice['table_number']; ?>"
                                       min="0"
                                       onblur="autoSaveTableNumber()"
                                       onkeypress="if(event.key==='Enter'){this.blur();}"
                                       title="برای ویرایش کلیک کنید">
                            </span>
                        </div>
                        <div class="item">
                            <span class="k">گارسون:</span>
                            <span class="v"><?php echo htmlspecialchars($invoice['waiter_name'] ?: 'بدون گارسون'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="items-section">
                <div class="section-title">
                    <i class="fa fa-list-ul"></i>
                    اقلام سفارش
                    <span class="count" id="items-count"><?php echo count($items); ?> قلم</span>
                </div>

                <div id="view-mode">
                    <?php if (count($items) == 0) { ?>
                        <div class="empty-items">
                            <i class="fa fa-cart-shopping"></i>
                            <div>هیچ آیتمی برای این فاکتور ثبت نشده است.</div>
                        </div>
                    <?php } else { ?>
                        <table class="items-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام آیتم</th>
                                <th class="num">قیمت واحد</th>
                                <th class="num">تعداد</th>
                                <th class="num">قیمت کل</th>
                            </tr>
                            </thead>
                            <tbody id="view-tbody">
                            <?php foreach ($items as $item) { ?>
                                <tr id="view-row-<?php echo $item['id']; ?>">
                                    <td><?php echo $item['id']; ?></td>
                                    <td>
                                        <div class="item-title-cell">
                                            <span class="title"><?php echo htmlspecialchars($item['item_title'] ?: 'آیتم حذف شده'); ?></span>
                                            <?php if (!empty($item['item_recipe'])) { ?>
                                                <span class="recipe"><?php echo htmlspecialchars($item['item_recipe']); ?></span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td class="num"><?php echo number_format($item['unit_price']); ?></td>
                                    <td class="num">× <?php echo $item['quantity']; ?></td>
                                    <td class="num line-total"
                                        id="view-total-<?php echo $item['id']; ?>"><?php echo number_format($item['line_total']); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>

                <div id="edit-mode" style="display: none;">
                    <table class="items-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام آیتم</th>
                            <th class="num">قیمت واحد</th>
                            <th class="num">تعداد</th>
                            <th class="num">قیمت کل</th>
                            <th class="num">عملیات</th>
                        </tr>
                        </thead>
                        <tbody id="edit-tbody">
                        <?php foreach ($items as $item) { ?>
                            <tr id="edit-row-<?php echo $item['id']; ?>">
                                <td><?php echo $item['id']; ?></td>
                                <td>
                                    <div class="item-title-cell">
                                        <span class="title"><?php echo htmlspecialchars($item['item_title'] ?: 'آیتم حذف شده'); ?></span>
                                        <?php if (!empty($item['item_recipe'])) { ?>
                                            <span class="recipe"><?php echo htmlspecialchars($item['item_recipe']); ?></span>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td class="num">
                                    <input type="number"
                                           name="unit_price"
                                           id="edit-price-<?php echo $item['id']; ?>"
                                           value="<?php echo $item['unit_price']; ?>"
                                           data-original-price="<?php echo $item['unit_price']; ?>"
                                           data-original-qty="<?php echo $item['quantity']; ?>"
                                           class="edit-inline-input row-field-<?php echo $item['id']; ?>"
                                           oninput="recalcRow(<?php echo $item['id']; ?>)"
                                           onblur="autoSaveRow(<?php echo $item['id']; ?>)">
                                </td>
                                <td class="num">
                                    <input type="number"
                                           name="quantity"
                                           id="edit-qty-<?php echo $item['id']; ?>"
                                           value="<?php echo $item['quantity']; ?>"
                                           class="edit-inline-input row-field-<?php echo $item['id']; ?>"
                                           oninput="recalcRow(<?php echo $item['id']; ?>)"
                                           onblur="autoSaveRow(<?php echo $item['id']; ?>)">
                                </td>
                                <td class="num line-total" id="edit-total-<?php echo $item['id']; ?>">
                                    <?php echo number_format($item['line_total']); ?>
                                </td>
                                <td class="num">
                                    <button type="button" class="btn-row-action btn-del"
                                            onclick="deleteItem(<?php echo $item['id']; ?>)" title="حذف">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <input type="hidden" name="action" value="update"
                                           class="row-field-<?php echo $item['id']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>"
                                           class="row-field-<?php echo $item['id']; ?>">
                                    <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>"
                                           class="row-field-<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="delete"
                                           class="del-field-<?php echo $item['id']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>"
                                           class="del-field-<?php echo $item['id']; ?>">
                                    <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>"
                                           class="del-field-<?php echo $item['id']; ?>">
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr class="add-row">
                            <td class="num">+</td>
                            <td>
                                <select name="menu_item_id" id="new-menu-item"
                                        class="add-select add-field"
                                        onchange="fillNewPrice()">
                                    <option value="">انتخاب کنید...</option>
                                    <?php foreach ($all_menu_items as $mi) { ?>
                                        <option value="<?php echo $mi['id']; ?>"
                                                data-price="<?php echo $mi['price']; ?>">
                                            <?php echo htmlspecialchars($mi['title']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="num">
                                <input type="number" name="unit_price" id="new-price" value="0"
                                       class="edit-inline-input add-field"
                                       oninput="recalcNewRow()">
                            </td>
                            <td class="num">
                                <input type="number" name="quantity" id="new-qty" value="1"
                                       class="edit-inline-input add-field"
                                       oninput="recalcNewRow()">
                            </td>
                            <td class="num line-total" id="new-total">0</td>
                            <td class="num">
                                <button type="button" class="btn-row-action btn-add"
                                        onclick="addItem()" title="افزودن آیتم">
                                    <i class="fa fa-plus"></i> افزودن
                                </button>
                                <input type="hidden" name="action" value="add" class="add-field">
                                <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>"
                                       class="add-field">
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="total-section">
                <div class="total-wrapper">
                    <div class="total-panel">

                        <div class="total-row">
                            <div class="label">
                                <i class="fa fa-list-ul"></i>
                                جمع اقلام
                            </div>
                            <div class="value">
                                <span id="grand-total-display"><?php echo number_format($grand_total); ?></span>
                                <small>تومان</small>
                            </div>
                        </div>

                        <div class="total-row discount-row">
                            <div class="label">
                                <i class="fa fa-tag"></i>
                                تخفیف
                            </div>
                            <div class="value">
                                <input type="number"
                                       id="discount-percent-input"
                                       class="discount-percent-input"
                                       value="<?php echo (int)$discount_percent; ?>"
                                       min="0"
                                       max="100"
                                       oninput="recalcDiscount()"
                                       onblur="autoSaveDiscount()"
                                       onkeypress="if(event.key==='Enter'){this.blur();}"
                                       title="درصد تخفیف را وارد کنید">
                                <span style="color:#e74c3c;font-weight:bold;">%</span>
                                <span id="discount-amount-display" style="margin-right:8px;">
                                    <?php echo number_format($discount_amount); ?>
                                </span>
                                <small>تومان</small>
                            </div>
                        </div>

                        <div class="total-row final-row">
                            <div class="label">
                                <i class="fa fa-coins"></i>
                                مبلغ قابل پرداخت
                            </div>
                            <div class="value">
                                <span id="final-total-display"><?php echo number_format($final_total); ?></span>
                                <small>تومان</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="invoice-footer">
                با تشکر از انتخاب شما
                <span class="footer-brand"><?php echo htmlspecialchars($invoice['cafe_title']); ?></span>
                — این فاکتور در تاریخ <?php echo $jfdate; ?> صادر شده است.
            </div>

        </div>

    <?php } ?>

</div>

<div class="custom-modal-backdrop" id="customModalBackdrop">
    <div class="custom-modal" id="customModal">
        <div class="modal-icon-area">
            <div class="modal-icon" id="customModalIcon">
                <i class="fa fa-info"></i>
            </div>
        </div>
        <div class="modal-body-content">
            <div class="modal-title-text" id="customModalTitle">پیام</div>
            <div class="modal-message" id="customModalMessage"></div>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-cancel" id="customModalCancel" style="display: none;">
                <i class="fa fa-times"></i> انصراف
            </button>
            <button type="button" class="modal-btn modal-btn-ok" id="customModalOk">
                <i class="fa fa-check"></i> تأیید
            </button>
        </div>
    </div>
</div>

<?php if ($invoice) { ?>
    <script>
        var INVOICE_ID = <?php echo (int)$invoice_id; ?>;
        var AJAX_URL = "invoice_item_ajax.php";
        var DISCOUNT_PERCENT = <?php echo (float)$discount_percent; ?>;
        var SAVED_DISCOUNT_PERCENT = <?php echo (float)$discount_percent; ?>;
        var CURRENT_STATUS = <?php echo (int)$invoice['status']; ?>;

        var customModal = (function () {
            var backdrop = document.getElementById('customModalBackdrop');
            var modal = document.getElementById('customModal');
            var icon = document.getElementById('customModalIcon');
            var title = document.getElementById('customModalTitle');
            var message = document.getElementById('customModalMessage');
            var btnOk = document.getElementById('customModalOk');
            var btnCancel = document.getElementById('customModalCancel');
            var onOk = null, onCancel = null;

            function open(options) {
                var type = options.type || 'info';
                onOk = options.onOk || null;
                onCancel = options.onCancel || null;
                modal.className = 'custom-modal modal-' + type;
                var icons = {
                    'success': 'fa-circle-check', 'error': 'fa-circle-xmark',
                    'warning': 'fa-triangle-exclamation', 'info': 'fa-circle-info',
                    'confirm': 'fa-circle-question'
                };
                icon.innerHTML = '<i class="fa ' + (icons[type] || 'fa-info') + '"></i>';
                title.innerText = options.title || 'پیام';
                message.innerHTML = options.message || '';
                btnOk.innerHTML = '<i class="fa ' + (options.okIcon || 'fa-check') + '"></i> ' + (options.okText || 'تأیید');
                if (options.showCancel) {
                    btnCancel.style.display = 'inline-flex';
                    btnCancel.innerHTML = '<i class="fa ' + (options.cancelIcon || 'fa-times') + '"></i> ' + (options.cancelText || 'انصراف');
                } else {
                    btnCancel.style.display = 'none';
                }
                backdrop.classList.add('show');
                btnOk.focus();
            }

            function close() {
                backdrop.classList.remove('show');
                onOk = null;
                onCancel = null;
            }

            btnOk.addEventListener('click', function () {
                var cb = onOk;
                close();
                if (typeof cb === 'function') cb();
            });
            btnCancel.addEventListener('click', function () {
                var cb = onCancel;
                close();
                if (typeof cb === 'function') cb();
            });
            backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) {
                    var cb = onCancel;
                    close();
                    if (typeof cb === 'function') cb();
                }
            });
            document.addEventListener('keydown', function (e) {
                if (!backdrop.classList.contains('show')) return;
                if (e.key === 'Escape') {
                    var cb = onCancel;
                    close();
                    if (typeof cb === 'function') cb();
                }
                if (e.key === 'Enter') {
                    var cb = onOk;
                    close();
                    if (typeof cb === 'function') cb();
                }
            });

            return {
                success: function (t, m, cb) {
                    open({type: 'success', title: t, message: m, onOk: cb});
                },
                error: function (t, m, cb) {
                    open({type: 'error', title: t, message: m, onOk: cb, okText: 'باشه'});
                },
                warning: function (t, m, cb) {
                    open({type: 'warning', title: t, message: m, onOk: cb, okText: 'متوجه شدم'});
                },
                info: function (t, m, cb) {
                    open({type: 'info', title: t, message: m, onOk: cb, okText: 'باشه'});
                },
                confirm: function (t, m, onOk, onCancel) {
                    open({
                        type: 'confirm', title: t, message: m, onOk: onOk, onCancel: onCancel,
                        okText: 'بله', cancelText: 'انصراف', showCancel: true
                    });
                }
            };
        })();

        function toggleEditMode() {
            var viewMode = document.getElementById('view-mode');
            var editMode = document.getElementById('edit-mode');
            var btn = document.getElementById('toggle-edit-btn');

            if (editMode.style.display === 'none' || editMode.style.display === '') {
                viewMode.style.display = 'none';
                editMode.style.display = 'block';
                btn.innerHTML = '<i class="fa fa-eye"></i> پایان ویرایش';
                btn.classList.add('active');
            } else {
                viewMode.style.display = 'block';
                editMode.style.display = 'none';
                btn.innerHTML = '<i class="fa fa-edit"></i> ویرایش اقلام';
                btn.classList.remove('active');
            }
        }

        function unformatNumber(str) {
            if (str === undefined || str === null) return 0;
            return parseFloat(String(str).replace(/,/g, '').trim()) || 0;
        }

        function formatNumber(n) {
            return separate(n);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function flashRow(rowId) {
            var row = document.getElementById(rowId);
            if (row) {
                row.classList.remove('flash-success');
                void row.offsetWidth;
                row.classList.add('flash-success');
            }
        }

        function recalcRow(id) {
            var price = parseFloat(document.getElementById('edit-price-' + id).value) || 0;
            var qty = parseInt(document.getElementById('edit-qty-' + id).value) || 0;
            document.getElementById('edit-total-' + id).innerText = formatNumber(price * qty);
            recalcGrand();
        }

        function recalcNewRow() {
            var price = parseFloat(document.getElementById('new-price').value) || 0;
            var qty = parseInt(document.getElementById('new-qty').value) || 0;
            document.getElementById('new-total').innerText = formatNumber(price * qty);
        }

        function recalcDiscount() {
            var input = document.getElementById('discount-percent-input');
            var percent = parseFloat(input.value) || 0;
            if (percent < 0) percent = 0;
            if (percent > 100) percent = 100;

            var grandTotal = unformatNumber(document.getElementById('grand-total-display').innerText);
            var discountAmount = (grandTotal * percent) / 100;
            var finalTotal = grandTotal - discountAmount;

            document.getElementById('discount-amount-display').innerText = formatNumber(discountAmount);
            document.getElementById('final-total-display').innerText = formatNumber(finalTotal);
            DISCOUNT_PERCENT = percent;
        }

        function recalcGrand() {
            var grand = 0;
            var totals = document.querySelectorAll('[id^="edit-total-"]');
            totals.forEach(function (el) {
                grand += unformatNumber(el.innerText);
            });
            document.getElementById('grand-total-display').innerText = formatNumber(grand);
            recalcDiscount();
            document.getElementById('items-count').innerText = totals.length + ' قلم';

            var viewMode = document.getElementById('view-mode');
            if (totals.length === 0) {
                viewMode.innerHTML = '<div class="empty-items">' +
                    '<i class="fa fa-cart-shopping"></i>' +
                    '<div>هیچ آیتمی برای این فاکتور ثبت نشده است.</div></div>';
            }
        }

        function fillNewPrice() {
            var sel = document.getElementById('new-menu-item');
            var opt = sel.options[sel.selectedIndex];
            document.getElementById('new-price').value = opt.getAttribute('data-price') || 0;
            recalcNewRow();
        }

        function autoSaveDiscount() {
            var input = document.getElementById('discount-percent-input');
            var percent = parseInt(input.value) || 0;
            if (percent < 0) percent = 0;
            if (percent > 100) percent = 100;
            input.value = percent;

            if (percent === SAVED_DISCOUNT_PERCENT) return;

            var hidden = document.getElementById('discount-percent-hidden');
            if (hidden) hidden.value = percent;

            input.classList.remove('saved', 'save-error');
            input.classList.add('saving');

            postobj.post_url = AJAX_URL;
            postobj.send_type = "post";
            postobj.after_success = function (data) {
                var res;
                try {
                    res = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    input.classList.remove('saving');
                    input.classList.add('save-error');
                    customModal.error('خطای سرور', 'پاسخ سرور نامعتبر است.');
                    return;
                }
                input.classList.remove('saving');

                if (res.success) {
                    SAVED_DISCOUNT_PERCENT = parseFloat(res.discount_percent);
                    DISCOUNT_PERCENT = parseFloat(res.discount_percent);
                    input.value = res.discount_percent;
                    document.getElementById('discount-amount-display').innerText = formatNumber(res.discount_amount);
                    document.getElementById('final-total-display').innerText = formatNumber(res.final_total);
                    document.getElementById('grand-total-display').innerText = formatNumber(res.grand_total);
                    input.classList.add('saved');
                    setTimeout(function () {
                        input.classList.remove('saved');
                    }, 1200);
                } else {
                    input.classList.add('save-error');
                    customModal.error('خطا در ذخیره', res.message || 'خطای نامشخص');
                    input.value = SAVED_DISCOUNT_PERCENT;
                    recalcDiscount();
                    setTimeout(function () {
                        input.classList.remove('save-error');
                    }, 1500);
                }
            };
            postobj.after_error = function () {
                input.classList.remove('saving');
                input.classList.add('save-error');
                input.value = SAVED_DISCOUNT_PERCENT;
                recalcDiscount();
                customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
                setTimeout(function () {
                    input.classList.remove('save-error');
                }, 1500);
            };
            res_obj_postdata('discount-field');
        }

        function autoSaveTableNumber() {
            var input = document.getElementById('table-number-input');
            var tableNumber = parseInt(input.value) || 0;
            if (tableNumber < 0) tableNumber = 0;
            input.value = tableNumber;

            var origValue = parseInt(input.getAttribute('data-original')) || 0;
            if (tableNumber === origValue) return;

            var hidden = document.getElementById('table-number-hidden');
            if (hidden) hidden.value = tableNumber;

            input.classList.remove('saved', 'save-error');
            input.classList.add('saving');

            postobj.post_url = AJAX_URL;
            postobj.send_type = "post";
            postobj.after_success = function (data) {
                var res;
                try {
                    res = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    input.classList.remove('saving');
                    input.classList.add('save-error');
                    return;
                }
                input.classList.remove('saving');
                if (res.success) {
                    input.setAttribute('data-original', res.table_number);
                    input.value = res.table_number;
                    input.classList.add('saved');
                    setTimeout(function () {
                        input.classList.remove('saved');
                    }, 1200);
                } else {
                    input.classList.add('save-error');
                    input.value = origValue;
                    customModal.error('خطا در ذخیره', res.message || 'خطای نامشخص');
                    setTimeout(function () {
                        input.classList.remove('save-error');
                    }, 1500);
                }
            };
            postobj.after_error = function () {
                input.classList.remove('saving');
                input.classList.add('save-error');
                input.value = origValue;
                customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
                setTimeout(function () {
                    input.classList.remove('save-error');
                }, 1500);
            };
            res_obj_postdata('table-field');
        }

        function autoSaveStatus() {
            var select = document.getElementById('status-select');
            var newStatus = parseInt(select.value);
            var previousStatus = CURRENT_STATUS;

            if (newStatus === previousStatus) return;

            var hidden = document.getElementById('status-hidden');
            if (hidden) hidden.value = newStatus;

            select.classList.remove('saved', 'save-error');
            select.classList.add('saving');

            postobj.post_url = AJAX_URL;
            postobj.send_type = "post";
            postobj.after_success = function (data) {
                var res;
                try {
                    res = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    select.classList.remove('saving');
                    select.classList.add('save-error');
                    select.value = previousStatus;
                    customModal.error('خطای سرور', 'پاسخ سرور نامعتبر است.');
                    return;
                }
                select.classList.remove('saving');

                if (res.success) {
                    CURRENT_STATUS = res.status;
                    select.value = res.status;
                    select.classList.add('saved');
                    setTimeout(function () {
                        select.classList.remove('saved');
                    }, 1200);
                    customModal.success('موفق', 'وضعیت فاکتور به «' + res.status_text + '» تغییر یافت.');
                } else {
                    select.classList.add('save-error');
                    select.value = previousStatus;
                    customModal.error('خطا در ذخیره', res.message || 'خطای نامشخص');
                    setTimeout(function () {
                        select.classList.remove('save-error');
                    }, 1500);
                }
            };
            postobj.after_error = function () {
                select.classList.remove('saving');
                select.classList.add('save-error');
                select.value = previousStatus;
                customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
                setTimeout(function () {
                    select.classList.remove('save-error');
                }, 1500);
            };
            res_obj_postdata('status-field');
        }

        function autoSaveRow(id) {
            var priceEl = document.getElementById('edit-price-' + id);
            var qtyEl = document.getElementById('edit-qty-' + id);
            if (!priceEl || !qtyEl) return;

            var newPrice = parseFloat(priceEl.value) || 0;
            var newQty = parseInt(qtyEl.value) || 0;
            var origPrice = parseFloat(priceEl.getAttribute('data-original-price')) || 0;
            var origQty = parseInt(priceEl.getAttribute('data-original-qty')) || 0;

            if (newPrice === origPrice && newQty === origQty) return;

            if (newQty <= 0) {
                customModal.warning('توجه', 'تعداد باید بیشتر از صفر باشد.');
                qtyEl.value = origQty;
                recalcRow(id);
                return;
            }
            if (newPrice < 0) {
                customModal.warning('توجه', 'قیمت نمی‌تواند منفی باشد.');
                priceEl.value = origPrice;
                recalcRow(id);
                return;
            }

            priceEl.classList.remove('saved', 'save-error');
            qtyEl.classList.remove('saved', 'save-error');
            priceEl.classList.add('saving');
            qtyEl.classList.add('saving');

            postobj.post_url = AJAX_URL;
            postobj.send_type = "post";
            postobj.after_success = function (data) {
                var res;
                try {
                    res = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    priceEl.classList.remove('saving');
                    qtyEl.classList.remove('saving');
                    priceEl.classList.add('save-error');
                    qtyEl.classList.add('save-error');
                    customModal.error('خطای سرور', 'پاسخ سرور نامعتبر است.');
                    return;
                }
                priceEl.classList.remove('saving');
                qtyEl.classList.remove('saving');

                if (res.success) {
                    priceEl.setAttribute('data-original-price', newPrice);
                    qtyEl.setAttribute('data-original-qty', newQty);
                    document.getElementById('edit-total-' + id).innerText = formatNumber(res.line_total);

                    var viewTotal = document.getElementById('view-total-' + id);
                    if (viewTotal) viewTotal.innerText = formatNumber(res.line_total);
                    var viewRow = document.getElementById('view-row-' + id);
                    if (viewRow) {
                        var vcells = viewRow.querySelectorAll('td');
                        vcells[2].innerText = formatNumber(newPrice);
                        vcells[3].innerText = '× ' + newQty;
                    }
                    recalcGrand();
                    priceEl.classList.add('saved');
                    qtyEl.classList.add('saved');
                    setTimeout(function () {
                        priceEl.classList.remove('saved');
                        qtyEl.classList.remove('saved');
                    }, 1200);
                } else {
                    priceEl.classList.add('save-error');
                    qtyEl.classList.add('save-error');
                    priceEl.value = origPrice;
                    qtyEl.value = origQty;
                    recalcRow(id);
                    customModal.error('خطا در ذخیره', res.message || 'خطای نامشخص');
                    setTimeout(function () {
                        priceEl.classList.remove('save-error');
                        qtyEl.classList.remove('save-error');
                    }, 1500);
                }
            };
            postobj.after_error = function () {
                priceEl.classList.remove('saving');
                qtyEl.classList.remove('saving');
                priceEl.classList.add('save-error');
                qtyEl.classList.add('save-error');
                priceEl.value = origPrice;
                qtyEl.value = origQty;
                recalcRow(id);
                customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
                setTimeout(function () {
                    priceEl.classList.remove('save-error');
                    qtyEl.classList.remove('save-error');
                }, 1500);
            };
            res_obj_postdata('row-field-' + id);
        }

        function addItem() {
            var menuItemId = document.getElementById('new-menu-item').value;
            if (!menuItemId) {
                customModal.warning('توجه', 'لطفاً یک آیتم منو انتخاب کنید.');
                return;
            }
            var qty = parseInt(document.getElementById('new-qty').value) || 0;
            if (qty <= 0) {
                customModal.warning('توجه', 'تعداد باید بیشتر از صفر باشد.');
                return;
            }

            postobj.post_url = AJAX_URL;
            postobj.send_type = "post";
            postobj.after_success = function (data) {
                var res;
                try {
                    res = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    customModal.error('خطای سرور', 'پاسخ سرور نامعتبر است.');
                    return;
                }

                if (res.success && res.item) {
                    appendItemRow(res.item);
                    document.getElementById('new-menu-item').value = '';
                    document.getElementById('new-price').value = 0;
                    document.getElementById('new-qty').value = 1;
                    document.getElementById('new-total').innerText = '0';
                    recalcGrand();
                    customModal.success('موفق', 'آیتم با موفقیت اضافه شد.');
                } else {
                    customModal.error('خطا در ثبت', res.message || 'خطای نامشخص');
                }
            };
            postobj.after_error = function () {
                customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
            };
            res_obj_postdata('add-field');
        }

        function appendItemRow(item) {
            var viewMode = document.getElementById('view-mode');
            if (!document.getElementById('view-tbody')) {
                viewMode.innerHTML =
                    '<table class="items-table"><thead><tr>' +
                    '<th>#</th><th>نام آیتم</th>' +
                    '<th class="num">قیمت واحد</th>' +
                    '<th class="num">تعداد</th>' +
                    '<th class="num">قیمت کل</th>' +
                    '</tr></thead><tbody id="view-tbody"></tbody></table>';
            }

            var recipeHtml = item.item_recipe ? '<span class="recipe">' + escapeHtml(item.item_recipe) + '</span>' : '';

            var viewHtml =
                '<tr id="view-row-' + item.id + '">' +
                '<td>' + item.id + '</td>' +
                '<td><div class="item-title-cell">' +
                '<span class="title">' + escapeHtml(item.item_title) + '</span>' + recipeHtml +
                '</div></td>' +
                '<td class="num">' + formatNumber(item.unit_price) + '</td>' +
                '<td class="num">× ' + item.quantity + '</td>' +
                '<td class="num line-total" id="view-total-' + item.id + '">' + formatNumber(item.line_total) + '</td>' +
                '</tr>';
            document.getElementById('view-tbody').insertAdjacentHTML('beforeend', viewHtml);

            var editHtml =
                '<tr id="edit-row-' + item.id + '">' +
                '<td>' + item.id + '</td>' +
                '<td><div class="item-title-cell">' +
                '<span class="title">' + escapeHtml(item.item_title) + '</span>' + recipeHtml +
                '</div></td>' +
                '<td class="num"><input type="number" name="unit_price" id="edit-price-' + item.id + '" ' +
                'value="' + item.unit_price + '" ' +
                'data-original-price="' + item.unit_price + '" ' +
                'data-original-qty="' + item.quantity + '" ' +
                'class="edit-inline-input row-field-' + item.id + '" ' +
                'oninput="recalcRow(' + item.id + ')" onblur="autoSaveRow(' + item.id + ')"></td>' +
                '<td class="num"><input type="number" name="quantity" id="edit-qty-' + item.id + '" ' +
                'value="' + item.quantity + '" ' +
                'class="edit-inline-input row-field-' + item.id + '" ' +
                'oninput="recalcRow(' + item.id + ')" onblur="autoSaveRow(' + item.id + ')"></td>' +
                '<td class="num line-total" id="edit-total-' + item.id + '">' + formatNumber(item.line_total) + '</td>' +
                '<td class="num">' +
                '<button type="button" class="btn-row-action btn-del" onclick="deleteItem(' + item.id + ')" title="حذف">' +
                '<i class="fa fa-trash"></i></button>' +
                '<input type="hidden" name="action" value="update" class="row-field-' + item.id + '">' +
                '<input type="hidden" name="id" value="' + item.id + '" class="row-field-' + item.id + '">' +
                '<input type="hidden" name="invoice_id" value="' + INVOICE_ID + '" class="row-field-' + item.id + '">' +
                '<input type="hidden" name="action" value="delete" class="del-field-' + item.id + '">' +
                '<input type="hidden" name="id" value="' + item.id + '" class="del-field-' + item.id + '">' +
                '<input type="hidden" name="invoice_id" value="' + INVOICE_ID + '" class="del-field-' + item.id + '">' +
                '</td></tr>';
            document.getElementById('edit-tbody').insertAdjacentHTML('beforeend', editHtml);

            flashRow('edit-row-' + item.id);
            flashRow('view-row-' + item.id);
        }

        function deleteItem(id) {
            customModal.confirm(
                'تأیید حذف',
                'آیا از حذف این آیتم مطمئن هستید؟<br><small style="color:#95a5a6;">این عملیات قابل بازگشت نیست.</small>',
                function () {
                    postobj.post_url = AJAX_URL;
                    postobj.send_type = "post";
                    postobj.after_success = function (data) {
                        var res;
                        try {
                            res = typeof data === 'string' ? JSON.parse(data) : data;
                        } catch (e) {
                            customModal.error('خطای سرور', 'پاسخ سرور نامعتبر است.');
                            return;
                        }

                        if (res.success) {
                            var viewRow = document.getElementById('view-row-' + id);
                            if (viewRow) viewRow.remove();
                            var editRow = document.getElementById('edit-row-' + id);
                            if (editRow) editRow.remove();
                            recalcGrand();
                            customModal.success('موفق', 'آیتم با موفقیت حذف شد.');
                        } else {
                            customModal.error('خطا در حذف', res.message || 'خطای نامشخص');
                        }
                    };
                    postobj.after_error = function () {
                        customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
                    };
                    res_obj_postdata('del-field-' + id);
                }
            );
        }

        document.addEventListener('DOMContentLoaded', function () {
            var discountInput = document.getElementById('discount-percent-input');
            if (discountInput) discountInput.setAttribute('data-original', discountInput.value);

            var tableInput = document.getElementById('table-number-input');
            if (tableInput) tableInput.setAttribute('data-original', tableInput.value);

            var discountField = document.createElement('input');
            discountField.type = 'hidden';
            discountField.name = 'discount_percent';
            discountField.id = 'discount-percent-hidden';
            discountField.className = 'discount-field';
            discountField.value = DISCOUNT_PERCENT;
            document.body.appendChild(discountField);

            var discountActionField = document.createElement('input');
            discountActionField.type = 'hidden';
            discountActionField.name = 'action';
            discountActionField.className = 'discount-field';
            discountActionField.value = 'update_discount';
            document.body.appendChild(discountActionField);

            var discountInvoiceField = document.createElement('input');
            discountInvoiceField.type = 'hidden';
            discountInvoiceField.name = 'invoice_id';
            discountInvoiceField.className = 'discount-field';
            discountInvoiceField.value = INVOICE_ID;
            document.body.appendChild(discountInvoiceField);

            var tableField = document.createElement('input');
            tableField.type = 'hidden';
            tableField.name = 'table_number';
            tableField.id = 'table-number-hidden';
            tableField.className = 'table-field';
            tableField.value = tableInput ? tableInput.value : 0;
            document.body.appendChild(tableField);

            var tableActionField = document.createElement('input');
            tableActionField.type = 'hidden';
            tableActionField.name = 'action';
            tableActionField.className = 'table-field';
            tableActionField.value = 'update_table';
            document.body.appendChild(tableActionField);

            var tableInvoiceField = document.createElement('input');
            tableInvoiceField.type = 'hidden';
            tableInvoiceField.name = 'invoice_id';
            tableInvoiceField.className = 'table-field';
            tableInvoiceField.value = INVOICE_ID;
            document.body.appendChild(tableInvoiceField);

            var statusField = document.createElement('input');
            statusField.type = 'hidden';
            statusField.name = 'status';
            statusField.id = 'status-hidden';
            statusField.className = 'status-field';
            statusField.value = CURRENT_STATUS;
            document.body.appendChild(statusField);

            var statusActionField = document.createElement('input');
            statusActionField.type = 'hidden';
            statusActionField.name = 'action';
            statusActionField.className = 'status-field';
            statusActionField.value = 'update_status';
            document.body.appendChild(statusActionField);

            var statusInvoiceField = document.createElement('input');
            statusInvoiceField.type = 'hidden';
            statusInvoiceField.name = 'invoice_id';
            statusInvoiceField.className = 'status-field';
            statusInvoiceField.value = INVOICE_ID;
            document.body.appendChild(statusInvoiceField);

            if (discountInput) {
                discountInput.addEventListener('input', function () {
                    document.getElementById('discount-percent-hidden').value = this.value;
                });
            }
            if (tableInput) {
                tableInput.addEventListener('input', function () {
                    document.getElementById('table-number-hidden').value = this.value;
                });
            }

            var statusSelect = document.getElementById('status-select');
            if (statusSelect) {
                statusSelect.addEventListener('change', function () {
                    document.getElementById('status-hidden').value = this.value;
                });
            }
        });
    </script>
<?php } ?>

<?php include("footer.php"); ?>
</body>
</html>