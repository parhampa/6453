<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>منوی دیجیتال کافی‌شاپ</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- ================= مودال خوش‌آمدگویی ================= -->
  <div class="modal fade welcome-modal" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <div class="modal-icon"><i class="fa-solid fa-mug-saucer"></i></div>
          <h3 id="welcomeModalLabel">خوش اومدی 👋</h3>
          <p class="modal-sub">قبل از دیدن منو، یه لحظه اطلاعاتت رو وارد کن تا سفارش و نظراتت رو به اسم خودت ثبت کنیم.</p>

          <form id="welcomeForm" novalidate>
            <div class="name-row">
              <div class="form-field">
                <label for="firstNameInput">نام</label>
                <div class="input-group-custom">
                  <i class="fa-regular fa-user"></i>
                  <input type="text" id="firstNameInput" placeholder="مثلاً سارا" autocomplete="given-name" />
                </div>
              </div>
              <div class="form-field">
                <label for="lastNameInput">نام خانوادگی</label>
                <div class="input-group-custom">
                  <i class="fa-regular fa-user"></i>
                  <input type="text" id="lastNameInput" placeholder="مثلاً احمدی" autocomplete="family-name" />
                </div>
              </div>
            </div>

            <div class="form-field">
              <label for="phoneInput">شماره تلفن</label>
              <div class="input-group-custom">
                <i class="fa-solid fa-phone"></i>
                <input type="tel" id="phoneInput" placeholder="09xxxxxxxxx" inputmode="numeric" autocomplete="tel" />
              </div>
              <div class="field-error" id="formError">لطفاً همه‌ی فیلدها رو با یک شماره تلفن معتبر پر کن.</div>
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
            <i class="fa-solid fa-paper-plane"></i>
            <span>ارسال به گارسون</span>
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
            <h1>کافه روزمهر</h1>
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

    <!-- ==================== صفحه‌ی دسته‌بندی‌ها ==================== -->
    <main class="page" id="categoriesPage">

      <section class="brand-hero">
        <div class="brand-logo">
          <img src="https://picsum.photos/seed/rozmehr-logo/240/240" alt="لوگوی کافه روزمهر" />
        </div>
        <h1 class="brand-hero-name">کافه روزمهر</h1>
        <p class="brand-hero-slogan">هر فنجان، یک لحظه‌ی آرامش</p>
      </section>

      <div class="page-hero">
        <div class="eyebrow-name" id="greetingText">سلام 👋</div>
        <h2>چی میل داری؟</h2>
        <p>از بین دسته‌بندی‌های زیر انتخاب کن تا آیتم‌های اون بخش رو ببینی.</p>
      </div>

      <div class="category-grid" id="categoryGrid">

        <!-- بار گرم -->
        <button class="category-card" type="button" data-cat="hot-bar">
          <div class="category-visual icon-only"><i class="fa-solid fa-mug-hot"></i></div>
          <div>
            <div class="category-title">بار گرم</div>
            <div class="category-count">۳ آیتم</div>
          </div>
        </button>

        <!-- بار سرد -->
        <button class="category-card" type="button" data-cat="cold-bar">
          <div class="category-visual"><img src="https://picsum.photos/seed/cold-bar-cafe/400/400" alt="بار سرد" loading="lazy" /></div>
          <div>
            <div class="category-title">بار سرد</div>
            <div class="category-count">۳ آیتم</div>
          </div>
        </button>

        <!-- صبحانه -->
        <button class="category-card" type="button" data-cat="breakfast">
          <div class="category-visual"><img src="https://picsum.photos/seed/breakfast-cafe/400/400" alt="صبحانه" loading="lazy" /></div>
          <div>
            <div class="category-title">صبحانه</div>
            <div class="category-count">۲ آیتم</div>
          </div>
        </button>

        <!-- ناهار -->
        <button class="category-card" type="button" data-cat="lunch">
          <div class="category-visual icon-only"><i class="fa-solid fa-utensils"></i></div>
          <div>
            <div class="category-title">ناهار</div>
            <div class="category-count">۲ آیتم</div>
          </div>
        </button>

        <!-- شام -->
        <button class="category-card" type="button" data-cat="dinner">
          <div class="category-visual"><img src="https://picsum.photos/seed/dinner-cafe/400/400" alt="شام" loading="lazy" /></div>
          <div>
            <div class="category-title">شام</div>
            <div class="category-count">۱ آیتم</div>
          </div>
        </button>

        <!-- کیک -->
        <button class="category-card" type="button" data-cat="cake">
          <div class="category-visual"><img src="https://picsum.photos/seed/cake-cafe/400/400" alt="کیک" loading="lazy" /></div>
          <div>
            <div class="category-title">کیک</div>
            <div class="category-count">۲ آیتم</div>
          </div>
        </button>

        <!-- نوشیدنی بدون قهوه -->
        <button class="category-card" type="button" data-cat="non-coffee">
          <div class="category-visual icon-only"><i class="fa-solid fa-lemon"></i></div>
          <div>
            <div class="category-title">نوشیدنی‌های بدون قهوه</div>
            <div class="category-count">۲ آیتم</div>
          </div>
        </button>

        <!-- دسر -->
        <button class="category-card" type="button" data-cat="dessert">
          <div class="category-visual"><img src="https://picsum.photos/seed/dessert-cafe/400/400" alt="دسر" loading="lazy" /></div>
          <div>
            <div class="category-title">دسر</div>
            <div class="category-count">۱ آیتم</div>
          </div>
        </button>

      </div>

      <!-- اطلاعات کافه -->
      <footer class="cafe-info">
        <h3 class="cafe-info-title">درباره‌ی ما</h3>
        <div class="cafe-info-grid">
          <div class="info-item">
            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="info-text">
              <span class="info-label">آدرس</span>
              <span class="info-value">تهران، خیابان ولیعصر، پلاک ۱۲۳</span>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="info-text">
              <span class="info-label">تماس</span>
              <span class="info-value ltr" dir="ltr">021-12345678</span>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
            <div class="info-text">
              <span class="info-label">ساعات کاری</span>
              <span class="info-value">هر روز، ۸ صبح تا ۱۲ شب</span>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon"><i class="fa-brands fa-instagram"></i></div>
            <div class="info-text">
              <span class="info-label">اینستاگرام</span>
              <span class="info-value ltr" dir="ltr">@cafe.rozmehr</span>
            </div>
          </div>
        </div>
      </footer>
    </main>

    <!-- ==================== صفحه‌ی آیتم‌ها ==================== -->
    <main class="page" id="itemsPage" style="display: none">
      <div class="items-header">
        <h2 id="itemsPageTitle">دسته‌بندی</h2>
        <span class="items-count" id="itemsPageCount"></span>
      </div>

      <div class="item-feed" id="itemFeed">

        <!-- ==================== آیتم hb-1 ==================== -->
        <article class="post-card" data-item="hb-1" data-category="hot-bar" data-price="75000" data-count="3" data-sum="14">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-mug-hot"></i></div>
            <div class="head-text">
              <strong>اسپرسو دبل</strong>
              <span>بار گرم</span>
            </div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/espresso-shot/600/600" alt="اسپرسو دبل" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام" title="اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام" title="تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ" title="واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۷۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper" role="group" aria-label="تعداد سفارش">
                <button class="qty-btn minus" type="button" aria-label="کاهش تعداد"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش تعداد"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">اسپرسو دبل</div>
            <p class="post-desc">دو شات اسپرسوی غلیظ با کرمای طلایی، برای شروعی پرانرژی. دانه‌های تازه‌آسیاب، بدون شکر پیشنهاد می‌شه.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </span>
                <span>۴.۷ از ۵ (۳ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۲)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">ن</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>نیما رستمی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                    </div>
                    <p>طعمش فوق‌العادس، دقیقاً همون تلخی که دوست دارم.</p>
                  </div>
                </div>
                <div class="comment-item">
                  <div class="avatar-sm">ا</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>الهام کریمی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star" style="opacity:0.25"></i></span>
                    </div>
                    <p>خوب بود ولی یکم داغ‌تر از حد معمول اومد.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم hb-2 ==================== -->
        <article class="post-card" data-item="hb-2" data-category="hot-bar" data-price="98000" data-count="4" data-sum="19">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-mug-hot"></i></div>
            <div class="head-text"><strong>لاته وانیلی</strong><span>بار گرم</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/vanilla-latte/600/600" alt="لاته وانیلی" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۹۸٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">لاته وانیلی</div>
            <p class="post-desc">اسپرسو با شیر بخارداده و شربت وانیل خانگی. نرم، شیرین و مناسب عصرهای آروم.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۴.۸ از ۵ (۴ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۱)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">پ</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>پارسا احمدی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                    </div>
                    <p>بهترین لاته‌ای بود که تو یه کافه خوردم.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم hb-3 ==================== -->
        <article class="post-card" data-item="hb-3" data-category="hot-bar" data-price="105000" data-count="1" data-sum="4">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-mug-hot"></i></div>
            <div class="head-text"><strong>موکای کلاسیک</strong><span>بار گرم</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/classic-mocha/600/600" alt="موکای کلاسیک" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۰۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">موکای کلاسیک</div>
            <p class="post-desc">ترکیب اسپرسو، شکلات تلخ بلژیکی و شیر گرم، تاپ شده با خامه.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star" style="opacity:0.28"></i></span>
                <span>۴.۰ از ۵ (۱ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم cb-1 ==================== -->
        <article class="post-card" data-item="cb-1" data-category="cold-bar" data-price="82000" data-count="2" data-sum="9">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-mug-saucer"></i></div>
            <div class="head-text"><strong>آیس آمریکانو</strong><span>بار سرد</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/iced-americano/600/600" alt="آیس آمریکانو" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۸۲٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">آیس آمریکانو</div>
            <p class="post-desc">اسپرسو روی یخ با آب سرد، ساده و خنک‌کننده برای روزهای گرم.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۴.۵ از ۵ (۲ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۱)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">س</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>سینا مرادی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                    </div>
                    <p>تابستون بهترین انتخابه.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم cb-2 ==================== -->
        <article class="post-card" data-item="cb-2" data-category="cold-bar" data-price="112000" data-count="2" data-sum="10">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-mug-saucer"></i></div>
            <div class="head-text"><strong>کلد برو با شیر</strong><span>بار سرد</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/cold-brew-milk/600/600" alt="کلد برو با شیر" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۱۲٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">کلد برو با شیر</div>
            <p class="post-desc">کلدبرو دوازده‌ساعته با شیر یخ‌زده و کمی شربت کارامل.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۵.۰ از ۵ (۲ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم cb-3 ==================== -->
        <article class="post-card" data-item="cb-3" data-category="cold-bar" data-price="118000" data-count="3" data-sum="12">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-mug-saucer"></i></div>
            <div class="head-text"><strong>فراپه شکلاتی</strong><span>بار سرد</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/choco-frappe/600/600" alt="فراپه شکلاتی" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۱۸٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">فراپه شکلاتی</div>
            <p class="post-desc">شیک یخ‌زده با شکلات، اسپرسو و کرم روی آن. دسرگونه و پرانرژی.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star" style="opacity:0.28"></i></span>
                <span>۴.۰ از ۵ (۳ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۱)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">م</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>مریم صادقی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star" style="opacity:0.25"></i></span>
                    </div>
                    <p>شیرینیش یکم زیاد بود برای من.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم bf-1 ==================== -->
        <article class="post-card" data-item="bf-1" data-category="breakfast" data-price="145000" data-count="3" data-sum="13">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-egg"></i></div>
            <div class="head-text"><strong>تخم‌مرغ عسلی با نان تست</strong><span>صبحانه</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/eggs-toast/600/600" alt="تخم‌مرغ عسلی با نان تست" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۴۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">تخم‌مرغ عسلی با نان تست</div>
            <p class="post-desc">دو عدد تخم‌مرغ عسلی، نان تست کره‌ای و گوجه‌ی کبابی در کنارش.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star" style="opacity:0.28"></i></span>
                <span>۴.۳ از ۵ (۳ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم bf-2 ==================== -->
        <article class="post-card" data-item="bf-2" data-category="breakfast" data-price="165000" data-count="4" data-sum="19">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-egg"></i></div>
            <div class="head-text"><strong>پنکیک با عسل و توت‌فرنگی</strong><span>صبحانه</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/pancake-berries/600/600" alt="پنکیک با عسل و توت‌فرنگی" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۶۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">پنکیک با عسل و توت‌فرنگی</div>
            <p class="post-desc">سه لایه پنکیک نرم، عسل طبیعی و توت‌فرنگی تازه.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۴.۸ از ۵ (۴ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۱)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">آ</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>آیدا نوری</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                    </div>
                    <p>خیلی نرم و خوشمزه بود، حتماً دوباره سفارش می‌دم.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم ln-1 ==================== -->
        <article class="post-card" data-item="ln-1" data-category="lunch" data-price="210000" data-count="2" data-sum="9">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-utensils"></i></div>
            <div class="head-text"><strong>پاستا آلفردو مرغ</strong><span>ناهار</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/chicken-alfredo/600/600" alt="پاستا آلفردو مرغ" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۲۱۰٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">پاستا آلفردو مرغ</div>
            <p class="post-desc">پاستای پنه با سس خامه‌ای، مرغ گریل‌شده و پارمزان تازه رنده‌شده.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۴.۵ از ۵ (۲ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم ln-2 ==================== -->
        <article class="post-card" data-item="ln-2" data-category="lunch" data-price="195000" data-count="1" data-sum="5">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-utensils"></i></div>
            <div class="head-text"><strong>سالاد سزار با میگو</strong><span>ناهار</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/caesar-shrimp/600/600" alt="سالاد سزار با میگو" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۹۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">سالاد سزار با میگو</div>
            <p class="post-desc">کاهوی تازه، میگوی سرخ‌شده، پنیر پارمزان و سس سزار خانگی.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۵.۰ از ۵ (۱ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم dn-1 ==================== -->
        <article class="post-card" data-item="dn-1" data-category="dinner" data-price="265000" data-count="3" data-sum="13">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-drumstick-bite"></i></div>
            <div class="head-text"><strong>استیک مرغ با سبزیجات گریل</strong><span>شام</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/chicken-steak/600/600" alt="استیک مرغ با سبزیجات گریل" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۲۶۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">استیک مرغ با سبزیجات گریل</div>
            <p class="post-desc">سینه‌مرغ گریل‌شده با سس مخصوص و سبزیجات فصل، مناسب شام سبک.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star" style="opacity:0.28"></i></span>
                <span>۴.۳ از ۵ (۳ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۱)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">ب</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>بهنام یوسفی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star" style="opacity:0.25"></i></span>
                    </div>
                    <p>طعم خوبی داشت، پرس‌ش هم مناسب بود.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم ck-1 ==================== -->
        <article class="post-card" data-item="ck-1" data-category="cake" data-price="135000" data-count="3" data-sum="15">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-cake-candles"></i></div>
            <div class="head-text"><strong>کیک شکلاتی لاوا</strong><span>کیک</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/lava-cake/600/600" alt="کیک شکلاتی لاوا" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۳۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">کیک شکلاتی لاوا</div>
            <p class="post-desc">کیک شکلاتی گرم با مغز مذاب، سرو شده با یک اسکوپ بستنی وانیلی.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۵.۰ از ۵ (۳ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۱)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">ر</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>رویا فرهادی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                    </div>
                    <p>مغزش عالیه، حتماً امتحان کنید.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم ck-2 ==================== -->
        <article class="post-card" data-item="ck-2" data-category="cake" data-price="128000" data-count="2" data-sum="9">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-cake-candles"></i></div>
            <div class="head-text"><strong>چیزکیک نیویورکی</strong><span>کیک</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/newyork-cheesecake/600/600" alt="چیزکیک نیویورکی" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۲۸٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">چیزکیک نیویورکی</div>
            <p class="post-desc">چیزکیک کلاسیک با بیسکوییت له‌شده و سس توت قرمز.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۴.۵ از ۵ (۲ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم nc-1 ==================== -->
        <article class="post-card" data-item="nc-1" data-category="non-coffee" data-price="88000" data-count="2" data-sum="9">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-lemon"></i></div>
            <div class="head-text"><strong>لیموناد نعنا</strong><span>نوشیدنی‌های بدون قهوه</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/mint-lemonade/600/600" alt="لیموناد نعنا" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۸۸٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">لیموناد نعنا</div>
            <p class="post-desc">لیموی تازه، نعنای خنک و کمی سودا؛ نوشیدنی بدون کافئین برای روزهای گرم.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۴.۵ از ۵ (۲ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم nc-2 ==================== -->
        <article class="post-card" data-item="nc-2" data-category="non-coffee" data-price="95000" data-count="3" data-sum="14">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-lemon"></i></div>
            <div class="head-text"><strong>هات چاکلت</strong><span>نوشیدنی‌های بدون قهوه</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/hot-chocolate/600/600" alt="هات چاکلت" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۹۵٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">هات چاکلت</div>
            <p class="post-desc">شکلات تلخ ذوب‌شده با شیر گرم و کمی دارچین.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۴.۷ از ۵ (۳ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۱)</span>
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
                <div class="comment-item">
                  <div class="avatar-sm">ک</div>
                  <div class="comment-body">
                    <div class="comment-name-row">
                      <strong>کیانا رضایی</strong>
                      <span class="comment-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                    </div>
                    <p>خیلی خوش‌طعمه، دارچینش نکته‌ی خوبیه.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- ==================== آیتم ds-1 ==================== -->
        <article class="post-card" data-item="ds-1" data-category="dessert" data-price="132000" data-count="2" data-sum="10">
          <div class="post-head">
            <div class="avatar"><i class="fa-solid fa-ice-cream"></i></div>
            <div class="head-text"><strong>تیرامیسو</strong><span>دسر</span></div>
          </div>

          <div class="post-media"><img src="https://picsum.photos/seed/tiramisu-cafe/600/600" alt="تیرامیسو" loading="lazy" /></div>

          <div class="post-actions">
            <div class="post-actions-icons">
              <button class="icon-btn like-btn" type="button" aria-label="پسندیدن"><i class="fa-regular fa-heart"></i></button>
              <button class="icon-btn comment-jump" type="button" aria-label="نظرات"><i class="fa-regular fa-comment"></i></button>
              <span class="share-sep"></span>
              <button class="icon-btn share-btn" type="button" data-platform="instagram" aria-label="اشتراک در اینستاگرام"><i class="fa-brands fa-instagram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="telegram" aria-label="اشتراک در تلگرام"><i class="fa-brands fa-telegram"></i></button>
              <button class="icon-btn share-btn" type="button" data-platform="whatsapp" aria-label="اشتراک در واتس‌اپ"><i class="fa-brands fa-whatsapp"></i></button>
            </div>

            <div class="order-control">
              <span class="order-price">۱۳۲٬۰۰۰ تومان</span>
              <button class="add-order-btn" type="button"><i class="fa-solid fa-plus"></i><span>افزودن به سفارش</span></button>
              <div class="qty-stepper">
                <button class="qty-btn minus" type="button" aria-label="کاهش"><i class="fa-solid fa-minus"></i></button>
                <span class="qty-value">۰</span>
                <button class="qty-btn plus" type="button" aria-label="افزایش"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>

          <div class="post-body">
            <div class="post-title">تیرامیسو</div>
            <p class="post-desc">لایه‌های بیسکوییت آغشته به قهوه و کرم ماسکارپونه، پودر شده با کاکائو.</p>

            <div class="rating-block">
              <div class="rating-summary">
                <span class="stars-readonly"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                <span>۵.۰ از ۵ (۲ نظر)</span>
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
              <span class="toggle-label">مشاهده نظرات (۰)</span>
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
                <div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>
              </div>
            </div>
          </div>
        </article>

      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>