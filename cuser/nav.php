<?php
// ---- تشخیص صفحه جاری ----
$current_page = basename($_SERVER['PHP_SELF']);

// ---- تابع کمکی برای تشخیص کلاس فعال ----
function is_active($page, $current)
{
    return $page === $current ? 'active' : '';
}

// ---- واکشی اطلاعات گارسون از سشن ----
$thisuser = $_SESSION['tel'] ?? '';
$dbt = new database();
$dbt->connect()->query("select * from `waiters` where `tel`='$thisuser' limit 1");
$fildt = mysqli_fetch_assoc($dbt->res);
$avatar = mb_substr($fildt['fullname'] ?? '', 0, 1, 'UTF-8');
?>

<style>
    /* ---------- آیتم‌های منوی کناری (بدون کشویی) ---------- */
    .sidebar-menu > li > a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        color: #c8d1da;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
        position: relative;
        margin: 2px 0;
    }

    .sidebar-menu > li > a:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #fff;
    }

    .sidebar-menu > li > a i:first-child {
        width: 20px;
        text-align: center;
        font-size: 14px;
        color: var(--panel-accent, #16a085);
    }

    .sidebar-menu > li > a .menu-text {
        flex-grow: 1;
    }

    /* ===== آیتم فعال (صفحه جاری) ===== */
    .sidebar-menu > li > a.active {
        background: linear-gradient(90deg,
        rgba(22, 160, 133, 0.30) 0%,
        rgba(22, 160, 133, 0.15) 60%,
        transparent 100%);
        color: #ffffff;
        font-weight: 700;
        padding-right: 20px;
        box-shadow: inset -3px 0 0 0 var(--panel-accent, #16a085);
    }

    .sidebar-menu > li > a.active i:first-child {
        color: #1abc9c;
        transform: scale(1.15);
    }

    .sidebar-menu > li > a.active::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 70%;
        background: var(--panel-accent, #16a085);
        border-radius: 0 3px 3px 0;
        box-shadow: 0 0 8px rgba(22, 160, 133, 0.6);
    }

    /* ---------- آیتم برجسته (فاکتور سریع) ---------- */
    .sidebar-menu > li > a.highlight-item {
        background: linear-gradient(90deg,
        rgba(243, 156, 18, 0.20) 0%,
        rgba(243, 156, 18, 0.05) 60%,
        transparent 100%);
        color: #f5c37a;
    }

    .sidebar-menu > li > a.highlight-item i:first-child {
        color: #f39c12;
    }

    .sidebar-menu > li > a.highlight-item:hover {
        background: linear-gradient(90deg,
        rgba(243, 156, 18, 0.35) 0%,
        rgba(243, 156, 18, 0.15) 60%,
        transparent 100%);
        color: #ffffff;
        padding-right: 20px;
    }

    .sidebar-menu > li > a.highlight-item:hover i:first-child {
        color: #ffb143;
    }

    .sidebar-menu > li > a.highlight-item.active {
        background: linear-gradient(90deg,
        rgba(243, 156, 18, 0.40) 0%,
        rgba(243, 156, 18, 0.20) 60%,
        transparent 100%);
        color: #ffffff;
        font-weight: 700;
        box-shadow: inset -3px 0 0 0 #f39c12;
    }

    .sidebar-menu > li > a.highlight-item.active i:first-child {
        color: #ffb143;
        transform: scale(1.15);
    }

    .sidebar-menu > li > a.highlight-item.active::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 70%;
        background: #f39c12;
        border-radius: 0 3px 3px 0;
        box-shadow: 0 0 8px rgba(243, 156, 18, 0.6);
    }

    /* برچسب کوچک «سریع» کنار آیتم */
    .sidebar-menu > li > a .badge-fast {
        background: #f39c12;
        color: #fff;
        font-size: 9px;
        font-weight: bold;
        padding: 1px 6px;
        border-radius: 8px;
        margin-right: auto;
        letter-spacing: 0.3px;
    }

    /* ---------- برچسب عنوان بخش ---------- */
    .sidebar-menu .section-label {
        list-style: none;
        font-size: 10.5px;
        font-weight: bold;
        color: #7b8b9a;
        padding: 16px 16px 6px;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-menu .section-label::after {
        content: '';
        flex-grow: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.15), transparent);
        border-radius: 1px;
    }

    /* جداکننده */
    .sidebar-menu .divider {
        list-style: none;
        height: 1px;
        background: rgba(255, 255, 255, 0.08);
        margin: 10px 16px;
        border-radius: 1px;
    }
</style>

<ul class="sidebar-menu" id="sidebarMenu">

    <!-- ========== داشبورد ========== -->
    <li>
        <a href="index.php" class="<?php echo is_active('index.php', $current_page); ?>">
            <i class="fas fa-gauge-high"></i>
            <span class="menu-text">داشبورد</span>
        </a>
    </li>

    <li class="divider"></li>

    <!-- ========== فروش و فاکتور ========== -->
    <li class="section-label">
        <span>فروش و فاکتور</span>
    </li>

    <li>
        <a href="fast_invoice.php"
           class="highlight-item <?php echo is_active('fast_invoice.php', $current_page); ?>">
            <i class="fas fa-bolt"></i>
            <span class="menu-text">فاکتور سریع</span>
            <span class="badge-fast">سریع</span>
        </a>
    </li>

    <li>
        <a href="invoices.php?action=show"
           class="<?php echo is_active('invoices.php', $current_page); ?>">
            <i class="fas fa-file-invoice-dollar"></i>
            <span class="menu-text">فاکتورها</span>
        </a>
    </li>

    <li>
        <a href="invoice_items.php?action=show"
           class="<?php echo is_active('invoice_items.php', $current_page); ?>">
            <i class="fas fa-receipt"></i>
            <span class="menu-text">آیتم‌های فاکتور</span>
        </a>
    </li>

    <li class="divider"></li>

</ul>
</div>
</aside>

<!-- محتوای اصلی -->
<div class="main-content">
    <header class="dashboard-header">
        <div class="header-container">
            <button class="hamburger-btn" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
            <div class="search-wrapper header-search-desktop">
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="جستجو در منو..." id="desktopSearchInput">
                </div>
            </div>
            <div class="user-actions">
                <div class="dropdown notif-dropdown">
                    <div class="notification-bell dropdown-toggle" id="notificationDropdown" data-bs-toggle="dropdown"
                         aria-expanded="false">
                        <i class="fa-regular fa-bell"></i>
                        <span class="badge-dot"></span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                        <?php
                        $sqlt = "select * from `report` order by `id` desc limit 0,4";
                        $dbt = new database();
                        $dbt->connect()->query($sqlt);
                        while ($fildt = mysqli_fetch_assoc($dbt->res)) {
                            ?>
                            <li>
                                <div class="notif-item">
                                    <div class="notif-title"><?php echo($fildt['title']); ?></div>
                                    <div class="notif-time"><?php
                                        $dt = new date_man();
                                        echo($dt->roz_pish($fildt['post_date'], date("Y-m-d")));
                                        ?></div>
                                </div>
                            </li>
                            <?php
                        }
                        ?>
                        <li class="notif-footer"><a href="report.php?action=show">مشاهده بیشتر <i
                                        class="fas fa-arrow-left"></i></a></li>
                    </ul>
                </div>
                <div class="dropdown profile-dropdown">
                    <div class="user-info dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown"
                         aria-expanded="false">
                        <div class="user-avatar"><span><?php echo($avatar); ?></span></div>
                        <span class="user-name"><?php echo($fildt['fullname'] ?? ''); ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle"></i> پروفایل
                                من</a></li>
                        <li><a class="dropdown-item disabled" href="#"><i class="fas fa-chart-pie"></i> پیشخوان
                                اختصاصی</a></li>
                        <li><a class="dropdown-item disabled" href="#"><i class="fas fa-cog"></i> تنظیمات حساب</a>
                        </li>
                        <li><a class="dropdown-item" href="security.php"><i class="fas fa-lock"></i> تغییر رمز عبور</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i>
                                خروج</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="content-area">