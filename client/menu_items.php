<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html>
<title>تعریف آیتم منو</title>
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
<body style="direction: rtl;">

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container mt-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="panel-card">
                <div class="card-header">
                    تعریف آیتم منو
                </div>
                <div class="card-body p-4">
                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("menu_items", "id", 1, "تعریف آیتم‌های منو");

                    $cfid = get_cafe_id();
                    $fm->setwhere(" `category_id` in (select `id` from `cafe_categories` where `cafe_id`=$cfid) ");
                    $fm->deletewhere(" `category_id` in (select `id` from `cafe_categories` where `cafe_id`=$cfid) ");
                    $fm->set_where_edit(" `category_id` in (select `id` from `cafe_categories` where `cafe_id`=$cfid) ");

                    $fm->CSRF_token();

                    // عنوان آیتم (اجباری - نمایش در جدول) - نوع: متنی (0)
                    $fm->label("عنوان آیتم", "form-label")
                        ->input()
                        ->inpname("title")
                        ->inpid("title")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("title", 0, 1, "عنوان آیتم", 1, 1);

                    // تصویر آیتم (اختیاری - عدم نمایش در جدول) - فایل
                    $fm->fileinput("تصویر آیتم (اختیاری)", "image", "form-control mb-3", "form-label", 0);

                    // متن توضیحات رسپی (اختیاری - عدم نمایش در جدول) - متن طولانی
                    $fm->label("متن توضیحات رسپی (اختیاری)", "form-label")
                        ->texarea()
                        ->areaid("recipe")
                        ->areaname("recipe")
                        ->areaclasses("form-control mb-3")
                        ->end()
                        ->sndform("recipe", 0, 0, "توضیحات رسپی", 0, 0);

                    // قیمت (اجباری - نمایش در جدول) - نوع: عددی (1)
                    $fm->label("قیمت", "form-label")
                        ->input()
                        ->inpname("price")
                        ->inpid("price")
                        ->inptype("number")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("price", 1, 1, "قیمت", 1, 1);

                    // دسته بندی مربوطه (اجباری - نمایش در جدول) - سلکت از جدول cafe_categories
                    $fm->label("دسته بندی مربوطه", "form-label")
                        ->select()
                        ->selectname("category_id")
                        ->selectid("category_id")
                        ->selectclasses("form-select mb-3 required-field")
                        ->selectaddval("", "انتخاب دسته بندی");
                    $sql = "SELECT id, title FROM cafe_categories where `cafe_id`=$cfid";
                    $db = new database();
                    $db->connect()->query($sql);
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $fm->selectaddval($row['id'], $row['title']);
                    }
                    $fm->end()
                        ->sndform("category_id", 2, 1, "دسته بندی", 1, 1);

                    // دکمه ثبت
                    $fm->input()
                        ->inptype("submit")
                        ->inpval("ثبت آیتم منو")
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