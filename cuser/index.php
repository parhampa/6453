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
<script src="../lib/js/palib.js"></script>
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
        --card-radius: 12px;
        --card-shadow: 0 2px 10px rgba(44, 62, 80, 0.05);
        --card-shadow-hover: 0 8px 24px rgba(44, 62, 80, 0.10);
    }

    body {
        background-color: var(--panel-bg);
        font-family: Tahoma, "Segoe UI", sans-serif;
        color: var(--panel-text);
    }

    .section-title {
        font-size: 13px;
        font-weight: bold;
        color: var(--panel-primary);
        margin: 0 0 10px;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.2px;
    }

    .section-title .title-icon {
        width: 26px;
        height: 26px;
        background: linear-gradient(135deg, var(--panel-primary), #3d5468);
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        box-shadow: 0 2px 6px rgba(44, 62, 80, 0.20);
    }

    .section-title .title-text {
        flex-grow: 1;
    }

    .section-title .title-hint {
        font-size: 10px;
        color: #95a5a6;
        font-weight: normal;
        background: #f0f3f6;
        padding: 3px 10px;
        border-radius: 10px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #f0f3f6;
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
        min-height: 72px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: var(--accent-line, transparent);
        border-radius: 0 4px 4px 0;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-shadow-hover);
        border-color: var(--accent-line, #e8ecef);
    }

    .stat-card .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #fff;
        flex-shrink: 0;
        order: -1;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.10);
    }

    .stat-card .stat-info {
        text-align: right;
        flex-grow: 1;
        min-width: 0;
    }

    .stat-card .stat-info .stat-label {
        font-size: 11px;
        color: #7f8c9b;
        margin-bottom: 3px;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-card .stat-info .stat-value {
        font-size: 18px;
        font-weight: bold;
        color: var(--panel-text);
        line-height: 1.1;
        letter-spacing: -0.3px;
    }

    .stat-card .stat-info .stat-value small {
        font-size: 10px;
        font-weight: normal;
        color: #95a5a6;
        margin-right: 4px;
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

    .stat-card[data-accent="invoice"]::before {
        background: linear-gradient(180deg, #2c3e50, #34495e);
    }

    .fast-invoice-btn {
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: #fff;
        text-decoration: none;
        border-radius: var(--card-radius);
        padding: 14px 20px;
        box-shadow: 0 6px 20px rgba(243, 156, 18, 0.30);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .fast-invoice-btn::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -30%;
        width: 60%;
        height: 200%;
        background: rgba(255, 255, 255, 0.15);
        transform: rotate(25deg);
        transition: all 0.6s ease;
        pointer-events: none;
    }

    .fast-invoice-btn:hover::before {
        left: 130%;
    }

    .fast-invoice-btn:hover {
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(243, 156, 18, 0.45);
    }

    .fast-invoice-btn .fi-icon {
        width: 46px;
        height: 46px;
        background: rgba(255, 255, 255, 0.22);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .fast-invoice-btn .fi-icon i {
        animation: boltPulse 2.5s ease-in-out infinite;
    }

    @keyframes boltPulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.15);
            opacity: 0.9;
        }
    }

    .fast-invoice-btn .fi-text {
        flex-grow: 1;
        position: relative;
        z-index: 1;
    }

    .fast-invoice-btn .fi-text .fi-title {
        font-size: 15.5px;
        font-weight: bold;
        margin-bottom: 3px;
    }

    .fast-invoice-btn .fi-text .fi-sub {
        font-size: 11.5px;
        opacity: 0.9;
    }

    .fast-invoice-btn .fi-arrow {
        font-size: 18px;
        opacity: 0.85;
        transition: transform 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .fast-invoice-btn:hover .fi-arrow {
        transform: translateX(-6px);
    }

    .search-invoice-card {
        background: #ffffff;
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f3f6;
        overflow: hidden;
    }

    .search-invoice-body {
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .search-icon-lead {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, var(--panel-primary), #3d5468);
        color: #fff;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(44, 62, 80, 0.20);
    }

    .search-field {
        position: relative;
        flex: 1;
        min-width: 140px;
    }

    .search-field input {
        width: 100%;
        height: 38px;
        padding: 0 34px 0 12px;
        border: 1.5px solid #e8ecef;
        border-radius: 9px;
        font-size: 13px;
        font-family: Tahoma;
        background: #fbfcfd;
        color: var(--panel-text);
        transition: all 0.2s ease;
    }

    .search-field input:focus {
        border-color: var(--panel-accent);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.10);
        outline: none;
    }

    .search-field input::placeholder {
        color: #b8c3cd;
        font-size: 12px;
    }

    .search-field i {
        position: absolute;
        right: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
        font-size: 12.5px;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .search-field input:focus + i {
        color: var(--panel-accent);
    }

    .search-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 38px;
        padding: 0 18px;
        border: none;
        border-radius: 9px;
        font-size: 12.5px;
        font-weight: bold;
        font-family: Tahoma;
        background: var(--panel-accent);
        color: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 3px 8px rgba(22, 160, 133, 0.20);
        white-space: nowrap;
    }

    .search-btn-primary:hover {
        background: #12876f;
        transform: translateY(-1px);
    }

    .search-btn-primary:active {
        transform: translateY(0);
    }

    .search-btn-primary.loading {
        pointer-events: none;
        opacity: 0.9;
        position: relative;
        padding-right: 18px;
    }

    .search-btn-primary.loading::after {
        content: '';
        position: absolute;
        width: 13px;
        height: 13px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spinSearch 0.8s linear infinite;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    @keyframes spinSearch {
        to {
            transform: translateY(-50%) rotate(360deg);
        }
    }

    .search-btn-clear {
        width: 38px;
        height: 38px;
        border: 1.5px solid #e8ecef;
        background: #ffffff;
        border-radius: 9px;
        color: #95a5a6;
        cursor: pointer;
        font-size: 12.5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .search-btn-clear:hover {
        background: #e74c3c;
        border-color: #e74c3c;
        color: #fff;
        transform: rotate(90deg);
    }

    .search-results {
        display: none;
        border-top: 1px solid #f0f3f6;
        padding: 10px 12px 12px;
        animation: fadeInResults 0.3s ease;
    }

    .search-results.show {
        display: block;
    }

    @keyframes fadeInResults {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .search-results-header {
        font-size: 11.5px;
        font-weight: bold;
        color: var(--panel-primary);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    .search-results-header .count-badge {
        background: linear-gradient(135deg, #e8f5e9, #d5f4ea);
        color: #16a085;
        padding: 3px 11px;
        border-radius: 12px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .search-empty {
        text-align: center;
        padding: 22px 16px;
        color: #95a5a6;
        font-size: 12px;
    }

    .search-empty i {
        font-size: 32px;
        color: #d5dde3;
        display: block;
        margin-bottom: 8px;
    }

    .search-result-item {
        background: #ffffff;
        border: 1px solid #eef2f5;
        border-radius: 9px;
        padding: 8px 10px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
        animation: searchSlideIn 0.3s ease;
    }

    .search-result-item:last-child {
        margin-bottom: 0;
    }

    @keyframes searchSlideIn {
        from {
            opacity: 0;
            transform: translateX(-12px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .search-result-item:hover {
        border-color: var(--panel-accent);
        background: #f9fdfb;
        box-shadow: 0 3px 10px rgba(22, 160, 133, 0.08);
        transform: translateX(-3px);
    }

    .search-result-item .result-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--panel-primary), #3d5468);
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        line-height: 1;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(44, 62, 80, 0.15);
    }

    .search-result-item .result-icon i {
        font-size: 12px;
    }

    .search-result-item .result-icon .inv-id {
        font-size: 9.5px;
        font-weight: bold;
        margin-top: 2px;
        opacity: 0.9;
    }

    .search-result-item .result-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 3px 14px;
        align-items: center;
    }

    .search-result-item .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        white-space: nowrap;
    }

    .search-result-item .info-chip i {
        color: var(--panel-accent);
        font-size: 10px;
        width: 12px;
        text-align: center;
    }

    .search-result-item .info-chip .k {
        color: #95a5a6;
        font-size: 10.5px;
    }

    .search-result-item .info-chip .v {
        color: var(--panel-text);
        font-weight: 600;
    }

    .search-result-item .info-chip.amount .v {
        color: var(--panel-accent);
        font-weight: 700;
    }

    .search-result-item .result-actions {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-shrink: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 2px 9px;
        border-radius: 11px;
        font-size: 10px;
        font-weight: bold;
        white-space: nowrap;
    }

    .st-notseen {
        background: #ecf0f1;
        color: #7f8c8d;
    }

    .st-seen {
        background: #e3f2fd;
        color: #2980b9;
    }

    .st-inprogress {
        background: #fef5e7;
        color: #d68910;
    }

    .st-failed {
        background: #fdecea;
        color: #c0392b;
    }

    .st-done {
        background: #e8f5e9;
        color: #27ae60;
    }

    .st-waitpay {
        background: #fef0e6;
        color: #d35400;
    }

    .st-paid {
        background: #d5f4ea;
        color: #16a085;
    }

    .result-view-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 11px;
        background: var(--panel-accent);
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 600;
        font-family: Tahoma;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }

    .result-view-btn:hover {
        background: #12876f;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(22, 160, 133, 0.25);
    }

    @media (max-width: 640px) {
        .search-invoice-body {
            gap: 6px;
        }

        .search-icon-lead {
            display: none;
        }

        .search-field {
            width: 100%;
            flex: 1 1 100%;
        }

        .search-btn-primary {
            flex: 1;
        }

        .search-result-item {
            flex-wrap: wrap;
            padding: 10px;
        }

        .search-result-item .result-actions {
            width: 100%;
            justify-content: space-between;
            margin-top: 4px;
            padding-top: 6px;
            border-top: 1px dashed #eef2f5;
        }

        .stat-card {
            padding: 10px 12px;
            min-height: 66px;
        }

        .stat-card .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        .stat-card .stat-info .stat-value {
            font-size: 16px;
        }
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

// ---- شناسه گارسونی که لاگین کرده ----
$wid = (int)get_waiters_id();

// ---- آمار دسته‌بندی‌شده — فقط فاکتورهای خود گارسون ----
$groups = [
    'invoices' => [
        'title' => 'آمار فاکتورهای من',
        'icon' => 'fa-file-invoice-dollar',
        'stats' => [
            ['label' => 'کل',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid"),
                'icon' => 'fa-file-invoice-dollar', 'class' => 'ic-invoice-total',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'مشاهده نشده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid AND status = 0"),
                'icon' => 'fa-eye-slash', 'class' => 'ic-invoice-notseen',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'مشاهده شده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid AND status = 1"),
                'icon' => 'fa-eye', 'class' => 'ic-invoice-seen',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'در حال انجام',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid AND status = 2"),
                'icon' => 'fa-spinner', 'class' => 'ic-invoice-inprogress',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'غیر قابل انجام',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid AND status = 3"),
                'icon' => 'fa-circle-xmark', 'class' => 'ic-invoice-failed',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'انجام شده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid AND status = 4"),
                'icon' => 'fa-circle-check', 'class' => 'ic-invoice-done',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'در انتظار پرداخت',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid AND status = 5"),
                'icon' => 'fa-hourglass-half', 'class' => 'ic-invoice-waitpay',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'پرداخت شده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE waiter_id = $wid AND status = 6"),
                'icon' => 'fa-money-bill-wave', 'class' => 'ic-invoice-paid',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
        ]
    ],
];
?>

<div class="container-fluid px-3 py-3">

    <div class="row g-2 mb-3">
        <div class="col-lg-5">
            <a href="fast_invoice.php" class="fast-invoice-btn">
                <div class="fi-icon">
                    <i class="fa fa-bolt"></i>
                </div>
                <div class="fi-text">
                    <div class="fi-title">ثبت فاکتور سریع</div>
                    <div class="fi-sub">ایجاد فاکتور جدید در چند ثانیه</div>
                </div>
                <div class="fi-arrow">
                    <i class="fa fa-arrow-left"></i>
                </div>
            </a>
        </div>

        <div class="col-lg-7">
            <div class="search-invoice-card">
                <div class="search-invoice-body">
                    <div class="search-icon-lead">
                        <i class="fa fa-search"></i>
                    </div>

                    <div class="search-field">
                        <input type="number"
                               id="q-table"
                               placeholder="شماره میز"
                               min="1"
                               dir="ltr"
                               style="text-align: center;">
                        <i class="fa fa-table-cells-large"></i>
                    </div>

                    <div class="search-field">
                        <input type="tel"
                               id="q-tel"
                               placeholder="شماره تماس مشتری"
                               dir="ltr"
                               style="text-align: center;">
                        <i class="fa fa-phone"></i>
                    </div>

                    <button type="button"
                            class="search-btn-primary"
                            id="btn-search-invoice"
                            onclick="searchInvoices()">
                        <i class="fa fa-search"></i>
                        <span>جستجو</span>
                    </button>

                    <button type="button"
                            class="search-btn-clear"
                            id="btn-clear-search-invoice"
                            onclick="clearInvoiceSearch()"
                            title="پاک کردن">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="search-results" id="search-results"></div>
            </div>
        </div>
    </div>

    <h5 class="section-title">
        <span class="title-icon"><i class="fa fa-file-invoice-dollar"></i></span>
        <span class="title-text"><?php echo $groups['invoices']['title']; ?></span>
        <span class="title-hint">فقط فاکتورهای خود شما</span>
    </h5>

    <div class="row g-2 mb-3">
        <?php foreach ($groups['invoices']['stats'] as $s) { ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="stat-card" data-accent="<?php echo $s['accent']; ?>">
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

<input type="hidden" class="sf" name="action" id="sf-action" value="search_invoices">
<input type="hidden" class="sf" name="q_table" id="sf-table" value="">
<input type="hidden" class="sf" name="q_tel" id="sf-tel" value="">

<script>
    var SEARCH_AJAX_URL = "invoice_search_ajax.php";
    var isSearching = false;

    function searchInvoices() {
        if (isSearching) return;

        var table = document.getElementById('q-table').value.trim();
        var tel = document.getElementById('q-tel').value.trim();

        if (table === '' && tel === '') {
            showSearchResults('<div class="search-empty">' +
                '<i class="fa fa-search"></i>' +
                '<div>لطفاً شماره میز یا شماره تماس مشتری را وارد کنید</div>' +
                '</div>');
            return;
        }

        var btn = document.getElementById('btn-search-invoice');
        btn.classList.add('loading');
        isSearching = true;

        postobj.post_url = SEARCH_AJAX_URL;
        postobj.send_type = "post";
        postobj.after_success = function (data) {
            btn.classList.remove('loading');
            isSearching = false;

            var res;
            try {
                res = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                showSearchResults('<div class="search-empty">' +
                    '<i class="fa fa-circle-exclamation" style="color:#e74c3c;"></i>' +
                    '<div>خطا در پردازش پاسخ سرور</div>' +
                    '</div>');
                return;
            }

            if (!res.success) {
                showSearchResults('<div class="search-empty">' +
                    '<i class="fa fa-circle-exclamation" style="color:#e74c3c;"></i>' +
                    '<div>' + (res.message || 'خطای نامشخص') + '</div>' +
                    '</div>');
                return;
            }

            if (res.count === 0) {
                showSearchResults('<div class="search-empty">' +
                    '<i class="fa fa-folder-open"></i>' +
                    '<div>فاکتوری با این مشخصات یافت نشد</div>' +
                    '</div>');
                return;
            }

            renderSearchResults(res.results);
        };
        postobj.after_error = function () {
            btn.classList.remove('loading');
            isSearching = false;
            showSearchResults('<div class="search-empty">' +
                '<i class="fa fa-wifi" style="color:#e74c3c;"></i>' +
                '<div>ارتباط با سرور برقرار نشد</div>' +
                '</div>');
        };

        document.getElementById('sf-action').value = 'search_invoices';
        document.getElementById('sf-table').value = table;
        document.getElementById('sf-tel').value = tel;

        res_obj_postdata('sf');
    }

    function showSearchResults(html) {
        var container = document.getElementById('search-results');
        container.innerHTML = html;
        container.classList.add('show');
    }

    function renderSearchResults(results) {
        var html = '';
        html += '<div class="search-results-header">' +
            '<span><i class="fa fa-list"></i> آخرین ' + results.length + ' فاکتور یافت‌شده</span>' +
            '<span class="count-badge">' + results.length + ' مورد</span>' +
            '</div>';

        for (var i = 0; i < results.length; i++) {
            var r = results[i];
            var total = parseFloat(r.final_total) || 0;
            var totalFormatted = separate(total);

            html += '<div class="search-result-item">' +
                '<div class="result-icon">' +
                '<i class="fa fa-file-invoice"></i>' +
                '<span class="inv-id">#' + r.invoice_id + '</span>' +
                '</div>' +
                '<div class="result-info">' +
                '<span class="info-chip">' +
                '<i class="fa fa-calendar"></i>' +
                '<span class="k">تاریخ:</span>' +
                '<span class="v">' + r.invoice_date + '</span>' +
                '</span>' +
                '<span class="info-chip">' +
                '<i class="fa fa-table-cells-large"></i>' +
                '<span class="k">میز:</span>' +
                '<span class="v">' + r.table_number + '</span>' +
                '</span>' +
                '<span class="info-chip">' +
                '<i class="fa fa-user"></i>' +
                '<span class="v">' + escapeHtml(r.full_name) + '</span>' +
                '</span>' +
                '<span class="info-chip">' +
                '<i class="fa fa-phone"></i>' +
                '<span class="v" dir="ltr">' + escapeHtml(r.customer_tel) + '</span>' +
                '</span>' +
                '<span class="info-chip amount">' +
                '<i class="fa fa-coins"></i>' +
                '<span class="v">' + totalFormatted + ' تومان</span>' +
                '</span>' +
                '</div>' +
                '<div class="result-actions">' +
                '<span class="status-pill ' + r.status_class + '">' + r.status_text + '</span>' +
                '<a href="invoice_print.php?id=' + r.invoice_id + '" target="_blank" class="result-view-btn">' +
                '<i class="fa fa-eye"></i>' +
                '<span>مشاهده</span>' +
                '</a>' +
                '</div>' +
                '</div>';
        }

        showSearchResults(html);
    }

    function clearInvoiceSearch() {
        document.getElementById('q-table').value = '';
        document.getElementById('q-tel').value = '';
        var container = document.getElementById('search-results');
        container.innerHTML = '';
        container.classList.remove('show');
        document.getElementById('q-table').focus();
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var t1 = document.getElementById('q-table');
        var t2 = document.getElementById('q-tel');
        if (t1) t1.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchInvoices();
            }
        });
        if (t2) t2.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchInvoices();
            }
        });
    });
</script>

<?php
include("footer.php");
?>
</body>
</html>