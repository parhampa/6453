<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>تعریف آیتم فاکتور</title>
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

    .auto-filled {
        background-color: #f0f8f5 !important;
        border-color: #a8d5c4 !important;
        font-weight: bold;
        color: var(--panel-accent);
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
                    تعریف آیتم فاکتور
                </div>
                <div class="card-body p-4">
                    <?php
                    $fm = new makeform();
                    $fm->set_tbl_key("invoice_items", "id", 1, "آیتم‌های فاکتور");
                    $fm->CSRF_token();

                    /* ═══════════════════════════════════════════════════
                       گارسون جاری + کافه‌ی مربوط به گارسون
                       ═══════════════════════════════════════════════════ */
                    $wid = (int)get_waiters_id();

                    $db = new database();
                    $db->connect()->query("SELECT cafe_id FROM `waiters` WHERE id = $wid LIMIT 1");
                    $wrow = mysqli_fetch_assoc($db->res);
                    $cfid = (int)($wrow['cafe_id'] ?? 0);

                    /* ⭐ فیلتر: فقط آیتم‌های مربوط به فاکتورهای خود گارسون */
                    $fm->setwhere(" `invoice_id` in(select `id` from `invoices` where `waiter_id`=$wid) ");
                    $fm->deletewhere(" `invoice_id` in(select `id` from `invoices` where `waiter_id`=$wid) ");
                    $fm->set_where_edit(" `invoice_id` in(select `id` from `invoices` where `waiter_id`=$wid) ");

                    // ---- فاکتور مورد نظر (اجباری - نمایش در جدول) ----
                    $fm->label("فاکتور مورد نظر", "form-label")
                        ->select()
                        ->selectname("invoice_id")
                        ->selectid("invoice_id")
                        ->selectclasses("form-select mb-3 required-field")
                        ->selectaddval("", "انتخاب فاکتور");

                    // ⭐ فقط فاکتورهای خود گارسون
                    $sql = "SELECT 
                                i.id AS invoice_id, 
                                i.invoice_date, 
                                c.name AS customer_name, 
                                c.family AS customer_family
                            FROM `invoices` i
                            LEFT JOIN `customers` c ON c.id = i.customer_id
                            WHERE i.waiter_id = $wid
                            ORDER BY i.id DESC";

                    $db->connect()->query($sql);
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $year = substr($row['invoice_date'], 0, 4);
                        $month = substr($row['invoice_date'], 5, 2);
                        $day = substr($row['invoice_date'], 8, 2);
                        $jdate = gregorian_to_jalali($year, $month, $day);
                        $jfdate = $jdate[0] . "-" . $jdate[1] . "-" . $jdate[2];

                        $customer_name = trim($row['customer_name'] . ' ' . $row['customer_family']);
                        if ($customer_name === '') {
                            $customer_name = 'مشتری #' . $row['invoice_id'];
                        }

                        $label = $jfdate . ' | ' . $customer_name;
                        $fm->selectaddval($row['invoice_id'], $label);
                    }
                    $fm->end()
                        ->sndform("invoice_id", 2, 1, "فاکتور", 1, 1);

                    // ---- آیتم مورد نظر (اجباری - نمایش در جدول) ----
                    $fm->label("آیتم مورد نظر", "form-label")
                        ->select()
                        ->selectname("menu_item_id")
                        ->selectid("menu_item_id")
                        ->selectclasses("form-select mb-3 required-field")
                        ->selectaddval("", "انتخاب آیتم منو");

                    // ⭐ آیتم‌های منو از کافه‌ی گارسون
                    $item_prices = [];
                    $sql2 = "SELECT mi.id, mi.title, mi.price 
                             FROM `menu_items` mi 
                             WHERE mi.category_id IN (SELECT id FROM `cafe_categories` WHERE cafe_id = $cfid)
                             ORDER BY mi.title";
                    $db->connect()->query($sql2);
                    while ($row = mysqli_fetch_assoc($db->res)) {
                        $fm->selectaddval($row['id'], $row['title']);
                        $item_prices[$row['id']] = $row['price'];
                    }
                    $fm->end()
                        ->sndform("menu_item_id", 2, 1, "آیتم منو", 1, 1);

                    // ---- قیمت واحد (اجباری) ----
                    $fm->label("قیمت واحد (در لحظه فروش)", "form-label")
                        ->input()
                        ->inpname("unit_price")
                        ->inpid("unit_price")
                        ->inptype("number")
                        ->inpclasses("form-control mb-3 required-field")
                        ->end()
                        ->sndform("unit_price", 1, 1, "قیمت واحد", 1, 1);

                    // ---- تعداد (اجباری) ----
                    $fm->label("تعداد", "form-label")
                        ->input()
                        ->inpname("quantity")
                        ->inpid("quantity")
                        ->inptype("number")
                        ->inpclasses("form-control mb-3 required-field")
                        ->inpval("1")
                        ->end()
                        ->sndform("quantity", 1, 1, "تعداد", 1, 1);

                    // دکمه ثبت
                    $fm->input()
                        ->inptype("submit")
                        ->inpval("ثبت آیتم فاکتور")
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

<script>
    var itemPrices = <?php echo json_encode($item_prices, JSON_UNESCAPED_UNICODE); ?>;

    $(document).ready(function () {
        function fillPrice() {
            var selectedId = $('#menu_item_id').val();
            var $priceInput = $('#unit_price');

            if (selectedId && itemPrices[selectedId] !== undefined) {
                $priceInput.val(itemPrices[selectedId]);
                $priceInput.attr('data-original-price', itemPrices[selectedId]);
                $priceInput.addClass('auto-filled');
            } else {
                $priceInput.val('');
                $priceInput.removeAttr('data-original-price');
                $priceInput.removeClass('auto-filled');
            }
        }

        $('#menu_item_id').on('change', fillPrice);
        fillPrice();

        $('#unit_price').on('input', function () {
            var originalPrice = $(this).attr('data-original-price');
            var currentPrice = $(this).val();

            if (originalPrice !== undefined && currentPrice != originalPrice) {
                $(this).removeClass('auto-filled');
                $(this).css({
                    'background-color': '#fff5f5',
                    'border-color': '#f5b7b1',
                    'color': '#c0392b'
                });
            } else if (originalPrice !== undefined) {
                $(this).addClass('auto-filled');
                $(this).css({
                    'background-color': '',
                    'border-color': '',
                    'color': ''
                });
            }
        });
    });
</script>

<?php
include("footer.php");
?>
</body>
</html>