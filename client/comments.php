<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>تعریف نظر کاربر</title>
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
                    تعریف نظر کاربر
                </div>
                <div class="card-body p-4">
                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("comments", "id", 1, "نظرات کاربران");
                    $fm->CSRF_token();

                    $cfid = get_cafe_id();
                    $fm->setwhere(" `menu_item_id` in(select `id` from `menu_items` where `category_id` in (select id from `cafe_categories` where `cafe_id`=$cfid)) ");
                    $fm->deletewhere(" `menu_item_id` in(select `id` from `menu_items` where `category_id` in (select id from `cafe_categories` where `cafe_id`=$cfid)) ");
                    $fm->set_where_edit(" `menu_item_id` in(select `id` from `menu_items` where `category_id` in (select id from `cafe_categories` where `cafe_id`=$cfid)) ");


                    // نام و نام خانوادگی (اجباری - نمایش در جدول)
                    $fm->label("نام و نام خانوادگی", "form-label")
                        ->input()
                        ->inpname("fullname")
                        ->inpid("fullname")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("fullname", 0, 1, "نام و نام خانوادگی", 1, 1);

                    // امتیاز (اجباری - نمایش در جدول) - نوع: عددی (1)
                    $fm->label("امتیاز", "form-label")
                        ->input()
                        ->inpname("score")
                        ->inpid("score")
                        ->inptype("number")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("score", 1, 1, "امتیاز", 1, 1);

                    // متن نظر (اجباری - عدم نمایش در جدول) - متن طولانی
                    $fm->label("متن نظر", "form-label")
                        ->texarea()
                        ->areaid("comment_text")
                        ->areaname("comment_text")
                        ->areaclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("comment_text", 0, 1, "متن نظر", 0, 0);

                    // تاریخ (اجباری - نمایش در جدول)
                    $fm->dateinput("comment_date", "تاریخ", 1, 1, 0);

                    // آیتم مورد نظر (اجباری - نمایش در جدول) - سلکت از جدول menu_items
                    $fm->label("آیتم مورد نظر", "form-label")
                        ->select()
                        ->selectname("menu_item_id")
                        ->selectid("menu_item_id")
                        ->selectclasses("form-select mb-3 required-field")
                        ->selectaddval("", "انتخاب آیتم منو");
                    $sql = "SELECT id, title FROM menu_items where `category_id` in (select id from `cafe_categories` where `cafe_id`=$cfid)";
                    $db = new database();
                    $db->connect()->query($sql);
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $fm->selectaddval($row['id'], $row['title']);
                    }
                    $fm->end()
                        ->sndform("menu_item_id", 2, 1, "آیتم منو", 1, 1);

                    // وضعیت نظر (اجباری - نمایش در جدول) - سلکت
                    $fm->label("وضعیت نظر", "form-label")
                        ->select()
                        ->selectname("status")
                        ->selectid("status")
                        ->selectaddval(1, "تایید شده")
                        ->selectaddval(0, "تایید نشده")
                        ->selectclasses("form-select mb-3 required-field")
                        ->end()
                        ->sndform("status", 2, 1, "وضعیت", 1, 1);

                    // دکمه ثبت
                    $fm->input()
                        ->inptype("submit")
                        ->inpval("ثبت نظر")
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