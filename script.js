"use strict";

/* ==========================================================================
   تنظیمات
   ========================================================================== */
const CAFE_ID = (() => {
    const meta = document.querySelector('meta[name="cafe-id"]');
    return meta ? parseInt(meta.content, 10) : 0;
})();

const AJAX_URL = "order_send.php";

/* ==========================================================================
   وضعیت برنامه
   ========================================================================== */
const state = {
    user: null,
    tableNumber: null,
    orderQuantities: {},
    myRatings: {},
};

/* ==========================================================================
   ابزارها
   ========================================================================== */
function formatPrice(value) {
    return Number(value).toLocaleString("fa-IR") + " تومان";
}

function renderStaticStars(avg) {
    let html = "";
    for (let i = 1; i <= 5; i++) {
        html += `<i class="fa-solid fa-star" style="opacity:${i <= Math.round(avg) ? 1 : 0.28}"></i>`;
    }
    return html;
}

function el(html) {
    const w = document.createElement("div");
    w.innerHTML = html.trim();
    return w.firstElementChild;
}

function escapeHtml(str) {
    const d = document.createElement("div");
    d.textContent = str;
    return d.innerHTML;
}

function findItemCard(id) {
    if (id === null || id === undefined || id === "") return null;
    return document.querySelector(`.post-card[data-item="${id}"]`);
}

/* ✅ تابع ضد-خطا برای گرفتن اطلاعات کارت */
function getCardInfo(card) {
    if (!card || !card.dataset) return null;

    const titleEl = card.querySelector(".post-title");
    const imgEl = card.querySelector(".post-media img");

    return {
        id: card.dataset.item || "",
        price: Number(card.dataset.price || 0),
        title: titleEl ? titleEl.textContent.trim() : "",
        image: imgEl ? imgEl.src : "",
    };
}

function getCardCount(card) {
    return Number(card.dataset.count || 0);
}

function getCardSum(card) {
    return Number(card.dataset.sum || 0);
}

function getOrderCount() {
    return Object.values(state.orderQuantities).reduce((s, q) => s + q, 0);
}

function computeOrderTotal() {
    let total = 0;
    for (const [id, qty] of Object.entries(state.orderQuantities)) {
        const card = findItemCard(id);
        if (card) total += Number(card.dataset.price || 0) * qty;
    }
    return total;
}

function updateOrderCount() {
    const elCount = document.getElementById("orderCount");
    if (elCount) elCount.textContent = getOrderCount().toLocaleString("fa-IR");
}

function updateCardRatingDisplay(card) {
    if (!card) return;
    const starsEl = card.querySelector(".stars-readonly");
    const textEl = card.querySelector(".rating-summary span:last-child");
    if (!starsEl || !textEl) return;

    const count = getCardCount(card);
    const sum = getCardSum(card);
    const avg = count ? sum / count : 0;

    starsEl.innerHTML = renderStaticStars(avg);
    textEl.textContent = count
        ? `${avg.toFixed(1)} از ۵ (${count.toLocaleString("fa-IR")} نظر)`
        : "— از ۵ (۰ نظر)";
}

function updateCardQtyDisplay(card) {
    if (!card) return;
    const control = card.querySelector(".order-control");
    const val = card.querySelector(".qty-value");
    if (!control || !val) return;

    const qty = state.orderQuantities[card.dataset.item] || 0;
    if (qty > 0) {
        control.classList.add("has-qty");
        val.textContent = qty.toLocaleString("fa-IR");
    } else {
        control.classList.remove("has-qty");
        val.textContent = "۰";
    }
}

function showToast(message, icon = "fa-circle-check") {
    let toast = document.getElementById("appToast");
    if (!toast) {
        toast = el(`<div class="app-toast" id="appToast"></div>`);
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid ${icon}"></i><span>${message}</span>`;
    toast.classList.add("is-visible");
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.remove("is-visible"), 3200);
}

/* ==========================================================================
   ذخیره‌سازی محلی
   ========================================================================== */
const STORAGE_KEY = "cafe_customer_" + CAFE_ID;

function saveUserToStorage() {
    if (!state.user) return;
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            firstName: state.user.firstName,
            lastName: state.user.lastName,
            phone: state.user.phone,
            tableNumber: state.tableNumber,
        }));
    } catch (e) { /* ignore */
    }
}

function loadUserFromStorage() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return null;
        const data = JSON.parse(raw);
        if (!data.phone || !data.firstName || !data.tableNumber) return null;
        return data;
    } catch (e) {
        return null;
    }
}

/* ==========================================================================
   اشتراک‌گذاری
   ========================================================================== */
function shareItem(card, platform) {
    const info = getCardInfo(card);
    if (!info) return;

    const descEl = card.querySelector(".post-desc");
    const desc = descEl ? descEl.textContent.trim() : "";
    const url = `${location.origin}${location.pathname}#item-${info.id}`;
    const text = `${info.title}\n${desc}`;

    if (platform === "telegram") {
        window.open(
            `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`,
            "_blank", "noopener"
        );
        return;
    }

    if (platform === "whatsapp") {
        window.open(
            `https://wa.me/?text=${encodeURIComponent(text + "\n" + url)}`,
            "_blank", "noopener"
        );
        return;
    }

    if (platform === "instagram") {
        if (navigator.share) {
            navigator.share({title: info.title, text, url}).catch(() => {
            });
        } else if (navigator.clipboard) {
            navigator.clipboard.writeText(`${text}\n${url}`).then(() => {
                showToast("متن کپی شد. اینستاگرام باز می‌شه...", "fa-instagram");
                setTimeout(() => window.open("https://www.instagram.com/", "_blank", "noopener"), 600);
            }).catch(() => window.open("https://www.instagram.com/", "_blank", "noopener"));
        } else {
            window.open("https://www.instagram.com/", "_blank", "noopener");
        }
    }
}

/* ==========================================================================
   مودال خوش‌آمدگویی
   ========================================================================== */
const welcomeModalEl = document.getElementById("welcomeModal");
const welcomeModal = welcomeModalEl ? new bootstrap.Modal(welcomeModalEl) : null;
const welcomeForm = document.getElementById("welcomeForm");
const formError = document.getElementById("formError");

if (welcomeModalEl) {
    welcomeModalEl.addEventListener("show.bs.modal", () => {
        document.body.classList.add("modal-open-lock");
    });
    welcomeModalEl.addEventListener("hidden.bs.modal", () => {
        document.body.classList.remove("modal-open-lock");
    });
}

if (welcomeForm) {
    welcomeForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const firstName = document.getElementById("firstNameInput").value.trim();
        const lastName = document.getElementById("lastNameInput").value.trim();
        const phone = document.getElementById("phoneInput").value.trim();
        const tableInput = document.getElementById("tableNumberInput");
        const tableNumber = tableInput ? parseInt(tableInput.value, 10) : NaN;

        const phoneValid = /^0?9\d{9}$/.test(phone);
        const tableValid = !isNaN(tableNumber) && tableNumber >= 1 && tableNumber <= 99;

        if (!firstName || !lastName || !phoneValid || !tableValid) {
            if (formError) formError.classList.add("is-visible");
            return;
        }

        if (formError) formError.classList.remove("is-visible");

        state.user = {firstName, lastName, phone};
        state.tableNumber = tableNumber;

        saveUserToStorage();

        const greeting = document.getElementById("greetingText");
        if (greeting) greeting.textContent = `سلام ${firstName} جان 👋`;

        if (welcomeModal) welcomeModal.hide();
    });
}

/* ==========================================================================
   مودال سفارش
   ========================================================================== */
const orderModalEl = document.getElementById("orderModal");
const orderModal = orderModalEl ? new bootstrap.Modal(orderModalEl) : null;
const orderListEl = document.getElementById("orderList");
const orderTotalEl = document.getElementById("orderTotal");
const sendOrderBtn = document.getElementById("sendOrderBtn");

/**
 * ✅ حذف آیتم‌هایی از سبد که کارت‌شون توی صفحه نیست
 * این کار جلوی خطای getCardInfo رو می‌گیره
 */
function cleanInvalidCartItems() {
    Object.keys(state.orderQuantities).forEach((id) => {
        if (!findItemCard(id)) {
            delete state.orderQuantities[id];
        }
    });
}

function renderOrderModal() {
    if (!orderListEl) return;

    /* ✅ پاکسازی قبل از رندر */
    cleanInvalidCartItems();

    const entries = Object.entries(state.orderQuantities);
    orderListEl.innerHTML = "";

    /* ── سبد خالی ── */
    if (!entries.length) {
        orderListEl.appendChild(el(`
      <div class="order-empty">
        <div class="order-empty-icon"><i class="fa-solid fa-bag-shopping"></i></div>
        <p>هنوز چیزی به سفارشت اضافه نکردی.</p>
        <span>از منو یه چیز خوشمزه انتخاب کن 😋</span>
      </div>
    `));
        if (orderTotalEl) orderTotalEl.textContent = formatPrice(0);
        if (sendOrderBtn) sendOrderBtn.disabled = true;
        return;
    }

    if (sendOrderBtn) sendOrderBtn.disabled = false;

    /* ── هر آیتم ── */
    entries.forEach(([id, qty]) => {
        const card = findItemCard(id);
        if (!card) return;

        const info = getCardInfo(card);
        if (!info || !info.title) return; /* ✅ ضد-خطا */

        const row = el(`
      <div class="order-row">
        ${info.image
            ? `<img src="${info.image}" alt="${escapeHtml(info.title)}" loading="lazy" />`
            : `<div class="order-row-placeholder"><i class="fa-solid fa-mug-hot"></i></div>`
        }
        <div class="order-row-info">
          <strong>${escapeHtml(info.title)}</strong>
          <span>${formatPrice(info.price)} × ${qty.toLocaleString("fa-IR")}</span>
        </div>
        <div class="order-row-total">${formatPrice(info.price * qty)}</div>
        <button class="order-row-remove" type="button" aria-label="حذف">
          <i class="fa-solid fa-trash-can"></i>
        </button>
      </div>
    `);

        row.querySelector(".order-row-remove").addEventListener("click", () => {
            delete state.orderQuantities[id];
            const c = findItemCard(id);
            if (c) updateCardQtyDisplay(c);
            updateOrderCount();
            renderOrderModal();
        });

        orderListEl.appendChild(row);
    });

    if (orderTotalEl) orderTotalEl.textContent = formatPrice(computeOrderTotal());
}

const orderPillBtn = document.getElementById("orderPillBtn");
if (orderPillBtn) {
    orderPillBtn.addEventListener("click", () => {
        if (!state.user || !state.user.phone || !state.tableNumber) {
            if (welcomeModal) welcomeModal.show();
            return;
        }
        renderOrderModal();
        if (orderModal) orderModal.show();
    });
}

/* ==========================================================================
   ارسال سفارش
   ========================================================================== */
if (sendOrderBtn) {
    sendOrderBtn.addEventListener("click", async () => {
        if (!getOrderCount()) return;

        if (!state.user || !state.user.firstName || !state.user.phone || !state.tableNumber) {
            if (welcomeModal) welcomeModal.show();
            if (formError) formError.classList.add("is-visible");
            return;
        }

        const items = Object.entries(state.orderQuantities).map(([id, qty]) => ({
            id: Number(id),
            qty: Number(qty),
        }));

        if (!items.length) return;

        const originalHtml = sendOrderBtn.innerHTML;
        sendOrderBtn.disabled = true;
        sendOrderBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال ارسال...';

        try {
            const response = await fetch(AJAX_URL, {
                method: "POST",
                headers: {"Content-Type": "application/json; charset=utf-8"},
                body: JSON.stringify({
                    cafe_id: CAFE_ID,
                    table: state.tableNumber,
                    first_name: state.user.firstName,
                    last_name: state.user.lastName,
                    phone: state.user.phone,
                    items: items,
                }),
            });

            const res = await response.json();

            if (res && res.status === 1) {
                state.orderQuantities = {};
                updateOrderCount();
                document.querySelectorAll(".post-card").forEach(updateCardQtyDisplay);
                if (orderModal) orderModal.hide();

                showToast(
                    `سفارش شما ثبت شد ✅ شماره پیگیری: ${res.invoice_id.toLocaleString("fa-IR")}`,
                    "fa-circle-check"
                );
            } else {
                showToast(res && res.msg ? res.msg : "خطا در ثبت سفارش", "fa-triangle-exclamation");
            }
        } catch (err) {
            showToast("خطای ارتباط با سرور. دوباره تلاش کنید.", "fa-wifi");
        } finally {
            sendOrderBtn.disabled = false;
            sendOrderBtn.innerHTML = originalHtml;
        }
    });
}

/* ==========================================================================
   ناوبری
   ========================================================================== */
const categoriesPage = document.getElementById("categoriesPage");
const itemsPage = document.getElementById("itemsPage");
const backBtn = document.getElementById("backBtn");

function goToCategories() {
    if (itemsPage) itemsPage.style.display = "none";
    if (categoriesPage) categoriesPage.style.display = "block";
    if (backBtn) backBtn.classList.remove("is-visible");
    window.scrollTo({top: 0, behavior: "smooth"});
}

function goToItems(categoryId) {
    const catBtn = document.querySelector(`.category-card[data-cat="${categoryId}"]`);
    const catTitleEl = catBtn ? catBtn.querySelector(".category-title") : null;
    const catTitle = catTitleEl ? catTitleEl.textContent.trim() : "";

    let count = 0;
    document.querySelectorAll(".post-card").forEach((card) => {
        const match = card.dataset.category === String(categoryId);
        card.style.display = match ? "" : "none";
        if (match) count++;
    });

    const titleEl = document.getElementById("itemsPageTitle");
    const cntEl = document.getElementById("itemsPageCount");
    if (titleEl) titleEl.textContent = catTitle;
    if (cntEl) cntEl.textContent = `${count.toLocaleString("fa-IR")} آیتم`;

    if (categoriesPage) categoriesPage.style.display = "none";
    if (itemsPage) itemsPage.style.display = "block";
    if (backBtn) backBtn.classList.add("is-visible");
    window.scrollTo({top: 0, behavior: "smooth"});
}

if (backBtn) backBtn.addEventListener("click", goToCategories);

/* ==========================================================================
   راه‌اندازی کارت‌ها
   ========================================================================== */
function initCategories() {
    document.querySelectorAll(".category-card").forEach((btn) => {
        btn.addEventListener("click", () => goToItems(btn.dataset.cat));
    });
}

function wireItemCard(card) {
    const itemId = card.dataset.item;

    /* پسندیدن */
    const likeBtn = card.querySelector(".like-btn");
    if (likeBtn) {
        likeBtn.addEventListener("click", () => {
            likeBtn.classList.toggle("liked");
            const i = likeBtn.querySelector("i");
            if (i) {
                i.classList.toggle("fa-regular");
                i.classList.toggle("fa-solid");
            }
        });
    }

    /* اشتراک‌گذاری */
    card.querySelectorAll(".share-btn").forEach((btn) => {
        btn.addEventListener("click", () => shareItem(card, btn.dataset.platform));
    });

    /* تعداد سفارش */
    const control = card.querySelector(".order-control");
    const addBtn = control ? control.querySelector(".add-order-btn") : null;
    const minusBtn = control ? control.querySelector(".qty-btn.minus") : null;
    const plusBtn = control ? control.querySelector(".qty-btn.plus") : null;

    const setQty = (n) => {
        if (n <= 0) delete state.orderQuantities[itemId];
        else state.orderQuantities[itemId] = n;
        updateCardQtyDisplay(card);
        updateOrderCount();
    };

    if (addBtn) addBtn.addEventListener("click", () => setQty(1));
    if (minusBtn) minusBtn.addEventListener("click", () => setQty((state.orderQuantities[itemId] || 0) - 1));
    if (plusBtn) plusBtn.addEventListener("click", () => setQty((state.orderQuantities[itemId] || 0) + 1));

    /* امتیازدهی */
    const stars = [...card.querySelectorAll(".star-input i")];
    const initialMyRating = state.myRatings[itemId] || 0;
    stars.forEach((s) => s.classList.toggle("active", Number(s.dataset.value) <= initialMyRating));

    stars.forEach((star) => {
        star.addEventListener("click", () => {
            const value = Number(star.dataset.value);
            let count = getCardCount(card);
            let sum = getCardSum(card);
            const had = itemId in state.myRatings;
            const prev = state.myRatings[itemId];

            if (had) sum = sum - prev + value;
            else {
                count += 1;
                sum += value;
            }

            card.dataset.count = count;
            card.dataset.sum = sum;
            state.myRatings[itemId] = value;

            stars.forEach((s) => s.classList.toggle("active", Number(s.dataset.value) <= value));
            updateCardRatingDisplay(card);
        });
    });

    /* نظرات */
    const toggleBtn = card.querySelector(".comments-toggle");
    const panel = card.querySelector(".comments-panel");
    const jumpBtn = card.querySelector(".comment-jump");

    const openPanel = () => {
        if (panel) panel.classList.add("is-open");
        if (toggleBtn) toggleBtn.classList.add("is-open");
    };

    if (toggleBtn && panel) {
        toggleBtn.addEventListener("click", () => {
            const willOpen = !panel.classList.contains("is-open");
            panel.classList.toggle("is-open", willOpen);
            toggleBtn.classList.toggle("is-open", willOpen);
        });
    }

    if (jumpBtn && panel) {
        jumpBtn.addEventListener("click", () => {
            openPanel();
            panel.scrollIntoView({behavior: "smooth", block: "center"});
        });
    }

    /* ثبت نظر جدید */
    const form = card.querySelector(".comment-form");
    if (form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            const ta = form.querySelector("textarea");
            if (!ta) return;
            const text = ta.value.trim();
            if (!text) return;

            const name = state.user ? `${state.user.firstName} ${state.user.lastName}` : "مهمان";
            const starsCount = state.myRatings[itemId] || 0;
            const starsHtml = starsCount
                ? Array.from({length: 5})
                    .map((_, i) => `<i class="fa-solid fa-star" style="opacity:${i < starsCount ? 1 : 0.25}"></i>`)
                    .join("")
                : "";

            const commentEl = el(`
        <div class="comment-item">
          <div class="avatar-sm">${escapeHtml(name.trim().charAt(0) || "?")}</div>
          <div class="comment-body">
            <div class="comment-name-row">
              <strong>${escapeHtml(name)}</strong>
              ${starsHtml ? `<span class="comment-stars">${starsHtml}</span>` : ""}
            </div>
            <p>${escapeHtml(text)}</p>
          </div>
        </div>
      `);

            const empty = card.querySelector(".comment-empty");
            if (empty) empty.remove();

            const list = card.querySelector(".comment-list");
            if (list) list.appendChild(commentEl);

            ta.value = "";

            const newCount = card.querySelectorAll(".comment-list .comment-item").length;
            const label = toggleBtn ? toggleBtn.querySelector(".toggle-label") : null;
            if (label) {
                label.textContent = `مشاهده نظرات (${newCount.toLocaleString("fa-IR")})`;
            }

            openPanel();
        });
    }
}

function initItems() {
    document.querySelectorAll(".post-card").forEach((card) => {
        updateCardQtyDisplay(card);
        updateCardRatingDisplay(card);
        wireItemCard(card);
    });
}

/* ==========================================================================
   شروع
   ========================================================================== */
window.addEventListener("DOMContentLoaded", () => {
    const saved = loadUserFromStorage();
    if (saved) {
        state.user = {
            firstName: saved.firstName,
            lastName: saved.lastName,
            phone: saved.phone,
        };
        state.tableNumber = saved.tableNumber;

        const greeting = document.getElementById("greetingText");
        if (greeting) greeting.textContent = `سلام ${saved.firstName} جان 👋`;

        if (welcomeModalEl) welcomeModalEl.style.display = "none";
    } else {
        if (welcomeModal) welcomeModal.show();
    }

    updateOrderCount();
    initCategories();
    initItems();
});