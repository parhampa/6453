<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
include("calhead.php");  // ⬅️ برای get_cafe_id() و shamsibemiladi()

$cfid = (int)get_cafe_id();

if ($cfid <= 0) {
    die('<div style="text-align:center;padding:50px;font-family:Tahoma;">کافه‌ای برای این کاربر یافت نشد.</div>');
}

$db = new database();
$db->connect();

$all_items = [];
$all_categories = [];

$db->query("SELECT cc.id AS cat_id, cc.title AS cat_title 
            FROM `cafe_categories` cc 
            WHERE cc.cafe_id = $cfid 
            ORDER BY cc.title");
while ($row = mysqli_fetch_assoc($db->res)) {
    $all_categories[] = $row;
}

$db->query("SELECT mi.id, mi.title, mi.recipe, mi.price, mi.category_id, cc.title AS cat_title 
            FROM `menu_items` mi 
            JOIN `cafe_categories` cc ON cc.id = mi.category_id 
            WHERE cc.cafe_id = $cfid 
            ORDER BY cc.title, mi.title");
while ($row = mysqli_fetch_assoc($db->res)) {
    $all_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>فاکتور سریع</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
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
        --panel-accent-dark: #12876f;
        --panel-bg: #f4f6f9;
        --panel-text: #2c3e50;
        --border-color: #e8ecef;
        --danger: #e74c3c;
        --warning: #f39c12;
        --success: #27ae60;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: var(--panel-bg);
        font-family: Tahoma, "Segoe UI", sans-serif;
        color: var(--panel-text);
        margin: 0;
        padding: 0;
        -webkit-tap-highlight-color: transparent;
        overscroll-behavior-y: contain;
    }

    .fi-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 12px;
        padding-bottom: 100px;
    }

    .fi-header {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        padding: 12px 16px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .fi-header .fi-title {
        font-size: 16px;
        font-weight: bold;
        color: var(--panel-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fi-header .fi-title i {
        color: var(--panel-accent);
    }

    .fi-header .fi-back {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 14px;
        background: #f4f6f9;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        text-decoration: none;
        color: var(--panel-primary);
        font-size: 12.5px;
        font-weight: 600;
    }

    .fi-stepper {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        padding: 14px 20px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .fi-stepper .step {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        opacity: 0.4;
        transition: all 0.3s ease;
    }

    .fi-stepper .step.active {
        opacity: 1;
    }

    .fi-stepper .step.done .step-num {
        background: var(--success);
    }

    .fi-stepper .step.active .step-num {
        background: var(--panel-accent);
        box-shadow: 0 0 0 4px rgba(22, 160, 133, 0.15);
    }

    .fi-stepper .step-num {
        width: 32px;
        height: 32px;
        background: #cdd5dc;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .fi-stepper .step-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--panel-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .fi-stepper .step-line {
        flex: 0 0 20px;
        height: 2px;
        background: #cdd5dc;
    }

    .fi-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 12px;
    }

    .fi-card .fi-card-header {
        padding: 12px 16px;
        border-bottom: 2px solid #f0f3f6;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: bold;
        color: var(--panel-primary);
    }

    .fi-card .fi-card-header i {
        color: var(--panel-accent);
    }

    .fi-card .fi-card-body {
        padding: 16px;
    }

    .fi-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
    }

    .fi-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .fi-field label {
        font-size: 11.5px;
        font-weight: 600;
        color: #5a6b7a;
    }

    .fi-field label .req {
        color: var(--danger);
        margin-right: 2px;
    }

    .fi-input-wrap {
        position: relative;
    }

    .fi-input-wrap i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
        font-size: 14px;
        pointer-events: none;
    }

    .fi-input {
        width: 100%;
        padding: 11px 38px 11px 12px;
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        font-family: Tahoma;
        background: #fbfcfd;
        transition: all 0.2s ease;
        color: var(--panel-text);
    }

    .fi-input:focus {
        border-color: var(--panel-accent);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.12);
        outline: none;
    }

    .fi-input::placeholder {
        color: #b8c3cd;
        font-size: 12.5px;
    }

    /* ⭐ فیلد توضیحات */
    .fi-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        font-size: 13.5px;
        font-family: Tahoma;
        background: #fbfcfd;
        transition: all 0.2s ease;
        color: var(--panel-text);
        min-height: 90px;
        resize: vertical;
        line-height: 1.9;
    }

    .fi-textarea:focus {
        border-color: var(--panel-accent);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.12);
        outline: none;
    }

    .fi-textarea::placeholder {
        color: #b8c3cd;
        font-size: 12.5px;
    }

    /* نمایش تاریخ شمسی */
    .fi-date-hint {
        font-size: 10.5px;
        color: #95a5a6;
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .fi-date-hint i {
        font-size: 10px;
    }

    .fi-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 11px 22px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: Tahoma;
        text-decoration: none;
    }

    .fi-btn-primary {
        background: var(--panel-accent);
        color: #fff;
        box-shadow: 0 3px 10px rgba(22, 160, 133, 0.3);
    }

    .fi-btn-primary:hover {
        background: var(--panel-accent-dark);
        transform: translateY(-1px);
    }

    .fi-btn-primary:active {
        transform: translateY(0);
    }

    .fi-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .fi-btn-full {
        width: 100%;
    }

    .fi-search {
        position: relative;
        margin-bottom: 10px;
    }

    .fi-search i {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
        font-size: 15px;
        pointer-events: none;
    }

    .fi-search input {
        width: 100%;
        padding: 12px 44px 12px 42px;
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        font-size: 14px;
        font-family: Tahoma;
        background: #fbfcfd;
        transition: all 0.2s ease;
    }

    .fi-search input:focus {
        border-color: var(--panel-accent);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.12);
        outline: none;
    }

    .fi-search .clear-btn {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #eef2f5;
        border: none;
        cursor: pointer;
        color: #7f8c9b;
        font-size: 12px;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .fi-search .clear-btn.show {
        display: flex;
    }

    .fi-cats {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 6px;
        margin-bottom: 10px;
        scrollbar-width: thin;
    }

    .fi-cats::-webkit-scrollbar {
        height: 4px;
    }

    .fi-cats::-webkit-scrollbar-thumb {
        background: #cdd5dc;
        border-radius: 2px;
    }

    .fi-cat-chip {
        padding: 6px 14px;
        background: #f4f6f9;
        border: 1.5px solid transparent;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #5a6b7a;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: Tahoma;
        flex-shrink: 0;
    }

    .fi-cat-chip:hover {
        background: #e8edf2;
    }

    .fi-cat-chip.active {
        background: var(--panel-accent);
        color: #fff;
        border-color: var(--panel-accent);
    }

    .fi-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 8px;
        max-height: 460px;
        overflow-y: auto;
        padding: 2px;
    }

    .fi-items::-webkit-scrollbar {
        width: 6px;
    }

    .fi-items::-webkit-scrollbar-thumb {
        background: #cdd5dc;
        border-radius: 3px;
    }

    .fi-item {
        background: #ffffff;
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 12px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 4px;
        user-select: none;
        position: relative;
        overflow: hidden;
    }

    .fi-item:hover {
        border-color: var(--panel-accent);
        box-shadow: 0 4px 12px rgba(22, 160, 133, 0.15);
        transform: translateY(-2px);
    }

    .fi-item:active {
        transform: scale(0.97);
    }

    .fi-item.added {
        border-color: var(--success);
        background: #f0f8f5;
    }

    .fi-item .item-name {
        font-size: 13px;
        font-weight: bold;
        color: var(--panel-text);
        line-height: 1.4;
    }

    .fi-item .item-price {
        font-size: 12px;
        color: var(--panel-accent);
        font-weight: 600;
        direction: ltr;
        text-align: right;
    }

    .fi-item .item-price small {
        font-size: 10px;
        color: #95a5a6;
        font-weight: normal;
        direction: rtl;
    }

    .fi-item .item-add-icon {
        position: absolute;
        bottom: 8px;
        left: 8px;
        width: 24px;
        height: 24px;
        background: var(--panel-accent);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        opacity: 0.85;
    }

    .fi-empty {
        text-align: center;
        padding: 40px 20px;
        color: #95a5a6;
        font-size: 13px;
        grid-column: 1 / -1;
    }

    .fi-empty i {
        font-size: 36px;
        color: #d5dde3;
        display: block;
        margin-bottom: 10px;
    }

    .fi-cart-empty {
        text-align: center;
        padding: 30px 16px;
        color: #95a5a6;
        font-size: 12.5px;
    }

    .fi-cart-empty i {
        font-size: 40px;
        color: #d5dde3;
        display: block;
        margin-bottom: 12px;
    }

    .fi-cart-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #fbfcfd;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        margin-bottom: 8px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .fi-cart-item .cart-info {
        flex: 1;
        min-width: 0;
    }

    .fi-cart-item .cart-title {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--panel-text);
        margin-bottom: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fi-cart-item .cart-price {
        font-size: 11px;
        color: #95a5a6;
        direction: ltr;
        text-align: right;
    }

    .fi-cart-item .cart-price strong {
        color: var(--panel-accent);
        font-size: 12px;
    }

    .fi-cart-item .cart-controls {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }

    .fi-qty-btn {
        width: 28px;
        height: 28px;
        border: 1.5px solid var(--border-color);
        background: #ffffff;
        border-radius: 7px;
        cursor: pointer;
        color: var(--panel-text);
        font-size: 13px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        font-family: Tahoma;
    }

    .fi-qty-btn:hover {
        background: var(--panel-accent);
        border-color: var(--panel-accent);
        color: #fff;
    }

    .fi-qty-btn.danger:hover {
        background: var(--danger);
        border-color: var(--danger);
    }

    .fi-qty-value {
        min-width: 32px;
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        color: var(--panel-text);
        direction: ltr;
    }

    .fi-total-box {
        margin-top: 12px;
        padding: 14px 16px;
        background: linear-gradient(135deg, var(--panel-primary) 0%, #3d5468 100%);
        color: #fff;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .fi-total-box .label {
        font-size: 12.5px;
        opacity: 0.9;
    }

    .fi-total-box .value {
        font-size: 18px;
        font-weight: bold;
        direction: ltr;
    }

    .fi-total-box .value small {
        font-size: 11px;
        opacity: 0.85;
        margin-right: 4px;
        direction: rtl;
    }

    .fi-status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 8px;
    }

    .fi-status-option {
        padding: 12px 14px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        cursor: pointer;
        background: #ffffff;
        transition: all 0.2s ease;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--panel-text);
    }

    .fi-status-option:hover {
        border-color: var(--panel-accent);
        background: #f0f8f5;
    }

    .fi-status-option.selected {
        border-color: var(--panel-accent);
        background: var(--panel-accent);
        color: #fff;
        box-shadow: 0 4px 12px rgba(22, 160, 133, 0.25);
    }

    .fi-status-option .st-icon {
        font-size: 20px;
    }

    .fi-summary {
        background: #fafbfc;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 14px;
        border: 1px solid var(--border-color);
    }

    .fi-summary-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        font-size: 12.5px;
        border-bottom: 1px dashed #e8ecef;
    }

    .fi-summary-row:last-child {
        border-bottom: none;
    }

    .fi-summary-row .lbl {
        color: #7f8c9b;
    }

    .fi-summary-row .val {
        color: var(--panel-text);
        font-weight: 600;
    }

    .fi-sticky-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        border-top: 1px solid var(--border-color);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
        padding: 10px 16px;
        z-index: 100;
        display: none;
    }

    .fi-sticky-bar.show {
        display: block;
    }

    .fi-sticky-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .fi-sticky-total {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .fi-sticky-total .lbl {
        font-size: 11px;
        color: #7f8c9b;
    }

    .fi-sticky-total .val {
        font-size: 17px;
        font-weight: bold;
        color: var(--panel-accent);
        direction: ltr;
    }

    .fi-sticky-total .val small {
        font-size: 10px;
        color: #95a5a6;
        direction: rtl;
        margin-right: 3px;
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
    }

    .custom-modal-backdrop.show {
        display: flex;
    }

    .custom-modal {
        background: #ffffff;
        border-radius: 14px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
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
        width: 66px;
        height: 66px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }

    .custom-modal.modal-success .modal-icon {
        background: #d5f4ea;
        color: #16a085;
    }

    .custom-modal.modal-error .modal-icon {
        background: #fdecea;
        color: #c0392b;
    }

    .custom-modal.modal-warning .modal-icon {
        background: #fef5e7;
        color: #d68910;
    }

    .custom-modal.modal-info .modal-icon {
        background: #e3f2fd;
        color: #2980b9;
    }

    .custom-modal.modal-confirm .modal-icon {
        background: #fef0e6;
        color: #d35400;
    }

    .custom-modal .modal-body-content {
        padding: 8px 22px 20px;
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
    }

    .custom-modal .modal-actions {
        padding: 14px 20px 18px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .custom-modal .modal-btn {
        flex: 1;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
        font-family: Tahoma;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        max-width: 150px;
    }

    .custom-modal .modal-btn-ok {
        background: var(--panel-accent);
        color: #fff;
    }

    .custom-modal.modal-error .modal-btn-ok {
        background: var(--danger);
    }

    .custom-modal .modal-btn-cancel {
        background: #f4f6f9;
        color: var(--panel-primary);
        border: 1px solid var(--border-color);
    }

    @media (max-width: 768px) {
        .fi-page {
            padding: 8px;
            padding-bottom: 110px;
        }

        .fi-stepper {
            padding: 10px 12px;
        }

        .fi-stepper .step-title {
            font-size: 10.5px;
        }

        .fi-stepper .step-num {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }

        .fi-items {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            max-height: 380px;
        }

        .fi-form-grid {
            grid-template-columns: 1fr;
        }

        .fi-card .fi-card-body {
            padding: 12px;
        }

        .fi-status-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .fi-sticky-inner {
            gap: 8px;
        }

        .fi-sticky-total .val {
            font-size: 15px;
        }

        .fi-btn {
            padding: 10px 16px;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .fi-items {
            grid-template-columns: 1fr 1fr;
        }
    }

    .fi-btn.loading {
        position: relative;
        pointer-events: none;
        opacity: 0.8;
    }

    .fi-btn.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
    }

    @keyframes spin {
        to {
            transform: translateY(-50%) rotate(360deg);
        }
    }
</style>
<body>

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="fi-page">

    <div class="fi-header">
        <div class="fi-title">
            <i class="fa fa-bolt"></i>
            <span>فاکتور سریع</span>
        </div>
        <a href="invoices.php?action=show" class="fi-back">
            <i class="fa fa-arrow-right"></i>
            <span>بازگشت</span>
        </a>
    </div>

    <div class="fi-stepper">
        <div class="step active" id="step-ind-1">
            <div class="step-num">1</div>
            <div class="step-title">مشتری و میز</div>
        </div>
        <div class="step-line"></div>
        <div class="step" id="step-ind-2">
            <div class="step-num">2</div>
            <div class="step-title">انتخاب اقلام</div>
        </div>
        <div class="step-line"></div>
        <div class="step" id="step-ind-3">
            <div class="step-num">3</div>
            <div class="step-title">تایید نهایی</div>
        </div>
    </div>

    <!-- STEP 1 -->
    <div id="panel-1">
        <div class="fi-card">
            <div class="fi-card-header">
                <i class="fa fa-user-plus"></i>
                <span>اطلاعات مشتری و میز</span>
            </div>
            <div class="fi-card-body">
                <div class="fi-form-grid">
                    <div class="fi-field">
                        <label>شماره میز <span class="req">*</span></label>
                        <div class="fi-input-wrap">
                            <i class="fa fa-table-cells-large"></i>
                            <input type="number" id="inp-table" class="fi-input" placeholder="مثلاً 5" min="1"
                                   autofocus>
                        </div>
                    </div>

                    <div class="fi-field">
                        <label>شماره تماس مشتری <span class="req">*</span></label>
                        <div class="fi-input-wrap">
                            <i class="fa fa-phone"></i>
                            <input type="tel" id="inp-tel" class="fi-input" placeholder="09xxxxxxxxx"
                                   inputmode="numeric" dir="ltr" style="text-align: left;">
                        </div>
                    </div>

                    <div class="fi-field">
                        <label>نام <span class="req">*</span></label>
                        <div class="fi-input-wrap">
                            <i class="fa fa-user"></i>
                            <input type="text" id="inp-name" class="fi-input" placeholder="نام مشتری">
                        </div>
                    </div>

                    <div class="fi-field">
                        <label>نام خانوادگی <span class="req">*</span></label>
                        <div class="fi-input-wrap">
                            <i class="fa fa-user-tag"></i>
                            <input type="text" id="inp-family" class="fi-input" placeholder="نام خانوادگی">
                        </div>
                    </div>

                    <!-- ═══════ تاریخ تولد (شمسی) ═══════ -->
                    <div class="fi-field" style="grid-column: 1 / -1;">
                        <label>تاریخ تولد (اختیاری - شمسی)</label>
                        <div class="fi-input-wrap">
                            <i class="fa fa-calendar"></i>
                            <input type="text"
                                   id="tacustomer_birth"
                                   class="fi-input tacustomer_birth"
                                   placeholder="1370/05/15"
                                   dir="ltr"
                                   style="text-align: right; padding-right: 38px;"
                                   autocomplete="off"
                                   onchange="shamsibemiladi('customer_birth')"
                                   onkeyup="shamsibemiladi('customer_birth')"
                                   onblur="shamsibemiladi('customer_birth')">
                        </div>
                        <div class="fi-date-hint">
                            <i class="fa fa-info-circle"></i>
                            <span>تاریخ را به صورت شمسی وارد کنید (مثال: 1370/05/15)</span>
                        </div>
                        <!-- فیلد مخفی: مقدار میلادی برای ارسال به سرور -->
                        <input type="hidden"
                               class="cf"
                               id="customer_birth"
                               name="customer_birth"
                               value="">
                    </div>

                    <!-- ⭐ توضیحات بیشتر (اختیاری) -->
                    <div class="fi-field" style="grid-column: 1 / -1;">
                        <label>توضیحات بیشتر (اختیاری)</label>
                        <textarea id="inp-description"
                                  class="fi-textarea"
                                  placeholder="مثلاً: بدون شکر، بدون یخ، سفارش فوری، ..."
                                  maxlength="1000"
                                  rows="3"></textarea>
                        <div class="fi-date-hint">
                            <i class="fa fa-info-circle"></i>
                            <span>هر توضیح یا یادداشتی که می‌خواهید برای این فاکتور ثبت شود</span>
                        </div>
                    </div>
                </div>

                <button type="button" class="fi-btn fi-btn-primary fi-btn-full"
                        id="btn-step-1" style="margin-top: 16px;" onclick="submitCustomer()">
                    <i class="fa fa-arrow-left"></i>
                    <span>ادامه و انتخاب اقلام</span>
                </button>
            </div>
        </div>
    </div>

    <!-- STEP 2 -->
    <div id="panel-2" style="display:none;">
        <div class="row g-2">
            <div class="col-lg-7">
                <div class="fi-card">
                    <div class="fi-card-header">
                        <i class="fa fa-utensils"></i>
                        <span>منوی کافه</span>
                    </div>
                    <div class="fi-card-body">
                        <div class="fi-search">
                            <i class="fa fa-search"></i>
                            <input type="text" id="inp-search" placeholder="جستجوی آیتم منو..." autocomplete="off">
                            <button type="button" class="clear-btn" id="btn-clear-search">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>

                        <div class="fi-cats" id="cats-container">
                            <button type="button" class="fi-cat-chip active" data-cat="0">
                                <i class="fa fa-th-large"></i> همه
                            </button>
                            <?php foreach ($all_categories as $cat) { ?>
                                <button type="button" class="fi-cat-chip" data-cat="<?php echo (int)$cat['cat_id']; ?>">
                                    <?php echo htmlspecialchars($cat['cat_title']); ?>
                                </button>
                            <?php } ?>
                        </div>

                        <div class="fi-items" id="items-container"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="fi-card">
                    <div class="fi-card-header">
                        <i class="fa fa-shopping-bag"></i>
                        <span>سفارش شما</span>
                        <span id="cart-count"
                              style="margin-right: auto; background: #e8f5e9; color: #16a085; padding: 2px 10px; border-radius: 12px; font-size: 11px;">
                            0 قلم
                        </span>
                    </div>
                    <div class="fi-card-body" style="padding: 12px;">
                        <div id="cart-container">
                            <div class="fi-cart-empty">
                                <i class="fa fa-basket-shopping"></i>
                                <div>هنوز آیتمی اضافه نشده</div>
                                <div style="font-size: 11px; margin-top: 4px;">روی آیتم‌ها بزن تا اضافه شوند</div>
                            </div>
                        </div>

                        <div class="fi-total-box" id="total-box" style="display: none;">
                            <span class="label">جمع کل</span>
                            <span class="value">
                                <span id="cart-total">0</span>
                                <small>تومان</small>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3 -->
    <div id="panel-3" style="display:none;">
        <div class="fi-card">
            <div class="fi-card-header">
                <i class="fa fa-circle-check"></i>
                <span>تایید نهایی فاکتور</span>
            </div>
            <div class="fi-card-body">
                <div class="fi-summary" id="final-summary"></div>

                <div style="font-size: 13px; font-weight: bold; margin-bottom: 10px; color: var(--panel-primary);">
                    <i class="fa fa-flag-checkered" style="color: var(--panel-accent);"></i>
                    وضعیت فاکتور را انتخاب کنید:
                </div>

                <div class="fi-status-grid" id="status-grid">
                    <div class="fi-status-option" data-status="0">
                        <div class="st-icon">👁</div>
                        <span>مشاهده نشده</span>
                    </div>
                    <div class="fi-status-option" data-status="1">
                        <div class="st-icon">✓</div>
                        <span>مشاهده شده</span>
                    </div>
                    <div class="fi-status-option" data-status="2">
                        <div class="st-icon">⏳</div>
                        <span>در حال انجام</span>
                    </div>
                    <div class="fi-status-option" data-status="3">
                        <div class="st-icon">✕</div>
                        <span>غیر قابل انجام</span>
                    </div>
                    <div class="fi-status-option" data-status="4">
                        <div class="st-icon">✔</div>
                        <span>انجام شده</span>
                    </div>
                    <div class="fi-status-option" data-status="5">
                        <div class="st-icon">⏰</div>
                        <span>در انتظار پرداخت</span>
                    </div>
                    <div class="fi-status-option" data-status="6">
                        <div class="st-icon">💰</div>
                        <span>پرداخت شده</span>
                    </div>
                </div>

                <button type="button" class="fi-btn fi-btn-primary fi-btn-full"
                        id="btn-finalize" style="margin-top: 16px;" onclick="finalizeInvoice()">
                    <i class="fa fa-paper-plane"></i>
                    <span>ثبت نهایی فاکتور</span>
                </button>

                <button type="button" class="fi-btn"
                        style="margin-top: 10px; width: 100%; background: #f4f6f9; color: var(--panel-primary);"
                        onclick="goToStep(2)">
                    <i class="fa fa-arrow-right"></i>
                    <span>بازگشت به انتخاب اقلام</span>
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Sticky bar -->
<div class="fi-sticky-bar" id="sticky-bar">
    <div class="fi-sticky-inner">
        <div class="fi-sticky-total">
            <span class="lbl">جمع کل (<span id="sticky-count">0</span> قلم)</span>
            <span class="val">
                <span id="sticky-total">0</span>
                <small>تومان</small>
            </span>
        </div>
        <button type="button" class="fi-btn fi-btn-primary" id="sticky-action" onclick="stickyAction()">
            <i class="fa fa-arrow-left"></i>
            <span id="sticky-action-text">ادامه</span>
        </button>
    </div>
</div>

<!-- Modal -->
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

<!-- Hidden fields -->
<input type="hidden" class="cf" name="action" value="create_invoice">
<input type="hidden" class="cf" name="table_number" id="cf-table">
<input type="hidden" class="cf" name="customer_tel" id="cf-tel">
<input type="hidden" class="cf" name="customer_name" id="cf-name">
<input type="hidden" class="cf" name="customer_family" id="cf-family">
<input type="hidden" class="cf" name="description" id="cf-description">
<!-- customer_birth اینجا نیست چون در بالای فرم تعریف شده -->

<input type="hidden" class="aif" name="action" value="add_item">
<input type="hidden" class="aif" name="invoice_id" id="aif-invoice">
<input type="hidden" class="aif" name="menu_item_id" id="aif-menu">
<input type="hidden" class="aif" name="quantity" id="aif-qty" value="1">

<input type="hidden" class="dif" name="action" value="delete_item">
<input type="hidden" class="dif" name="invoice_id" id="dif-invoice">
<input type="hidden" class="dif" name="item_id" id="dif-item">

<input type="hidden" class="uif" name="action" value="update_quantity">
<input type="hidden" class="uif" name="invoice_id" id="uif-invoice">
<input type="hidden" class="uif" name="item_id" id="uif-item">
<input type="hidden" class="uif" name="quantity" id="uif-qty">

<input type="hidden" class="fif" name="action" value="finalize">
<input type="hidden" class="fif" name="invoice_id" id="fif-invoice">
<input type="hidden" class="fif" name="status" id="fif-status">

<script>
    var AJAX_URL = "fast_invoice_ajax.php";
    var ALL_ITEMS = <?php echo json_encode($all_items, JSON_UNESCAPED_UNICODE); ?>;

    var currentStep = 1;
    var currentInvoiceId = 0;
    var currentCustomerName = "";
    var currentCustomerFamily = "";
    var currentTableNumber = 0;
    var currentBirthDate = "";
    var currentDescription = "";
    var cartItems = [];
    var currentCategoryFilter = 0;
    var isBusy = false;

    function normalizeFa(str) {
        if (!str) return '';
        return String(str)
            .replace(/ي/g, 'ی').replace(/ك/g, 'ک').replace(/ۀ/g, 'ه')
            .replace(/[أإآا]/g, 'ا').replace(/\u200c/g, ' ')
            .replace(/\s+/g, ' ').trim().toLowerCase();
    }

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

    function goToStep(step) {
        currentStep = step;
        document.getElementById('panel-1').style.display = (step === 1) ? '' : 'none';
        document.getElementById('panel-2').style.display = (step === 2) ? '' : 'none';
        document.getElementById('panel-3').style.display = (step === 3) ? '' : 'none';

        for (var i = 1; i <= 3; i++) {
            var el = document.getElementById('step-ind-' + i);
            el.classList.remove('active', 'done');
            if (i < step) el.classList.add('done');
            if (i === step) el.classList.add('active');
        }

        var sticky = document.getElementById('sticky-bar');
        var actionText = document.getElementById('sticky-action-text');
        var actionIcon = document.querySelector('#sticky-action i');

        if (step === 1) {
            sticky.classList.remove('show');
        } else if (step === 2) {
            sticky.classList.add('show');
            actionText.innerText = 'ادامه';
            if (actionIcon) actionIcon.className = 'fa fa-arrow-left';
            updateCartUI();
        } else if (step === 3) {
            sticky.classList.add('show');
            actionText.innerText = 'ثبت نهایی';
            if (actionIcon) actionIcon.className = 'fa fa-paper-plane';
            updateCartUI();
            renderFinalSummary();
        }

        window.scrollTo({top: 0, behavior: 'smooth'});

        setTimeout(function () {
            if (step === 1) {
                var inp = document.getElementById('inp-table');
                if (inp) inp.focus();
            } else if (step === 2) {
                var inp = document.getElementById('inp-search');
                if (inp) inp.focus();
            }
        }, 200);
    }

    function stickyAction() {
        if (currentStep === 2) {
            if (cartItems.length === 0) {
                customModal.warning('توجه', 'حداقل یک آیتم به سفارش اضافه کنید.');
                return;
            }
            goToStep(3);
        } else if (currentStep === 3) {
            finalizeInvoice();
        }
    }

    /* ═══════════════════════════════════════════════════════
       تبدیل تاریخ شمسی به میلادی
    ═══════════════════════════════════════════════════════ */
    function convertBirthDate() {
        var jalaliInput = document.getElementById('tacustomer_birth');
        if (jalaliInput && typeof shamsibemiladi === 'function') {
            shamsibemiladi('customer_birth');
        }
    }

    function submitCustomer() {
        if (isBusy) return;

        var table = document.getElementById('inp-table').value.trim();
        var tel = document.getElementById('inp-tel').value.trim();
        var name = document.getElementById('inp-name').value.trim();
        var family = document.getElementById('inp-family').value.trim();
        var description = document.getElementById('inp-description').value.trim();

        // تبدیل تاریخ شمسی به میلادی
        convertBirthDate();
        var birthMiladi = document.getElementById('customer_birth').value.trim();

        if (table === '' || parseInt(table) <= 0) {
            customModal.warning('توجه', 'شماره میز را وارد کنید.');
            document.getElementById('inp-table').focus();
            return;
        }
        if (tel === '') {
            customModal.warning('توجه', 'شماره تماس مشتری را وارد کنید.');
            document.getElementById('inp-tel').focus();
            return;
        }
        if (name === '') {
            customModal.warning('توجه', 'نام مشتری را وارد کنید.');
            document.getElementById('inp-name').focus();
            return;
        }
        if (family === '') {
            customModal.warning('توجه', 'نام خانوادگی مشتری را وارد کنید.');
            document.getElementById('inp-family').focus();
            return;
        }

        document.getElementById('cf-table').value = table;
        document.getElementById('cf-tel').value = tel;
        document.getElementById('cf-name').value = name;
        document.getElementById('cf-family').value = family;
        document.getElementById('cf-description').value = description;

        var btn = document.getElementById('btn-step-1');
        btn.classList.add('loading');
        isBusy = true;

        postobj.post_url = AJAX_URL;
        postobj.send_type = "post";
        postobj.after_success = function (data) {
            btn.classList.remove('loading');
            isBusy = false;
            var res;
            try {
                res = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                customModal.error('خطای سرور', 'پاسخ سرور نامعتبر است.');
                return;
            }

            if (res.success) {
                currentInvoiceId = parseInt(res.invoice_id);
                currentCustomerName = name;
                currentCustomerFamily = family;
                currentTableNumber = parseInt(table);
                currentBirthDate = birthMiladi;
                currentDescription = description;

                document.getElementById('aif-invoice').value = currentInvoiceId;
                document.getElementById('dif-invoice').value = currentInvoiceId;
                document.getElementById('uif-invoice').value = currentInvoiceId;
                document.getElementById('fif-invoice').value = currentInvoiceId;

                customModal.success('موفق', 'فاکتور ایجاد شد. اکنون اقلام را انتخاب کنید.', function () {
                    goToStep(2);
                });
            } else {
                customModal.error('خطا', res.message || 'خطای نامشخص');
            }
        };
        postobj.after_error = function () {
            btn.classList.remove('loading');
            isBusy = false;
            customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
        };
        res_obj_postdata('cf');
    }

    function renderItems() {
        var container = document.getElementById('items-container');
        var query = normalizeFa(document.getElementById('inp-search').value);
        var filtered = [];

        for (var i = 0; i < ALL_ITEMS.length; i++) {
            var item = ALL_ITEMS[i];
            if (currentCategoryFilter > 0 && parseInt(item.category_id) !== currentCategoryFilter) continue;
            if (query !== '') {
                var haystack = normalizeFa(item.title + ' ' + (item.recipe || ''));
                if (haystack.indexOf(query) === -1) continue;
            }
            filtered.push(item);
        }

        if (filtered.length === 0) {
            container.innerHTML = '<div class="fi-empty"><i class="fa fa-search-minus"></i><div>آیتمی یافت نشد</div></div>';
            return;
        }

        var html = '';
        for (var i = 0; i < filtered.length; i++) {
            var item = filtered[i];
            var price = parseFloat(item.price) || 0;
            var inCart = isItemInCart(parseInt(item.id));
            var cls = 'fi-item' + (inCart ? ' added' : '');
            html += '<div class="' + cls + '" onclick="quickAddItem(' + item.id + ')">' +
                '<div class="item-name">' + escapeHtml(item.title) + '</div>' +
                '<div class="item-price">' + separate(price) + ' <small>تومان</small></div>' +
                '<div class="item-add-icon"><i class="fa fa-plus"></i></div>' +
                '</div>';
        }
        container.innerHTML = html;
    }

    function isItemInCart(menuItemId) {
        for (var i = 0; i < cartItems.length; i++) {
            if (parseInt(cartItems[i].menu_item_id) === parseInt(menuItemId)) return true;
        }
        return false;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function quickAddItem(menuItemId) {
        if (isBusy) return;
        if (currentInvoiceId <= 0) {
            customModal.error('خطا', 'فاکتور معتبر نیست.');
            return;
        }

        document.getElementById('aif-menu').value = menuItemId;
        document.getElementById('aif-qty').value = 1;
        document.getElementById('aif-invoice').value = currentInvoiceId;

        isBusy = true;

        postobj.post_url = AJAX_URL;
        postobj.send_type = "post";
        postobj.after_success = function (data) {
            isBusy = false;
            var res;
            try {
                res = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                customModal.error('خطای سرور', 'پاسخ نامعتبر');
                return;
            }

            if (res.success && res.item) {
                addOrUpdateCartItem(res.item, res.is_new);
                updateCartUI();
                renderItems();
            } else {
                customModal.error('خطا', res.message || 'خطای نامشخص');
            }
        };
        postobj.after_error = function () {
            isBusy = false;
            customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
        };
        res_obj_postdata('aif');
    }

    function addOrUpdateCartItem(item, isNew) {
        var found = false;
        for (var i = 0; i < cartItems.length; i++) {
            if (parseInt(cartItems[i].id) === parseInt(item.id)) {
                cartItems[i].quantity = parseInt(item.quantity);
                cartItems[i].line_total = parseFloat(item.line_total);
                found = true;
                break;
            }
        }
        if (!found) {
            cartItems.push({
                id: parseInt(item.id),
                menu_item_id: parseInt(item.menu_item_id),
                title: item.item_title,
                unit_price: parseFloat(item.unit_price),
                quantity: parseInt(item.quantity),
                line_total: parseFloat(item.line_total)
            });
        }
    }

    function updateCartUI() {
        var container = document.getElementById('cart-container');
        var totalBox = document.getElementById('total-box');

        if (cartItems.length === 0) {
            container.innerHTML = '<div class="fi-cart-empty">' +
                '<i class="fa fa-basket-shopping"></i>' +
                '<div>هنوز آیتمی اضافه نشده</div>' +
                '<div style="font-size: 11px; margin-top: 4px;">روی آیتم‌ها بزن تا اضافه شوند</div></div>';
            totalBox.style.display = 'none';
            updateTotalsDisplay();
            return;
        }

        var html = '';
        for (var i = 0; i < cartItems.length; i++) {
            var item = cartItems[i];
            html += '<div class="fi-cart-item" data-id="' + item.id + '">' +
                '<div class="cart-info">' +
                '<div class="cart-title">' + escapeHtml(item.title) + '</div>' +
                '<div class="cart-price">' + separate(item.unit_price) + ' × ' + item.quantity + ' = ' +
                '<strong>' + separate(item.line_total) + '</strong></div>' +
                '</div>' +
                '<div class="cart-controls">' +
                '<button type="button" class="fi-qty-btn" onclick="changeQty(' + item.id + ', -1)">−</button>' +
                '<span class="fi-qty-value">' + item.quantity + '</span>' +
                '<button type="button" class="fi-qty-btn" onclick="changeQty(' + item.id + ', 1)">+</button>' +
                '<button type="button" class="fi-qty-btn danger" onclick="deleteCartItem(' + item.id + ')" title="حذف">' +
                '<i class="fa fa-trash"></i></button>' +
                '</div></div>';
        }
        container.innerHTML = html;
        totalBox.style.display = '';
        updateTotalsDisplay();
    }

    function updateTotalsDisplay() {
        var total = 0, count = 0;
        for (var i = 0; i < cartItems.length; i++) {
            total += parseFloat(cartItems[i].line_total) || 0;
            count += parseInt(cartItems[i].quantity) || 0;
        }
        document.getElementById('cart-total').innerText = separate(total);
        document.getElementById('sticky-total').innerText = separate(total);
        document.getElementById('cart-count').innerText = cartItems.length + ' قلم';
        document.getElementById('sticky-count').innerText = cartItems.length + ' قلم';
    }

    function changeQty(itemId, delta) {
        if (isBusy) return;
        var item = null;
        for (var i = 0; i < cartItems.length; i++) {
            if (parseInt(cartItems[i].id) === parseInt(itemId)) {
                item = cartItems[i];
                break;
            }
        }
        if (!item) return;

        var newQty = parseInt(item.quantity) + delta;
        if (newQty <= 0) {
            deleteCartItem(itemId);
            return;
        }

        document.getElementById('uif-item').value = itemId;
        document.getElementById('uif-qty').value = newQty;
        document.getElementById('uif-invoice').value = currentInvoiceId;

        item.quantity = newQty;
        item.line_total = item.unit_price * newQty;
        updateCartUI();

        isBusy = true;
        postobj.post_url = AJAX_URL;
        postobj.send_type = "post";
        postobj.after_success = function (data) {
            isBusy = false;
            var res;
            try {
                res = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                return;
            }
            if (res.success) {
                for (var i = 0; i < cartItems.length; i++) {
                    if (parseInt(cartItems[i].id) === parseInt(itemId)) {
                        cartItems[i].quantity = parseInt(res.quantity);
                        cartItems[i].line_total = parseFloat(res.line_total);
                        break;
                    }
                }
                updateCartUI();
            } else {
                customModal.error('خطا', res.message || 'خطا');
            }
        };
        postobj.after_error = function () {
            isBusy = false;
            customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
        };
        res_obj_postdata('uif');
    }

    function deleteCartItem(itemId) {
        if (isBusy) return;
        customModal.confirm('تأیید حذف', 'آیا این آیتم از سفارش حذف شود؟', function () {
            document.getElementById('dif-item').value = itemId;
            document.getElementById('dif-invoice').value = currentInvoiceId;
            isBusy = true;
            postobj.post_url = AJAX_URL;
            postobj.send_type = "post";
            postobj.after_success = function (data) {
                isBusy = false;
                var res;
                try {
                    res = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    return;
                }
                if (res.success) {
                    for (var i = 0; i < cartItems.length; i++) {
                        if (parseInt(cartItems[i].id) === parseInt(itemId)) {
                            cartItems.splice(i, 1);
                            break;
                        }
                    }
                    updateCartUI();
                    renderItems();
                } else {
                    customModal.error('خطا', res.message || 'خطا');
                }
            };
            postobj.after_error = function () {
                isBusy = false;
                customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
            };
            res_obj_postdata('dif');
        });
    }

    function renderFinalSummary() {
        var total = 0, count = 0;
        for (var i = 0; i < cartItems.length; i++) {
            total += parseFloat(cartItems[i].line_total) || 0;
            count += parseInt(cartItems[i].quantity) || 0;
        }

        var html = '';
        html += '<div class="fi-summary-row"><span class="lbl">مشتری:</span>' +
            '<span class="val">' + escapeHtml(currentCustomerName + ' ' + currentCustomerFamily) + '</span></div>';
        html += '<div class="fi-summary-row"><span class="lbl">شماره میز:</span>' +
            '<span class="val">' + currentTableNumber + '</span></div>';
        html += '<div class="fi-summary-row"><span class="lbl">تعداد اقلام:</span>' +
            '<span class="val">' + cartItems.length + ' ردیف (' + count + ' عدد)</span></div>';

        // ⭐ نمایش توضیحات در خلاصه نهایی
        if (currentDescription && currentDescription.trim() !== '') {
            html += '<div class="fi-summary-row"><span class="lbl">توضیحات:</span>' +
                '<span class="val" style="max-width: 60%; text-align: left; direction: rtl;">' +
                escapeHtml(currentDescription) + '</span></div>';
        }

        html += '<div class="fi-summary-row"><span class="lbl">جمع کل:</span>' +
            '<span class="val" style="color: var(--panel-accent); font-size: 15px;">' +
            separate(total) + ' تومان</span></div>';

        document.getElementById('final-summary').innerHTML = html;
    }

    var selectedStatus = -1;

    function initStatusGrid() {
        var options = document.querySelectorAll('.fi-status-option');
        for (var i = 0; i < options.length; i++) {
            options[i].addEventListener('click', function () {
                for (var j = 0; j < options.length; j++) options[j].classList.remove('selected');
                this.classList.add('selected');
                selectedStatus = parseInt(this.getAttribute('data-status'));
            });
        }
    }

    function finalizeInvoice() {
        if (isBusy) return;
        if (selectedStatus < 0) {
            customModal.warning('توجه', 'لطفاً وضعیت فاکتور را انتخاب کنید.');
            return;
        }
        if (cartItems.length === 0) {
            customModal.warning('توجه', 'فاکتور خالی است.');
            return;
        }

        document.getElementById('fif-invoice').value = currentInvoiceId;
        document.getElementById('fif-status').value = selectedStatus;

        var btn = document.getElementById('btn-finalize');
        btn.classList.add('loading');
        isBusy = true;

        postobj.post_url = AJAX_URL;
        postobj.send_type = "post";
        postobj.after_success = function (data) {
            btn.classList.remove('loading');
            isBusy = false;
            var res;
            try {
                res = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                customModal.error('خطای سرور', 'پاسخ نامعتبر');
                return;
            }

            if (res.success) {
                customModal.success('ثبت موفق', 'فاکتور با موفقیت ثبت شد.', function () {
                    location.href = 'invoices.php?action=show';
                });
            } else {
                customModal.error('خطا در ثبت', res.message || 'خطای نامشخص');
            }
        };
        postobj.after_error = function () {
            btn.classList.remove('loading');
            isBusy = false;
            customModal.error('خطای ارتباط', 'ارتباط با سرور برقرار نشد.');
        };
        res_obj_postdata('fif');
    }

    document.addEventListener('DOMContentLoaded', function () {
        initStatusGrid();

        // ⬇️ فعال‌سازی تقویم شمسی روی فیلد تاریخ تولد
        if (typeof Calendar !== 'undefined' && Calendar.setup) {
            try {
                Calendar.setup({
                    inputField: "tacustomer_birth",
                    button: "tacustomer_birth",
                    ifFormat: "%Y/%m/%d",
                    dateType: "jalali",
                    weekNumbers: false
                });
            } catch (e) {
                console.log('Calendar setup skipped:', e);
            }
        }

        var searchInput = document.getElementById('inp-search');
        var clearBtn = document.getElementById('btn-clear-search');

        searchInput.addEventListener('input', function () {
            if (this.value.length > 0) clearBtn.classList.add('show');
            else clearBtn.classList.remove('show');
            renderItems();
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                this.value = '';
                clearBtn.classList.remove('show');
                renderItems();
            }
        });

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.classList.remove('show');
            searchInput.focus();
            renderItems();
        });

        var chips = document.querySelectorAll('.fi-cat-chip');
        for (var i = 0; i < chips.length; i++) {
            chips[i].addEventListener('click', function () {
                for (var j = 0; j < chips.length; j++) chips[j].classList.remove('active');
                this.classList.add('active');
                currentCategoryFilter = parseInt(this.getAttribute('data-cat'));
                renderItems();
            });
        }

        renderItems();

        ['inp-table', 'inp-tel', 'inp-name', 'inp-family', 'tacustomer_birth'].forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitCustomer();
                }
            });
        });

        document.getElementById('inp-table').focus();
    });
</script>

<?php include("footer.php"); ?>
</body>
</html>