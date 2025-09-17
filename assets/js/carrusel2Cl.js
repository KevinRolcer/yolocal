// carousel.js (versión mejorada: botones + drag con mouse y touch, sin cambiar tamaños)
class Carousel {
  constructor(selector = '#carousel', prevId = 'prevBtn', nextId = 'nextBtn') {
    this.track = document.querySelector(selector);
    if (!this.track) return console.warn('Carousel: #carousel no encontrado');

    this.slides = Array.from(this.track.querySelectorAll('.tarjetaC'));
    this.totalSlides = this.slides.length || 1;
    this.prevBtn = document.getElementById(prevId);
    this.nextBtn = document.getElementById(nextId);

    this.currentSlide = 0;
    this.isAnimating = false;

    // estado del drag
    this._drag = {
      active: false,
      startX: 0,
      startY: 0,
      lastX: 0,
      startTranslate: 0
    };

    // valores que se calculan en runtime
    this.slideWidth = 0;
    this.threshold = 50; // se reajusta en updateSizes

    this._init();
  }

  _init() {
    // estilo mínimo necesario (no modifica anchos de las slides)
    this.track.style.willChange = 'transform';
    this.track.style.touchAction = 'pan-y'; // permite scroll vertical, bloquea horizontal nativo
    // transición por defecto
    this.track.style.transition = 'transform 400ms ease';

    this._calcSizes();
    this._bind();
    // opcional: autoplay (si quieres, lo activas)
    // this._startAuto();
  }

  _calcSizes() {
    // no forzamos tamaños; solo medimos la anchura real de la primera slide
    const first = this.slides[0];
    this.slideWidth = first ? Math.round(first.getBoundingClientRect().width) : 0;
    // umbral para considerar swipe: 15% del ancho o 50px mínimo
    this.threshold = Math.max(50, Math.round(this.slideWidth * 0.15));
    // forzamos el snap al slide actual por si hubo resize
    this._applyTranslate(-this.currentSlide * this.slideWidth, true);
  }

  _bind() {
    // botones
    if (this.prevBtn) {
      this.prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.goToPrevious();
      });
    }
    if (this.nextBtn) {
      this.nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.goToNext();
      });
    }

    // teclado
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') this.goToPrevious();
      if (e.key === 'ArrowRight') this.goToNext();
    });

    // pointer events (funciona touch + mouse)
    this.track.addEventListener('pointerdown', (ev) => this._onPointerDown(ev));
    window.addEventListener('pointermove', (ev) => this._onPointerMove(ev));
    window.addEventListener('pointerup', (ev) => this._onPointerUp(ev));
    window.addEventListener('pointercancel', (ev) => this._onPointerUp(ev));

    // hover pause/resume si usas autoplay
    this.track.addEventListener('mouseenter', () => this._stopAuto && this._stopAuto());
    this.track.addEventListener('mouseleave', () => this._startAuto && this._startAuto());

    // recalcular tamaños al hacer resize
    let t;
    window.addEventListener('resize', () => {
      clearTimeout(t);
      t = setTimeout(() => this._calcSizes(), 120);
    });
  }

  _onPointerDown(ev) {
    // solo botón principal del mouse o touch
    if (ev.pointerType === 'mouse' && ev.button !== 0) return;
    this._drag.active = true;
    this._drag.startX = ev.clientX;
    this._drag.startY = ev.clientY;
    this._drag.lastX = ev.clientX;
    this._drag.startTranslate = -this.currentSlide * this.slideWidth;
    // desactivar transición para seguir movimiento
    this.track.style.transition = 'none';
    // evitar selección de texto mientras arrastra
    document.body.style.userSelect = 'none';
    // capture pointer si es posible para seguir el movimiento incluso fuera del elemento
    try { ev.target.setPointerCapture(ev.pointerId); } catch (e) {}
  }

  _onPointerMove(ev) {
    if (!this._drag.active) return;
    const dx = ev.clientX - this._drag.startX;
    const dy = ev.clientY - this._drag.startY;

    // si el gesto es claramente vertical, abortamos el drag para permitir scroll natural
    if (Math.abs(dy) > Math.abs(dx) && Math.abs(dy) > 10) {
      this._drag.active = false;
      this.track.style.transition = 'transform 400ms ease';
      this._applyTranslate(-this.currentSlide * this.slideWidth); // snap back
      document.body.style.userSelect = '';
      return;
    }

    // prevenir comportamiento por defecto cuando arrastre horizontal
    ev.preventDefault();
    this._drag.lastX = ev.clientX;
    const pos = this._drag.startTranslate + dx;
    // aplicar la transform directamente (px) usando translate3d para GPU
    this.track.style.transform = `translate3d(${pos}px,0,0)`;
  }

  _onPointerUp(ev) {
    if (!this._drag.active) return;
    this._drag.active = false;
    // restaurar transición
    this.track.style.transition = 'transform 400ms ease';
    document.body.style.userSelect = '';

    const dx = ev.clientX - this._drag.startX;
    // si el movimiento supera el umbral, navegar; sino "snap" al slide actual
    if (Math.abs(dx) > this.threshold) {
      if (dx < 0) this.goToNext();
      else this.goToPrevious();
    } else {
      // snap de regreso
      this._applyTranslate(-this.currentSlide * this.slideWidth);
    }

    // release pointer capture si se obtuvo
    try { ev.target.releasePointerCapture && ev.target.releasePointerCapture(ev.pointerId); } catch (e) {}
  }

  _applyTranslate(px, immediate = false) {
    if (immediate) {
      const prev = this.track.style.transition;
      this.track.style.transition = 'none';
      this.track.style.transform = `translate3d(${px}px,0,0)`;
      // forzar reflow
      void this.track.offsetWidth;
      this.track.style.transition = prev || 'transform 400ms ease';
    } else {
      this.track.style.transform = `translate3d(${px}px,0,0)`;
    }
  }

  goToSlide(index) {
    if (this.isAnimating) return;
    // wrap circular
    this.currentSlide = ((index % this.totalSlides) + this.totalSlides) % this.totalSlides;
    const px = -this.currentSlide * this.slideWidth;
    this.isAnimating = true;
    this._applyTranslate(px, false);
    // bloqueo de animación durante la transición
    setTimeout(() => { this.isAnimating = false; }, 420);
  }

  goToNext() {
    this.goToSlide(this.currentSlide + 1);
  }

  goToPrevious() {
    this.goToSlide(this.currentSlide - 1);
  }

  // Si en algún momento quieres autoplay, puedes descomentar/usar esto:
  _startAuto() {
    this._stopAuto();
    this._auto = setInterval(() => this.goToNext(), 4000);
  }
  _stopAuto() {
    if (this._auto) { clearInterval(this._auto); this._auto = null; }
  }
}

// Inicializar al cargar DOM
document.addEventListener('DOMContentLoaded', () => {
  window.carousel = new Carousel('#carousel', 'prevBtn', 'nextBtn');
});
