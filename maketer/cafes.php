<?php
/* خاموش کردن notice برای جلوگیری از پیام‌های اضافی */
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
include("calhead.php");

/* ═══════════════════════════════════════════════════
   شناسه بازاریاب جاری
   ═══════════════════════════════════════════════════ */
$mid = (int)get_marketers_id();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>ثبت کافه جدید — پنل بازاریاب‌ها</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
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
    }

    body {
        background-color: var(--panel-bg);
        font-family: Tahoma, "Segoe UI", sans-serif;
        color: var(--panel-text);
    }

    .panel-card {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .panel-card .card-header {
        background: linear-gradient(135deg, var(--panel-primary), #3d5468);
        color: #fff;
        text-align: center;
        padding: 14px;
        font-weight: bold;
        border-bottom: 3px solid var(--panel-accent);
        font-size: 14.5px;
        letter-spacing: 0.3px;
    }

    .panel-card .form-label {
        font-weight: 600;
        color: var(--panel-text);
        margin-bottom: 6px;
        font-size: 13px;
    }

    .panel-card .form-control,
    .panel-card .form-select {
        border: 1.5px solid #e8ecef;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 13px;
        font-family: Tahoma;
        background-color: #fbfcfd;
        transition: all 0.2s ease;
    }

    .panel-card .form-control:focus,
    .panel-card .form-select:focus {
        border-color: var(--panel-accent);
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.12);
        background-color: #fff;
        outline: none;
    }

    .panel-card .btn-panel {
        background: var(--panel-accent);
        color: #fff;
        border: none;
        border-radius: 9px;
        padding: 11px;
        font-weight: bold;
        font-size: 13.5px;
        font-family: Tahoma;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(22, 160, 133, 0.25);
        cursor: pointer;
    }

    .panel-card .btn-panel:hover {
        background: #12876f;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(22, 160, 133, 0.35);
    }

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

    .form-row-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 0;
    }

    @media (max-width: 640px) {
        .form-row-2col {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>
<body style="direction: rtl;">

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container mt-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-9">
            <div class="panel-card">
                <div class="card-header">
                    <i class="fa fa-store"></i>
                    ثبت کافه جدید
                </div>
                <div class="card-body p-4">

                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("cafes", "id", 1, "ثبت کافه‌های من");
                    $fm->CSRF_token();

                    /* ⭐ فقط کافه‌های خودِ بازاریاب */
                    $fm->setwhere(" `marketer_id`=$mid ");
                    $fm->deletewhere(" `marketer_id`=$mid ");
                    $fm->set_where_edit(" `marketer_id`=$mid ");

                    /* ⭐ غیرفعال کردن ویرایش و حذف */
                    $fm->alow_edit = false;
                    $fm->alow_del = false;

                    /* ⭐ مقادیر خودکار */
                    $fm->set_int_val("marketer_id", $mid);
                    $fm->set_int_val("status", 1);
                    $fm->set_str_val("register_date", date('Y-m-d'));

                    /* ---- عنوان کافه (متن، اجباری) ---- */
                    $fm->label("عنوان کافه", "form-label")
                        ->input()
                        ->inpname("title")
                        ->inpid("title")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("title", 0, 1, "عنوان کافه", 1, 1);

                    /* ---- شعار کافه (متن، اختیاری) ---- */
                    $fm->label("شعار کافه", "form-label")
                        ->input()
                        ->inpname("slogan")
                        ->inpid("slogan")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("slogan", 0, 0, "شعار کافه");

                    /* ---- تلفن ۱ و تلفن ۲ ---- */
                    echo '<div class="form-row-2col">';

                    echo '<div>';
                    $fm->label("شماره تماس ۱", "form-label")
                        ->input()
                        ->inpname("tel1")
                        ->inpid("tel1")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("tel1", 0, 1, "شماره تماس ۱");
                    echo '</div>';

                    echo '<div>';
                    $fm->label("شماره تماس ۲", "form-label")
                        ->input()
                        ->inpname("tel2")
                        ->inpid("tel2")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("tel2", 0, 0, "شماره تماس ۲");
                    echo '</div>';

                    echo '</div>';

                    /* ---- نام مدیر و موبایل مدیر ---- */
                    echo '<div class="form-row-2col">';

                    echo '<div>';
                    $fm->label("نام مدیر کافه", "form-label")
                        ->input()
                        ->inpname("manager_name")
                        ->inpid("manager_name")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("manager_name", 0, 1, "نام مدیر کافه", 1, 1);
                    echo '</div>';

                    echo '<div>';
                    $fm->label("موبایل مدیر کافه", "form-label")
                        ->input()
                        ->inpname("manager_mobile")
                        ->inpid("manager_mobile")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("manager_mobile", 0, 1, "موبایل مدیر", 1, 1);
                    echo '</div>';

                    echo '</div>';

                    /* ---- آدرس کافه (متن، اجباری) ---- */
                    $fm->label("آدرس کافه", "form-label")
                        ->input()
                        ->inpname("address")
                        ->inpid("address")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("address", 0, 1, "آدرس کافه");

                    /* ---- اینستاگرام و ساعت کاری ---- */
                    echo '<div class="form-row-2col">';

                    echo '<div>';
                    $fm->label("اینستاگرام", "form-label")
                        ->input()
                        ->inpname("instagram")
                        ->inpid("instagram")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("instagram", 0, 0, "اینستاگرام");
                    echo '</div>';

                    echo '<div>';
                    $fm->label("ساعت کاری", "form-label")
                        ->input()
                        ->inpname("working_hours")
                        ->inpid("working_hours")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("working_hours", 0, 1, "ساعت کاری");
                    echo '</div>';

                    echo '</div>';

                    /* ---- کلمه عبور کافه (متن، اجباری) ---- */
                    $fm->label("کلمه عبور کافه", "form-label")
                        ->input()
                        ->inpname("pass")
                        ->inpid("pass")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("pass", 0, 1, "کلمه عبور", 0);

                    /* ---- لوگوی کافه (اختیاری) ---- */
                    $fm->fileinput("لوگوی کافه (اختیاری)", "logo", "form-control mb-3", "form-label", 0);

                    /* ---- دکمه ثبت ---- */
                    $fm->input()
                        ->inptype("submit")
                        ->inpval("ثبت کافه جدید")
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
include("footer.php"); ?>
</body>
</html>