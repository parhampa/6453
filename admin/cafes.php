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
<title>کافه ها</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/new.css">
<?php
include("calhead.php");
?>
<body style="direction: rtl;">

<?php include("top.php"); ?>
<?php include("nav.php"); ?>
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

    /* ⭐ ستاره قرمز برای textarea در بالای کادر */
    textarea.required-field {
        background-position: left 12px top 12px;
    }

    .form-control:not(.required-field),
    .form-select:not(.required-field) {
        padding-left: 12px;
    }

    .required-field:focus {
        background-position: left 12px center;
    }

    textarea.required-field:focus {
        background-position: left 12px top 12px;
    }
</style>

<div class="container mt-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="panel-card">
                <div class="card-header">
                    ثبت کافه جدید
                </div>
                <div class="card-body p-4">
                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("cafes", "id", 1, "تعریف کافه‌ها");
                    $fm->CSRF_token();

                    // عنوان کافه (اجباری - نمایش در جدول) - نوع: متنی (0)
                    $fm->label("عنوان کافه", "form-label")
                        ->input()
                        ->inpname("title")
                        ->inpid("title")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("title", 0, 1, "عنوان کافه", 1, 1);

                    // شعار کافه (اختیاری - عدم نمایش در جدول) - متن طولانی
                    $fm->label("شعار کافه (اختیاری)", "form-label")
                        ->texarea()
                        ->areaid("slogan")
                        ->areaname("slogan")
                        ->areaclasses("form-control mb-3")
                        ->end()
                        ->sndform("slogan", 0, 0, "شعار کافه", 0, 0);

                    // شماره تماس ۱ (اجباری - نمایش در جدول) - نوع: متنی (0)
                    $fm->label("شماره تماس ۱", "form-label")
                        ->input()
                        ->inpname("tel1")
                        ->inpid("tel1")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("tel1", 0, 1, "شماره تماس", 1, 1);

                    // شماره تماس ۲ (اختیاری - عدم نمایش در جدول) - نوع: متنی (0)
                    $fm->label("شماره تماس ۲ (اختیاری)", "form-label")
                        ->input()
                        ->inpname("tel2")
                        ->inpid("tel2")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("tel2", 0, 0, "شماره تماس ۲", 0, 0);

                    // نام و نام خانوادگی مدیریت کافه (اجباری - نمایش در جدول) - نوع: متنی (0)
                    $fm->label("نام و نام خانوادگی مدیریت کافه", "form-label")
                        ->input()
                        ->inpname("manager_name")
                        ->inpid("manager_name")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("manager_name", 0, 1, "مدیریت", 1, 1);

                    // شماره تلفن همراه مدیریت کافه (اجباری - عدم نمایش در جدول) - نوع: متنی (0)
                    $fm->label("شماره تلفن همراه مدیریت کافه", "form-label")
                        ->input()
                        ->inpname("manager_mobile")
                        ->inpid("manager_mobile")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("manager_mobile", 0, 1, "موبایل مدیر", 0, 0);

                    // آدرس کافه (اجباری - عدم نمایش در جدول) - متن طولانی
                    $fm->label("آدرس کافه", "form-label")
                        ->texarea()
                        ->areaid("address")
                        ->areaname("address")
                        ->areaclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("address", 0, 1, "آدرس کافه", 0, 0);

                    // اینستاگرام کافه (اختیاری - عدم نمایش در جدول) - نوع: متنی (0)
                    $fm->label("اینستاگرام کافه (اختیاری)", "form-label")
                        ->input()
                        ->inpname("instagram")
                        ->inpid("instagram")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("instagram", 0, 0, "اینستاگرام", 0, 0);

                    // ساعت کاری (اجباری - عدم نمایش در جدول) - نوع: متنی (0)
                    $fm->label("ساعت کاری", "form-label")
                        ->input()
                        ->inpname("working_hours")
                        ->inpid("working_hours")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("working_hours", 0, 1, "ساعت کاری", 0, 0);

                    // لوگوی کافه (اختیاری - عدم نمایش در جدول)
                    $fm->fileinput("لوگوی کافه (اختیاری)", "logo", "form-control mb-3", "form-label", 0);

                    // کلمه عبور (اجباری - عدم نمایش در جدول) - نوع: متنی (0)
                    $fm->label("کلمه عبور", "form-label")
                        ->input()
                        ->inptype("password")
                        ->inpname("pass")
                        ->inpid("pass")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("pass", 0, 1, "کلمه عبور", 0, 0);

                    // بازاریاب مربوطه (اجباری - عدم نمایش در جدول) - نوع: سلکت‌باکس (2)
                    $fm->label("بازاریاب مربوطه", "form-label", "", "")
                        ->select()
                        ->selectname("marketer_id")
                        ->selectid("marketer_id")
                        ->selectclasses("form-select mb-3 required-field")
                        ->selectaddval("", "انتخاب بازاریاب");
                    $sql = "SELECT id, name, family FROM marketers WHERE status = 1";
                    $db = new database();
                    $db->connect()->query($sql);
                    while ($fild = mysqli_fetch_assoc($db->res)) {
                        $fm->selectaddval($fild['id'], $fild['name'] . " " . $fild['family']);
                    }
                    $fm->end()
                        ->sndform("marketer_id", 2, 1, "بازاریاب", 0, 0);

                    // تاریخ ثبت مشتری (اجباری - عدم نمایش در جدول)
                    $fm->dateinput("register_date", "تاریخ ثبت مشتری", 1, 0, 1);

                    // وضعیت کافه (اجباری - نمایش در جدول) - نوع: سلکت‌باکس (2)
                    $fm->label("وضعیت کافه", "form-label")
                        ->select()
                        ->selectname("status")
                        ->selectid("status")
                        ->selectaddval(1, "فعال")
                        ->selectaddval(0, "غیر فعال")
                        ->selectclasses("form-select mb-3 required-field")
                        ->end()
                        ->sndform("status", 2, 1, "وضعیت", 1, 1);

                    // دکمه ثبت
                    $fm->input()
                        ->inptype("submit")
                        ->inpval("ثبت کافه")
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