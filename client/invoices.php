<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>تعریف فاکتور</title>
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
                    تعریف فاکتور
                </div>
                <div class="card-body p-4">
                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("invoices", "id", 1, "تعریف فاکتورها");

                    // ⭐ اضافه شدن دکمه «مشاهده فاکتور» به هر ردیف جدول
                    $fm->setInclude("invoice_view_btn.php");

                    $fm->CSRF_token();

                    $cfid = get_cafe_id();
                    $fm->setwhere(" `cafe_id`=$cfid ");
                    $fm->deletewhere(" `cafe_id`=$cfid ");
                    $fm->set_where_edit(" `cafe_id`=$cfid ");
                    $fm->set_int_val("cafe_id", $cfid);

                    // تاریخ فاکتور (اجباری - نمایش در جدول)
                    $fm->dateinput("invoice_date", "تاریخ فاکتور", 1, 1, 1);

                    // ---- شماره میز و درصد تخفیف (دو ستونه) ----
                    echo '<div class="form-row-2col">';

                    // شماره میز (اجباری - نمایش در جدول) - نوع: عددی (1)
                    echo '<div>';
                    $fm->label("شماره میز", "form-label")
                        ->input()
                        ->inpname("table_number")
                        ->inpid("table_number")
                        ->inptype("number")
                        ->inpclasses("form-control required-field")
                        ->end()
                        ->sndform("table_number", 1, 1, "شماره میز", 1, 1);
                    echo '</div>';

                    // درصد تخفیف (اختیاری - نمایش در جدول) - نوع: عددی (1) - پیش‌فرض 0
                    echo '<div>';
                    $fm->label("درصد تخفیف", "form-label")
                        ->input()
                        ->inpname("discount_percent")
                        ->inpid("discount_percent")
                        ->inptype("number")
                        ->inpclasses("form-control")
                        ->inpval("0")
                        ->end()
                        ->sndform("discount_percent", 1, 0, "درصد تخفیف", 1, 0);
                    echo '</div>';

                    echo '</div>';
                    // ---- پایان دو ستونه ----

                    // مشتری مورد نظر (اجباری - نمایش در جدول) - سلکت از جدول customers
                    $fm->label("مشتری مورد نظر", "form-label")
                        ->select()
                        ->selectname("customer_id")
                        ->selectid("customer_id")
                        ->selectclasses("form-select mb-3 required-field")
                        ->selectaddval("", "انتخاب مشتری");
                    $sql = "SELECT id, name, family FROM customers";
                    $db = new database();
                    $db->connect()->query($sql);
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $label = trim($row['name'] . ' ' . $row['family']);
                        if ($label === '') {
                            $label = 'مشتری #' . $row['id'];
                        }
                        $fm->selectaddval($row['id'], $label);
                    }
                    $fm->end()
                        ->sndform("customer_id", 2, 1, "مشتری", 1, 1);

                    // کافه مورد نظر (اجباری - نمایش در جدول) - سلکت از جدول cafes
                    $fm->label("کافه مورد نظر", "form-label")
                        ->select()
                        ->selectname("cafe_id")
                        ->selectid("cafe_id")
                        ->selectclasses("form-select mb-3 required-field")
                        ->selectaddval("", "انتخاب کافه");
                    $sql = "SELECT id, title FROM cafes";
                    $db->connect()->query($sql);
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $fm->selectaddval($row['id'], $row['title']);
                    }
                    $fm->end()
                        ->sndform("cafe_id", 2, 1, "کافه", 1, 1);

                    // گارسون ایجاد کننده (اختیاری - نمایش در جدول) - سلکت از جدول waiters
                    $fm->label("گارسون ایجاد کننده", "form-label")
                        ->select()
                        ->selectname("waiter_id")
                        ->selectid("waiter_id")
                        ->selectclasses("form-select mb-3")
                        ->selectaddval(0, "بدون گارسون");
                    $sql = "SELECT id, fullname FROM waiters";
                    $db->connect()->query($sql);
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $fm->selectaddval($row['id'], $row['fullname']);
                    }
                    $fm->end()
                        ->sndform("waiter_id", 2, 0, "گارسون", 1, 0);

                    // وضعیت فاکتور (اجباری - نمایش در جدول) - سلکت
                    $fm->label("وضعیت فاکتور", "form-label")
                        ->select()
                        ->selectname("status")
                        ->selectid("status")
                        ->selectaddval(0, "مشاهده نشده")
                        ->selectaddval(1, "مشاهده شده")
                        ->selectaddval(2, "در حال انجام")
                        ->selectaddval(3, "غیر قابل انجام")
                        ->selectaddval(4, "انجام شده")
                        ->selectaddval(5, "در انتظار پرداخت")
                        ->selectaddval(6, "پرداخت شده")
                        ->selectclasses("form-select mb-3 required-field")
                        ->end()
                        ->sndform("status", 2, 1, "وضعیت فاکتور", 1, 1);

                    // دکمه ثبت
                    $fm->input()
                        ->inptype("submit")
                        ->inpval("ثبت فاکتور")
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