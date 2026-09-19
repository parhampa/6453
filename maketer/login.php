<?php
session_start();
include("../lib/php/lib_include.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>ورود به پنل بازاریاب ها</title>
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
    html, body { height: 100%; direction: rtl; }
    body {
        font-family: Tahoma, "Segoe UI", sans-serif;
        font-size: 13px;
        background: linear-gradient(135deg, #f4f6f9 0%, #e3e9ef 100%);
        color: #2c3e50;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }
    .login-wrapper { width: 100%; max-width: 420px; padding: 16px; }
    .login-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
        overflow: hidden;
    }
    .login-card .login-header {
        background: #2c3e50;
        color: #fff;
        text-align: center;
        padding: 22px 18px 18px;
        border-bottom: 3px solid #16a085;
    }
    .login-card .login-header h3 { font-size: 17px; font-weight: bold; margin: 0; }
    .login-card .login-body { padding: 24px 26px; }
    .login-card .form-control {
        border: 1px solid #d6dbe1;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 13px;
        background: #fbfcfd;
    }
    .login-card .form-control:focus {
        border-color: #16a085;
        box-shadow: 0 0 0 0.2rem rgba(22, 160, 133, 0.15);
        background: #fff;
    }
    .login-footer {
        text-align: center;
        padding: 14px 18px;
        background: #f9fafb;
        border-top: 1px solid #eef2f5;
        font-size: 12px;
        color: #7f8c9b;
    }
</style>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h3><i class="fa fa-lock"></i> ورود به پنل بازاریاب ها</h3>
        </div>
        <div class="login-body">
            <?php
            $lg = new loginpg();
            $lg->inputclass = "form-control";
            $lg->showlogin("marketers", "tel", "pass", "index.php");
            ?>
        </div>
        <div class="login-footer">
            <i class="fa fa-shield-halved"></i> پنل مدیریت
        </div>
    </div>
</div>

</body>
</html>