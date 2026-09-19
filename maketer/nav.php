<?php
$current_page = basename($_SERVER['PHP_SELF']);

if (!function_exists('is_active')) {
    function is_active($page, $current)
    {
        return $page === $current ? 'active' : '';
    }
}

/* واکشی اطلاعات کاربر از سشن */
$thisuser = $_SESSION['tel'] ?? '';
$dbt = new database();
$dbt->connect();

$fm_tmp = new makeform();
$user_safe = $fm_tmp->sqlstr($thisuser);

$dbt->query("select * from `marketers` where `tel`='$user_safe' limit 1");
$fildt = mysqli_fetch_assoc($dbt->res);

$display_val = $fildt['name'] ?? '';
$avatar = mb_substr($display_val, 0, 1, 'UTF-8');
?>

<style>
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
    .sidebar-menu > li > a:hover { background: rgba(255,255,255,0.06); color: #fff; }
    .sidebar-menu > li > a i:first-child {
        width: 20px; text-align: center; font-size: 14px;
        color: var(--panel-accent, #16a085);
    }
    .sidebar-menu > li > a .menu-text { flex-grow: 1; }
    .sidebar-menu > li > a.active {
        background: linear-gradient(90deg, rgba(22,160,133,0.30) 0%, rgba(22,160,133,0.15) 60%, transparent 100%);
        color: #fff;
        font-weight: 700;
        padding-right: 20px;
        box-shadow: inset -3px 0 0 0 var(--panel-accent, #16a085);
    }
    .sidebar-menu > li > a.active i:first-child { color: #1abc9c; transform: scale(1.15); }
    .sidebar-menu .divider {
        list-style: none;
        height: 1px;
        background: rgba(255,255,255,0.08);
        margin: 10px 16px;
        border-radius: 1px;
    }

    /* برچسب عنوان بخش */
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
        background: linear-gradient(90deg, rgba(255,255,255,0.15), transparent);
        border-radius: 1px;
    }

    /* آیتم برجسته */
    .sidebar-menu > li > a.highlight-item {
        background: linear-gradient(90deg, rgba(243,156,18,0.20) 0%, rgba(243,156,18,0.05) 60%, transparent 100%);
        color: #f5c37a;
    }
    .sidebar-menu > li > a.highlight-item i:first-child { color: #f39c12; }
    .sidebar-menu > li > a.highlight-item:hover {
        background: linear-gradient(90deg, rgba(243,156,18,0.35) 0%, rgba(243,156,18,0.15) 60%, transparent 100%);
        color: #fff;
    }
    .sidebar-menu > li > a.highlight-item.active {
        background: linear-gradient(90deg, rgba(243,156,18,0.40) 0%, rgba(243,156,18,0.20) 60%, transparent 100%);
        color: #fff;
        box-shadow: inset -3px 0 0 0 #f39c12;
    }
    .sidebar-menu > li > a.highlight-item.active i:first-child { color: #ffb143; }

    /* برچسب کوچک */
    .sidebar-menu > li > a .badge-new {
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

    <!-- ════════ داشبورد ════════ -->
    <li>
        <a href="index.php" class="<?php echo is_active('index.php', $current_page); ?>">
            <i class="fas fa-gauge-high"></i>
            <span class="menu-text">داشبورد</span>
        </a>
    </li>

    <li class="divider"></li>

    <!-- ════════ مدیریت کافه‌ها ════════ -->
    <li class="section-label">
        <span>مدیریت کافه‌ها</span>
    </li>

    <li>
        <a href="cafes.php"
           class="highlight-item <?php echo (is_active('cafes.php', $current_page) && !isset($_GET['action'])) ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i>
            <span class="menu-text">ثبت کافه جدید</span>
            <span class="badge-new">جدید</span>
        </a>
    </li>

    <li>
        <a href="cafes.php?action=show"
           class="<?php echo (is_active('cafes.php', $current_page) && isset($_GET['action']) && $_GET['action'] === 'show') ? 'active' : ''; ?>">
            <i class="fas fa-mug-hot"></i>
            <span class="menu-text">کافه‌های ثبت شده</span>
        </a>
    </li>

    <li class="divider"></li>

</ul>
</div>
</aside>

<div class="main-content">
    <header class="dashboard-header">
        <div class="header-container">
            <button class="hamburger-btn" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
            <div class="search-wrapper header-search-desktop">
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="جستجو..." id="desktopSearchInput">
                </div>
            </div>
            <div class="user-actions">
                <div class="dropdown profile-dropdown">
                    <div class="user-info dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown"
                         aria-expanded="false">
                        <div class="user-avatar"><span><?php echo htmlspecialchars($avatar); ?></span></div>
                        <span class="user-name"><?php echo htmlspecialchars($display_val); ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle"></i> پروفایل من</a></li>
                        <li><a class="dropdown-item" href="security.php"><i class="fas fa-lock"></i> تغییر رمز عبور</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> خروج</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="content-area">