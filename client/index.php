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

    /* ═══════════════════════════════════════════════════
       عنوان بخش‌ها
    ═══════════════════════════════════════════════════ */
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

    /* ═══════════════════════════════════════════════════
       کارت‌های آماری
    ═══════════════════════════════════════════════════ */
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

    .ic-sales-today {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
    }

    .ic-sales-week {
        background: linear-gradient(135deg, #2980b9, #4aa3df);
    }

    .ic-sales-month {
        background: linear-gradient(135deg, #16a085, #1abc9c);
    }

    .stat-card[data-accent="cafe"]::before {
        background: linear-gradient(180deg, #16a085, #1abc9c);
    }

    .stat-card[data-accent="category"]::before {
        background: linear-gradient(180deg, #8e44ad, #9b59b6);
    }

    .stat-card[data-accent="menu"]::before {
        background: linear-gradient(180deg, #d35400, #e67e22);
    }

    .stat-card[data-accent="waiter"]::before {
        background: linear-gradient(180deg, #167ac6, #4aa3df);
    }

    .stat-card[data-accent="comment"]::before {
        background: linear-gradient(180deg, #8e44ad, #b07cc6);
    }

    .stat-card[data-accent="invoice"]::before {
        background: linear-gradient(180deg, #2c3e50, #34495e);
    }

    .stat-card[data-accent="sales"]::before {
        background: linear-gradient(180deg, #f39c12, #e67e22);
    }

    /* ═══════════════════════════════════════════════════
       دکمه فاکتور سریع
    ═══════════════════════════════════════════════════ */
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

    /* ═══════════════════════════════════════════════════
       کارت جستجوی فاکتور
    ═══════════════════════════════════════════════════ */
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

    /* ═══════════════════════════════════════════════════
       کارت‌های فروش (ویژه)
    ═══════════════════════════════════════════════════ */
    .sales-card {
        background: #ffffff;
        border: 1px solid #f0f3f6;
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.25s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .sales-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--sales-accent, #16a085);
    }

    .sales-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-shadow-hover);
    }

    .sales-card[data-accent="today"]::after {
        background: linear-gradient(90deg, #f39c12, #f1c40f);
    }

    .sales-card[data-accent="week"]::after {
        background: linear-gradient(90deg, #2980b9, #4aa3df);
    }

    .sales-card[data-accent="month"]::after {
        background: linear-gradient(90deg, #16a085, #1abc9c);
    }

    .sales-card .sales-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .sales-card .sales-info {
        flex-grow: 1;
        text-align: right;
        min-width: 0;
    }

    .sales-card .sales-info .sales-label {
        font-size: 11.5px;
        color: #7f8c9b;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .sales-card .sales-info .sales-value {
        font-size: 19px;
        font-weight: bold;
        color: var(--panel-text);
        letter-spacing: -0.3px;
        line-height: 1.1;
    }

    .sales-card .sales-info .sales-value small {
        font-size: 10.5px;
        color: #95a5a6;
        font-weight: normal;
        margin-right: 4px;
    }

    /* ═══════════════════════════════════════════════════
       لیست گارسون‌ها و فروش امروز
    ═══════════════════════════════════════════════════ */
    .waiters-sales-card {
        background: #ffffff;
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f3f6;
        overflow: hidden;
    }

    .waiters-sales-header {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f3f6;
        background: linear-gradient(135deg, #fafbfc, #ffffff);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .waiters-sales-header .ws-title {
        font-size: 12.5px;
        font-weight: bold;
        color: var(--panel-primary);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .waiters-sales-header .ws-title i {
        color: var(--panel-accent);
    }

    .waiters-sales-header .ws-count {
        background: linear-gradient(135deg, #e8f5e9, #d5f4ea);
        color: #16a085;
        padding: 3px 11px;
        border-radius: 12px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .waiters-sales-list {
        padding: 8px 12px 12px;
    }

    .waiter-sale-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 9px;
        transition: all 0.2s ease;
        border-bottom: 1px dashed #f0f3f6;
    }

    .waiter-sale-row:last-child {
        border-bottom: none;
    }

    .waiter-sale-row:hover {
        background: #f9fdfb;
        transform: translateX(-3px);
    }

    /* رتبه */
    .waiter-sale-row .rank-badge {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
        flex-shrink: 0;
        color: #fff;
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    }

    .waiter-sale-row.rank-1 .rank-badge {
        background: linear-gradient(135deg, #f1c40f, #f39c12);
        box-shadow: 0 2px 6px rgba(243, 156, 18, 0.4);
    }

    .waiter-sale-row.rank-2 .rank-badge {
        background: linear-gradient(135deg, #bdc3c7, #95a5a6);
        box-shadow: 0 2px 6px rgba(149, 165, 166, 0.4);
    }

    .waiter-sale-row.rank-3 .rank-badge {
        background: linear-gradient(135deg, #d35400, #e67e22);
        box-shadow: 0 2px 6px rgba(211, 84, 0, 0.4);
    }

    /* آواتار */
    .waiter-sale-row .waiter-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--panel-primary), #3d5468);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: bold;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(44, 62, 80, 0.18);
    }

    .waiter-sale-row .waiter-info {
        flex-grow: 1;
        min-width: 0;
        text-align: right;
    }

    .waiter-sale-row .waiter-info .waiter-name {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--panel-text);
        margin-bottom: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .waiter-sale-row .waiter-info .waiter-meta {
        font-size: 10.5px;
        color: #95a5a6;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .waiter-sale-row .waiter-info .waiter-meta span {
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .waiter-sale-row .waiter-info .waiter-meta i {
        font-size: 9.5px;
        color: var(--panel-accent);
    }

    /* مبلغ فروش */
    .waiter-sale-row .waiter-amount {
        text-align: left;
        flex-shrink: 0;
    }

    .waiter-sale-row .waiter-amount .amount-value {
        font-size: 14px;
        font-weight: bold;
        color: var(--panel-accent);
        direction: ltr;
        line-height: 1.1;
        letter-spacing: -0.3px;
    }

    .waiter-sale-row .waiter-amount .amount-label {
        font-size: 9.5px;
        color: #95a5a6;
        margin-top: 2px;
    }

    /* حالت خالی */
    .waiters-empty {
        text-align: center;
        padding: 30px 20px;
        color: #95a5a6;
        font-size: 12.5px;
    }

    .waiters-empty i {
        font-size: 36px;
        color: #d5dde3;
        display: block;
        margin-bottom: 10px;
    }

    /* ═══════════════════════════════════════════════════
       ریسپانسیو
    ═══════════════════════════════════════════════════ */
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

        .waiter-sale-row .waiter-avatar {
            width: 34px;
            height: 34px;
            font-size: 13px;
        }

        .waiter-sale-row .waiter-amount .amount-value {
            font-size: 12.5px;
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

// ---- تابع کمکی برای جمع مبالغ ----
function sum_sales($sql)
{
    $db = new database();
    $db->connect()->query($sql);
    $row = mysqli_fetch_row($db->res);
    return $row ? (float)$row[0] : 0;
}

// ---- شناسه کافه فعلی ----
$cfid = (int)get_cafe_id();

// ---- آمار فروش کافه فعلی ----
$sales_today = sum_sales("
    SELECT COALESCE(SUM(ii.unit_price * ii.quantity), 0)
    FROM `invoice_items` ii
    JOIN `invoices` i ON i.id = ii.invoice_id
    WHERE DATE(i.invoice_date) = CURDATE() 
      AND i.status = 6 
      AND i.cafe_id = $cfid
");

$sales_week = sum_sales("
    SELECT COALESCE(SUM(ii.unit_price * ii.quantity), 0)
    FROM `invoice_items` ii
    JOIN `invoices` i ON i.id = ii.invoice_id
    WHERE i.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
      AND i.status = 6 
      AND i.cafe_id = $cfid
");

$sales_month = sum_sales("
    SELECT COALESCE(SUM(ii.unit_price * ii.quantity), 0)
    FROM `invoice_items` ii
    JOIN `invoices` i ON i.id = ii.invoice_id
    WHERE i.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
      AND i.status = 6 
      AND i.cafe_id = $cfid
");

// ═══════════════════════════════════════════════════════
// لیست گارسون‌ها با فروش امروز
// ═══════════════════════════════════════════════════════
$waiters_sales = [];
$db_ws = new database();
$db_ws->connect();

$sql_ws = "SELECT 
                w.id,
                w.fullname,
                w.tel,
                w.status,
                COALESCE((
                    SELECT SUM(ii.unit_price * ii.quantity)
                    FROM `invoice_items` ii
                    JOIN `invoices` i ON i.id = ii.invoice_id
                    WHERE i.waiter_id = w.id
                      AND i.cafe_id = $cfid
                      AND DATE(i.invoice_date) = CURDATE()
                      AND i.status = 6
                ), 0) AS today_sales,
                COALESCE((
                    SELECT COUNT(DISTINCT i.id)
                    FROM `invoices` i
                    WHERE i.waiter_id = w.id
                      AND i.cafe_id = $cfid
                      AND DATE(i.invoice_date) = CURDATE()
                      AND i.status = 6
                ), 0) AS today_invoices
            FROM `waiters` w
            WHERE w.cafe_id = $cfid
            ORDER BY today_sales DESC, w.fullname ASC";

$db_ws->query($sql_ws);

while ($row = mysqli_fetch_assoc($db_ws->res)) {
    $waiters_sales[] = $row;
}

// ---- آمار دسته‌بندی‌شده ----
$groups = [
    'menu' => [
        'title' => 'محتوای منو',
        'icon' => 'fa-utensils',
        'stats' => [
            ['label' => 'دسته‌بندی کافه',
                'value' => count_rows("SELECT COUNT(*) FROM cafe_categories WHERE cafe_id = $cfid"),
                'icon' => 'fa-layer-group', 'class' => 'ic-category',
                'unit' => 'دسته', 'accent' => 'category'],
            ['label' => 'آیتم‌های منو',
                'value' => count_rows("
                SELECT COUNT(*) FROM menu_items mi 
                JOIN cafe_categories cc ON cc.id = mi.category_id 
                WHERE cc.cafe_id = $cfid
             "),
                'icon' => 'fa-utensils', 'class' => 'ic-menu-item',
                'unit' => 'آیتم', 'accent' => 'menu'],
        ]
    ],
    'comments' => [
        'title' => 'نظرات مشتریان',
        'icon' => 'fa-comments',
        'stats' => [
            ['label' => 'کل نظرات',
                'value' => count_rows("
                SELECT COUNT(*) FROM comments cm 
                JOIN menu_items mi ON mi.id = cm.menu_item_id 
                JOIN cafe_categories cc ON cc.id = mi.category_id 
                WHERE cc.cafe_id = $cfid
             "),
                'icon' => 'fa-comments', 'class' => 'ic-comment-total',
                'unit' => 'نظر', 'accent' => 'comment'],
            ['label' => 'تایید شده',
                'value' => count_rows("
                SELECT COUNT(*) FROM comments cm 
                JOIN menu_items mi ON mi.id = cm.menu_item_id 
                JOIN cafe_categories cc ON cc.id = mi.category_id 
                WHERE cc.cafe_id = $cfid AND cm.status = 1
             "),
                'icon' => 'fa-circle-check', 'class' => 'ic-comment-approved',
                'unit' => 'نظر', 'accent' => 'comment'],
            ['label' => 'تایید نشده',
                'value' => count_rows("
                SELECT COUNT(*) FROM comments cm 
                JOIN menu_items mi ON mi.id = cm.menu_item_id 
                JOIN cafe_categories cc ON cc.id = mi.category_id 
                WHERE cc.cafe_id = $cfid AND cm.status = 0
             "),
                'icon' => 'fa-circle-xmark', 'class' => 'ic-comment-pending',
                'unit' => 'نظر', 'accent' => 'comment'],
        ]
    ],
    'invoices' => [
        'title' => 'فاکتورها',
        'icon' => 'fa-file-invoice-dollar',
        'stats' => [
            ['label' => 'کل',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid"),
                'icon' => 'fa-file-invoice-dollar', 'class' => 'ic-invoice-total',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'مشاهده نشده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid AND status = 0"),
                'icon' => 'fa-eye-slash', 'class' => 'ic-invoice-notseen',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'مشاهده شده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid AND status = 1"),
                'icon' => 'fa-eye', 'class' => 'ic-invoice-seen',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'در حال انجام',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid AND status = 2"),
                'icon' => 'fa-spinner', 'class' => 'ic-invoice-inprogress',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'غیر قابل انجام',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid AND status = 3"),
                'icon' => 'fa-circle-xmark', 'class' => 'ic-invoice-failed',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'انجام شده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid AND status = 4"),
                'icon' => 'fa-circle-check', 'class' => 'ic-invoice-done',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'در انتظار پرداخت',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid AND status = 5"),
                'icon' => 'fa-hourglass-half', 'class' => 'ic-invoice-waitpay',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
            ['label' => 'پرداخت شده',
                'value' => count_rows("SELECT COUNT(*) FROM invoices WHERE cafe_id = $cfid AND status = 6"),
                'icon' => 'fa-money-bill-wave', 'class' => 'ic-invoice-paid',
                'unit' => 'فاکتور', 'accent' => 'invoice'],
        ]
    ],
];
?>

<div class="container-fluid px-3 py-3">

    <!-- ════════ ردیف بالا: دکمه فاکتور سریع + جستجو ════════ -->
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

    <!-- ════════ بخش فروش ════════ -->
    <h5 class="section-title">
        <span class="title-icon"><i class="fa fa-coins"></i></span>
        <span class="title-text">آمار فروش</span>
        <span class="title-hint">فقط فاکتورهای پرداخت‌شده</span>
    </h5>

    <div class="row g-2 mb-3">
        <div class="col-12 col-md-4">
            <div class="sales-card" data-accent="today">
                <div class="sales-icon ic-sales-today">
                    <i class="fa fa-sun"></i>
                </div>
                <div class="sales-info">
                    <div class="sales-label">فروش امروز</div>
                    <div class="sales-value">
                        <?php echo number_format($sales_today); ?>
                        <small>تومان</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="sales-card" data-accent="week">
                <div class="sales-icon ic-sales-week">
                    <i class="fa fa-calendar-week"></i>
                </div>
                <div class="sales-info">
                    <div class="sales-label">فروش این هفته</div>
                    <div class="sales-value">
                        <?php echo number_format($sales_week); ?>
                        <small>تومان</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="sales-card" data-accent="month">
                <div class="sales-icon ic-sales-month">
                    <i class="fa fa-calendar-days"></i>
                </div>
                <div class="sales-info">
                    <div class="sales-label">فروش این ماه</div>
                    <div class="sales-value">
                        <?php echo number_format($sales_month); ?>
                        <small>تومان</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ════════ بخش فروش امروز گارسون‌ها ════════ -->
    <h5 class="section-title">
        <span class="title-icon"><i class="fa fa-bell-concierge"></i></span>
        <span class="title-text">فروش امروز گارسون‌ها</span>
        <span class="title-hint">مرتب‌شده بر اساس مبلغ فروش</span>
    </h5>

    <div class="row g-2 mb-3">
        <div class="col-12">
            <div class="waiters-sales-card">
                <div class="waiters-sales-header">
                    <div class="ws-title">
                        <i class="fa fa-list-ol"></i>
                        <span>عملکرد امروز گارسون‌ها</span>
                    </div>
                    <span class="ws-count"><?php echo count($waiters_sales); ?> گارسون</span>
                </div>

                <?php if (count($waiters_sales) == 0) { ?>

                    <div class="waiters-empty">
                        <i class="fa fa-user-slash"></i>
                        <div>هیچ گارسونی برای این کافه ثبت نشده است</div>
                    </div>

                <?php } else { ?>

                    <div class="waiters-sales-list">
                        <?php
                        $rank = 1;
                        foreach ($waiters_sales as $w) {
                            $initial = mb_substr($w['fullname'], 0, 1, 'UTF-8');
                            $sales = (float)$w['today_sales'];
                            $invoices = (int)$w['today_invoices'];
                            $waiter_status = (int)$w['status'];

                            $row_class = 'waiter-sale-row';
                            if ($rank <= 3 && $sales > 0) {
                                $row_class .= ' rank-' . $rank;
                            }
                            ?>
                            <div class="<?php echo $row_class; ?>">
                                <div class="rank-badge">
                                    <?php if ($rank <= 3 && $sales > 0) { ?>
                                        <i class="fa fa-crown"></i>
                                    <?php } else { ?>
                                        <?php echo $rank; ?>
                                    <?php } ?>
                                </div>

                                <div class="waiter-avatar">
                                    <?php echo htmlspecialchars($initial); ?>
                                </div>

                                <div class="waiter-info">
                                    <div class="waiter-name">
                                        <?php echo htmlspecialchars($w['fullname']); ?>
                                        <?php if ($waiter_status == 0) { ?>
                                            <span style="font-size: 10px; color: #e74c3c; background: #fdecea; padding: 2px 7px; border-radius: 8px; margin-right: 5px; font-weight: normal;">
                                                غیرفعال
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <div class="waiter-meta">
                                        <span>
                                            <i class="fa fa-receipt"></i>
                                            <?php echo $invoices; ?> فاکتور
                                        </span>
                                        <?php if (!empty($w['tel'])) { ?>
                                            <span>
                                                <i class="fa fa-phone"></i>
                                                <span dir="ltr"><?php echo htmlspecialchars($w['tel']); ?></span>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="waiter-amount">
                                    <?php if ($sales > 0) { ?>
                                        <div class="amount-value"><?php echo number_format($sales); ?></div>
                                        <div class="amount-label">تومان</div>
                                    <?php } else { ?>
                                        <div class="amount-value" style="color: #95a5a6; font-size: 12px;">—</div>
                                        <div class="amount-label">بدون فروش</div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php
                            $rank++;
                        }
                        ?>
                    </div>

                <?php } ?>
            </div>
        </div>
    </div>

    <!-- ════════ بخش محتوای منو ════════ -->
    <h5 class="section-title">
        <span class="title-icon"><i class="fa fa-utensils"></i></span>
        <span class="title-text"><?php echo $groups['menu']['title']; ?></span>
    </h5>

    <div class="row g-2 mb-3">
        <?php foreach ($groups['menu']['stats'] as $s) { ?>
            <div class="col-6">
                <div class="stat-card" data-accent="<?php echo $s['accent']; ?>">
                    <div class="stat-info">
                        <div class="stat-label"><?php echo $s['label']; ?></div>
                        <div class="stat-value">
                            <?php echo $s['value']; ?>
                            <?php if ($s['unit'] !== '') { ?>
                                <small><?php echo $s['unit']; ?></small>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="stat-icon <?php echo $s['class']; ?>">
                        <i class="fa <?php echo $s['icon']; ?>"></i>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- ════════ بخش نظرات ════════ -->
    <h5 class="section-title">
        <span class="title-icon"><i class="fa fa-comments"></i></span>
        <span class="title-text"><?php echo $groups['comments']['title']; ?></span>
    </h5>

    <div class="row g-2 mb-3">
        <?php foreach ($groups['comments']['stats'] as $s) { ?>
            <div class="col-6 col-md-4">
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

    <!-- ════════ بخش فاکتورها ════════ -->
    <h5 class="section-title">
        <span class="title-icon"><i class="fa fa-file-invoice-dollar"></i></span>
        <span class="title-text"><?php echo $groups['invoices']['title']; ?></span>
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

<!-- فیلدهای مخفی جستجوی فاکتور -->
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