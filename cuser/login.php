<?php
/**
 * Created by PhpStorm.
 * User: ormazd
 * Date: 8/25/2020
 * Time: 4:01 PM
 */
session_start();
include("../lib/php/lib_include.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>ورود به پنل مدیریت</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<style>
    :root {
        --panel-primary: #2c3e50;
        --panel-secondary: #34495e;
        --panel-accent: #16a085;
        --panel-bg: #f4f6f9;
        --panel-text: #2c3e50;
    }

    html, body {
        height: 100%;
        direction: rtl;
    }

    body {
        font-family: Tahoma, "Segoe UI", sans-serif;
        font-size: 13px;
        background: linear-gradient(135deg, var(--panel-bg) 0%, #e3e9ef 100%);
        color: var(--panel-text);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }

    .login-wrapper {
        width: 100%;
        max-width: 420px;
        padding: 16px;
    }

    .login-card {
        background: #ffffff;
        border: none;
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
        overflow: hidden;
    }

    .login-card .login-header {
        background: var(--panel-primary);
        color: #fff;
        text-align: center;
        padding: 22px 18px 18px;
        border-bottom: 3px solid var(--panel-accent);
    }

    .login-card .login-header img {
        width: 110px;
        max-width: 50%;
        margin-bottom: 10px;
    }

    .login-card .login-header h3 {
        font-size: 17px;
        font-weight: bold;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .login-card .login-body {
        padding: 24px 26px;
        text-align: right;
    }

    .login-card .form-label {
        font-weight: 600;
        color: var(--panel-text);
        margin-bottom: 6px;
        font-size: 13px;
        display: block;
        text-align: right;
    }

    .login-card .form-control {
        border: 1px solid #d6dbe1;
        border-radius: 6px;
        padding: 10px 12px;
        transition: all 0.2s ease;
        background-color: #fbfcfd;
        font-size: 13px;
        text-align: right;
        direction: rtl;
    }

    .login-card .form-control:focus {
        border-color: var(--panel-accent);
        box-shadow: 0 0 0 0.2rem rgba(22, 160, 133, 0.15);
        background-color: #fff;
    }

    .login-card .btn-success {
        background: var(--panel-accent);
        border-color: var(--panel-accent);
        font-weight: bold;
        padding: 9px 26px;
    }

    .login-card .btn-success:hover {
        background: var(--panel-secondary);
        border-color: var(--panel-secondary);
    }

    .login-footer {
        text-align: center;
        padding: 14px 18px;
        background: #f9fafb;
        border-top: 1px solid #eef2f5;
        font-size: 12px;
        color: #7f8c9b;
    }

    .login-footer .brand {
        color: var(--panel-accent);
        font-weight: bold;
    }

    .login-footer .fa-shield-halved {
        color: var(--panel-accent);
        margin-left: 4px;
    }
</style>
<body onload="scch()">

<div class="login-wrapper" id="fdive">
    <div class="login-card">
        <div class="login-header">
            <img src="daminologo.png" alt="لوگو">
            <h3>
                <i class="fa fa-lock me-2"></i>
                ورود به پنل گارسون
            </h3>
        </div>
        <div class="login-body" id="sdive">
            <?php
            $lg = new loginpg();
            $lg->inputclass = "form-control";
            $lg->showlogin("waiters", "tel", "pass", "index.php");
            ?>
        </div>
        <div class="login-footer">
            <i class="fa fa-shield-halved"></i>
            طراحی و پیاده‌سازی
            <span class="brand">تیم نرم افزاری x4y</span>
        </div>
    </div>
</div>

<script>
    function scch() {
        if (window.screen.availWidth <= 600) {
            document.getElementById('fdive').style.padding = "8px";
        }
    }
</script>
</body>
</html>