<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html>
<title>تعریف مشتری</title>
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

    .panel-card .form-control,
    .panel-card .form-select {
        border: 1px solid #d6dbe1;
        border-radius: 6px;
        padding: 9px 12px;
        transition: all 0.2s ease;
        background-color: #fbfcfd;
    }

    .panel-card .form-control:focus,
    .panel-card .form-select:focus {
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

    /* ⭐ ستاره قرمز داخل فیلدهای اجباری */
    .required-field {
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24'><text x='0' y='20' font-size='22' fill='%23e74c3c' font-family='Arial'>*</text></svg>");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 12px 12px;
        padding-left: 32px;
    }

    .form-control:not(.required-field),
    .form-select:not(.required-field) {
        padding-left: 12px;
    }

    .required-field:focus {
        background-position: left 12px center;
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
                    تعریف مشتری
                </div>
                <div class="card-body p-4">
                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("customers", "id", 1, "تعریف مشتریان");
                    $fm->CSRF_token();

                    // نام (اختیاری - نمایش در جدول)
                    $fm->label("نام", "form-label")
                        ->input()
                        ->inpname("name")
                        ->inpid("name")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("name", 0, 0, "نام", 1, 1);

                    // نام خانوادگی (اختیاری - نمایش در جدول)
                    $fm->label("نام خانوادگی", "form-label")
                        ->input()
                        ->inpname("family")
                        ->inpid("family")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("family", 0, 0, "نام خانوادگی", 1, 1);

                    // شماره تماس (اختیاری - نمایش در جدول)
                    $fm->label("شماره تماس", "form-label")
                        ->input()
                        ->inpname("tel")
                        ->inpid("tel")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("tel", 0, 0, "شماره تماس", 1, 1);

                    // تاریخ تولد (اختیاری - نمایش در جدول)
                    $fm->dateinput("birth_date", "تاریخ تولد", 0, 1, 0);

                    // میلی ثانیه (اجباری - عدم نمایش در جدول)
                    $fm->label("میلی ثانیه", "form-label")
                        ->input()
                        ->inpname("mili")
                        ->inpid("mili")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("mili", 0, 1, "میلی ثانیه");

                    // دکمه ثبت
                    $fm->input()
                        ->inptype("submit")
                        ->inpval("ثبت مشتری")
                        ->inpclasses("btn btn-panel w-100 mt-2")
                        ->end();

                    $fm->addform();
                    $fm->show();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include("footer.php");
?>
</body>
</html>