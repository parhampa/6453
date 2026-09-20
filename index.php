<?php
/* ================================================================
   ⚙️  تنظیمات
   ================================================================ */
$CAFE_ID = 1;

$PATHS = [
    'lib' => 'lib_include.php',
    'bootstrap_css' => 'bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css',
    'bootstrap_js' => 'bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js',
    'fontawesome' => 'fontawesome-free-6.7.2-web/css/all.min.css',
    'jquery' => 'lib/js/jquery.js',
    'palib' => 'lib/js/palib.js',
    'style' => 'style.css',
    'script' => 'script.js',
];

session_start();
include_once $PATHS['lib'];

$db = new database();
$db->connect();

$cid = (int)$CAFE_ID;

/* کافه */
$db->query("SELECT * FROM `cafes` WHERE `id` = $cid AND `status` = 1 LIMIT 1");
$cafe = mysqli_fetch_assoc($db->res);

if (!$cafe) {
    http_response_code(404);
    die('کافه مورد نظر یافت نشد یا غیرفعال است.');
}

/* دسته‌بندی‌ها */
$categories = [];
$db->connect()->query("SELECT * FROM `cafe_categories` WHERE `cafe_id` = $cid ORDER BY `id`");
while ($row = mysqli_fetch_assoc($db->res)) $categories[] = $row;

/* آیتم‌های منو */
$menu_items = [];
$db->connect()->query("
    SELECT mi.*, cc.title AS cat_title
    FROM `menu_items` mi
    JOIN `cafe_categories` cc ON cc.id = mi.category_id
    WHERE cc.cafe_id = $cid
    ORDER BY mi.category_id, mi.id
");
while ($row = mysqli_fetch_assoc($db->res)) $menu_items[] = $row;

/* نظرات تأیید‌شده */
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

/* تعداد آیتم هر دسته */
$cat_counts = [];
foreach ($menu_items as $mi) {
    $cat = (int)$mi['category_id'];
    $cat_counts[$cat] = ($cat_counts[$cat] ?? 0) + 1;
}

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
    while (strpos($path, '../') === 0) $path = substr($path, 3);
    return ltrim($path, '/');
}

function category_icon($title)
{
    $map = [
        'بار گرم' => 'fa-mug-hot', 'بار سرد' => 'fa-mug-saucer',
        'صبحانه' => 'fa-egg', 'ناهار' => 'fa-utensils',
        'شام' => 'fa-drumstick-bite', 'کیک' => 'fa-cake-candles',
        'نوشیدنی' => 'fa-lemon', 'دسر' => 'fa-ice-cream',
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

    <style>
        /* ═══════════ استایل فیلد شماره میز در مودال ═══════════ */
        .table-row {
            margin-bottom: 14px;
        }

        .table-row .table-hint {
            font-size: 11.5px;
            color: #8b7355;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .table-row .table-hint i {
            color: #c69c6d;
        }

        .table-number-input {
            width: 100%;
            padding: 11px 40px 11px 14px;
            border: 1.5px solid #e8e0d5;
            border-radius: 10px;
            background: #fbfcfd;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            color: #3e2723;
            text-align: center;
            letter-spacing: 2px;
            transition: all 0.2s ease;
        }

        .table-number-input:focus {
            border-color: #6d4c41;
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(109, 76, 65, 0.12);
        }

        .table-number-input::placeholder {
            color: #b8a99a;
            font-weight: normal;
            letter-spacing: normal;
        }

        /* ============ Order Row Placeholder ============ */
        .order-row-placeholder {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f5efe7, #e5c49d);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            flex-shrink: 0;
            opacity: 0.85;
        }
    </style>
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

                    <!-- ============ شماره میز (فیلد جدید) ============ -->
                    <div class="table-row">
                        <label for="tableNumberInput">شماره میز</label>
                        <div class="input-group-custom" style="position:relative;">
                            <i class="fa-solid fa-table-cells-large"
                               style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#8b7355; font-size:14px; pointer-events:none;"></i>
                            <input type="number" id="tableNumberInput" class="table-number-input" placeholder="مثلاً ۵"
                                   min="1" max="99" inputmode="numeric"/>
                        </div>
                        <div class="table-hint">
                            <i class="fa-solid fa-circle-info"></i>
                            شماره میز روی میزتون نوشته شده — لطفاً درست وارد کنید.
                        </div>
                    </div>

                    <div class="field-error" id="formError">لطفاً همه‌ی فیلدها رو با یک شماره تلفن و شماره میز معتبر پر
                        کن.
                    </div>

                    <button type="submit" class="continue-btn">ادامه</button>
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
                <div class="order-total-row">
                    <span>جمع کل</span>
                    <strong id="orderTotal">۰ تومان</strong>
                </div>
                <button class="send-order-btn" id="sendOrderBtn" type="button">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>ارسال به صندوق</span>
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
            <button class="order-pill" id="orderPillBtn" type="button">
                <i class="fa-solid fa-bag-shopping"></i>
                سفارش من
                <span class="count" id="orderCount">0</span>
            </button>
        </div>
    </header>

    <!-- صفحه دسته‌بندی‌ها -->
    <main class="page" id="categoriesPage">
        <section class="brand-hero">
            <div class="brand-logo">
                <?php if (!empty($cafe['logo'])): ?>
                    <img src="<?= htmlspecialchars(fix_img($cafe['logo'])) ?>"
                         alt="<?= htmlspecialchars($cafe['title']) ?>"/>
                <?php else: ?>
                    <i class="fa-solid fa-mug-hot" style="font-size:64px;color:#c69c6d;"></i>
                <?php endif; ?>
            </div>
            <h1 class="brand-hero-name"><?= htmlspecialchars($cafe['title']) ?></h1>
            <p class="brand-hero-slogan"><?= htmlspecialchars($cafe['slogan'] ?? '') ?></p>
        </section>

        <div class="page-hero">
            <div class="eyebrow-name" id="greetingText">سلام 👋</div>
            <h2>چی میل داری؟</h2>
            <p>از بین دسته‌بندی‌های زیر انتخاب کن تا آیتم‌های اون بخش رو ببینی.</p>
        </div>

        <div class="category-grid" id="categoryGrid">
            <?php foreach ($categories as $cat):
                $cat_id = (int)$cat['id'];
                $cat_count = $cat_counts[$cat_id] ?? 0;
                if ($cat_count === 0) continue;
                $has_img = !empty($cat['image']);
                ?>
                <button class="category-card" type="button" data-cat="<?= $cat_id ?>">
                    <?php if ($has_img): ?>
                        <div class="category-visual">
                            <img src="<?= htmlspecialchars(fix_img($cat['image'])) ?>"
                                 alt="<?= htmlspecialchars($cat['title']) ?>" loading="lazy"/>
                        </div>
                    <?php else: ?>
                        <div class="category-visual icon-only">
                            <i class="fa-solid <?= category_icon($cat['title']) ?>"></i>
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
                        <div class="info-text"><span class="info-label">آدرس</span><span
                                    class="info-value"><?= htmlspecialchars($cafe['address']) ?></span></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($cafe['tel1'])): ?>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="info-text"><span class="info-label">تماس</span><span class="info-value ltr"
                                                                                         dir="ltr"><?= htmlspecialchars($cafe['tel1']) ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($cafe['working_hours'])): ?>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
                        <div class="info-text"><span class="info-label">ساعات کاری</span><span
                                    class="info-value"><?= htmlspecialchars($cafe['working_hours']) ?></span></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($cafe['instagram'])): ?>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-brands fa-instagram"></i></div>
                        <div class="info-text"><span class="info-label">اینستاگرام</span><span class="info-value ltr"
                                                                                               dir="ltr">@<?= htmlspecialchars(ltrim($cafe['instagram'], '@')) ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </footer>
    </main>

    <!-- صفحه آیتم‌ها -->
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
                         data-sum="<?= $c_sum ?>">

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
                            <div style="background: linear-gradient(135deg, #f5efe7 0%, #e5c49d 100%); min-height: 280px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid <?= $icon ?>"
                                   style="font-size: 72px; color: #fff; opacity: 0.55;"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="post-actions">
                        <div class="post-actions-icons">
                            <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i
                                        class="fa-regular fa-heart"></i></button>
                            <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i
                                        class="fa-regular fa-comment"></i></button>
                            <span class="share-sep"></span>
                            <button class="icon-btn share-btn" type="button" data-platform="instagram"
                                    title="اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
                            <button class="icon-btn share-btn" type="button" data-platform="telegram" title="تلگرام"><i
                                        class="fa-brands fa-telegram"></i></button>
                            <button class="icon-btn share-btn" type="button" data-platform="whatsapp" title="واتس‌اپ"><i
                                        class="fa-brands fa-whatsapp"></i></button>
                        </div>

                        <div class="order-control">
                            <span class="order-price"><?= fa_num($item['price']) ?> تومان</span>
                            <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span>
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
                      <i class="fa-solid fa-star" style="opacity:<?= $i <= round($score) ? 1 : 0.28 ?>"></i>
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
                            <form class="comment-form">
                                <textarea placeholder="نظرت رو درباره‌ی این آیتم بنویس..." required></textarea>
                                <div class="comment-form-row">
                                    <span class="rate-label">امتیازی که ثبت کردی برای این نظر لحاظ می‌شه.</span>
                                    <button type="submit" class="submit-comment-btn">ارسال نظر</button>
                                </div>
                            </form>

                            <div class="comment-list">
                                <?php if (!empty($comments)): ?>
                                    <?php foreach ($comments as $c):
                                        $initial = mb_substr($c['fullname'] ?: 'م', 0, 1, 'UTF-8');
                                        $cs = (int)$c['score'];
                                        ?>
                                        <div class="comment-item">
                                            <div class="avatar-sm"><?= htmlspecialchars($initial) ?></div>
                                            <div class="comment-body">
                                                <div class="comment-name-row">
                                                    <strong><?= htmlspecialchars($c['fullname']) ?></strong>
                                                    <span class="comment-stars">
                          <?php for ($i = 1; $i <= 5; $i++): ?>
                              <i class="fa-solid fa-star" style="opacity:<?= $i <= $cs ? 1 : 0.25 ?>"></i>
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