"use strict";

/* ==========================================================================
   وضعیت برنامه
   ========================================================================== */

const state = {
  user: null,
  orderQuantities: {},
  myRatings: {},
};

/* ==========================================================================
   ابزارهای کمکی
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
  return document.querySelector(`.post-card[data-item="${id}"]`);
}

function getCardInfo(card) {
  return {
    id: card.dataset.item,
    price: Number(card.dataset.price || 0),
    title: card.querySelector(".post-title").textContent.trim(),
    image: card.querySelector(".post-media img").src,
  };
}

function getCardCount(card) { return Number(card.dataset.count || 0); }
function getCardSum(card)   { return Number(card.dataset.sum || 0); }

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
  document.getElementById("orderCount").textContent =
    getOrderCount().toLocaleString("fa-IR");
}

function updateCardRatingDisplay(card) {
  const count = getCardCount(card);
  const sum = getCardSum(card);
  const avg = count ? sum / count : 0;
  card.querySelector(".stars-readonly").innerHTML = renderStaticStars(avg);
  card.querySelector(".rating-summary span:last-child").textContent = count
    ? `${avg.toFixed(1)} از ۵ (${count.toLocaleString("fa-IR")} نظر)`
    : "— از ۵ (۰ نظر)";
}

function updateCardQtyDisplay(card) {
  const qty = state.orderQuantities[card.dataset.item] || 0;
  const control = card.querySelector(".order-control");
  const val = card.querySelector(".qty-value");
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
   اشتراک‌گذاری
   ========================================================================== */

function shareItem(card, platform) {
  const info = getCardInfo(card);
  const desc = card.querySelector(".post-desc").textContent.trim();
  const url = `${location.origin}${location.pathname}#item-${info.id}`;
  const text = `${info.title} از کافه روزمهر ☕\n${desc}`;

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
      navigator.share({ title: info.title, text, url }).catch(() => {});
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
const welcomeModal = new bootstrap.Modal(welcomeModalEl);
const welcomeForm = document.getElementById("welcomeForm");
const formError = document.getElementById("formError");

welcomeModalEl.addEventListener("show.bs.modal", () => {
  document.body.classList.add("modal-open-lock");
});
welcomeModalEl.addEventListener("hidden.bs.modal", () => {
  document.body.classList.remove("modal-open-lock");
});

welcomeForm.addEventListener("submit", (e) => {
  e.preventDefault();
  const firstName = document.getElementById("firstNameInput").value.trim();
  const lastName  = document.getElementById("lastNameInput").value.trim();
  const phone     = document.getElementById("phoneInput").value.trim();
  if (!firstName || !lastName || !/^0?9\d{9}$/.test(phone)) {
    formError.classList.add("is-visible");
    return;
  }
  formError.classList.remove("is-visible");
  state.user = { firstName, lastName, phone };
  document.getElementById("greetingText").textContent = `سلام ${firstName} جان 👋`;
  welcomeModal.hide();
});

/* ==========================================================================
   مودال سفارش
   ========================================================================== */

const orderModalEl = document.getElementById("orderModal");
const orderModal = new bootstrap.Modal(orderModalEl);
const orderListEl = document.getElementById("orderList");
const orderTotalEl = document.getElementById("orderTotal");
const sendOrderBtn = document.getElementById("sendOrderBtn");

function renderOrderModal() {
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
    orderTotalEl.textContent = formatPrice(0);
    sendOrderBtn.disabled = true;
    return;
  }

  sendOrderBtn.disabled = false;

  entries.forEach(([id, qty]) => {
    const card = findItemCard(id);
    if (!card) return;
    const info = getCardInfo(card);

    const row = el(`
      <div class="order-row">
        <img src="${info.image}" alt="${info.title}" loading="lazy" />
        <div class="order-row-info">
          <strong>${info.title}</strong>
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

  orderTotalEl.textContent = formatPrice(computeOrderTotal());
}

document.getElementById("orderPillBtn").addEventListener("click", () => {
  renderOrderModal();
  orderModal.show();
});

sendOrderBtn.addEventListener("click", () => {
  if (!getOrderCount()) return;
  state.orderQuantities = {};
  updateOrderCount();
  document.querySelectorAll(".post-card").forEach(updateCardQtyDisplay);
  orderModal.hide();
  showToast("سفارش شما به گارسون ارسال شد. به‌زودی سر میز می‌رسیم ☕");
});

/* ==========================================================================
   ناوبری
   ========================================================================== */

const categoriesPage = document.getElementById("categoriesPage");
const itemsPage = document.getElementById("itemsPage");
const backBtn = document.getElementById("backBtn");

function goToCategories() {
  itemsPage.style.display = "none";
  categoriesPage.style.display = "block";
  backBtn.classList.remove("is-visible");
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function goToItems(categoryId) {
  const catBtn = document.querySelector(`.category-card[data-cat="${categoryId}"]`);
  const catTitle = catBtn ? catBtn.querySelector(".category-title").textContent.trim() : "";

  let count = 0;
  document.querySelectorAll(".post-card").forEach((card) => {
    const match = card.dataset.category === categoryId;
    card.style.display = match ? "" : "none";
    if (match) count++;
  });

  document.getElementById("itemsPageTitle").textContent = catTitle;
  document.getElementById("itemsPageCount").textContent = `${count} آیتم`;

  categoriesPage.style.display = "none";
  itemsPage.style.display = "block";
  backBtn.classList.add("is-visible");
  window.scrollTo({ top: 0, behavior: "smooth" });
}

backBtn.addEventListener("click", goToCategories);

/* ==========================================================================
   راه‌اندازی
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
  likeBtn.addEventListener("click", () => {
    likeBtn.classList.toggle("liked");
    const i = likeBtn.querySelector("i");
    i.classList.toggle("fa-regular");
    i.classList.toggle("fa-solid");
  });

  /* اشتراک‌گذاری */
  card.querySelectorAll(".share-btn").forEach((btn) => {
    btn.addEventListener("click", () => shareItem(card, btn.dataset.platform));
  });

  /* تعداد سفارش */
  const control = card.querySelector(".order-control");
  const addBtn = control.querySelector(".add-order-btn");
  const minusBtn = control.querySelector(".qty-btn.minus");
  const plusBtn = control.querySelector(".qty-btn.plus");

  const setQty = (n) => {
    if (n <= 0) delete state.orderQuantities[itemId];
    else state.orderQuantities[itemId] = n;
    updateCardQtyDisplay(card);
    updateOrderCount();
  };

  addBtn.addEventListener("click", () => setQty(1));
  minusBtn.addEventListener("click", () => setQty((state.orderQuantities[itemId] || 0) - 1));
  plusBtn.addEventListener("click", () => setQty((state.orderQuantities[itemId] || 0) + 1));

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
      else { count += 1; sum += value; }

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
    panel.classList.add("is-open");
    toggleBtn.classList.add("is-open");
  };

  toggleBtn.addEventListener("click", () => {
    const willOpen = !panel.classList.contains("is-open");
    panel.classList.toggle("is-open", willOpen);
    toggleBtn.classList.toggle("is-open", willOpen);
  });

  jumpBtn.addEventListener("click", () => {
    openPanel();
    panel.scrollIntoView({ behavior: "smooth", block: "center" });
  });

  /* ثبت نظر جدید */
  const form = card.querySelector(".comment-form");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const ta = form.querySelector("textarea");
    const text = ta.value.trim();
    if (!text) return;

    const name = state.user
      ? `${state.user.firstName} ${state.user.lastName}`
      : "مهمان";
    const starsCount = state.myRatings[itemId] || 0;

    const starsHtml = starsCount
      ? Array.from({ length: 5 })
          .map((_, i) => `<i class="fa-solid fa-star" style="opacity:${i < starsCount ? 1 : 0.25}"></i>`)
          .join("")
      : "";

    const commentEl = el(`
      <div class="comment-item">
        <div class="avatar-sm">${(name.trim().charAt(0) || "?")}</div>
        <div class="comment-body">
          <div class="comment-name-row">
            <strong>${name}</strong>
            ${starsHtml ? `<span class="comment-stars">${starsHtml}</span>` : ""}
          </div>
          <p>${escapeHtml(text)}</p>
        </div>
      </div>
    `);

    // حذف پیام خالی بودن
    const empty = card.querySelector(".comment-empty");
    if (empty) empty.remove();

    card.querySelector(".comment-list").appendChild(commentEl);
    ta.value = "";

    const newCount = card.querySelectorAll(".comment-list .comment-item").length;
    toggleBtn.querySelector(".toggle-label").textContent =
      `مشاهده نظرات (${newCount.toLocaleString("fa-IR")})`;

    openPanel();
  });
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
  welcomeModal.show();
  updateOrderCount();
  initCategories();
  initItems();
});