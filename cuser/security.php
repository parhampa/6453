<?php
/**
 * تغییر رمز عبور گارسون
 */
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html>
<title>تغییر رمز عبور</title>
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
<?php include("calhead.php"); ?>
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

    .panel-card {
        background: #ffffff;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .panel-card .card-header {
        background: var(--panel-primary);
        color: #fff;
        text-align: center;
        padding: 14px;
        font-weight: bold;
        border-bottom: 3px solid var(--panel-accent);
    }

    .panel-card .form-label {
        font-weight: 600;
        color: var(--panel-text);
        margin-bottom: 6px;
        font-size: 14px;
    }

    .panel-card .form-control {
        border: 1px solid #d6dbe1;
        border-radius: 6px;
        padding: 9px 12px;
        transition: all 0.2s ease;
        background-color: #fbfcfd;
    }

    .panel-card .form-control:focus {
        border-color: var(--panel-accent);
        box-shadow: 0 0 0 0.2rem rgba(22, 160, 133, 0.15);
        background-color: #fff;
    }

    .panel-card .btn-panel {
        background: var(--panel-accent);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 10px;
        font-weight: bold;
        transition: background 0.2s ease;
    }

    .panel-card .btn-panel:hover {
        background: var(--panel-secondary);
        color: #fff;
    }

    .required-field {
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24'><text x='0' y='20' font-size='22' fill='%23e74c3c' font-family='Arial'>*</text></svg>");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 12px 12px;
        padding-left: 32px;
    }

    .form-control:not(.required-field) {
        padding-left: 12px;
    }

    .required-field:focus {
        background-position: left 12px center;
    }

    .custom-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .custom-modal-overlay.show {
        display: flex;
        animation: fadeInOverlay 0.2s ease-out;
    }

    .custom-modal-box {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        padding: 32px 24px 22px;
        text-align: center;
        animation: popIn 0.3s ease-out;
        position: relative;
    }

    .custom-modal-box .msg-icon-wrap {
        width: 74px;
        height: 74px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 38px;
        color: #fff;
    }

    .custom-modal-box.success .msg-icon-wrap {
        background: linear-gradient(135deg, #16a085, #1abc9c);
        box-shadow: 0 8px 24px rgba(22, 160, 133, 0.4);
    }

    .custom-modal-box.error .msg-icon-wrap {
        background: linear-gradient(135deg, #c0392b, #e74c3c);
        box-shadow: 0 8px 24px rgba(192, 57, 43, 0.4);
    }

    .custom-modal-box .msg-title {
        font-size: 20px;
        font-weight: bold;
        color: var(--panel-text);
        margin-bottom: 8px;
    }

    .custom-modal-box .msg-text {
        font-size: 15px;
        color: #5a6472;
        line-height: 1.9;
        word-break: break-word;
        max-height: 240px;
        overflow-y: auto;
        margin-bottom: 20px;
    }

    .custom-modal-box .btn-modal-close {
        background: var(--panel-primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 32px;
        font-weight: bold;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .custom-modal-box .btn-modal-close:hover {
        background: var(--panel-accent);
        color: #fff;
    }

    @keyframes popIn {
        0%   { transform: scale(0.8); opacity: 0; }
        60%  { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); }
    }

    @keyframes fadeInOverlay {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
</style>
<body style="direction: rtl;">

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container mt-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="panel-card">
                <div class="card-header">
                    تغییر کلمه عبور
                </div>
                <div class="card-body p-4">

                    <form id="security-form" onsubmit="return false;">

                        <label class="form-label">کلمه عبور پیشین</label>
                        <input type="password" name="pass" id="pass"
                               class="form-control mb-3 required-field datasender"
                               autocomplete="off">

                        <label class="form-label">کلمه عبور جدید</label>
                        <input type="password" name="newpass" id="newpass"
                               class="form-control mb-3 required-field datasender"
                               autocomplete="off">

                        <label class="form-label">تکرار کلمه عبور جدید</label>
                        <input type="password" name="newpass2" id="newpass2"
                               class="form-control mb-3 required-field datasender"
                               autocomplete="off">

                        <button type="button" class="btn btn-panel w-100 mt-2" onclick="snddata()">
                            اعمال تغییرات
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="custom-modal-overlay" id="msgModal">
    <div class="custom-modal-box" id="msgBox">
        <div id="msgIconSlot"></div>
        <div class="msg-title" id="msgTitle"></div>
        <div class="msg-text" id="msgText"></div>
        <button type="button" class="btn-modal-close" onclick="closeMsg()">باشه</button>
    </div>
</div>

<script>
    var __msgRedirect = null;

    function showMsg(type, title, text, redirect) {
        var isSuccess = (type === 'success');

        __msgRedirect = redirect || null;

        var $box = document.getElementById('msgBox');
        $box.classList.remove('success', 'error');
        $box.classList.add(isSuccess ? 'success' : 'error');

        var iconHtml = '<div class="msg-icon-wrap">' +
            '<i class="fa-solid ' + (isSuccess ? 'fa-check' : 'fa-xmark') + '"></i>' +
            '</div>';
        document.getElementById('msgIconSlot').innerHTML = iconHtml;

        document.getElementById('msgTitle').innerText = title || (isSuccess ? 'موفق' : 'خطا');
        document.getElementById('msgText').innerText  = text  || '';

        document.getElementById('msgModal').classList.add('show');
    }

    function closeMsg() {
        document.getElementById('msgModal').classList.remove('show');
        if (__msgRedirect) {
            var target = __msgRedirect;
            __msgRedirect = null;
            location.replace(target);
        }
    }

    document.getElementById('msgModal').addEventListener('click', function (e) {
        if (e.target === this) closeMsg();
    });

    function snddata() {
        var formData = {};
        $('.datasender').each(function () {
            formData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: 'security_edit.php',
            type: 'POST',
            data: formData,
            dataType: 'text',
            success: function (raw) {
                var trimmed = (raw || '').trim();
                var resjs;
                try {
                    resjs = JSON.parse(trimmed);
                } catch (e) {
                    showMsg('error', 'خطا', 'پاسخ نامعتبر از سرور دریافت شد.');
                    return;
                }

                if (parseInt(resjs.status, 10) === 1) {
                    showMsg('success', 'انجام شد', resjs.msg, 'logout.php');
                } else {
                    showMsg('error', 'خطا', resjs.msg);
                }
            },
            error: function () {
                showMsg('error', 'خطا', 'ارتباط با سرور برقرار نشد.');
            }
        });
    }
</script>

<?php include("footer.php"); ?>
</body>
</html>