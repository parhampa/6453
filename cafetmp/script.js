"use strict";

/* ==========================================================================
   تنظیمات
   ========================================================================== */
const CAFE_ID = (() => {
    const meta = document.querySelector('meta[name="cafe-id"]');
    return meta ? parseInt(meta.content, 10) : 0;
})();

const AJAX_URL = "order_send.php";
const BIRTHDATE_URL = "customer_birthdate.php";
const ORDERS_URL = "customer_orders.php";
const COMMENT_SEND_URL = "comment_send.php";
const CUSTOMER_COMMENTS_URL = "customer_comments.php";

/* ==========================================================================
   وضعیت برنامه
   ========================================================================== */
const state = {
    user: null,
    tableNumber: null,
    orderQuantities: {},
    myRatings: {},
    myComments: {},
    editingItemId: null,
    currentDiscount: 0,
    birthdateChecked: false,
    isSearchMode: false,
    searchQuery: "",
    currentCategoryId: null,
};

/* ==========================================================================
   ابزارها
   ========================================================================== */
function formatPrice(value) {
    return Number(value).toLocaleString("fa-IR") + " تومان";
}

function faNum(n) {
    n = Math.round(Number(n) || 0);
    return n.toLocaleString("fa-IR");
}

function parseFaNum(str) {
    const map = {
        "۰": "0", "۱": "1", "۲": "2", "۳": "3", "۴": "4",
        "۵": "5", "۶": "6", "۷": "7", "۸": "8", "۹": "9"
    };
    let out = "";
    str = String(str);
    for (let i = 0; i < str.length; i++) {
        const ch = str.charAt(i);
        if (map[ch] !== undefined) out += map[ch];
        else if (ch >= "0" && ch <= "9") out += ch;
    }
    return parseInt(out, 10) || 0;
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

function normalizePersian(str) {
    if (!str) return "";
    return String(str)
        .toLowerCase()
        .replace(/ي/g, "ی")
        .replace(/ك/g, "ک")
        .replace(/ة/g, "ه")
        .replace(/[\u064B-\u0652\u0670]/g, "")
        .replace(/[\u200B-\u200F\u202A-\u202E]/g, "")
        .replace(/\u200C/g, " ")
        .replace(/[ًٌٍَُِّْ]/g, "")
        .replace(/[^\p{L}\p{N}\s]/gu, " ")
        .replace(/\s+/g, " ")
        .trim();
}

function findItemCard(id) {
    if (id === null || id === undefined || id === "") return null;
    return document.querySelector(`#itemFeed .post-card[data-item="${id}"], #searchResultsFeed .post-card[data-item="${id}"]`);
}

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
   مودال پیام اختصاصی
   ========================================================================== */
const appMessageModalEl = document.getElementById("appMessageModal");
let appMessageModal = null;

if (appMessageModalEl) {
    appMessageModal = new bootstrap.Modal(appMessageModalEl, {
        backdrop: "static",
        keyboard: true
    });
}

const messageIconMap = {
    info: {icon: "fa-circle-info", cls: "type-info"},
    success: {icon: "fa-circle-check", cls: "type-success"},
    warning: {icon: "fa-triangle-exclamation", cls: "type-warning"},
    error: {icon: "fa-circle-xmark", cls: "type-error"},
    question: {icon: "fa-circle-question", cls: "type-warning"},
    delete: {icon: "fa-trash-can", cls: "type-error"}
};

function showMessage(opts) {
    return new Promise((resolve) => {
        if (!appMessageModal) {
            console.log("[message]", opts);
            resolve(true);
            return;
        }

        const o = Object.assign({
            type: "info",
            title: "پیام",
            text: "",
            okText: "تأیید",
            cancelText: "انصراف",
            showCancel: false,
            danger: false
        }, opts || {});

        const info = messageIconMap[o.type] || messageIconMap.info;

        const iconEl = document.getElementById("appMessageIcon");
        const titleEl = document.getElementById("appMessageModalLabel");
        const textEl = document.getElementById("appMessageText");
        const okBtn = document.getElementById("appMessageOkBtn");
        const cancelEl = document.getElementById("appMessageCancelBtn");

        if (iconEl) {
            iconEl.className = "app-message-icon " + info.cls;
            iconEl.innerHTML = `<i class="fa-solid ${info.icon}"></i>`;
        }

        if (titleEl) titleEl.textContent = o.title;
        if (textEl) textEl.textContent = o.text;

        if (okBtn) {
            okBtn.textContent = o.okText;
            okBtn.classList.toggle("app-message-btn-danger", !!o.danger);
        }

        if (cancelEl) {
            cancelEl.textContent = o.cancelText;
            cancelEl.style.display = o.showCancel ? "inline-flex" : "none";
        }

        const newOk = okBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newOk, okBtn);

        const newCancel = cancelEl.cloneNode(true);
        cancelEl.parentNode.replaceChild(newCancel, cancelEl);

        newOk.addEventListener("click", () => {
            appMessageModal.hide();
            resolve(true);
        });

        newCancel.addEventListener("click", () => {
            appMessageModal.hide();
            resolve(false);
        });

        let resolved = false;
        const onHidden = () => {
            if (!resolved) {
                resolved = true;
                resolve(false);
            }
            appMessageModalEl.removeEventListener("hidden.bs.modal", onHidden);
        };
        appMessageModalEl.addEventListener("hidden.bs.modal", onHidden);

        appMessageModal.show();
    });
}

function askConfirm(opts) {
    return showMessage(Object.assign({
        showCancel: true,
        type: "question",
        title: "تأیید عملیات",
        okText: "بله",
        cancelText: "انصراف"
    }, opts || {}));
}

function showFormMessage(formEl, text, type = "success") {
    const msgEl = formEl ? formEl.querySelector(".comment-form-msg") : null;
    if (!msgEl) return;
    msgEl.textContent = text;
    msgEl.className = "comment-form-msg is-" + type;
    msgEl.style.display = "block";
    clearTimeout(msgEl._timer);
    msgEl._timer = setTimeout(() => {
        msgEl.style.display = "none";
    }, 4000);
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
        if (!data.phone || !data.firstName) return null;
        return data;
    } catch (e) {
        return null;
    }
}

function getStoredUser() {
    const main = loadUserFromStorage();
    if (main) {
        return {
            phone: main.phone,
            firstName: main.firstName || "",
            lastName: main.lastName || "",
        };
    }

    try {
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (!key) continue;
            const raw = localStorage.getItem(key);
            if (!raw) continue;
            let obj;
            try {
                obj = JSON.parse(raw);
            } catch (e) {
                continue;
            }
            if (!obj || typeof obj !== "object") continue;

            let phone = obj.phone || obj.tel || obj.mobile || obj.phoneNumber || "";
            phone = String(phone).replace(/\D/g, "");
            if (!/^09\d{9}$/.test(phone)) continue;

            let fn = obj.firstName || obj.first_name || obj.name || obj.fname || "";
            let ln = obj.lastName || obj.last_name || obj.family || obj.lname || obj.family_name || "";

            if (!ln && fn && fn.indexOf(" ") > 0) {
                const parts = fn.trim().split(/\s+/);
                fn = parts.shift();
                ln = parts.join(" ");
            }

            return {
                phone: phone,
                firstName: String(fn).trim(),
                lastName: String(ln).trim(),
            };
        }
    } catch (e) { /* ignore */
    }
    return null;
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

        const phoneValid = /^0?9\d{9}$/.test(phone);

        if (!firstName || !lastName || !phoneValid) {
            if (formError) formError.classList.add("is-visible");
            return;
        }

        if (formError) formError.classList.remove("is-visible");

        state.user = {firstName, lastName, phone};
        saveUserToStorage();

        const greeting = document.getElementById("greetingText");
        if (greeting) greeting.textContent = `سلام ${firstName} جان 👋`;

        if (welcomeModal) welcomeModal.hide();

        loadMyComments();
        fetchDiscount();
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

if (orderModalEl) {
    orderModalEl.addEventListener("show.bs.modal", () => {
        document.body.classList.add("modal-open-lock");
    });
    orderModalEl.addEventListener("hidden.bs.modal", () => {
        document.body.classList.remove("modal-open-lock");
    });
}

function cleanInvalidCartItems() {
    Object.keys(state.orderQuantities).forEach((id) => {
        if (!findItemCard(id)) {
            delete state.orderQuantities[id];
        }
    });
}

function renderOrderModal() {
    if (!orderListEl) return;

    cleanInvalidCartItems();

    const entries = Object.entries(state.orderQuantities);
    orderListEl.innerHTML = "";

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
        updateDiscountDisplay();
        return;
    }

    if (sendOrderBtn) sendOrderBtn.disabled = false;

    entries.forEach(([id, qty]) => {
        const card = findItemCard(id);
        if (!card) return;

        const info = getCardInfo(card);
        if (!info || !info.title) return;

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
            const allCards = document.querySelectorAll(`.post-card[data-item="${id}"]`);
            allCards.forEach(updateCardQtyDisplay);
            updateOrderCount();
            renderOrderModal();
        });

        orderListEl.appendChild(row);
    });

    if (orderTotalEl) orderTotalEl.textContent = formatPrice(computeOrderTotal());
    updateDiscountDisplay();
}

const orderPillBtn = document.getElementById("orderPillBtn");
if (orderPillBtn) {
    orderPillBtn.addEventListener("click", () => {
        if (!state.user || !state.user.phone) {
            if (welcomeModal) welcomeModal.show();
            return;
        }
        renderOrderModal();
        if (orderModal) orderModal.show();
    });
}

/* ==========================================================================
   تخفیف
   ========================================================================== */
function fetchDiscount() {
    const user = getStoredUser();
    if (!user) return;

    fetch(ORDERS_URL, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            action: "discount",
            phone: user.phone,
            cafe_id: CAFE_ID
        })
    })
        .then((r) => r.json())
        .then((res) => {
            if (res && res.status === 1) {
                state.currentDiscount = parseInt(res.discount_percent, 10) || 0;
                updateDiscountDisplay();
            }
        })
        .catch(() => {
        });
}

function updateDiscountDisplay() {
    const totalEl = document.getElementById("orderTotal");
    const discRow = document.getElementById("orderDiscountRow");
    const discVal = document.getElementById("orderDiscountValue");
    const finalRow = document.getElementById("orderFinalRow");
    const finalTotal = document.getElementById("orderFinalTotal");

    if (!totalEl || !discRow) return;

    if (state.currentDiscount <= 0) {
        discRow.style.display = "none";
        finalRow.style.display = "none";
        return;
    }

    const subtotal = parseFaNum(totalEl.textContent);
    const discAmount = subtotal * state.currentDiscount / 100;
    const finalAmount = subtotal - discAmount;

    discVal.textContent = `${faNum(state.currentDiscount)}٪ (${formatPrice(discAmount)})`;
    finalTotal.textContent = formatPrice(finalAmount);

    discRow.style.display = "flex";
    finalRow.style.display = "flex";
}

/* ==========================================================================
   ارسال سفارش
   ========================================================================== */
if (sendOrderBtn) {
    sendOrderBtn.addEventListener("click", async () => {
        if (!getOrderCount()) return;

        if (!state.user || !state.user.firstName || !state.user.phone) {
            if (welcomeModal) welcomeModal.show();
            if (formError) formError.classList.add("is-visible");
            return;
        }

        const tableInput = document.getElementById("orderTableNumber");
        const tableError = document.getElementById("orderTableError");
        const tableNumber = tableInput ? parseInt(tableInput.value, 10) : NaN;

        if (!tableNumber || tableNumber < 1 || tableNumber > 99) {
            if (tableError) tableError.classList.add("is-visible");
            if (tableInput) tableInput.focus();
            return;
        }
        if (tableError) tableError.classList.remove("is-visible");

        state.tableNumber = tableNumber;

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
                    table: tableNumber,
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

                saveUserToStorage();

                if (orderModal) orderModal.hide();

                showMessage({
                    type: "success",
                    title: "سفارش ثبت شد ✅",
                    text: `شماره پیگیری شما: ${Number(res.invoice_id).toLocaleString("fa-IR")}`,
                    okText: "عالیه",
                    showCancel: false,
                });
            } else {
                showMessage({
                    type: "error",
                    title: "خطا در ثبت سفارش",
                    text: (res && res.msg) ? res.msg : "مشکلی پیش آمد. دوباره تلاش کن.",
                    okText: "متوجه شدم",
                    showCancel: false,
                });
            }
        } catch (err) {
            showMessage({
                type: "error",
                title: "خطای ارتباط",
                text: "ارتباط با سرور برقرار نشد. اتصال اینترنتت رو بررسی کن و دوباره تلاش کن.",
                okText: "باشه",
                showCancel: false,
            });
        } finally {
            sendOrderBtn.disabled = false;
            sendOrderBtn.innerHTML = originalHtml;
        }
    });
}

const orderTableInput = document.getElementById("orderTableNumber");
if (orderTableInput) {
    orderTableInput.addEventListener("input", () => {
        const tableError = document.getElementById("orderTableError");
        if (tableError) tableError.classList.remove("is-visible");
    });
}

/* ==========================================================================
   مودال تاریخ تولد
   ========================================================================== */
function showBirthdateModal() {
    const elModal = document.getElementById("birthdateModal");
    if (!elModal) return;
    const modal = bootstrap.Modal.getOrCreateInstance(elModal);
    modal.show();
}

function checkBirthdate() {
    if (state.birthdateChecked) return;

    const user = getStoredUser();
    if (!user) return;

    state.birthdateChecked = true;

    fetch(BIRTHDATE_URL, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            action: "check",
            phone: user.phone,
            first_name: user.firstName,
            last_name: user.lastName
        })
    })
        .then((r) => r.json())
        .then((res) => {
            if (res && res.status === 1 && res.has_birthdate === 0) {
                showBirthdateModal();
            }
        })
        .catch(() => {
            state.birthdateChecked = false;
        });
}

function bindBirthdateForm() {
    const form = document.getElementById("birthdateForm");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const user = getStoredUser();
        if (!user) return;

        const jy = parseInt(document.getElementById("bd_year").value, 10);
        const jm = parseInt(document.getElementById("bd_month").value, 10);
        const jd = parseInt(document.getElementById("bd_day").value, 10);
        const err = document.getElementById("bdError");

        if (!jy || !jm || !jd) {
            err.textContent = "لطفاً تاریخ تولدت رو کامل انتخاب کن.";
            err.style.display = "block";
            return;
        }
        err.style.display = "none";

        fetch(BIRTHDATE_URL, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                action: "save",
                phone: user.phone,
                first_name: user.firstName,
                last_name: user.lastName,
                jy: jy, jm: jm, jd: jd
            })
        })
            .then((r) => r.json())
            .then((res) => {
                if (res && res.status === 1) {
                    const modalEl = document.getElementById("birthdateModal");
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    showToast("تاریخ تولدت ثبت شد 🎂", "fa-cake-candles");
                } else {
                    err.textContent = (res && res.msg) ? res.msg : "خطا در ثبت تاریخ تولد";
                    err.style.display = "block";
                }
            })
            .catch(() => {
                err.textContent = "خطای ارتباط با سرور";
                err.style.display = "block";
            });
    });

    const skip = document.getElementById("bdSkipBtn");
    if (skip) {
        skip.addEventListener("click", () => {
            const modalEl = document.getElementById("birthdateModal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    }
}

/* ==========================================================================
   نظرات و امتیازها
   ========================================================================== */

function loadMyComments() {
    const user = getStoredUser();
    if (!user) return;

    fetch(CUSTOMER_COMMENTS_URL, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            phone: user.phone,
            cafe_id: CAFE_ID
        })
    })
        .then((r) => r.json())
        .then((res) => {
            if (!res || res.status !== 1 || !res.comments) return;

            Object.keys(res.comments).forEach((itemId) => {
                const info = res.comments[itemId];
                const score = parseInt(info.my_score, 10) || 0;
                const text = info.my_comment_text || "";
                const commentId = parseInt(info.my_comment_id, 10) || 0;

                if (score > 0) state.myRatings[itemId] = score;
                if (commentId > 0) {
                    state.myComments[itemId] = {
                        id: commentId,
                        score: score,
                        text: text,
                    };
                }

                const cards = document.querySelectorAll(`.post-card[data-item="${itemId}"]`);
                cards.forEach((card) => {
                    const stars = card.querySelectorAll(".star-input i");
                    stars.forEach((s) => {
                        s.classList.toggle("active", Number(s.dataset.value) <= score);
                    });

                    const commentItem = card.querySelector(
                        `.comment-item[data-comment-id="${commentId}"]`
                    );

                    if (commentItem) {
                        markCommentAsMine(card, commentItem, commentId);
                    }
                });
            });
        })
        .catch(() => {
        });
}

function markCommentAsMine(card, commentItem, commentId) {
    commentItem.classList.add("is-mine");

    const nameRow = commentItem.querySelector(".comment-name-row strong");
    if (nameRow && !nameRow.querySelector(".comment-badge")) {
        nameRow.innerHTML += ' <span class="comment-badge">(شما)</span>';
    }

    const body = commentItem.querySelector(".comment-body");
    if (body && !body.querySelector(".comment-actions")) {
        const actionsEl = el(`
          <div class="comment-actions">
            <button type="button" class="comment-action-btn edit-btn">
              <i class="fa-solid fa-pen"></i> ویرایش
            </button>
            <button type="button" class="comment-action-btn delete-btn">
              <i class="fa-solid fa-trash-can"></i> حذف
            </button>
          </div>
        `);
        actionsEl.querySelector(".edit-btn").addEventListener("click", () => startEditComment(card, commentId));
        actionsEl.querySelector(".delete-btn").addEventListener("click", () => deleteComment(card, commentId));
        body.appendChild(actionsEl);
    }
}

function startEditComment(card, commentId) {
    if (!card) return;

    const itemId = card.dataset.item;
    const myComment = state.myComments[itemId];
    if (!myComment) return;

    state.editingItemId = itemId;

    const form = card.querySelector(".comment-form");
    const textarea = form.querySelector("textarea");
    const submitBtn = form.querySelector(".submit-comment-btn");

    textarea.value = myComment.text;
    state.myRatings[itemId] = myComment.score;

    const stars = card.querySelectorAll(".star-input i");
    stars.forEach((s) => {
        s.classList.toggle("active", Number(s.dataset.value) <= myComment.score);
    });

    form.classList.add("is-editing");
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> ذخیره تغییرات';
    }

    if (!form.querySelector(".comment-form-cancel-btn")) {
        const actionsEl = form.querySelector(".comment-form-actions");
        const cancelBtn = el(`
          <button type="button" class="comment-form-cancel-btn">
            <i class="fa-solid fa-xmark"></i> لغو ویرایش
          </button>
        `);
        cancelBtn.addEventListener("click", () => cancelEditComment(card));
        if (actionsEl) {
            actionsEl.appendChild(cancelBtn);
        }
    }

    const panel = card.querySelector(".comments-panel");
    const toggleBtn = card.querySelector(".comments-toggle");
    if (panel) panel.classList.add("is-open");
    if (toggleBtn) toggleBtn.classList.add("is-open");

    form.scrollIntoView({behavior: "smooth", block: "center"});
    textarea.focus();
}

function cancelEditComment(card) {
    if (!card) return;

    const itemId = card.dataset.item;
    state.editingItemId = null;

    const form = card.querySelector(".comment-form");
    const textarea = form.querySelector("textarea");
    const submitBtn = form.querySelector(".submit-comment-btn");

    textarea.value = "";
    form.classList.remove("is-editing");

    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> ارسال نظر';
    }

    const msgEl = form.querySelector(".comment-form-msg");
    if (msgEl) msgEl.style.display = "none";

    const myRating = state.myRatings[itemId] || 0;
    const stars = card.querySelectorAll(".star-input i");
    stars.forEach((s) => {
        s.classList.toggle("active", Number(s.dataset.value) <= myRating);
    });
}

function deleteComment(card, commentId) {
    if (!card || !commentId) return;

    askConfirm({
        type: "delete",
        title: "حذف نظر",
        text: "مطمئنی می‌خوای نظرت رو برای این آیتم حذف کنی؟ این کار قابل بازگشت نیست.",
        okText: "بله، حذف کن",
        cancelText: "انصراف",
        danger: true,
    }).then((confirmed) => {
        if (!confirmed) return;

        const user = getStoredUser();
        if (!user) return;

        const itemId = card.dataset.item;

        fetch(COMMENT_SEND_URL, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                action: "delete",
                phone: user.phone,
                menu_item_id: Number(itemId)
            })
        })
            .then((r) => r.json())
            .then((res) => {
                if (!res || res.status !== 1) {
                    showMessage({
                        type: "error",
                        title: "خطا در حذف",
                        text: (res && res.msg) ? res.msg : "مشکلی در حذف نظر پیش آمد.",
                        okText: "باشه",
                        showCancel: false,
                    });
                    return;
                }

                delete state.myComments[itemId];
                delete state.myRatings[itemId];

                const allCards = document.querySelectorAll(`.post-card[data-item="${itemId}"]`);
                allCards.forEach((c) => {
                    const item = c.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
                    if (item) item.remove();

                    c.dataset.count = res.new_count;
                    c.dataset.sum = res.new_sum;
                    updateCardRatingDisplay(c);

                    const toggleBtn = c.querySelector(".comments-toggle");
                    const label = toggleBtn ? toggleBtn.querySelector(".toggle-label") : null;
                    if (label) {
                        label.textContent = `مشاهده نظرات (${Number(res.new_count).toLocaleString("fa-IR")})`;
                    }

                    const list = c.querySelector(".comment-list");
                    if (list && list.querySelectorAll(".comment-item").length === 0) {
                        list.innerHTML = '<div class="comment-empty">هنوز نظری ثبت نشده؛ اولین نفر باش!</div>';
                    }

                    const stars = c.querySelectorAll(".star-input i");
                    stars.forEach((s) => s.classList.remove("active"));

                    if (state.editingItemId === itemId) {
                        cancelEditComment(c);
                    }
                });

                showToast("نظرت حذف شد", "fa-trash-can");
            })
            .catch(() => {
                showMessage({
                    type: "error",
                    title: "خطای ارتباط",
                    text: "ارتباط با سرور برقرار نشد. دوباره تلاش کن.",
                    okText: "باشه",
                    showCancel: false,
                });
            });
    });
}

function submitComment(card, score, text) {
    const user = getStoredUser();
    if (!user) {
        showMessage({
            type: "warning",
            title: "اطلاعات ناقص",
            text: "برای ثبت نظر، اول اطلاعات خودت رو کامل کن.",
            okText: "باشه",
            showCancel: false,
        });
        if (welcomeModal) welcomeModal.show();
        return Promise.reject("no-user");
    }

    const itemId = card.dataset.item;

    return fetch(COMMENT_SEND_URL, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            action: "save",
            phone: user.phone,
            menu_item_id: Number(itemId),
            score: score,
            comment_text: text
        })
    })
        .then((r) => r.json());
}

function applyCommentToCard(card, comment, newCount, newSum, isUpdate, commentId) {
    if (!card) return;

    card.dataset.count = newCount;
    card.dataset.sum = newSum;
    updateCardRatingDisplay(card);

    const list = card.querySelector(".comment-list");
    if (!list) return;

    const empty = list.querySelector(".comment-empty");
    if (empty) empty.remove();

    if (isUpdate) {
        const oldMine = list.querySelector(".comment-item.is-mine");
        if (oldMine) oldMine.remove();
    }

    const initials = (comment.fullname || "?").trim().charAt(0) || "?";
    const starsHtml = Array.from({length: 5})
        .map((_, i) => `<i class="fa-solid fa-star" style="opacity:${i < comment.score ? 1 : 0.25}"></i>`)
        .join("");

    const newEl = el(`
      <div class="comment-item is-mine" data-comment-id="${commentId}">
        <div class="avatar-sm">${escapeHtml(initials)}</div>
        <div class="comment-body">
          <div class="comment-name-row">
            <strong>${escapeHtml(comment.fullname)} <span class="comment-badge">(شما)</span></strong>
            <span class="comment-stars">${starsHtml}</span>
          </div>
          <p>${escapeHtml(comment.comment_text)}</p>
          <div class="comment-actions">
            <button type="button" class="comment-action-btn edit-btn">
              <i class="fa-solid fa-pen"></i> ویرایش
            </button>
            <button type="button" class="comment-action-btn delete-btn">
              <i class="fa-solid fa-trash-can"></i> حذف
            </button>
          </div>
        </div>
      </div>
    `);

    newEl.querySelector(".edit-btn").addEventListener("click", () => startEditComment(card, commentId));
    newEl.querySelector(".delete-btn").addEventListener("click", () => deleteComment(card, commentId));

    list.prepend(newEl);

    const toggleBtn = card.querySelector(".comments-toggle");
    const label = toggleBtn ? toggleBtn.querySelector(".toggle-label") : null;
    if (label) {
        label.textContent = `مشاهده نظرات (${Number(newCount).toLocaleString("fa-IR")})`;
    }
}

function wireItemCard(card) {
    const itemId = card.dataset.item;

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

    card.querySelectorAll(".share-btn").forEach((btn) => {
        btn.addEventListener("click", () => shareItem(card, btn.dataset.platform));
    });

    const control = card.querySelector(".order-control");
    const addBtn = control ? control.querySelector(".add-order-btn") : null;
    const minusBtn = control ? control.querySelector(".qty-btn.minus") : null;
    const plusBtn = control ? control.querySelector(".qty-btn.plus") : null;

    const setQty = (n) => {
        if (n <= 0) delete state.orderQuantities[itemId];
        else state.orderQuantities[itemId] = n;

        const allCards = document.querySelectorAll(`.post-card[data-item="${itemId}"]`);
        allCards.forEach(updateCardQtyDisplay);
        updateOrderCount();
    };

    if (addBtn) addBtn.addEventListener("click", () => setQty(1));
    if (minusBtn) minusBtn.addEventListener("click", () => setQty((state.orderQuantities[itemId] || 0) - 1));
    if (plusBtn) plusBtn.addEventListener("click", () => setQty((state.orderQuantities[itemId] || 0) + 1));

    const stars = [...card.querySelectorAll(".star-input i")];
    const initialMyRating = state.myRatings[itemId] || 0;
    stars.forEach((s) => s.classList.toggle("active", Number(s.dataset.value) <= initialMyRating));

    stars.forEach((star) => {
        star.addEventListener("click", () => {
            const value = Number(star.dataset.value);
            state.myRatings[itemId] = value;
            stars.forEach((s) => s.classList.toggle("active", Number(s.dataset.value) <= value));
        });
    });

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

    const form = card.querySelector(".comment-form");
    if (form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();

            const ta = form.querySelector("textarea");
            if (!ta) return;
            const text = ta.value.trim();

            if (!text) {
                showFormMessage(form, "لطفاً متن نظرت رو بنویس.", "error");
                return;
            }

            const score = state.myRatings[itemId] || 0;
            if (score < 1 || score > 5) {
                showFormMessage(form, "لطفاً اول امتیازت رو با ستاره‌ها انتخاب کن.", "error");
                return;
            }

            const submitBtn = form.querySelector(".submit-comment-btn");
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال ارسال...';

            submitComment(card, score, text)
                .then((res) => {
                    if (res && res.status === 1) {
                        const isUpdate = res.action === "update";
                        const commentId = res.comment_id || 0;

                        state.myComments[itemId] = {
                            id: commentId,
                            score: score,
                            text: text,
                        };

                        applyCommentToCard(
                            card,
                            res.comment,
                            res.new_count,
                            res.new_sum,
                            isUpdate,
                            commentId
                        );

                        ta.value = "";
                        cancelEditComment(card);

                        showFormMessage(
                            form,
                            isUpdate ? "نظرت با موفقیت به‌روزرسانی شد ✅" : "نظرت با موفقیت ثبت شد ✅",
                            "success"
                        );

                        showToast(
                            isUpdate ? "نظرت به‌روزرسانی شد" : "نظرت ثبت شد، ممنون!",
                            "fa-circle-check"
                        );

                        openPanel();
                    } else {
                        const msg = (res && res.msg) ? res.msg : "خطا در ثبت نظر";
                        showFormMessage(form, msg, "error");
                        showToast(msg, "fa-triangle-exclamation");
                    }
                })
                .catch((err) => {
                    if (err === "no-user") return;
                    showFormMessage(form, "خطای ارتباط با سرور. دوباره تلاش کن.", "error");
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    if (state.editingItemId === itemId) {
                        submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> ذخیره تغییرات';
                    } else {
                        submitBtn.innerHTML = originalHtml;
                    }
                });
        });
    }
}

function initItems() {
    document.querySelectorAll("#itemFeed .post-card").forEach((card) => {
        updateCardQtyDisplay(card);
        updateCardRatingDisplay(card);
        wireItemCard(card);
    });
}

/* ==========================================================================
   جستجو
   ========================================================================== */
const searchBox = document.getElementById("searchBox");
const searchInput = document.getElementById("searchInput");
const searchClearBtn = document.getElementById("searchClearBtn");
const searchSection = document.getElementById("searchSection");
const searchChipsEl = document.getElementById("searchChips");
const searchResultsSection = document.getElementById("searchResultsSection");
const searchResultsFeed = document.getElementById("searchResultsFeed");
const searchResultsCount = document.getElementById("searchResultsCount");
const searchResultsEmpty = document.getElementById("searchResultsEmpty");
const searchResultsEmptyBtn = document.getElementById("searchResultsEmptyBtn");
const categoriesNormalView = document.getElementById("categoriesNormalView");

/* کش عرض سرچ‌بار برای نگه داشتن حالت sticky */
let searchSectionTop = 0;
let searchObserver = null;

/**
 * محاسبه‌ی موقعیت اولیه‌ی سرچ‌بار برای فعال‌سازی sticky
 */
function updateSearchSectionPosition() {
    if (!searchSection) return;

    /* اگه استیکی بود، موقتاً برش داریم تا موقعیت طبیعی حساب بشه */
    const wasSticky = searchSection.classList.contains("is-sticky");
    if (wasSticky) {
        searchSection.classList.remove("is-sticky");
    }

    const rect = searchSection.getBoundingClientRect();
    searchSectionTop = rect.top + window.pageYOffset;

    if (wasSticky) {
        searchSection.classList.add("is-sticky");
    }
}

/**
 * فعال/غیرفعال کردن حالت sticky بر اساس اسکرول
 */
function handleSearchScroll() {
    if (!searchSection) return;

    /* فقط وقتی در حالت جستجو هستیم یا فوکوس روی input هست، sticky میشه */
    const shouldSticky = state.isSearchMode ||
        document.activeElement === searchInput;

    if (!shouldSticky) {
        searchSection.classList.remove("is-sticky");
        return;
    }

    const scrollY = window.pageYOffset;

    /* فاصله از بالای صفحه برای فعال کردن sticky */
    if (scrollY >= searchSectionTop - 80) {
        searchSection.classList.add("is-sticky");
    } else {
        searchSection.classList.remove("is-sticky");
    }
}

/**
 * کلون کردن کارت منو برای نمایش در نتایج جستجو
 */
function cloneCardForSearch(sourceCard) {
    const clone = sourceCard.cloneNode(true);

    clone.style.display = "";

    const titleEl = clone.querySelector(".post-title");
    if (titleEl) {
        delete titleEl.dataset.originalHtml;
    }

    return clone;
}

/**
 * هایلایت کلمات جستجو در عنوان کارت
 */
function highlightCard(card, query) {
    const titleEl = card.querySelector(".post-title");
    if (!titleEl) return;

    if (!titleEl.dataset.originalHtml) {
        titleEl.dataset.originalHtml = titleEl.innerHTML;
    }

    const original = titleEl.dataset.originalHtml;
    const words = query.split(" ").filter((w) => w.length >= 2);

    if (!words.length) {
        titleEl.innerHTML = original;
        return;
    }

    let html = original;
    words.forEach((word) => {
        if (word.length < 2) return;
        const regex = new RegExp(`(${word.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`, "gi");
        html = html.replace(regex, '<span class="search-highlight">$1</span>');
    });

    titleEl.innerHTML = html;
}

/**
 * اجرای جستجو
 */
function performSearch(query) {
    const q = normalizePersian(query);
    state.searchQuery = q;

    if (!q) {
        exitSearchMode();
        return;
    }

    state.isSearchMode = true;

    if (searchSection) searchSection.classList.add("is-searching");

    /* پیدا کردن آیتم‌های مطابق */
    const sourceCards = document.querySelectorAll("#itemFeed .post-card");
    const matches = [];

    sourceCards.forEach((card) => {
        const searchText = normalizePersian(card.dataset.searchText || "");
        const titleText = normalizePersian(card.querySelector(".post-title")?.textContent || "");
        const descText = normalizePersian(card.querySelector(".post-desc")?.textContent || "");

        const haystack = `${searchText} ${titleText} ${descText}`;
        const matched = q.split(" ").every((word) => haystack.includes(word));

        if (matched) matches.push(card);
    });

    /* نمایش بخش نتایج */
    if (searchResultsSection) searchResultsSection.classList.add("is-visible");
    if (categoriesNormalView) categoriesNormalView.style.display = "none";
    if (itemsPage) itemsPage.style.display = "none";

    /* پاک کردن نتایج قبلی */
    if (searchResultsFeed) searchResultsFeed.innerHTML = "";

    /* اگه نتیجه‌ای نبود */
    if (matches.length === 0) {
        if (searchResultsEmpty) searchResultsEmpty.classList.add("is-visible");
        if (searchResultsCount) searchResultsCount.textContent = "";
        return;
    }

    if (searchResultsEmpty) searchResultsEmpty.classList.remove("is-visible");

    /* کلون کردن کارت‌ها و اضافه کردن به نتایج */
    matches.forEach((source) => {
        const clone = cloneCardForSearch(source);
        if (searchResultsFeed) searchResultsFeed.appendChild(clone);

        updateCardQtyDisplay(clone);
        updateCardRatingDisplay(clone);
        wireItemCard(clone);
        highlightCard(clone, q);
    });

    /* آپدیت شمارنده */
    if (searchResultsCount) {
        searchResultsCount.textContent = `${matches.length.toLocaleString("fa-IR")} آیتم پیدا شد`;
    }

    /* ⚠️ اسکرول به بخش نتایج — فقط یک بار اون هم به شکل ملایم،
       و اگه از موقعیت اولیه‌ی سرچ پایین‌تریم.
       یا حتی اصلاً اسکرول نکنیم — برای همینه که حذفش کردیم */
    /* NO SCROLL */
}

/**
 * خارج شدن از حالت جستجو
 */
function exitSearchMode() {
    state.isSearchMode = false;
    state.searchQuery = "";

    if (searchResultsSection) searchResultsSection.classList.remove("is-visible");
    if (categoriesNormalView) categoriesNormalView.style.display = "block";

    if (searchResultsFeed) searchResultsFeed.innerHTML = "";
    if (searchResultsEmpty) searchResultsEmpty.classList.remove("is-visible");
    if (searchResultsCount) searchResultsCount.textContent = "";

    if (searchSection) {
        searchSection.classList.remove("is-searching");
        /* اگه فوکوس روی input نبود، sticky رو هم بردار */
        if (document.activeElement !== searchInput) {
            searchSection.classList.remove("is-sticky");
        }
    }
}

function debounce(fn, delay) {
    let timer = null;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/* --- رویدادهای سرچ‌بار --- */
if (searchInput) {
    searchInput.addEventListener("focus", () => {
        if (searchBox) searchBox.classList.add("is-focused");
        /* اگه متن داره، sticky فعال بشه */
        if (searchInput.value.trim()) {
            handleSearchScroll();
        }
    });

    searchInput.addEventListener("blur", () => {
        if (searchBox) searchBox.classList.remove("is-focused");
        /* بعد از بلور، اگه در حالت جستجو نبودیم sticky رو بردار */
        setTimeout(() => {
            if (!state.isSearchMode && searchSection) {
                searchSection.classList.remove("is-sticky");
            }
        }, 200);
    });

    const debouncedSearch = debounce((val) => {
        performSearch(val);
    }, 200);

    searchInput.addEventListener("input", (e) => {
        const val = e.target.value;

        if (searchBox) {
            searchBox.classList.toggle("has-value", val.length > 0);
        }

        document.querySelectorAll(".search-chip").forEach((c) => c.classList.remove("is-active"));

        /* اگه متن داره، sticky فعال بشه */
        if (val.trim()) {
            updateSearchSectionPosition();
            if (searchSection) searchSection.classList.add("is-sticky");
        }

        debouncedSearch(val);
    });

    searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            e.preventDefault();
            searchInput.value = "";
            if (searchBox) searchBox.classList.remove("has-value");
            document.querySelectorAll(".search-chip").forEach((c) => c.classList.remove("is-active"));
            exitSearchMode();
        }
    });
}

/* دکمه پاک کردن */
if (searchClearBtn) {
    searchClearBtn.addEventListener("click", () => {
        if (searchInput) {
            searchInput.value = "";
            searchInput.focus();
        }
        if (searchBox) searchBox.classList.remove("has-value");
        document.querySelectorAll(".search-chip").forEach((c) => c.classList.remove("is-active"));
        exitSearchMode();
    });
}

/* چیپ‌های پیشنهادی */
document.querySelectorAll(".search-chip").forEach((chip) => {
    chip.addEventListener("click", () => {
        const term = chip.dataset.chip || "";
        if (!term) return;

        document.querySelectorAll(".search-chip").forEach((c) => c.classList.remove("is-active"));
        chip.classList.add("is-active");

        if (searchInput) {
            searchInput.value = term;
            if (searchBox) searchBox.classList.add("has-value");
        }

        /* فعال‌سازی sticky */
        updateSearchSectionPosition();
        if (searchSection) searchSection.classList.add("is-sticky");

        performSearch(term);
    });
});

/* دکمه پاک کردن جستجو در حالت خالی */
if (searchResultsEmptyBtn) {
    searchResultsEmptyBtn.addEventListener("click", () => {
        if (searchInput) {
            searchInput.value = "";
            searchInput.focus();
        }
        if (searchBox) searchBox.classList.remove("has-value");
        document.querySelectorAll(".search-chip").forEach((c) => c.classList.remove("is-active"));
        exitSearchMode();
    });
}

/* لیسنر اسکرول برای فعال/غیرفعال کردن sticky */
window.addEventListener("scroll", handleSearchScroll, {passive: true});
window.addEventListener("resize", updateSearchSectionPosition, {passive: true});

/* ==========================================================================
   مودال پیگیری سفارشات
   ========================================================================== */
const trackingModalEl = document.getElementById("trackingModal");
const trackingModal = trackingModalEl ? new bootstrap.Modal(trackingModalEl) : null;
const trackingBackBtn = document.getElementById("trackingBackBtn");
const trackingHeaderIcon = document.getElementById("trackingHeaderIcon");
const trackingModalLabel = document.getElementById("trackingModalLabel");
const trackingListView = document.getElementById("trackingListView");
const trackingDetailView = document.getElementById("trackingDetailView");

let lastTrackingOrders = [];

function showTrackingList() {
    if (trackingListView) trackingListView.classList.add("is-visible");
    if (trackingDetailView) trackingDetailView.classList.remove("is-visible");

    if (trackingBackBtn) trackingBackBtn.classList.remove("is-visible");
    if (trackingModalLabel) trackingModalLabel.textContent = "پیگیری سفارش‌ها";
    if (trackingHeaderIcon) {
        trackingHeaderIcon.classList.remove("fa-receipt");
        trackingHeaderIcon.classList.add("fa-clock-rotate-left");
    }
}

function showTrackingDetail(order) {
    if (!order) return;

    if (trackingListView) trackingListView.classList.remove("is-visible");
    if (trackingDetailView) trackingDetailView.classList.add("is-visible");

    if (trackingBackBtn) trackingBackBtn.classList.add("is-visible");
    if (trackingModalLabel) trackingModalLabel.textContent = "جزئیات سفارش";
    if (trackingHeaderIcon) {
        trackingHeaderIcon.classList.remove("fa-clock-rotate-left");
        trackingHeaderIcon.classList.add("fa-receipt");
    }

    const idEl = document.getElementById("trackingDetailId");
    const statusEl = document.getElementById("trackingDetailStatus");

    if (idEl) idEl.textContent = "سفارش #" + faNum(order.id);

    if (statusEl) {
        statusEl.className = "tracking-status " + (order.status_color || "status-pending");
        statusEl.innerHTML =
            '<i class="fa-solid ' + escapeHtml(order.status_icon) + '"></i> ' +
            escapeHtml(order.status_label);
    }

    const metaEl = document.getElementById("trackingDetailMeta");
    if (metaEl) {
        let dateStr = order.date || "";
        try {
            dateStr = new Date(order.date + "T12:00:00").toLocaleDateString("fa-IR");
        } catch (e) { /* ignore */
        }

        let metaHtml = "";
        metaHtml += '<span><i class="fa-solid fa-calendar"></i> ' + escapeHtml(dateStr) + "</span>";
        if (parseInt(order.table, 10) > 0) {
            metaHtml += '<span><i class="fa-solid fa-table-cells-large"></i> میز ' + faNum(order.table) + "</span>";
        }
        if (order.cafe_title) {
            metaHtml += '<span><i class="fa-solid fa-mug-hot"></i> ' + escapeHtml(order.cafe_title) + "</span>";
        }
        metaEl.innerHTML = metaHtml;
    }

    const itemsEl = document.getElementById("trackingDetailItems");
    if (itemsEl) {
        let itemsHtml = "";

        if (order.items && order.items.length) {
            order.items.forEach(function (it) {
                itemsHtml += '<div class="tracking-detail-item">'
                    + '<div class="tracking-detail-item-icon"><i class="fa-solid fa-utensils"></i></div>'
                    + '<div class="tracking-detail-item-info">'
                    + '<div class="tracking-detail-item-title">' + escapeHtml(it.title) + "</div>"
                    + '<div class="tracking-detail-item-qty">'
                    + "تعداد: " + faNum(it.qty) + " × " + faNum(it.price) + " تومان"
                    + "</div>"
                    + "</div>"
                    + '<div class="tracking-detail-item-price">' + faNum(it.price * it.qty) + "</div>"
                    + "</div>";
            });
        } else {
            itemsHtml = '<div class="tracking-empty is-visible"><i class="fa-solid fa-box-open"></i><p>آیتمی برای این سفارش ثبت نشده</p></div>';
        }

        itemsEl.innerHTML = itemsHtml;
    }

    const sumEl = document.getElementById("trackingDetailSummary");
    if (sumEl) {
        const subtotal = Number(order.subtotal) || 0;
        const discountPercent = parseInt(order.discount_percent, 10) || 0;
        const total = Number(order.total) || 0;
        const discountAmount = subtotal - total;

        let sumHtml = "";

        sumHtml += '<div class="tracking-summary-row">'
            + '<span><i class="fa-solid fa-receipt"></i> جمع کل</span>'
            + "<strong>" + faNum(subtotal) + " تومان</strong>"
            + "</div>";

        if (discountPercent > 0) {
            sumHtml += '<div class="tracking-summary-row discount">'
                + '<span><i class="fa-solid fa-tag"></i> تخفیف (' + faNum(discountPercent) + "٪)</span>"
                + "<strong>− " + faNum(discountAmount) + " تومان</strong>"
                + "</div>";
        }

        sumHtml += '<div class="tracking-summary-row payable">'
            + '<span><i class="fa-solid fa-money-bill-wave"></i> مبلغ قابل پرداخت</span>'
            + "<strong>" + faNum(total) + " تومان</strong>"
            + "</div>";

        sumEl.innerHTML = sumHtml;
    }
}

function openTrackingModal() {
    const user = getStoredUser();
    if (!user) {
        if (welcomeModal) welcomeModal.show();
        return;
    }
    showTrackingList();
    if (trackingModal) trackingModal.show();
}

function loadTrackingOrders() {
    const user = getStoredUser();
    if (!user) return;

    const loading = document.getElementById("trackingLoading");
    const empty = document.getElementById("trackingEmpty");
    const list = document.getElementById("trackingList");

    showTrackingList();

    if (loading) loading.classList.add("is-visible");
    if (empty) empty.classList.remove("is-visible");
    if (list) list.innerHTML = "";

    fetch(ORDERS_URL, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            action: "list",
            phone: user.phone,
            cafe_id: CAFE_ID
        })
    })
        .then((r) => r.json())
        .then((res) => {
            if (loading) loading.classList.remove("is-visible");
            if (!res || res.status !== 1) {
                if (list) list.innerHTML = '<div class="tracking-empty is-visible"><i class="fa-solid fa-triangle-exclamation"></i><p>خطا در دریافت سفارشات</p></div>';
                return;
            }
            lastTrackingOrders = res.orders || [];
            renderOrders(lastTrackingOrders);
        })
        .catch(() => {
            if (loading) loading.classList.remove("is-visible");
            if (list) list.innerHTML = '<div class="tracking-empty is-visible"><i class="fa-solid fa-triangle-exclamation"></i><p>خطای ارتباط با سرور</p></div>';
        });
}

function renderOrders(orders) {
    const empty = document.getElementById("trackingEmpty");
    const list = document.getElementById("trackingList");

    if (!orders || orders.length === 0) {
        if (empty) empty.classList.add("is-visible");
        if (list) list.innerHTML = "";
        return;
    }
    if (empty) empty.classList.remove("is-visible");

    let html = "";
    orders.forEach((o, index) => {
        let dateStr = o.date || "";
        try {
            dateStr = new Date(o.date + "T12:00:00").toLocaleDateString("fa-IR");
        } catch (e) { /* ignore */
        }

        const discountPercent = parseInt(o.discount_percent, 10) || 0;
        const subtotal = Number(o.subtotal) || 0;
        const total = Number(o.total) || 0;
        const discountAmount = discountPercent > 0 ? (subtotal - total) : 0;

        let discountHtml = "";
        if (discountPercent > 0) {
            discountHtml = '<div class="tracking-discount">'
                + '<i class="fa-solid fa-tag"></i>'
                + " تخفیف " + faNum(discountPercent) + "٪"
                + ' <span class="tracking-discount-amount">(− ' + faNum(discountAmount) + ' تومان)</span>'
                + '</div>';
        }

        let totalsHtml = "";
        if (discountPercent > 0) {
            totalsHtml = '<div class="tracking-total">'
                + "<span>قابل پرداخت</span>"
                + "<strong>" + faNum(total) + " تومان</strong>"
                + "</div>";
        } else {
            totalsHtml = '<div class="tracking-total">'
                + "<span>مبلغ نهایی</span>"
                + "<strong>" + faNum(total) + " تومان</strong>"
                + "</div>";
        }

        html += '<div class="tracking-item" data-order-index="' + index + '">'
            + '<div class="tracking-head">'
            + '<span class="tracking-id">سفارش #' + faNum(o.id) + "</span>"
            + '<div class="tracking-item-head-right">'
            + '<span class="tracking-status ' + escapeHtml(o.status_color) + '">'
            + '<i class="fa-solid ' + escapeHtml(o.status_icon) + '"></i> '
            + escapeHtml(o.status_label)
            + "</span>"
            + '<span class="tracking-item-arrow"><i class="fa-solid fa-chevron-left"></i></span>'
            + "</div>"
            + "</div>"
            + '<div class="tracking-meta">'
            + '<span><i class="fa-solid fa-calendar"></i> ' + escapeHtml(dateStr) + "</span>"
            + (parseInt(o.table, 10) > 0
                ? '<span><i class="fa-solid fa-table-cells-large"></i> میز ' + faNum(o.table) + "</span>"
                : "")
            + (o.items_count
                ? '<span><i class="fa-solid fa-utensils"></i> ' + faNum(o.items_count) + " آیتم</span>"
                : "")
            + "</div>"
            + '<div class="tracking-footer">'
            + discountHtml
            + totalsHtml
            + "</div>"
            + "</div>";
    });

    if (list) list.innerHTML = html;

    if (list) {
        list.querySelectorAll(".tracking-item").forEach((item) => {
            item.addEventListener("click", () => {
                const idx = parseInt(item.dataset.orderIndex, 10);
                const order = lastTrackingOrders[idx];
                if (order) showTrackingDetail(order);
            });
        });
    }
}

const trackingPillBtn = document.getElementById("trackingPillBtn");
if (trackingPillBtn) {
    trackingPillBtn.addEventListener("click", openTrackingModal);
}

if (trackingBackBtn) {
    trackingBackBtn.addEventListener("click", showTrackingList);
}

if (trackingModalEl) {
    trackingModalEl.addEventListener("show.bs.modal", () => {
        document.body.classList.add("modal-open-lock");
        loadTrackingOrders();
    });
    trackingModalEl.addEventListener("hidden.bs.modal", () => {
        document.body.classList.remove("modal-open-lock");
        showTrackingList();
    });
}

/* ==========================================================================
   ناوبری
   ========================================================================== */
const categoriesPage = document.getElementById("categoriesPage");
const itemsPage = document.getElementById("itemsPage");
const backBtn = document.getElementById("backBtn");

function goToCategories() {
    if (state.isSearchMode) {
        if (searchInput) searchInput.value = "";
        if (searchBox) searchBox.classList.remove("has-value");
        document.querySelectorAll(".search-chip").forEach((c) => c.classList.remove("is-active"));
        exitSearchMode();
        return;
    }

    if (itemsPage) itemsPage.style.display = "none";
    if (categoriesPage) categoriesPage.style.display = "block";
    if (backBtn) backBtn.classList.remove("is-visible");
    window.scrollTo({top: 0, behavior: "smooth"});
}

function goToItems(categoryId) {
    if (state.isSearchMode) {
        if (searchInput) searchInput.value = "";
        if (searchBox) searchBox.classList.remove("has-value");
        document.querySelectorAll(".search-chip").forEach((c) => c.classList.remove("is-active"));
        exitSearchMode();
    }

    state.currentCategoryId = categoryId;

    const catBtn = document.querySelector(`.category-card[data-cat="${categoryId}"]`);
    const catTitleEl = catBtn ? catBtn.querySelector(".category-title") : null;
    const catTitle = catTitleEl ? catTitleEl.textContent.trim() : "";

    let count = 0;
    document.querySelectorAll("#itemFeed .post-card").forEach((card) => {
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
        state.tableNumber = saved.tableNumber || null;

        const greeting = document.getElementById("greetingText");
        if (greeting) greeting.textContent = `سلام ${saved.firstName} جان 👋`;

        if (welcomeModalEl) welcomeModalEl.style.display = "none";
    } else {
        if (welcomeModal) welcomeModal.show();
    }

    updateOrderCount();
    initCategories();
    initItems();
    bindBirthdateForm();

    if (saved && saved.phone) {
        fetchDiscount();
        loadMyComments();
    }

    /* محاسبه‌ی موقعیت اولیه‌ی سرچ‌بار */
    setTimeout(updateSearchSectionPosition, 100);
    window.addEventListener("load", updateSearchSectionPosition);

    function tryCheckBirthdate() {
        if (state.birthdateChecked) return;

        if (welcomeModalEl && welcomeModalEl.classList.contains("show")) {
            return;
        }

        const user = getStoredUser();
        if (!user) return;

        checkBirthdate();
    }

    setTimeout(tryCheckBirthdate, 800);
    setTimeout(tryCheckBirthdate, 2000);
    setTimeout(tryCheckBirthdate, 4000);

    if (welcomeModalEl) {
        welcomeModalEl.addEventListener("hidden.bs.modal", () => {
            setTimeout(() => {
                state.birthdateChecked = false;
                tryCheckBirthdate();
                fetchDiscount();
                loadMyComments();
                updateSearchSectionPosition();
            }, 500);
        });
    }
});