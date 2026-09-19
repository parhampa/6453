<?php
// ---- تشخیص صفحه جاری ----
$current_page = basename($_SERVER['PHP_SELF']);

// ---- نگاشت گروه‌ها به صفحات عضو ----
$menu_groups = [
    'groupCafes' => [
        'waiters.php',
        'cafe_categories.php',
        'menu_items.php',
    ],
    'groupSales' => [
        'invoices.php',
        'invoice_items.php',
        'fast_invoice.php',
    ],
    'groupFeedback' => [
        'comments.php',
    ],
];

// ---- تشخیص گروه باز بر اساس صفحه جاری ----
$open_group = '';
foreach ($menu_groups as $group_id => $pages) {
    if (in_array($current_page, $pages)) {
        $open_group = $group_id;
        break;
    }
}

// ---- تابع کمکی برای تشخیص کلاس فعال ----
function is_active($page, $current)
{
    return $page === $current ? 'active' : '';
}

?>

<style>
    /* ---------- گروه‌بندی منوی کناری ---------- */
    .sidebar-menu .menu-group {
        margin: 2px 0;
    }

    .sidebar-menu .menu-group > .menu-group-toggle {
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
        cursor: pointer;
        position: relative;
    }

    .sidebar-menu .menu-group > .menu-group-toggle:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #fff;
    }

    .sidebar-menu .menu-group > .menu-group-toggle i:first-child {
        width: 20px;
        text-align: center;
        font-size: 14px;
        color: var(--panel-accent, #16a085);
    }

    .sidebar-menu .menu-group > .menu-group-toggle span {
        flex-grow: 1;
    }

    .sidebar-menu .menu-group > .menu-group-toggle .menu-arrow {
        font-size: 10px;
        transition: transform 0.3s ease;
        color: #8b98a5;
    }

    .sidebar-menu .menu-group > .menu-group-toggle[aria-expanded="true"] .menu-arrow {
        transform: rotate(180deg);
    }

    /* گروهی که فرزند فعال دارد، خودش هم کمی برجسته می‌شود */
    .sidebar-menu .menu-group.has-active > .menu-group-toggle {
        color: #fff;
        background: rgba(22, 160, 133, 0.10);
    }

    .sidebar-menu .menu-group.has-active > .menu-group-toggle i:first-child {
        color: #1abc9c;
    }

    .sidebar-menu .submenu {
        list-style: none;
        padding: 4px 0 4px 0;
        margin: 0;
        background: rgba(0, 0, 0, 0.12);
        border-radius: 8px;
        margin-top: 4px;
        position: relative;
    }

    .sidebar-menu .submenu::before {
        content: '';
        position: absolute;
        right: 26px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: linear-gradient(to bottom, transparent, rgba(22, 160, 133, 0.35), transparent);
        border-radius: 2px;
    }

    .sidebar-menu .submenu li a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px 8px 14px;
        color: #b8c3cd;
        text-decoration: none;
        font-size: 12.5px;
        border-radius: 6px;
        margin: 1px 8px;
        transition: all 0.2s ease;
        position: relative;
    }

    .sidebar-menu .submenu li a:hover {
        background: rgba(22, 160, 133, 0.15);
        color: #fff;
        padding-right: 20px;
    }

    .sidebar-menu .submenu li a i {
        width: 18px;
        text-align: center;
        font-size: 12px;
        color: #7fb8a8;
        transition: color 0.2s ease;
    }

    .sidebar-menu .submenu li a:hover i {
        color: var(--panel-accent, #16a085);
    }

    /* ===== آیتم فعال (صفحه جاری) ===== */
    .sidebar-menu .submenu li a.active {
        background: linear-gradient(90deg,
        rgba(22, 160, 133, 0.30) 0%,
        rgba(22, 160, 133, 0.15) 60%,
        transparent 100%);
        color: #ffffff;
        font-weight: 700;
        padding-right: 20px;
        box-shadow: inset -3px 0 0 0 var(--panel-accent, #16a085);
    }

    .sidebar-menu .submenu li a.active i {
        color: #1abc9c;
        transform: scale(1.15);
    }

    .sidebar-menu .submenu li a.active::after {
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
    .sidebar-menu .submenu li a.highlight-item {
        background: linear-gradient(90deg,
        rgba(243, 156, 18, 0.20) 0%,
        rgba(243, 156, 18, 0.05) 60%,
        transparent 100%);
        color: #f5c37a;
    }

    .sidebar-menu .submenu li a.highlight-item i {
        color: #f39c12;
    }

    .sidebar-menu .submenu li a.highlight-item:hover {
        background: linear-gradient(90deg,
        rgba(243, 156, 18, 0.35) 0%,
        rgba(243, 156, 18, 0.15) 60%,
        transparent 100%);
        color: #ffffff;
        padding-right: 20px;
    }

    .sidebar-menu .submenu li a.highlight-item:hover i {
        color: #ffb143;
    }

    .sidebar-menu .submenu li a.highlight-item.active {
        background: linear-gradient(90deg,
        rgba(243, 156, 18, 0.40) 0%,
        rgba(243, 156, 18, 0.20) 60%,
        transparent 100%);
        color: #ffffff;
        font-weight: 700;
        box-shadow: inset -3px 0 0 0 #f39c12;
    }

    .sidebar-menu .submenu li a.highlight-item.active i {
        color: #ffb143;
        transform: scale(1.15);
    }

    .sidebar-menu .submenu li a.highlight-item.active::after {
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
    .sidebar-menu .submenu li a .badge-fast {
        background: #f39c12;
        color: #fff;
        font-size: 9px;
        font-weight: bold;
        padding: 1px 6px;
        border-radius: 8px;
        margin-right: auto;
        letter-spacing: 0.3px;
    }
</style>

<ul class="sidebar-menu" id="sidebarMenu">
    <!-- ========== داشبورد ========== -->
    <li>
        <a href="index.php" class="<?php echo is_active('index.php', $current_page); ?>">
            <i class="fas fa-gauge-high"></i>
            داشبورد
        </a>
    </li>

    <!-- ========== گروه: مدیریت کافه‌ها ========== -->
    <li class="menu-group <?php echo $open_group === 'groupCafes' ? 'has-active' : ''; ?>">
        <a class="menu-group-toggle <?php echo $open_group !== 'groupCafes' ? 'collapsed' : ''; ?>"
           data-bs-toggle="collapse" href="#groupCafes"
           role="button"
           aria-expanded="<?php echo $open_group === 'groupCafes' ? 'true' : 'false'; ?>"
           aria-controls="groupCafes">
            <i class="fas fa-mug-saucer"></i>
            <span>مدیریت کافه‌ها</span>
            <i class="fas fa-chevron-down menu-arrow"></i>
        </a>
        <ul class="collapse submenu <?php echo $open_group === 'groupCafes' ? 'show' : ''; ?>" id="groupCafes">
            <li>
                <a href="waiters.php?action=show"
                   class="<?php echo is_active('waiters.php', $current_page); ?>">
                    <i class="fas fa-bell-concierge"></i>
                    گارسون کافه‌ها
                </a>
            </li>
            <li>
                <a href="cafe_categories.php?action=show"
                   class="<?php echo is_active('cafe_categories.php', $current_page); ?>">
                    <i class="fas fa-tags"></i>
                    دسته‌بندی کافه‌ها
                </a>
            </li>
            <li>
                <a href="menu_items.php?action=show"
                   class="<?php echo is_active('menu_items.php', $current_page); ?>">
                    <i class="fas fa-utensils"></i>
                    آیتم‌های منو
                </a>
            </li>
        </ul>
    </li>

    <!-- ========== گروه: فروش و فاکتور ========== -->
    <li class="menu-group <?php echo $open_group === 'groupSales' ? 'has-active' : ''; ?>">
        <a class="menu-group-toggle <?php echo $open_group !== 'groupSales' ? 'collapsed' : ''; ?>"
           data-bs-toggle="collapse" href="#groupSales"
           role="button"
           aria-expanded="<?php echo $open_group === 'groupSales' ? 'true' : 'false'; ?>"
           aria-controls="groupSales">
            <i class="fas fa-cash-register"></i>
            <span>فروش و فاکتور</span>
            <i class="fas fa-chevron-down menu-arrow"></i>
        </a>
        <ul class="collapse submenu <?php echo $open_group === 'groupSales' ? 'show' : ''; ?>" id="groupSales">
            <li>
                <a href="fast_invoice.php"
                   class="highlight-item <?php echo is_active('fast_invoice.php', $current_page); ?>">
                    <i class="fas fa-bolt"></i>
                    فاکتور سریع
                    <span class="badge-fast">سریع</span>
                </a>
            </li>
            <li>
                <a href="invoices.php?action=show"
                   class="<?php echo is_active('invoices.php', $current_page); ?>">
                    <i class="fas fa-file-invoice-dollar"></i>
                    فاکتورها
                </a>
            </li>
            <li>
                <a href="invoice_items.php?action=show"
                   class="<?php echo is_active('invoice_items.php', $current_page); ?>">
                    <i class="fas fa-receipt"></i>
                    آیتم‌های فاکتور
                </a>
            </li>
        </ul>
    </li>

    <!-- ========== گروه: نظرات و بازخورد ========== -->
    <li class="menu-group <?php echo $open_group === 'groupFeedback' ? 'has-active' : ''; ?>">
        <a class="menu-group-toggle <?php echo $open_group !== 'groupFeedback' ? 'collapsed' : ''; ?>"
           data-bs-toggle="collapse" href="#groupFeedback"
           role="button"
           aria-expanded="<?php echo $open_group === 'groupFeedback' ? 'true' : 'false'; ?>"
           aria-controls="groupFeedback">
            <i class="fas fa-comments"></i>
            <span>نظرات و بازخورد</span>
            <i class="fas fa-chevron-down menu-arrow"></i>
        </a>
        <ul class="collapse submenu <?php echo $open_group === 'groupFeedback' ? 'show' : ''; ?>" id="groupFeedback">
            <li>
                <a href="comments.php?action=show"
                   class="<?php echo is_active('comments.php', $current_page); ?>">
                    <i class="fas fa-comment-dots"></i>
                    نظرات مشتریان
                </a>
            </li>
        </ul>
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
                        <?php
                        $thisuser = $_SESSION['username'];
                        $sqlt = "select * from `admin_user` where `username`='$thisuser'";
                        $dbt = new database();
                        $dbt->connect()->query($sqlt);
                        $fildt = mysqli_fetch_assoc($dbt->res);
                        $avatar = mb_substr($fildt['name'], 0, 1, 'UTF-8');
                        ?>
                        <div class="user-avatar"><span><?php echo($avatar); ?></span></div>
                        <span class="user-name"><?php echo($fildt['name'] . " " . $fildt['family']); ?></span>
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