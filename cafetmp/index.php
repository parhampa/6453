<?php
/* ================================================================
   ⚙️ تنظیمات
   ================================================================ */
$CAFE_ID = 1;

$PATHS = [
    'lib' => '../lib/php/lib_include.php',
    'bootstrap_css' => '../bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css',
    'bootstrap_js' => '../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js',
    'fontawesome' => '../fontawesome-free-6.7.2-web/css/all.min.css',
    'jquery' => '../lib/js/jquery.js',
    'palib' => '../lib/js/palib.js',
    'style' => 'style.css',
    'script' => 'script.js',
];

session_start();
include_once $PATHS['lib'];

$db = new database();
$db->connect();

$cid = (int)$CAFE_ID;

$db->query("SELECT * FROM `cafes` WHERE `id` = $cid AND `status` = 1 LIMIT 1");
$cafe = mysqli_fetch_assoc($db->res);

if (!$cafe) {
    http_response_code(404);
    die('کافه مورد نظر یافت نشد یا غیرفعال است.');
}

$categories = [];
$db->connect()->query("SELECT * FROM `cafe_categories` WHERE `cafe_id` = $cid ORDER BY `id`");
while ($row = mysqli_fetch_assoc($db->res)) $categories[] = $row;

$menu_items = [];
$db->connect()->query("
    SELECT mi.*, cc.title AS cat_title
    FROM `menu_items` mi
    JOIN `cafe_categories` cc ON cc.id = mi.category_id
    WHERE cc.cafe_id = $cid
    ORDER BY mi.category_id, mi.id
");
while ($row = mysqli_fetch_assoc($db->res)) $menu_items[] = $row;

$comments_by_item = [];
$db->connect()->query("
    SELECT cm.*, mi.title AS item_title
    FROM `comments` cm
    JOIN `menu_items` mi ON mi.id = cm.menu_item_id
    JOIN `cafe_categories` cc ON cc.id = mi.category_id
    WHERE cc.cafe_id = $cid AND cm.status = 1
    ORDER BY cm.id DESC
");
while ($row = mysqli_fetch_assoc($db->res)) {
    $item_id = (int)$row['menu_item_id'];
    $comments_by_item[$item_id][] = $row;
}

$cat_counts = [];
foreach ($menu_items as $mi) {
    $cat = (int)$mi['category_id'];
    $cat_counts[$cat] = ($cat_counts[$cat] ?? 0) + 1;
}

/* ---------- سال شمسی جاری برای فرم تاریخ تولد ---------- */
$gy = (int)date('Y');
$gm = (int)date('n');
$gd = (int)date('j');
$current_jy = $gy - 621;
if ($gm < 3 || ($gm == 3 && $gd < 21)) $current_jy--;
$bd_year_max = $current_jy - 15;
$bd_year_min = $current_jy - 90;

$persian_months = [
    1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
    4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
    7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
    10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
];

/* ---------- توابع کمکی ---------- */
function fa_num($n)
{
    $n = number_format((float)$n, 0, '.', '٬');
    return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
        ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], $n);
}

function fa_float($n, $d = 1)
{
    $n = number_format((float)$n, $d, '.', '');
    return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
        ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], $n);
}

function item_rating($comments)
{
    if (empty($comments)) return ['score' => 0, 'count' => 0, 'sum' => 0];
    $sum = 0;
    foreach ($comments as $c) $sum += (int)$c['score'];
    return ['score' => round($sum / count($comments), 1), 'count' => count($comments), 'sum' => $sum];
}

function fix_img($path)
{
    if (empty($path)) return '';
    $path = str_replace('\\', '/', trim($path));
    return $path;
}

function category_icon($title)
{
    $map = [
        'بار گرم' => 'fa-mug-hot', 'بار سرد' => 'fa-mug-saucer',
        'صبحانه' => 'fa-egg', 'ناهار' => 'fa-utensils',
        'شام' => 'fa-drumstick-bite', 'کیک' => 'fa-cake-candles',
        'نوشیدنی' => 'fa-lemon', 'دسر' => 'fa-ice-cream',
        'قهوه' => 'fa-mug-hot', 'چای' => 'fa-mug-saucer',
        'آبمیوه' => 'fa-glass-water', 'شیک' => 'fa-ice-cream',
    ];
    foreach ($map as $k => $v) {
        if (mb_strpos($title, $k, 0, 'UTF-8') !== false) return $v;
    }
    return 'fa-mug-hot';
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="cafe-id" content="<?= (int)$cid ?>"/>
    <title><?= htmlspecialchars($cafe['title']) ?> — منوی دیجیتال</title>

    <link href="<?= $PATHS['bootstrap_css'] ?>" rel="stylesheet"/>
    <link rel="stylesheet" href="<?= $PATHS['fontawesome'] ?>"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap"
          rel="stylesheet"/>
    <link rel="stylesheet" href="<?= $PATHS['style'] ?>"/>
</head>
<body>

<!-- ================= مودال خوش‌آمدگویی ================= -->
<div class="modal fade welcome-modal" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-icon"><i class="fa-solid fa-mug-saucer"></i></div>
                <h3 id="welcomeModalLabel">خوش اومدی 👋</h3>
                <p class="modal-sub">قبل از دیدن منو، یه لحظه اطلاعاتت رو وارد کن تا سفارش و نظراتت رو به اسم خودت ثبت
                    کنیم.</p>

                <form id="welcomeForm" novalidate>
                    <div class="name-row">
                        <div class="form-field">
                            <label for="firstNameInput">نام</label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" id="firstNameInput" placeholder="مثلاً سارا"
                                       autocomplete="given-name"/>
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="lastNameInput">نام خانوادگی</label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" id="lastNameInput" placeholder="مثلاً احمدی"
                                       autocomplete="family-name"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="phoneInput">شماره تلفن</label>
                        <div class="input-group-custom">
                            <i class="fa-solid fa-phone"></i>
                            <input type="tel" id="phoneInput" placeholder="09xxxxxxxxx" inputmode="numeric"
                                   autocomplete="tel"/>
                        </div>
                    </div>

                    <div class="field-error" id="formError">
                        لطفاً نام، نام خانوادگی و شماره تلفن رو درست وارد کن.
                    </div>

                    <button type="submit" class="continue-btn">ادامه</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= مودال تاریخ تولد ================= -->
<div class="modal fade welcome-modal" id="birthdateModal" tabindex="-1" aria-labelledby="birthdateModalLabel"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-icon"><i class="fa-solid fa-cake-candles"></i></div>
                <h3 id="birthdateModalLabel">یه چیز کوچیک دیگه 🎂</h3>
                <p class="modal-sub">اگه تاریخ تولدت رو برامون ثبت کنی، تولدت رو یادمون می‌مونه و می‌تونیم برات تخفیف
                    ویژه‌ی تولد در نظر بگیریم.</p>

                <form id="birthdateForm" novalidate>
                    <div class="birthdate-row">
                        <div class="form-field">
                            <label for="bd_day">روز</label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-calendar"></i>
                                <select id="bd_day">
                                    <option value="">—</option>
                                    <?php for ($d = 1; $d <= 31; $d++): ?>
                                        <option value="<?= $d ?>"><?= fa_num($d) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="bd_month">ماه</label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-calendar-days"></i>
                                <select id="bd_month">
                                    <option value="">—</option>
                                    <?php foreach ($persian_months as $num => $name): ?>
                                        <option value="<?= $num ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="bd_year">سال</label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-calendar-check"></i>
                                <select id="bd_year">
                                    <option value="">—</option>
                                    <?php for ($y = $bd_year_max; $y >= $bd_year_min; $y--): ?>
                                        <option value="<?= $y ?>"><?= fa_num($y) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field-error" id="bdError" style="display:none;">
                        لطفاً تاریخ تولدت رو کامل انتخاب کن.
                    </div>

                    <button type="submit" class="continue-btn">ثبت تاریخ تولد</button>
                    <button type="button" id="bdSkipBtn" class="skip-btn">فعلاً رد کن</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= مودال سفارش من ================= -->
<div class="modal fade order-modal" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="order-modal-title">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <h5 id="orderModalLabel">سفارش من</h5>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="بستن">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="order-list" id="orderList"></div>
            </div>

            <div class="modal-footer">
                <div class="table-row" id="orderTableRow">
                    <label for="orderTableNumber">شماره میز</label>
                    <div class="input-group-custom">
                        <i class="fa-solid fa-table-cells-large"></i>
                        <input type="number"
                               id="orderTableNumber"
                               class="table-number-input"
                               placeholder="مثلاً ۵"
                               min="1" max="99"
                               inputmode="numeric"/>
                    </div>
                    <div class="table-hint">
                        <i class="fa-solid fa-circle-info"></i>
                        شماره میز روی میزتون نوشته شده — قبل از ارسال سفارش واردش کن.
                    </div>
                    <div class="field-error" id="orderTableError">
                        لطفاً شماره میز معتبر (۱ تا ۹۹) وارد کن.
                    </div>
                </div>

                <!-- ⭐ فیلد توضیحات بیشتر (اختیاری) -->
                <div class="order-description-row" id="orderDescriptionRow">
                    <label for="orderDescription">
                        <i class="fa-solid fa-comment-dots"></i>
                        توضیحات بیشتر (اختیاری)
                    </label>
                    <textarea id="orderDescription"
                              class="order-description-input"
                              placeholder="مثلاً: بدون شکر، بدون یخ، سفارش فوری، ..."
                              maxlength="1000"
                              rows="2"></textarea>
                </div>

                <div class="order-total-row">
                    <span>جمع کل</span>
                    <strong id="orderTotal">۰ تومان</strong>
                </div>

                <div class="order-discount-row" id="orderDiscountRow">
                    <span><i class="fa-solid fa-tag"></i> تخفیف شما</span>
                    <strong id="orderDiscountValue">۰٪</strong>
                </div>

                <div class="order-final-row" id="orderFinalRow">
                    <span>مبلغ نهایی</span>
                    <strong id="orderFinalTotal">۰ تومان</strong>
                </div>

                <button class="send-order-btn" id="sendOrderBtn" type="button">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>ارسال به صندوق</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= مودال پیگیری سفارشات ================= -->
<div class="modal fade order-modal" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <div class="order-modal-title">
                    <button type="button" class="btn-back-custom" id="trackingBackBtn" aria-label="بازگشت">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <i class="fa-solid fa-clock-rotate-left" id="trackingHeaderIcon"></i>
                    <h5 id="trackingModalLabel">پیگیری سفارش‌ها</h5>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="بستن">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">

                <div class="tracking-view" id="trackingListView">
                    <div class="tracking-loading" id="trackingLoading">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>در حال دریافت سفارش‌ها...</span>
                    </div>
                    <div class="tracking-empty" id="trackingEmpty">
                        <i class="fa-solid fa-receipt"></i>
                        <p>هنوز سفارشی ثبت نکردی</p>
                    </div>
                    <div class="tracking-list" id="trackingList"></div>
                </div>

                <div class="tracking-view" id="trackingDetailView">
                    <div class="tracking-detail-head">
                        <div class="tracking-detail-title">
                            <span class="tracking-detail-id" id="trackingDetailId">سفارش #—</span>
                            <span class="tracking-status" id="trackingDetailStatus">—</span>
                        </div>
                        <div class="tracking-detail-meta" id="trackingDetailMeta"></div>
                    </div>

                    <div class="tracking-detail-items" id="trackingDetailItems"></div>

                    <div class="tracking-detail-summary" id="trackingDetailSummary"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     مودال پیام اختصاصی
     ================================================================ -->
<div class="modal fade app-message-modal" id="appMessageModal" tabindex="-1"
     aria-labelledby="appMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="app-message-body">
                <div class="app-message-icon" id="appMessageIcon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <h3 class="app-message-title" id="appMessageModalLabel">پیام</h3>
                <p class="app-message-text" id="appMessageText">متن پیام</p>
            </div>

            <div class="app-message-footer">
                <button type="button" class="app-message-btn app-message-btn-cancel"
                        id="appMessageCancelBtn" style="display:none;">
                    انصراف
                </button>
                <button type="button" class="app-message-btn app-message-btn-primary"
                        id="appMessageOkBtn">
                    تأیید
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ================= محتوای اصلی ================= -->
<div id="appRoot">
    <header class="top-bar">
        <div class="top-bar-inner">
            <div class="brand">
                <button class="back-btn" id="backBtn" type="button">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>بازگشت</span>
                </button>
                <div class="brand-mark"><i class="fa-solid fa-mug-hot"></i></div>
                <div class="brand-text">
                    <h1><?= htmlspecialchars($cafe['title']) ?></h1>
                    <span>منوی دیجیتال</span>
                </div>
            </div>

            <div class="top-bar-actions">
                <button class="tracking-pill" id="trackingPillBtn" type="button">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>پیگیری</span>
                </button>
                <button class="order-pill" id="orderPillBtn" type="button">
                    <i class="fa-solid fa-bag-shopping"></i>
                    سفارش من
                    <span class="count" id="orderCount">0</span>
                </button>
            </div>
        </div>
    </header>

    <main class="page" id="categoriesPage">
        <section class="brand-hero">
            <div class="brand-logo">
                <?php if (!empty($cafe['logo'])): ?>
                    <img src="<?= htmlspecialchars(fix_img($cafe['logo'])) ?>"
                         alt="<?= htmlspecialchars($cafe['title']) ?>"/>
                <?php else: ?>
                    <i class="fa-solid fa-mug-hot brand-logo-placeholder"></i>
                <?php endif; ?>
            </div>
            <h1 class="brand-hero-name"><?= htmlspecialchars($cafe['title']) ?></h1>
            <p class="brand-hero-slogan"><?= htmlspecialchars($cafe['slogan'] ?? '') ?></p>
        </section>

        <section class="search-section" id="searchSection">
            <div class="search-inner">
                <div class="search-label">
                    <span class="search-label-line"></span>
                    <span class="search-label-text">
                        <i class="fa-solid fa-sparkles"></i>
                        دنبال چی می‌گردی؟
                    </span>
                    <span class="search-label-line"></span>
                </div>

                <div class="search-box" id="searchBox">
                    <div class="search-box-glow"></div>

                    <div class="search-box-inner">
                        <div class="search-icon-wrap">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        </div>

                        <input type="text"
                               id="searchInput"
                               class="search-input"
                               placeholder="مثلاً کاپوچینو، چیزکیک، لیموناد..."
                               autocomplete="off"
                               spellcheck="false"
                               inputmode="search"/>

                        <button type="button" class="search-clear-btn" id="searchClearBtn" aria-label="پاک کردن">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                        <div class="search-kbd-hint" id="searchKbdHint">
                            <span class="kbd">ESC</span>
                        </div>
                    </div>
                </div>

                <div class="search-chips" id="searchChips">
                    <span class="chips-label">
                        <i class="fa-solid fa-fire"></i>
                        پیشنهادهای پرطرفدار:
                    </span>

                    <div class="chips-wrap">
                        <button type="button" class="search-chip" data-chip="قهوه">
                            <i class="fa-solid fa-mug-hot"></i>
                            <span>قهوه</span>
                        </button>
                        <button type="button" class="search-chip" data-chip="چای">
                            <i class="fa-solid fa-mug-saucer"></i>
                            <span>چای</span>
                        </button>
                        <button type="button" class="search-chip" data-chip="شیک">
                            <i class="fa-solid fa-ice-cream"></i>
                            <span>شیک</span>
                        </button>
                        <button type="button" class="search-chip" data-chip="کیک">
                            <i class="fa-solid fa-cake-candles"></i>
                            <span>کیک</span>
                        </button>
                        <button type="button" class="search-chip" data-chip="نوشیدنی">
                            <i class="fa-solid fa-glass-water"></i>
                            <span>نوشیدنی</span>
                        </button>
                        <button type="button" class="search-chip" data-chip="صبحانه">
                            <i class="fa-solid fa-egg"></i>
                            <span>صبحانه</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="search-results-section" id="searchResultsSection">
            <div class="search-results-header">
                <div class="search-results-title">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>نتایج جستجو</span>
                </div>
                <span class="search-results-count" id="searchResultsCount"></span>
            </div>

            <div class="item-feed" id="searchResultsFeed"></div>

            <div class="search-empty" id="searchResultsEmpty">
                <div class="search-empty-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3>نتیجه‌ای پیدا نشد</h3>
                <p>عبارت دیگه‌ای رو امتحان کن یا فیلتر رو پاک کن تا همه‌ی آیتم‌ها رو ببینی.</p>
                <button type="button" class="search-empty-btn" id="searchResultsEmptyBtn">
                    <i class="fa-solid fa-rotate-right"></i>
                    پاک کردن جستجو
                </button>
            </div>
        </section>

        <div id="categoriesNormalView">
            <div class="page-hero">
                <div class="eyebrow-name" id="greetingText">سلام 👋</div>
                <h2>چی میل داری؟</h2>
                <p>از بین دسته‌بندی‌های زیر انتخاب کن تا آیتم‌های اون بخش رو ببینی.</p>
            </div>

            <div class="category-grid" id="categoryGrid">
                <?php foreach ($categories as $cat):
                    $cat_id = (int)$cat['id'];
                    $cat_count = $cat_counts[$cat_id] ?? 0;
                    $has_cat_img = !empty($cat['image']);
                    $has_logo = !empty($cafe['logo']);
                    $fallback_icon = category_icon($cat['title']);
                    ?>
                    <button class="category-card" type="button" data-cat="<?= $cat_id ?>">

                        <?php if ($has_cat_img): ?>
                            <div class="category-visual">
                                <img src="<?= htmlspecialchars(fix_img($cat['image'])) ?>"
                                     alt="<?= htmlspecialchars($cat['title']) ?>" loading="lazy"/>
                            </div>
                        <?php elseif ($has_logo): ?>
                            <div class="category-visual">
                                <img src="<?= htmlspecialchars(fix_img($cafe['logo'])) ?>"
                                     alt="<?= htmlspecialchars($cat['title']) ?>" loading="lazy"/>
                            </div>
                        <?php else: ?>
                            <div class="category-visual icon-only">
                                <i class="fa-solid <?= $fallback_icon ?>"></i>
                            </div>
                        <?php endif; ?>

                        <div>
                            <div class="category-title"><?= htmlspecialchars($cat['title']) ?></div>
                            <div class="category-count"><?= fa_num($cat_count) ?> آیتم</div>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>

            <footer class="cafe-info">
                <h3 class="cafe-info-title">درباره‌ی ما</h3>
                <div class="cafe-info-grid">
                    <?php if (!empty($cafe['address'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="info-text">
                                <span class="info-label">آدرس</span>
                                <span class="info-value"><?= htmlspecialchars($cafe['address']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($cafe['tel1'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="info-text">
                                <span class="info-label">تماس</span>
                                <span class="info-value ltr" dir="ltr"><?= htmlspecialchars($cafe['tel1']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($cafe['working_hours'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
                            <div class="info-text">
                                <span class="info-label">ساعات کاری</span>
                                <span class="info-value"><?= htmlspecialchars($cafe['working_hours']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($cafe['instagram'])): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-brands fa-instagram"></i></div>
                            <div class="info-text">
                                <span class="info-label">اینستاگرام</span>
                                <span class="info-value ltr"
                                      dir="ltr">@<?= htmlspecialchars(ltrim($cafe['instagram'], '@')) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </footer>
        </div>
    </main>

    <main class="page" id="itemsPage" style="display: none">
        <div class="items-header">
            <h2 id="itemsPageTitle">دسته‌بندی</h2>
            <span class="items-count" id="itemsPageCount"></span>
        </div>

        <div class="item-feed" id="itemFeed">
            <?php foreach ($menu_items as $item):
                $item_id = (int)$item['id'];
                $comments = $comments_by_item[$item_id] ?? [];
                $rating = item_rating($comments);
                $score = $rating['score'];
                $c_count = $rating['count'];
                $c_sum = $rating['sum'];
                $has_img = !empty($item['image']);
                $icon = category_icon($item['cat_title']);
                ?>
                <article class="post-card"
                         data-item="<?= $item_id ?>"
                         data-category="<?= (int)$item['category_id'] ?>"
                         data-price="<?= (int)$item['price'] ?>"
                         data-count="<?= $c_count ?>"
                         data-sum="<?= $c_sum ?>"
                         data-search-text="<?= htmlspecialchars(mb_strtolower($item['title'] . ' ' . ($item['recipe'] ?? '') . ' ' . $item['cat_title'], 'UTF-8')) ?>">

                    <div class="post-head">
                        <div class="avatar"><i class="fa-solid <?= $icon ?>"></i></div>
                        <div class="head-text">
                            <strong><?= htmlspecialchars($item['title']) ?></strong>
                            <span><?= htmlspecialchars($item['cat_title']) ?></span>
                        </div>
                    </div>

                    <div class="post-media">
                        <?php if ($has_img): ?>
                            <img src="<?= htmlspecialchars(fix_img($item['image'])) ?>"
                                 alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy"/>
                        <?php else: ?>
                            <div class="post-media-placeholder">
                                <i class="fa-solid <?= $icon ?>"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="post-actions">
                        <div class="post-actions-icons">
                            <button class="icon-btn like-btn" type="button" aria-label="پسندیدن">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                            <button class="icon-btn comment-jump" type="button" aria-label="نظرات">
                                <i class="fa-regular fa-comment"></i>
                            </button>
                            <span class="share-sep"></span>
                            <button class="icon-btn share-btn" type="button" data-platform="instagram"
                                    title="اینستاگرام">
                                <i class="fa-brands fa-instagram"></i>
                            </button>
                            <button class="icon-btn share-btn" type="button" data-platform="telegram" title="تلگرام">
                                <i class="fa-brands fa-telegram"></i>
                            </button>
                            <button class="icon-btn share-btn" type="button" data-platform="whatsapp" title="واتس‌اپ">
                                <i class="fa-brands fa-whatsapp"></i>
                            </button>
                        </div>

                        <div class="order-control">
                            <span class="order-price"><?= fa_num($item['price']) ?> تومان</span>
                            <button class="add-order-btn" type="button">
                                <i class="fa-solid fa-plus"></i>
                                <span>افزودن به سفارش</span>
                            </button>
                            <div class="qty-stepper">
                                <button class="qty-btn minus" type="button"><i class="fa-solid fa-minus"></i></button>
                                <span class="qty-value">۰</span>
                                <button class="qty-btn plus" type="button"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="post-body">
                        <div class="post-title"><?= htmlspecialchars($item['title']) ?></div>
                        <p class="post-desc"><?= htmlspecialchars($item['recipe'] ?: 'توضیحاتی برای این آیتم ثبت نشده است.') ?></p>

                        <div class="rating-block">
                            <div class="rating-summary">
                                <span class="stars-readonly">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-solid fa-star"
                                           style="opacity:<?= $i <= round($score) ? 1 : 0.28 ?>"></i>
                                    <?php endfor; ?>
                                </span>
                                <span><?= $c_count > 0 ? fa_float($score) . ' از ۵ (' . fa_num($c_count) . ' نظر)' : '— از ۵ (۰ نظر)' ?></span>
                            </div>
                            <div class="rate-form">
                                <span class="rate-label">امتیاز شما:</span>
                                <div class="star-input">
                                    <i class="fa-solid fa-star" data-value="1"></i>
                                    <i class="fa-solid fa-star" data-value="2"></i>
                                    <i class="fa-solid fa-star" data-value="3"></i>
                                    <i class="fa-solid fa-star" data-value="4"></i>
                                    <i class="fa-solid fa-star" data-value="5"></i>
                                </div>
                            </div>
                        </div>

                        <button class="comments-toggle" type="button">
                            <span class="toggle-label">مشاهده نظرات (<?= fa_num($c_count) ?>)</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>

                        <div class="comments-panel">
                            <form class="comment-form" data-item-id="<?= $item_id ?>">
                                <textarea placeholder="نظرت رو درباره‌ی این آیتم بنویس..." maxlength="500"
                                          required></textarea>
                                <div class="comment-form-row">
                                    <span class="rate-label">
                                        <i class="fa-solid fa-circle-info"></i>
                                        برای ثبت نظر، اول امتیازت رو با ستاره‌های بالا انتخاب کن.
                                    </span>
                                    <div class="comment-form-actions">
                                        <button type="submit" class="submit-comment-btn">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            ارسال نظر
                                        </button>
                                    </div>
                                </div>
                                <div class="comment-form-msg" style="display:none;"></div>
                            </form>

                            <div class="comment-list">
                                <?php if (!empty($comments)): ?>
                                    <?php foreach ($comments as $c):
                                        $initial = mb_substr($c['fullname'] ?: 'م', 0, 1, 'UTF-8');
                                        $cs = (int)$c['score'];
                                        $comment_id = (int)$c['id'];
                                        ?>
                                        <div class="comment-item" data-comment-id="<?= $comment_id ?>">
                                            <div class="avatar-sm"><?= htmlspecialchars($initial) ?></div>
                                            <div class="comment-body">
                                                <div class="comment-name-row">
                                                    <strong><?= htmlspecialchars($c['fullname']) ?></strong>
                                                    <span class="comment-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fa-solid fa-star"
                                                               style="opacity:<?= $i <= $cs ? 1 : 0.25 ?>"></i>
                                                        <?php endfor; ?>
                                                    </span>
                                                </div>
                                                <p><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<script src="<?= $PATHS['jquery'] ?>"></script>
<script src="<?= $PATHS['palib'] ?>"></script>
<script src="<?= $PATHS['bootstrap_js'] ?>"></script>
<script src="<?= $PATHS['script'] ?>"></script>
</body>
</html>