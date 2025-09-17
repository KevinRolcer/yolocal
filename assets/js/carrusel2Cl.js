// carousel.js
class Carousel {
  constructor({
    trackSelector = '#carousel',
    prevSelector = '#prevBtn',
    nextSelector = '#nextBtn',
    autoplay = true,
    interval = 4000,
    swipeThreshold = 50 // px mínimo para considerar swipe
  } = {}) {
    this.track = document.querySelector(trackSelector);
    if (!this.track) {
      console.warn('Carousel: track no encontrado con selector', trackSelector);
      return;
    }

    this.container = this.track.parentElement; // .contenedorCarruselC
    this.slides = Array.from(this.track.querySelectorAll('.tarjetaC'));
    this.totalSlides = this.slides.length;
    this.prevBtn = document.querySelector(prevSelector);
    this.nextBtn = document.querySelector(nextSelector);

    this.currentSlide = 0;
    this.isAnimating = false;
    this.autoPlayInterval = null;

    this.options = { autoplay, interval, swipeThreshold };

    this._pointer = { active: false, startX: 0, startY: 0, startTranslate: 0 };

    this.init();
  }

  init() {
    this.setupStyles();
    this.updateSizes(); // set widths and position
    this.bindEvents();
    if (this.options.autoplay) this.startAutoPlay();
  }

  setupStyles() {
    // Aseguramos overflow y transición básica si el CSS no lo contiene
    this.container.style.overflow = 'hidden';
    this.track.style.display = 'flex';
    this.track.style.transition = 'transform 500ms ease';
    this.track.style.willChange = 'transform';
    // slides: asegurar que no colapsen
    this.slides.forEach(s => s.style.boxSizing = 'border-box');
  }

  updateSizes() {
    // Recomputa el ancho de slide según el ancho del contenedor (responsive)
    this.slideWidth = this.container.clientWidth || this.track.clientWidth || 300;
    this.slides.forEach(slide => {
      slide.style.minWidth = `${this.slideWidth}px`;
      slide.style.flex = '0 0 auto';
    });
    // Colocar al slide actual (sin animación)
    this._applyTranslate(-this.currentSlide * this.slideWidth, true);
  }

  bindEvents() {
    // Botones
    if (this.prevBtn) {
      this.prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.stopAutoPlay();
        this.goToPrevious();
        this.startAutoPlay();
      });
    } else console.warn('Carousel: prevBtn no encontrado');

    if (this.nextBtn) {
      this.nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.stopAutoPlay();
        this.goToNext();
        this.startAutoPlay();
      });
    } else console.warn('Carousel: nextBtn no encontrado');

    // Teclas
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        this.stopAutoPlay();
        this.goToPrevious();
        this.startAutoPlay();
      }
      if (e.key === 'ArrowRight') {
        e.preventDefault();
        this.stopAutoPlay();
        this.goToNext();
        this.startAutoPlay();
      }
    });

    // Pointer events unificados (mouse + touch)
    this.track.addEventListener('pointerdown', (ev) => this._onPointerDown(ev));
    window.addEventListener('pointermove', (ev) => this._onPointerMove(ev));
    window.addEventListener('pointerup', (ev) => this._onPointerUp(ev));
    window.addEventListener('pointercancel', (ev) => this._onPointerUp(ev));

    // Pausar autoplay al hover (solo si hay autoplay)
    this.track.addEventListener('mouseenter', () => this.stopAutoPlay());
    this.track.addEventListener('mouseleave', () => this.startAutoPlay());

    // Recalcular al redimensionar
    let resizeTimeout = null;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => this.updateSizes(), 100);
    });
  }

  _onPointerDown(ev) {
    // solo botón izquierdo o touch
    if (ev.pointerType === 'mouse' && ev.button !== 0) return;

    this._pointer.active = true;
    this._pointer.startX = ev.clientX;
    this._pointer.startY = ev.clientY;
    this._pointer.startTranslate = -this.currentSlide * this.slideWidth;
    // quitar transición para seguir el dedo
    this.track.style.transition = 'none';
    // capturar pointer (si está disponible)
    try { ev.target.setPointerCapture(ev.pointerId); } catch (err) {}
    this.stopAutoPlay();
  }

  _onPointerMove(ev) {
    if (!this._pointer.active) return;

    const dx = ev.clientX - this._pointer.startX;
    const dy = ev.clientY - this._pointer.startY;

    // Si el movimiento vertical es mayor y notorio, cancelamos arrastre para permitir scroll
    if (Math.abs(dy) > Math.abs(dx) && Math.abs(dy) > 10) {
      // cancelar arrastre y permitir scroll
      this._pointer.active = false;
      this.track.style.transition = 'transform 500ms ease';
      this._applyTranslate(this._pointer.startTranslate, true); // regresa al inicio del slide
      this.startAutoPlay();
      return;
    }

    // prevenir comportamiento por defecto cuando detectamos arrastre horizontal
    ev.preventDefault();
    const pos = this._pointer.startTranslate + dx;
    this.track.style.transform = `translateX(${pos}px)`;
  }

  _onPointerUp(ev) {
    if (!this._pointer.active) return;
    this._pointer.active = false;
    // restaurar transición
    this.track.style.transition = 'transform 500ms ease';

    const dx = ev.clientX - this._pointer.startX;
    // swipe mínimo
    if (Math.abs(dx) > this.options.swipeThreshold) {
      if (dx < 0) this.goToNext();
      else this.goToPrevious();
    } else {
      // snap al slide actual (regresa)
      this._applyTranslate(-this.currentSlide * this.slideWidth);
    }

    this.startAutoPlay();
    // release capture
    try { ev.target.releasePointerCapture && ev.target.releasePointerCapture(ev.pointerId); } catch (err) {}
  }

  _applyTranslate(px, immediate = false) {
    if (immediate) {
      // sin transición: apagar, aplicar, forzar reflow y restaurar transición
      const prevTrans = this.track.style.transition;
      this.track.style.transition = 'none';
      this.track.style.transform = `translateX(${px}px)`;
      // force reflow
      void this.track.offsetWidth;
      this.track.style.transition = prevTrans || 'transform 500ms ease';
    } else {
      this.track.style.transform = `translateX(${px}px)`;
    }
  }

  goToSlide(index) {
    if (!this.track) return;
    // navegación circular
    this.currentSlide = ((index % this.totalSlides) + this.totalSlides) % this.totalSlides;
    const px = -this.currentSlide * this.slideWidth;
    this._applyTranslate(px);
  }

  goToNext() {
    if (!this.track) return;
    this.goToSlide(this.currentSlide + 1);
  }

  goToPrevious() {
    if (!this.track) return;
    this.goToSlide(this.currentSlide - 1);
  }

  startAutoPlay() {
    if (!this.options.autoplay || this.autoPlayInterval) return;
    this.autoPlayInterval = setInterval(() => this.goToNext(), this.options.interval);
  }

  stopAutoPlay() {
    if (this.autoPlayInterval) {
      clearInterval(this.autoPlayInterval);
      this.autoPlayInterval = null;
    }
  }
}

// Inicializar cuando DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  // si quieres opciones, pásalas aquí
  window.carousel = new Carousel({
    trackSelector: '#carousel',
    prevSelector: '#prevBtn',
    nextSelector: '#nextBtn',
    autoplay: true,
    interval: 4000,
    swipeThreshold: 50
  });
});
