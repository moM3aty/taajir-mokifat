(function () {
  "use strict";

  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- Hero headline reveal + fade sequence ---------- */
  function runHeroSequence() {
    var flapLines = document.querySelectorAll("[data-flap]");
    var sub = document.querySelector(".hero-sub");
    var actions = document.querySelector(".hero-actions");

    if (reduceMotion) {
      flapLines.forEach(function (el) { el.style.clipPath = "inset(0 0 0 0)"; });
      if (sub) sub.style.opacity = "1";
      if (actions) actions.style.opacity = "1";
      return;
    }

    flapLines.forEach(function (el, i) {
      setTimeout(function () {
        el.style.transition = "clip-path .8s cubic-bezier(.2,.8,.2,1)";
        el.style.clipPath = "inset(0 0 0 0)";
      }, 250 + i * 300);
    });

    setTimeout(function () {
      [sub, actions].forEach(function (el) {
        if (!el) return;
        el.style.transition = "opacity .7s ease";
        el.style.opacity = "1";
      });
    }, 250 + flapLines.length * 300 + 150);
  }

  if (document.readyState === "complete") {
    runHeroSequence();
  } else {
    window.addEventListener("load", runHeroSequence);
  }

  /* ---------- Service cards reveal ---------- */
  var serviceCards = document.querySelectorAll(".service-card");
  if ("IntersectionObserver" in window && serviceCards.length && !reduceMotion) {
    serviceCards.forEach(function (card) {
      card.style.opacity = "0";
      card.style.transform = "translateY(20px)";
    });
    var cardIo = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var i = Array.prototype.indexOf.call(serviceCards, entry.target);
            setTimeout(function () {
              entry.target.style.transition = "opacity .5s ease, transform .5s cubic-bezier(.2,.8,.2,1)";
              entry.target.style.opacity = "1";
              entry.target.style.transform = "translateY(0)";
            }, (i % 4) * 100);
            cardIo.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.3 }
    );
    serviceCards.forEach(function (card) { cardIo.observe(card); });
  }

  /* ---------- Mobile nav toggle ---------- */
  var navToggle = document.getElementById("navToggle");
  var mainNav = document.getElementById("mainNav");
  if (navToggle && mainNav) {
    navToggle.addEventListener("click", function () {
      var open = mainNav.classList.toggle("nav-open");
      navToggle.classList.toggle("is-active", open);
      navToggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
    mainNav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        mainNav.classList.remove("nav-open");
        navToggle.classList.remove("is-active");
        navToggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  /* ---------- FAQ accordion ---------- */
  var faqItems = document.querySelectorAll(".faq-item");
  faqItems.forEach(function (item) {
    var question = item.querySelector(".faq-question");
    question.addEventListener("click", function () {
      var isOpen = item.classList.contains("open");
      faqItems.forEach(function (other) {
        other.classList.remove("open");
        other.querySelector(".faq-question").setAttribute("aria-expanded", "false");
      });
      if (!isOpen) {
        item.classList.add("open");
        question.setAttribute("aria-expanded", "true");
      }
    });
  });

  /* ---------- Contact form -> WhatsApp ---------- */
  var contactForm = document.getElementById("contactForm");
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var data = new FormData(contactForm);
      var name = (data.get("name") || "").trim();
      var phone = (data.get("phone") || "").trim();
      var type = data.get("type") || "";
      var city = (data.get("city") || "").trim();
      var details = (data.get("details") || "").trim();

      var lines = [
        "طلب حجز جديد من موقع واحة المناسبات:",
        "الاسم: " + name,
        "الجوال: " + phone,
        "نوع المناسبة: " + type
      ];
      if (city) lines.push("المدينة: " + city);
      if (details) lines.push("التفاصيل: " + details);

      var message = encodeURIComponent(lines.join("\n"));
      window.open("https://wa.me/966503513824?text=" + message, "_blank");
    });
  }
})();