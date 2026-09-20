<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
include("calhead.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>انبار</title>
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
    :root { --panel-primary: #2c3e50; --panel-secondary: #34495e; --panel-accent: #16a085; --panel-bg: #f4f6f9; --panel-text: #2c3e50; }
    body { background-color: var(--panel-bg); font-family: Tahoma, sans-serif; color: var(--panel-text); }
    .panel-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); overflow: hidden; }
    .panel-card .card-header { background: linear-gradient(135deg, var(--panel-primary), #3d5468); color: #fff; text-align: center; padding: 14px; font-weight: bold; border-bottom: 3px solid var(--panel-accent); font-size: 14.5px; }
    .panel-card .form-label { font-weight: 600; color: var(--panel-text); margin-bottom: 6px; font-size: 13px; }
    .panel-card .form-control, .panel-card .form-select { border: 1.5px solid #e8ecef; border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: Tahoma; background-color: #fbfcfd; transition: all 0.2s ease; }
    .panel-card .form-control:focus, .panel-card .form-select:focus { border-color: var(--panel-accent); box-shadow: 0 0 0 3px rgba(22,160,133,0.12); background-color: #fff; outline: none; }
    .panel-card .btn-panel { background: var(--panel-accent); color: #fff; border: none; border-radius: 9px; padding: 11px; font-weight: bold; font-size: 13.5px; font-family: Tahoma; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(22,160,133,0.25); cursor: pointer; }
    .panel-card .btn-panel:hover { background: #12876f; color: #fff; transform: translateY(-1px); }
    .required-field { background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24'><text x='0' y='20' font-size='22' fill='%23e74c3c' font-family='Arial'>*</text></svg>"); background-repeat: no-repeat; background-position: left 12px center; background-size: 12px 12px; padding-left: 32px; }
    .form-control:not(.required-field), .form-select:not(.required-field) { padding-left: 12px; }
    .tbl-thumb { display: inline-block; width: 44px; height: 44px; border-radius: 6px; overflow: hidden; border: 1.5px solid #e8ecef; background: #fff; padding: 2px; transition: all 0.2s ease; }
    .tbl-thumb:hover { border-color: var(--panel-accent); transform: scale(1.1); box-shadow: 0 4px 12px rgba(22,160,133,0.25); }
    .tbl-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .tbl-file-link { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; background: #e8f5e9; color: #16a085; border-radius: 7px; font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
    .tbl-file-link:hover { background: var(--panel-accent); color: #fff; }
</style>
<body style="direction: rtl;">

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container mt-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-9">
            <div class="panel-card">
                <div class="card-header">انبار</div>
                <div class="card-body p-4">

                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("anbar", "id", 1, "مدیریت انبار");

                    $fm->CSRF_token();
                    $fm->alow_edit = true;
                    $fm->alow_del  = true;

                    /* کافی شاپ مربوطه */
                    $fm->label("کافی شاپ مربوطه", "form-label")
                        ->select()->selectname("cafe_id")->selectid("cafe_id")
                        ->selectclasses("form-select mb-3")
                        ->selectaddval("", "انتخاب کافی شاپ مربوطه");
                    $db = new database();
                    $db->connect()->query("SELECT * FROM `cafes`");
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $display = $row['title'];
                        $fm->selectaddval($row['id'], $display);
                    }
                    $fm->end()->sndform("cafe_id", 2, 0, "کافی شاپ مربوطه", 1, 1);

                    /* عنوان کالا */
                    $fm->label("عنوان کالا", "form-label")
                        ->input()->inpname("title")->inpid("title")
                        ->inptype("text")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("title", 0, 1, "عنوان کالا", 1, 1);

                    /* تعداد */
                    $fm->label("تعداد", "form-label")
                        ->input()->inpname("cnt")->inpid("cnt")
                        ->inptype("number")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("cnt", 1, 0, "تعداد", 1, 1);

                    /* تاریخ ورود */
                    $fm->dateinput("date_input", "تاریخ ورود", 1, 1, 1);

                    /* تصویر کالا */
                    $fm->fileinput("تصویر کالا", "pic", "form-control mb-3", "form-label", 0);

                    /* توضیحات بیشتر */
                    $fm->label("توضیحات بیشتر", "form-label")
                        ->input()->inpname("txt")->inpid("txt")
                        ->inptype("textarea")
                        ->inpclasses("form-control mb-3")
                        ->end()
                        ->sndform("txt", 0, 0, "توضیحات بیشتر", 1, 0);

                    $fm->input()->inptype("submit")->inpval("ثبت")->inpclasses("btn btn-panel w-100 mt-2")->end();

                    $fm->addform();
                    $fm->show();
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
