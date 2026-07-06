document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector("[data-menu-toggle]");
  const menu = document.querySelector("[data-mobile-menu]");

  if (toggle && menu) {
    toggle.addEventListener("click", () => {
      menu.classList.toggle("open");
    });
  }

  const main = document.querySelector("[data-gallery-main]");
  document.querySelectorAll("[data-gallery-thumb]").forEach((thumb) => {
    thumb.addEventListener("click", () => {
      if (main) {
        main.src = thumb.dataset.galleryThumb;
      }
    });
  });

  const calculator = document.querySelector("[data-calculator]");
  if (calculator) {
    const fields = [
      "[data-calc-price]",
      "[data-calc-fee]",
      "[data-calc-shipping]",
      "[data-calc-service]",
    ].map((selector) => calculator.querySelector(selector));
    const total = calculator.querySelector("[data-calc-total]");

    const format = (value) =>
      new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        maximumFractionDigits: 0,
      }).format(value);

    const update = () => {
      const sum = fields.reduce((carry, field) => carry + Number(field?.value || 0), 0);
      if (total) total.textContent = format(sum);
    };

    fields.forEach((field) => field?.addEventListener("input", update));
    update();
  }

  setTimeout(() => {
    document.querySelectorAll(".toast").forEach((toast) => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(8px)";
      setTimeout(() => toast.remove(), 250);
    });
  }, 3200);
});
