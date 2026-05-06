(() => {
  const PALETTE = [
    "#ffffff",
    "#ffcd00",
    "#ebaa04",
    "#ffd666",
    "#fde047",
    "#c4b5fd",
    "#a78bfa",
    "#f9a8d4",
    "#7dd3fc",
  ];

  function pickColor() {
    return PALETTE[Math.floor(Math.random() * PALETTE.length)];
  }

  function applyRandomBrandColors() {
    const letters = document.querySelectorAll(
      ".hero-line-brand > .hero-char:not(.hero-space):not(.hero-o-slot)"
    );
    letters.forEach((el) => {
      el.style.color = pickColor();
    });
    const disc = document.querySelector(".hero-o-disc");
    if (disc) disc.style.backgroundColor = pickColor();
    const glyph = document.querySelector(".hero-o-glyph");
    if (glyph) glyph.style.color = pickColor();
  }

  function init() {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    const line = document.querySelector(".hero-line-brand");
    if (!line) return;

    applyRandomBrandColors();
    setInterval(applyRandomBrandColors, 400);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
