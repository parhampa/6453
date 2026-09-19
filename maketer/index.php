<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>داشبورد — پنل بازاریاب ها</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/new.css">
<?php include("calhead.php"); ?>
<style>
    :root {
        --panel-primary: #2c3e50;
        --panel-secondary: #34495e;
        --panel-accent: #16a085;
        --panel-bg: #f4f6f9;
        --panel-text: #2c3e50;
        --card-radius: 12px;
        --card-shadow: 0 2px 10px rgba(44, 62, 80, 0.05);
        --card-shadow-hover: 0 8px 24px rgba(44, 62, 80, 0.10);
    }

    body {
        background-color: var(--panel-bg);
        font-family: Tahoma, "Segoe UI", sans-serif;
        color: var(--panel-text);
    }

    /* ═══════════════════════════════════════════════════
       عنوان بخش
    ═══════════════════════════════════════════════════ */
    .section-title {
        font-size: 13px;
        font-weight: bold;
        color: var(--panel-primary);
        margin: 0 0 10px;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.2px;
    }

    .section-title .title-icon {
        width: 26px;
        height: 26px;
        background: linear-gradient(135deg, var(--panel-primary), #3d5468);
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        box-shadow: 0 2px 6px rgba(44, 62, 80, 0.20);
    }

    .section-title .title-text {
        flex-grow: 1;
    }

    .section-title .title-hint {
        font-size: 10px;
        color: #95a5a6;
        font-weight: normal;
        background: #f0f3f6;
        padding: 3px 10px;
        border-radius: 10px;
    }

    /* ═══════════════════════════════════════════════════
       کارت‌های آماری
    ═══════════════════════════════════════════════════ */
    .stat-card {
        background: #ffffff;
        border: 1px solid #f0f3f6;
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
        min-height: 92px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 5px;
        height: 100%;
        background: var(--accent-line, transparent);
        border-radius: 0 4px 4px 0;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-shadow-hover);
        border-color: var(--accent-line, #e8ecef);
    }

    .stat-card .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        flex-shrink: 0;
        order: -1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-card .stat-info {
        text-align: right;
        flex-grow: 1;
        min-width: 0;
    }

    .stat-card .stat-info .stat-label {
        font-size: 12.5px;
        color: #7f8c9b;
        margin-bottom: 6px;
        line-height: 1.3;
        font-weight: 600;
    }

    .stat-card .stat-info .stat-value {
        font-size: 26px;
        font-weight: bold;
        color: var(--panel-text);
        line-height: 1.1;
        letter-spacing: -0.5px;
    }

    .stat-card .stat-info .stat-value small {
        font-size: 11px;
        font-weight: normal;
        color: #95a5a6;
        margin-right: 5px;
    }

    /* ---------- رنگ آیکون‌ها ---------- */
    .ic-cafe-total {
        background: linear-gradient(135deg, #2c3e50, #34495e);
    }

    .ic-cafe-active {
        background: linear-gradient(135deg, #16a085, #1abc9c);
    }

    .ic-cafe-inactive {
        background: linear-gradient(135deg, #7f8c8d, #95a5a6);
    }

    .stat-card[data-accent="cafe-total"]::before {
        background: linear-gradient(180deg, #2c3e50, #34495e);
    }

    .stat-card[data-accent="cafe-active"]::before {
        background: linear-gradient(180deg, #16a085, #1abc9c);
    }

    .stat-card[data-accent="cafe-inactive"]::before {
        background: linear-gradient(180deg, #7f8c8d, #95a5a6);
    }

    /* ═══════════════════════════════════════════════════
       ریسپانسیو
    ═══════════════════════════════════════════════════ */
    @media (max-width: 640px) {
        .stat-card {
            padding: 12px 14px;
            min-height: 78px;
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            font-size: 18px;
        }

        .stat-card .stat-info .stat-value {
            font-size: 21px;
        }

        .stat-card .stat-info .stat-label {
            font-size: 11px;
        }
    }
</style>
<body style="direction: rtl;">

<?php
/* ═══════════════════════════════════════════════════
   شناسه بازاریاب جاری
   ═══════════════════════════════════════════════════ */
$mid = (int)get_marketers_id();

/* تابع کمکی برای شمارش */
function count_rows($sql)
{
    $db = new database();
    $db->connect()->query($sql);
    $row = mysqli_fetch_row($db->res);
    return $row ? (int)$row[0] : 0;
}

/* ═══════════════════════════════════════════════════
   آمار کافه‌های این بازاریاب
   ═══════════════════════════════════════════════════ */
$total_cafes    = count_rows("SELECT COUNT(*) FROM `cafes` WHERE `marketer_id` = $mid");
$active_cafes   = count_rows("SELECT COUNT(*) FROM `cafes` WHERE `marketer_id` = $mid AND `status` = 1");
$inactive_cafes = count_rows("SELECT COUNT(*) FROM `cafes` WHERE `marketer_id` = $mid AND `status` = 0");
?>

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container-fluid px-3 py-3">

    <!-- ════════ عنوان بخش ════════ -->
    <h5 class="section-title">
        <span class="title-icon"><i class="fa fa-mug-hot"></i></span>
        <span class="title-text">آمار کافه‌های من</span>
        <span class="title-hint">کافه‌هایی که ثبت کرده‌ام</span>
    </h5>

    <!-- ════════ کارت‌های آماری ════════ -->
    <div class="row g-3 mb-4">

        <!-- کل کافه‌ها -->
        <div class="col-12 col-md-4">
            <div class="stat-card" data-accent="cafe-total">
                <div class="stat-info">
                    <div class="stat-label">کل کافه‌ها</div>
                    <div class="stat-value">
                        <?php echo number_format($total_cafes); ?>
                        <small>کافه</small>
                    </div>
                </div>
                <div class="stat-icon ic-cafe-total">
                    <i class="fa fa-mug-hot"></i>
                </div>
            </div>
        </div>

        <!-- کافه‌های فعال -->
        <div class="col-12 col-md-4">
            <div class="stat-card" data-accent="cafe-active">
                <div class="stat-info">
                    <div class="stat-label">کافه‌های فعال</div>
                    <div class="stat-value" style="color:#16a085;">
                        <?php echo number_format($active_cafes); ?>
                        <small>کافه</small>
                    </div>
                </div>
                <div class="stat-icon ic-cafe-active">
                    <i class="fa fa-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- کافه‌های غیرفعال -->
        <div class="col-12 col-md-4">
            <div class="stat-card" data-accent="cafe-inactive">
                <div class="stat-info">
                    <div class="stat-label">کافه‌های غیرفعال</div>
                    <div class="stat-value" style="color:#7f8c8d;">
                        <?php echo number_format($inactive_cafes); ?>
                        <small>کافه</small>
                    </div>
                </div>
                <div class="stat-icon ic-cafe-inactive">
                    <i class="fa fa-circle-xmark"></i>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include("footer.php"); ?>
</body>
</html>