<?php
/**
 * Created by PhpStorm.
 * User: ormazd
 * Date: 8/25/2020
 * Time: 4:01 PM
 */
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html>
<title>داشبورد مدیریت</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/new.css">
<?php
include("calhead.php");
?>
<style>
    :root {
        --panel-primary: #2c3e50;
        --panel-secondary: #34495e;
        --panel-accent: #16a085;
        --panel-bg: #f4f6f9;
        --panel-text: #2c3e50;
    }

    body {
        background-color: var(--panel-bg);
        font-family: Tahoma, "Segoe UI", sans-serif;
        color: var(--panel-text);
    }

    /* ---------- کارت‌های آماری (فشرده) ---------- */
    .stat-card {
        background: #fbfcfd;
        border: 1px solid #eef2f5;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
        padding: 7px 9px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0, 0, 0, 0.07);
        background: #ffffff;
    }

    .stat-card .stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #fff;
        flex-shrink: 0;
        order: -1;
        margin-left: 7px;
    }

    .stat-card .stat-info {
        text-align: right;
        flex-grow: 1;
        min-width: 0;
    }

    .stat-card .stat-info .stat-label {
        font-size: 10.5px;
        color: #7f8c9b;
        margin-bottom: 1px;
        line-height: 1.25;
    }

    .stat-card .stat-info .stat-value {
        font-size: 14px;
        font-weight: bold;
        color: var(--panel-text);
        line-height: 1.15;
    }

    .stat-card .stat-info .stat-value small {
        font-size: 9px;
        font-weight: normal;
        color: #95a5a6;
        margin-right: 2px;
    }

    /* ---------- رنگ آیکون‌ها ---------- */
    .ic-cafe-active {
        background: linear-gradient(135deg, #16a085, #1abc9c);
    }

    .ic-cafe-inactive {
        background: linear-gradient(135deg, #7f8c8d, #95a5a6);
    }

    .ic-category {
        background: linear-gradient(135deg, #8e44ad, #9b59b6);
    }

    .ic-menu-item {
        background: linear-gradient(135deg, #d35400, #e67e22);
    }

    .ic-marketer-active {
        background: linear-gradient(135deg, #2980b9, #3498db);
    }

    .ic-marketer-inactive {
        background: linear-gradient(135deg, #c0392b, #e74c3c);
    }

    .ic-waiter-total {
        background: linear-gradient(135deg, #167ac6, #4aa3df);
    }

    .ic-waiter-active {
        background: linear-gradient(135deg, #0d7a5f, #1abc9c);
    }

    .ic-waiter-inactive {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    }

    .ic-comment-total {
        background: linear-gradient(135deg, #8e44ad, #b07cc6);
    }

    .ic-comment-approved {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
    }

    .ic-comment-pending {
        background: linear-gradient(135deg, #d35400, #e67e22);
    }

    .ic-invoice-total {
        background: linear-gradient(135deg, #2c3e50, #34495e);
    }

    .ic-invoice-notseen {
        background: linear-gradient(135deg, #7f8c8d, #95a5a6);
    }

    .ic-invoice-seen {
        background: linear-gradient(135deg, #2980b9, #4aa3df);
    }

    .ic-invoice-inprogress {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
    }

    .ic-invoice-failed {
        background: linear-gradient(135deg, #c0392b, #e74c3c);
    }

    .ic-invoice-done {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
    }

    .ic-invoice-waitpay {
        background: linear-gradient(135deg, #d35400, #e67e22);
    }

    .ic-invoice-paid {
        background: linear-gradient(135deg, #16a085, #1abc9c);
    }

    /* آیکون‌های فروش */
    .ic-sales-today {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
    }

    .ic-sales-week {
        background: linear-gradient(135deg, #2980b9, #4aa3df);
    }

    .ic-sales-month {
        background: linear-gradient(135deg, #16a085, #1abc9c);
    }

    /* ---------- کارت‌های نمودار و گروه ---------- */
    .chart-card,
    .group-card {
        background: #ffffff;
        border: none;
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
        padding: 12px 14px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .chart-card .card-section-title,
    .group-card .card-section-title {
        font-size: 12.5px;
        font-weight: bold;
        color: var(--panel-primary);
        text-align: center;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 2px solid #eef2f5;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .group-card .card-section-title .group-icon {
        color: var(--panel-accent);
        font-size: 12px;
    }

    .group-card .group-body {
        flex-grow: 1;
        display: flex;
    }

    .group-card .group-body .row {
        flex-grow: 1;
        width: 100%;
        align-content: stretch;
    }

    .group-card .group-body .row > [class*="col-"] {
        display: flex;
    }

    .group-card .group-body .row > [class*="col-"] .stat-card {
        width: 100%;
    }

    /* ---------- کارت‌های کافه (۲×۲ برای پر کردن فضا) ---------- */
    .cafes-group .cafe-stat-col .stat-card {
        min-height: 68px;
        padding: 10px 12px;
    }

    .cafes-group .cafe-stat-col .stat-card .stat-icon {
        width: 36px;
        height: 36px;
        font-size: 15px;
    }

    .cafes-group .cafe-stat-col .stat-card .stat-info .stat-label {
        font-size: 11.5px;
    }

    .cafes-group .cafe-stat-col .stat-card .stat-info .stat-value {
        font-size: 16px;
    }

    /* ---------- کارت‌های فروش ---------- */
    .sales-group .sales-stat {
        min-height: 75px;
        padding: 12px 14px;
        background: #ffffff;
        border: 1px solid #eef2f5;
    }

    .sales-group .sales-stat:hover {
        background: #fbfcfd;
        border-color: var(--panel-accent);
    }

    .sales-group .sales-stat .stat-info .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #5a6b7a;
        margin-bottom: 4px;
    }

    .sales-group .sales-stat .stat-info .stat-value {
        font-size: 18px;
        color: var(--panel-accent);
        letter-spacing: 0.3px;
    }

    .sales-group .sales-stat .stat-info .stat-value small {
        font-size: 10px;
        color: #95a5a6;
        margin-right: 3px;
    }

    .sales-group .sales-stat .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    /* ---------- نمودار کیکی (فشرده) ---------- */
    .pie-chart-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2px 0;
        height: 100%;
    }

    .pie-chart-container {
        position: relative;
        width: 155px;
        height: 155px;
    }

    .pie-chart {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.10);
        transition: transform 0.3s ease;
    }

    .pie-chart:hover {
        transform: scale(1.04);
    }

    .pie-chart-container .pie-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 84px;
        height: 84px;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .pie-chart-container .pie-center .pie-center-value {
        font-size: 17px;
        font-weight: bold;
        color: var(--panel-primary);
        line-height: 1;
    }

    .pie-chart-container .pie-center .pie-center-label {
        font-size: 9px;
        color: #95a5a6;
        margin-top: 3px;
    }

    /* راهنمای نمودار */
    .pie-legend {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 0;
    }

    .pie-legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        padding: 3px 6px;
        border-radius: 4px;
        transition: background 0.15s ease;
    }

    .pie-legend-item:hover {
        background: #f4f6f9;
    }

    .pie-legend-item .legend-right {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pie-legend-item .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .pie-legend-item .legend-label {
        color: var(--panel-text);
        font-weight: 600;
        font-size: 10.5px;
    }

    .pie-legend-item .legend-value {
        color: #7f8c9b;
        font-size: 10px;
    }

    /* ---------- بخش یادداشت‌های فعال ---------- */
    .notes-wrapper {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 18px;
        box-shadow: 0 3px 16px rgba(0, 0, 0, 0.05);
        margin-top: 14px;
        border: 1px solid #f0f3f6;
    }

    .notes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        border-bottom: 2px solid #eef2f5;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .notes-header .title-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notes-header .title-group .icon-badge {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--panel-accent), #1abc9c);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        box-shadow: 0 4px 12px rgba(22, 160, 133, 0.3);
    }

    .notes-header .title-group .title-text {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .notes-header .title-group .title-text .main {
        font-size: 14px;
        font-weight: bold;
        color: var(--panel-primary);
    }

    .notes-header .title-group .title-text .sub {
        font-size: 10.5px;
        color: #95a5a6;
    }

    .notes-header .stats-mini {
        display: flex;
        gap: 6px;
    }

    .notes-header .stats-mini .mini-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: #5a6b7a;
        padding: 5px 10px;
        background: #f4f6f9;
        border-radius: 18px;
        font-weight: 600;
        transition: background 0.2s ease;
    }

    .notes-header .stats-mini .mini-badge:hover {
        background: #e8edf2;
    }

    .notes-header .stats-mini .mini-badge i {
        color: var(--panel-accent);
        font-size: 11px;
    }

    /* ---------- کارت هر روز ---------- */
    .note-card {
        background: #ffffff;
        border: 1px solid #eef2f5;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .note-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.09);
        border-color: var(--panel-accent);
    }

    .note-card .note-header {
        background: linear-gradient(135deg, var(--panel-primary) 0%, #3d5468 100%);
        color: #fff;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .note-card .note-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 100px;
        height: 200%;
        background: rgba(255, 255, 255, 0.06);
        transform: rotate(25deg);
        pointer-events: none;
    }

    .note-card .note-header .date-info {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 1;
    }

    .note-card .note-header .date-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--panel-accent), #1abc9c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(22, 160, 133, 0.4);
    }

    .note-card .note-header .date-text {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .note-card .note-header .date-text .day {
        font-weight: bold;
        font-size: 12px;
        letter-spacing: 0.3px;
    }

    .note-card .note-header .date-text .sub {
        font-size: 9.5px;
        opacity: 0.75;
    }

    .note-card .note-header .count-badge {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: bold;
        position: relative;
        z-index: 1;
        white-space: nowrap;
    }

    .note-card .note-body {
        padding: 0;
        flex-grow: 1;
        max-height: 200px;
        overflow-y: auto;
        background: #fcfdfe;
    }

    .note-card .note-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .note-card .note-list li {
        padding: 9px 12px;
        border-bottom: 1px solid #f0f3f6;
        position: relative;
        transition: background 0.2s ease;
    }

    .note-card .note-list li:last-child {
        border-bottom: none;
    }

    .note-card .note-list li::before {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: var(--panel-accent);
        transition: height 0.25s ease;
        border-radius: 0 3px 3px 0;
    }

    .note-card .note-list li:hover {
        background: #f8fafb;
    }

    .note-card .note-list li:hover::before {
        height: 60%;
    }

    .note-card .note-title {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--panel-text);
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-bottom: 5px;
        line-height: 1.5;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .note-card .note-title:hover {
        color: var(--panel-accent);
    }

    .note-card .note-title .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--panel-accent);
        flex-shrink: 0;
        margin-top: 6px;
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.15);
    }

    .note-card .note-actions {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
        opacity: 0.55;
        transition: opacity 0.25s ease;
    }

    .note-card .note-list li:hover .note-actions {
        opacity: 1;
    }

    .note-card .note-actions a {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border-radius: 6px;
        background: #f4f6f9;
        transition: all 0.2s ease;
        font-size: 10px;
    }

    .note-card .note-actions a:hover {
        transform: translateY(-2px);
    }

    .note-card .note-actions a.action-view:hover {
        background: #e3f2fd;
    }

    .note-card .note-actions a.action-done:hover {
        background: #e8f5e9;
    }

    .note-card .note-actions a.action-down:hover {
        background: #ffebee;
    }

    .note-card .note-actions a.action-up:hover {
        background: #e8f5e9;
    }

    .note-card .note-body::-webkit-scrollbar {
        width: 5px;
    }

    .note-card .note-body::-webkit-scrollbar-thumb {
        background: #c5ccd3;
        border-radius: 3px;
    }

    .note-card .note-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .notes-empty {
        text-align: center;
        padding: 30px 20px;
        color: #95a5a6;
    }

    .notes-empty i {
        font-size: 36px;
        color: #d5dde3;
        margin-bottom: 10px;
        display: block;
    }

    .notes-empty .empty-title {
        font-size: 13px;
        font-weight: bold;
        color: #7f8c9b;
        margin-bottom: 3px;
    }

    .notes-empty .empty-sub {
        font-size: 11px;
        color: #a8b3be;
    }

    /* هم‌سطح شدن ردیف‌ها */
    .row-equal-height > [class*="col-"] {
        display: flex;
    }

    .row-equal-height > [class*="col-"] > * {
        width: 100%;
    }
</style>
<body style="direction: rtl;">

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<?php
// ---- تابع کمکی برای شمارش رکوردها ----
function count_rows($sql)
{
    $db = new database();
    $db->connect()->query($sql);
    $row = mysqli_fetch_row($db->res);
    return $row ? (int)$row[0] : 0;
}

// ---- تابع کمکی برای جمع مبالغ ----
function sum_sales($sql)
{
    $db = new database();
    $db->connect()->query($sql);
    $row = mysqli_fetch_row($db->res);
    return $row ? (float)$row[0] : 0;
}

// ---- داده‌های کاربران ----
$uc = new user_control();
$user_stats = [
    ['label' => 'امروز', 'value' => $uc->get_today_ip_count(), 'color' => '#16a085'],
    ['label' => 'دیروز', 'value' => $uc->get_yesterday_ip_count(), 'color' => '#3498db'],
    ['label' => '۲ روز پیش', 'value' => $uc->get_your_date_ip_count(2), 'color' => '#9b59b6'],
    ['label' => '۳ روز پیش', 'value' => $uc->get_your_date_ip_count(3), 'color' => '#e67e22'],
    ['label' => '۴ روز پیش', 'value' => $uc->get_your_date_ip_count(4), 'color' => '#e74c3c'],
    ['label' => '۵ روز پیش', 'value' => $uc->get_your_date_ip_count(5), 'color' => '#2ecc71'],
    ['label' => '۶ روز پیش', 'value' => $uc->get_your_date_ip_count(6), 'color' => '#95a5a6'],
];

// ---- محاسبه زاویه‌ها برای conic-gradient ----
$total_users = array_sum(array_column($user_stats, 'value'));
$gradient_parts = [];
$current_angle = 0;

if ($total_users > 0) {
    foreach ($user_stats as $s) {
        $angle = ($s['value'] / $total_users) * 360;
        $end_angle = $current_angle + $angle;
        $gradient_parts[] = $s['color'] . " {$current_angle}deg {$end_angle}deg";
        $current_angle = $end_angle;
    }
} else {
    $gradient_parts[] = "#e8ecef 0deg 360deg";
}
$pie_gradient = implode(', ', $gradient_parts);

// ---- آمار فروش کافه‌ها (بر اساس قیمت لحظه فروش) ----
// فاکتورهای پرداخت شده (status = 6) به عنوان فروش در نظر گرفته می‌شوند
// از unit_price ذخیره‌شده در invoice_items استفاده می‌کنیم تا تغییرات آینده قیمت، آمار قبلی را بهم نریزد

$sales_today = sum_sales("
    SELECT COALESCE(SUM(ii.unit_price * ii.quantity), 0)
    FROM `invoice_items` ii
    JOIN `invoices` i ON i.id = ii.invoice_id
    WHERE DATE(i.invoice_date) = CURDATE() AND i.status = 6
");

$sales_week = sum_sales("
    SELECT COALESCE(SUM(ii.unit_price * ii.quantity), 0)
    FROM `invoice_items` ii
    JOIN `invoices` i ON i.id = ii.invoice_id
    WHERE i.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND i.status = 6
");

$sales_month = sum_sales("
    SELECT COALESCE(SUM(ii.unit_price * ii.quantity), 0)
    FROM `invoice_items` ii
    JOIN `invoices` i ON i.id = ii.invoice_id
    WHERE i.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND i.status = 6
");

// ---- آمار دسته‌بندی‌شده ----
$groups = [
    'cafes' => [
        'title' => 'کافه‌ها و محتوای منو',
        'icon' => 'fa-mug-hot',
        'stats' => [
            ['label' => 'کافه‌های فعال', 'value' => count_rows("SELECT COUNT(*) FROM cafes WHERE status = 1"), 'icon' => 'fa-mug-hot', 'class' => 'ic-cafe-active', 'unit' => 'کافه'],
            ['label' => 'کافه‌های غیرفعال', 'value' => count_rows("SELECT COUNT(*) FROM cafes WHERE status = 0"), 'icon' => 'fa-mug-saucer', 'class' => 'ic-cafe-inactive', 'unit' => 'کافه'],
            ['label' => 'دسته‌بندی کافه‌ها', 'value' => count_rows("SELECT COUNT(*) FROM cafe_categories"), 'icon' => 'fa-layer-group', 'class' => 'ic-category', 'unit' => 'دسته'],
            ['label' => 'آیتم‌های منو', 'value' => count_rows("SELECT COUNT(*) FROM menu_items"), 'icon' => 'fa-utensils', 'class' => 'ic-menu-item', 'unit' => 'آیتم'],
        ]
    ],
    'marketers' => [
        'title' => 'بازاریابان',
        'icon' => 'fa-user-tie',
        'stats' => [
            ['label' => 'بازاریابان فعال', 'value' => count_rows("SELECT COUNT(*) FROM marketers WHERE status = 1"), 'icon' => 'fa-user-tie', 'class' => 'ic-marketer-active', 'unit' => 'نفر'],
            ['label' => 'بازاریابان غیرفعال', 'value' => count_rows("SELECT COUNT(*) FROM marketers WHERE status = 0"), 'icon' => 'fa-user-slash', 'class' => 'ic-marketer-inactive', 'unit' => 'نفر'],
        ]
    ],
    'waiters' => [
        'title' => 'گارسون‌های کافه',
        'icon' => 'fa-bell-concierge',
        'stats' => [
            ['label' => 'کل گارسون‌ها', 'value' => count_rows("SELECT COUNT(*) FROM waiters"), 'icon' => 'fa-users', 'class' => 'ic-waiter-total', 'unit' => 'نفر'],
            ['label' => 'گارسون‌های فعال', 'value' => count_rows("SELECT COUNT(*) FROM waiters WHERE status = 1"), 'icon' => 'fa-bell-concierge', 'class' => 'ic-waiter-active', 'unit' => 'نفر'],
            ['label' => 'گارسون‌های غیرفعال', 'value' => count_rows("SELECT COUNT(*) FROM waiters WHERE status = 0"), 'icon' => 'fa-user-slash', 'class' => 'ic-waiter-inactive', 'unit' => 'نفر'],
        ]
    ],
    'comments' => [
        'title' => 'نظرات مشتریان',
        'icon' => 'fa-comments',
        'stats' => [
            ['label' => 'کل نظرات', 'value' => count_rows("SELECT COUNT(*) FROM comments"), 'icon' => 'fa-comments', 'class' => 'ic-comment-total', 'unit' => 'نظر'],
            ['label' => 'تایید شده', 'value' => count_rows("SELECT COUNT(*) FROM comments WHERE status = 1"), 'icon' => 'fa-circle-check', 'class' => 'ic-comment-approved', 'unit' => 'نظر'],
            ['label' => 'تایید نشده', 'value' => count_rows("SELECT COUNT(*) FROM comments WHERE status = 0"), 'icon' => 'fa-circle-xmark', 'class' => 'ic-comment-pending', 'unit' => 'نظر'],
        ]
    ],
    'invoices' => [
        'title' => 'فاکتورها',
        'icon' => 'fa-file-invoice-dollar',
        'stats' => [
            ['label' => 'کل', 'value' => count_rows("SELECT COUNT(*) FROM invoices"), 'icon' => 'fa-file-invoice-dollar', 'class' => 'ic-invoice-total', 'unit' => 'فاکتور'],
            ['label' => 'مشاهده نشده', 'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE status = 0"), 'icon' => 'fa-eye-slash', 'class' => 'ic-invoice-notseen', 'unit' => 'فاکتور'],
            ['label' => 'مشاهده شده', 'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE status = 1"), 'icon' => 'fa-eye', 'class' => 'ic-invoice-seen', 'unit' => 'فاکتور'],
            ['label' => 'در حال انجام', 'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE status = 2"), 'icon' => 'fa-spinner', 'class' => 'ic-invoice-inprogress', 'unit' => 'فاکتور'],
            ['label' => 'غیر قابل انجام', 'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE status = 3"), 'icon' => 'fa-circle-xmark', 'class' => 'ic-invoice-failed', 'unit' => 'فاکتور'],
            ['label' => 'انجام شده', 'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE status = 4"), 'icon' => 'fa-circle-check', 'class' => 'ic-invoice-done', 'unit' => 'فاکتور'],
            ['label' => 'در انتظار پرداخت', 'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE status = 5"), 'icon' => 'fa-hourglass-half', 'class' => 'ic-invoice-waitpay', 'unit' => 'فاکتور'],
            ['label' => 'پرداخت شده', 'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE status = 6"), 'icon' => 'fa-money-bill-wave', 'class' => 'ic-invoice-paid', 'unit' => 'فاکتور'],
        ]
    ],
];
?>

<div class="container-fluid px-3 py-3">

    <!-- ---------- ردیف اول: نمودار + کافه‌ها ---------- -->
    <div class="row g-2 mb-2 row-equal-height">
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="card-section-title">
                    <i class="fa fa-chart-pie"></i>
                    توزیع کاربران ۷ روز اخیر
                </div>
                <div class="row align-items-center g-1" style="flex-grow:1;">
                    <div class="col-5">
                        <div class="pie-chart-wrapper">
                            <div class="pie-chart-container">
                                <div class="pie-chart"
                                     style="background: conic-gradient(<?php echo $pie_gradient; ?>);">
                                </div>
                                <div class="pie-center">
                                    <div class="pie-center-value"><?php echo $total_users; ?></div>
                                    <div class="pie-center-label">بازدید</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pie-legend">
                            <?php foreach ($user_stats as $s) {
                                $percent = $total_users > 0 ? round(($s['value'] / $total_users) * 100, 1) : 0;
                                ?>
                                <div class="pie-legend-item">
                                    <div class="legend-right">
                                        <span class="legend-dot" style="background: <?php echo $s['color']; ?>;"></span>
                                        <span class="legend-label"><?php echo $s['label']; ?></span>
                                    </div>
                                    <span class="legend-value">
                                        <?php echo $s['value']; ?> (<?php echo $percent; ?>%)
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="group-card cafes-group">
                <div class="card-section-title">
                    <i class="fa <?php echo $groups['cafes']['icon']; ?> group-icon"></i>
                    <?php echo $groups['cafes']['title']; ?>
                </div>
                <div class="group-body">
                    <div class="row g-2">
                        <?php foreach ($groups['cafes']['stats'] as $s) { ?>
                            <div class="col-6 cafe-stat-col">
                                <div class="stat-card">
                                    <div class="stat-info">
                                        <div class="stat-label"><?php echo $s['label']; ?></div>
                                        <div class="stat-value">
                                            <?php echo $s['value']; ?>
                                            <small><?php echo $s['unit']; ?></small>
                                        </div>
                                    </div>
                                    <div class="stat-icon <?php echo $s['class']; ?>">
                                        <i class="fa <?php echo $s['icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------- ردیف دوم: آمار فروش کافه‌ها ---------- -->
    <div class="row g-2 mb-2">
        <div class="col-12">
            <div class="group-card sales-group">
                <div class="card-section-title">
                    <i class="fa fa-coins group-icon"></i>
                    آمار فروش کافه‌ها
                    <span style="font-size: 10px; color: #95a5a6; font-weight: normal; margin-right: 6px;">(بر اساس قیمت لحظه فروش در فاکتورهای پرداخت شده)</span>
                </div>
                <div class="group-body">
                    <div class="row g-2">
                        <div class="col-6 col-md-4">
                            <div class="stat-card sales-stat">
                                <div class="stat-info">
                                    <div class="stat-label">مجموع فروش امروز</div>
                                    <div class="stat-value">
                                        <?php echo number_format($sales_today); ?>
                                        <small>تومان</small>
                                    </div>
                                </div>
                                <div class="stat-icon ic-sales-today">
                                    <i class="fa fa-sun"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card sales-stat">
                                <div class="stat-info">
                                    <div class="stat-label">مجموع فروش این هفته</div>
                                    <div class="stat-value">
                                        <?php echo number_format($sales_week); ?>
                                        <small>تومان</small>
                                    </div>
                                </div>
                                <div class="stat-icon ic-sales-week">
                                    <i class="fa fa-calendar-week"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card sales-stat">
                                <div class="stat-info">
                                    <div class="stat-label">مجموع فروش این ماه</div>
                                    <div class="stat-value">
                                        <?php echo number_format($sales_month); ?>
                                        <small>تومان</small>
                                    </div>
                                </div>
                                <div class="stat-icon ic-sales-month">
                                    <i class="fa fa-calendar-days"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------- ردیف سوم: بازاریابان + گارسون‌ها + نظرات ---------- -->
    <div class="row g-2 mb-2 row-equal-height">
        <div class="col-lg-4">
            <div class="group-card">
                <div class="card-section-title">
                    <i class="fa <?php echo $groups['marketers']['icon']; ?> group-icon"></i>
                    <?php echo $groups['marketers']['title']; ?>
                </div>
                <div class="group-body">
                    <div class="row g-2">
                        <?php foreach ($groups['marketers']['stats'] as $s) { ?>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-info">
                                        <div class="stat-label"><?php echo $s['label']; ?></div>
                                        <div class="stat-value">
                                            <?php echo $s['value']; ?>
                                            <small><?php echo $s['unit']; ?></small>
                                        </div>
                                    </div>
                                    <div class="stat-icon <?php echo $s['class']; ?>">
                                        <i class="fa <?php echo $s['icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="group-card">
                <div class="card-section-title">
                    <i class="fa <?php echo $groups['waiters']['icon']; ?> group-icon"></i>
                    <?php echo $groups['waiters']['title']; ?>
                </div>
                <div class="group-body">
                    <div class="row g-2">
                        <?php foreach ($groups['waiters']['stats'] as $s) { ?>
                            <div class="col-6 col-md-4">
                                <div class="stat-card">
                                    <div class="stat-info">
                                        <div class="stat-label"><?php echo $s['label']; ?></div>
                                        <div class="stat-value">
                                            <?php echo $s['value']; ?>
                                            <small><?php echo $s['unit']; ?></small>
                                        </div>
                                    </div>
                                    <div class="stat-icon <?php echo $s['class']; ?>">
                                        <i class="fa <?php echo $s['icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="group-card">
                <div class="card-section-title">
                    <i class="fa <?php echo $groups['comments']['icon']; ?> group-icon"></i>
                    <?php echo $groups['comments']['title']; ?>
                </div>
                <div class="group-body">
                    <div class="row g-2">
                        <?php foreach ($groups['comments']['stats'] as $s) { ?>
                            <div class="col-6 col-md-4">
                                <div class="stat-card">
                                    <div class="stat-info">
                                        <div class="stat-label"><?php echo $s['label']; ?></div>
                                        <div class="stat-value">
                                            <?php echo $s['value']; ?>
                                            <small><?php echo $s['unit']; ?></small>
                                        </div>
                                    </div>
                                    <div class="stat-icon <?php echo $s['class']; ?>">
                                        <i class="fa <?php echo $s['icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------- ردیف چهارم: فاکتورها ---------- -->
    <div class="row g-2 mb-2">
        <div class="col-12">
            <div class="group-card">
                <div class="card-section-title">
                    <i class="fa <?php echo $groups['invoices']['icon']; ?> group-icon"></i>
                    <?php echo $groups['invoices']['title']; ?>
                </div>
                <div class="group-body">
                    <div class="row g-2">
                        <?php foreach ($groups['invoices']['stats'] as $s) { ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="stat-card">
                                    <div class="stat-info">
                                        <div class="stat-label"><?php echo $s['label']; ?></div>
                                        <div class="stat-value">
                                            <?php echo $s['value']; ?>
                                            <small><?php echo $s['unit']; ?></small>
                                        </div>
                                    </div>
                                    <div class="stat-icon <?php echo $s['class']; ?>">
                                        <i class="fa <?php echo $s['icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------- یادداشت‌های فعال ---------- -->
    <?php
    $total_notes = count_rows("SELECT COUNT(*) FROM mynote WHERE fpage=1 AND vaz=0");
    $sqlt = "SELECT DISTINCT `tarikh` FROM `mynote` WHERE `fpage`=1 AND `vaz`=0 ORDER BY tarikh";
    $dbt = new database();
    $dbt->connect()->query($sqlt);
    $total_days = mysqli_num_rows($dbt->res);
    ?>

    <div class="notes-wrapper">
        <div class="notes-header">
            <div class="title-group">
                <div class="icon-badge">
                    <i class="fa fa-clipboard-list"></i>
                </div>
                <div class="title-text">
                    <span class="main">یادداشت‌های فعال</span>
                    <span class="sub">وظایف و یادآوری‌های در جریان</span>
                </div>
            </div>
            <div class="stats-mini">
                <span class="mini-badge">
                    <i class="fa fa-file-lines"></i>
                    <?php echo $total_notes; ?> یادداشت
                </span>
                <span class="mini-badge">
                    <i class="fa fa-calendar-days"></i>
                    <?php echo $total_days; ?> روز
                </span>
            </div>
        </div>

        <?php if ($total_days == 0) { ?>
            <div class="notes-empty">
                <i class="fa fa-clipboard-check"></i>
                <div class="empty-title">یادداشت فعالی وجود ندارد</div>
                <div class="empty-sub">همه یادداشت‌ها انجام شده‌اند</div>
            </div>
        <?php } else { ?>
            <div class="row g-2">
                <?php
                $dbt2 = new database();
                $dbt2->connect()->query($sqlt);

                while ($fildt = mysqli_fetch_assoc($dbt2->res)) {
                    $year = substr($fildt['tarikh'], 0, 4);
                    $month = substr($fildt['tarikh'], 5, 2);
                    $day = substr($fildt['tarikh'], 8, 2);
                    $jdate = gregorian_to_jalali($year, $month, $day);
                    $jfdate = $jdate[0] . "-" . $jdate[1] . "-" . $jdate[2];

                    $tarikh = $fildt['tarikh'];
                    $sqln = "SELECT * FROM `mynote` WHERE `tarikh`='$tarikh' AND `fpage`=1 AND vaz=0 ORDER BY `ordnum` DESC ";
                    $dbn = new database();
                    $dbn->connect()->query($sqln);
                    $day_count = mysqli_num_rows($dbn->res);
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="note-card">
                            <div class="note-header">
                                <div class="date-info">
                                    <div class="date-icon">
                                        <i class="fa fa-calendar-day"></i>
                                    </div>
                                    <div class="date-text">
                                        <span class="day"><?php echo $jfdate; ?></span>
                                        <span class="sub">یادداشت‌های این روز</span>
                                    </div>
                                </div>
                                <span class="count-badge"><?php echo $day_count; ?> مورد</span>
                            </div>
                            <div class="note-body">
                                <ul class="note-list">
                                    <?php
                                    $dbn2 = new database();
                                    $dbn2->connect()->query($sqln);
                                    while ($fildn = mysqli_fetch_assoc($dbn2->res)) {
                                        ?>
                                        <li>
                                            <a href="#" class="note-title"
                                               title="<?php echo($fildn['txt']); ?>"
                                               alt="<?php echo($fildn['txt']); ?>">
                                                <span class="dot"></span>
                                                <span><?php echo($fildn['title']); ?></span>
                                            </a>
                                            <div class="note-actions">
                                                <a href="mynote.php?action=editform&id=<?php echo($fildn['id']); ?>"
                                                   target="_blank" title="مشاهده"
                                                   class="action-view">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a>
                                                <a href="change_note_vaz.php?id=<?php echo($fildn['id']); ?>"
                                                   title="اتمام" class="action-done">
                                                    <i class="fa fa-check text-success"></i>
                                                </a>
                                                <a href="change_note_order.php?ty=0&id=<?php echo($fildn['id']); ?>"
                                                   title="پایین" class="action-down">
                                                    <i class="fa fa-arrow-down text-danger"></i>
                                                </a>
                                                <a href="change_note_order.php?ty=1&id=<?php echo($fildn['id']); ?>"
                                                   title="بالا" class="action-up">
                                                    <i class="fa fa-arrow-up text-success"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php
include("footer.php");
?>
</body>
</html>