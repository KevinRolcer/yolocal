class Carousel {
    constructor(selector = '#carousel', prevId = 'prevBtn', nextId = 'nextBtn') {
        this.track = document.querySelector(selector);
        if (!this.track) {
            console.warn('Carousel: #carousel no encontrado');
            return;
        }

        this.slides = Array.from(this.track.querySelectorAll('.tarjetaC'));
        this.totalSlides = this.slides.length || 1;
        this.prevBtn = document.getElementById(prevId);
        this.nextBtn = document.getElementById(nextId);
        this.indicadoresContainer = document.getElementById('indicadores');

        this.currentSlide = 0;
        this.isAnimating = false;
        this.slidesPerView = this.calculateSlidesPerView();

        this._drag = {
            active: false,
            startX: 0,
            startY: 0,
            lastX: 0,
            startTranslate: 0
        };

        this.slideWidth = 0;
        this.threshold = 50;

        this._init();
    }

    calculateSlidesPerView() {
        const containerWidth = this.track.parentElement.offsetWidth;
        const slideWidth = 300 + 24; // 300px + 1.5rem margin
        return Math.floor(containerWidth / slideWidth) || 1;
    }

    _init() {
        this.track.style.willChange = 'transform';
        this.track.style.touchAction = 'pan-y';
        this.track.style.transition = 'transform 400ms cubic-bezier(0.25, 0.46, 0.45, 0.94)';

        this._calcSizes();
        this._bind();
        this._createIndicators();
        this._updateIndicators();
        this._updateButtons();
    }

    _calcSizes() {
        const first = this.slides[0];
        if (first) {
            const rect = first.getBoundingClientRect();
            const style = window.getComputedStyle(first);
            const marginRight = parseFloat(style.marginRight) || 24;
            this.slideWidth = Math.round(rect.width + marginRight);
            this.threshold = Math.max(50, Math.round(this.slideWidth * 0.15));
        }
        this.slidesPerView = this.calculateSlidesPerView();
        this._applyTranslate(-this.currentSlide * this.slideWidth, true);
    }

    _createIndicators() {
        if (!this.indicadoresContainer) return;
        
        this.indicadoresContainer.innerHTML = '';
        const maxSlides = Math.max(0, this.totalSlides - this.slidesPerView + 1);
        
        for (let i = 0; i < maxSlides; i++) {
            const indicador = document.createElement('div');
            indicador.className = 'indicador';
            indicador.addEventListener('click', () => this.goToSlide(i));
            this.indicadoresContainer.appendChild(indicador);
        }
    }

    _updateIndicators() {
        if (!this.indicadoresContainer) return;
        
        const indicadores = this.indicadoresContainer.querySelectorAll('.indicador');
        indicadores.forEach((ind, index) => {
            ind.classList.toggle('activo', index === this.currentSlide);
        });
    }

    _updateButtons() {
        if (this.prevBtn) {
            this.prevBtn.disabled = this.currentSlide === 0;
            this.prevBtn.style.opacity = this.currentSlide === 0 ? '0.5' : '1';
        }
        
        if (this.nextBtn) {
            const maxSlide = Math.max(0, this.totalSlides - this.slidesPerView);
            this.nextBtn.disabled = this.currentSlide >= maxSlide;
            this.nextBtn.style.opacity = this.currentSlide >= maxSlide ? '0.5' : '1';
        }
    }

    _bind() {
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

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.goToPrevious();
            if (e.key === 'ArrowRight') this.goToNext();
        });

        this.track.addEventListener('pointerdown', (ev) => this._onPointerDown(ev));
        document.addEventListener('pointermove', (ev) => this._onPointerMove(ev));
        document.addEventListener('pointerup', (ev) => this._onPointerUp(ev));
        document.addEventListener('pointercancel', (ev) => this._onPointerUp(ev));

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this._calcSizes();
                this._createIndicators();
                this._updateIndicators();
                this._updateButtons();
            }, 120);
        });
    }

    _onPointerDown(ev) {
        if (ev.pointerType === 'mouse' && ev.button !== 0) return;
        
        this._drag.active = true;
        this._drag.startX = ev.clientX;
        this._drag.startY = ev.clientY;
        this._drag.lastX = ev.clientX;
        this._drag.startTranslate = -this.currentSlide * this.slideWidth;
        
        this.track.style.transition = 'none';
        document.body.style.userSelect = 'none';
        
        try { 
            ev.target.setPointerCapture(ev.pointerId); 
        } catch (e) {}
    }

    _onPointerMove(ev) {
        if (!this._drag.active) return;
        
        const dx = ev.clientX - this._drag.startX;
        const dy = ev.clientY - this._drag.startY;

        if (Math.abs(dy) > Math.abs(dx) && Math.abs(dy) > 10) {
            this._drag.active = false;
            this.track.style.transition = 'transform 400ms cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            this._applyTranslate(-this.currentSlide * this.slideWidth);
            document.body.style.userSelect = '';
            return;
        }

        ev.preventDefault();
        this._drag.lastX = ev.clientX;
        const pos = this._drag.startTranslate + dx;
        this.track.style.transform = `translate3d(${pos}px,0,0)`;
    }

    _onPointerUp(ev) {
        if (!this._drag.active) return;
        
        this._drag.active = false;
        this.track.style.transition = 'transform 400ms cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        document.body.style.userSelect = '';

        const dx = ev.clientX - this._drag.startX;
        if (Math.abs(dx) > this.threshold) {
            if (dx < 0) this.goToNext();
            else this.goToPrevious();
        } else {
            this._applyTranslate(-this.currentSlide * this.slideWidth);
        }

        try { 
            ev.target.releasePointerCapture && ev.target.releasePointerCapture(ev.pointerId); 
        } catch (e) {}
    }

    _applyTranslate(px, immediate = false) {
        if (immediate) {
            const prevTransition = this.track.style.transition;
            this.track.style.transition = 'none';
            this.track.style.transform = `translate3d(${px}px,0,0)`;
            void this.track.offsetWidth;
            this.track.style.transition = prevTransition || 'transform 400ms cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        } else {
            this.track.style.transform = `translate3d(${px}px,0,0)`;
        }
    }

    goToSlide(index) {
        if (this.isAnimating) return;
        
        const maxSlide = Math.max(0, this.totalSlides - this.slidesPerView);
        this.currentSlide = Math.max(0, Math.min(index, maxSlide));
        
        const px = -this.currentSlide * this.slideWidth;
        this.isAnimating = true;
        
        this._applyTranslate(px, false);
        this._updateIndicators();
        this._updateButtons();
        
        setTimeout(() => { 
            this.isAnimating = false; 
        }, 420);
    }

    goToNext() {
        const maxSlide = Math.max(0, this.totalSlides - this.slidesPerView);
        if (this.currentSlide < maxSlide) {
            this.goToSlide(this.currentSlide + 1);
        }
    }

    goToPrevious() {
        if (this.currentSlide > 0) {
            this.goToSlide(this.currentSlide - 1);
        }
    }

    _startAuto() {
        this._stopAuto();
        this._auto = setInterval(() => {
            if (this.currentSlide >= Math.max(0, this.totalSlides - this.slidesPerView)) {
                this.goToSlide(0); // Volver al inicio
            } else {
                this.goToNext();
            }
        }, 4000);
    }

    _stopAuto() {
        if (this._auto) { 
            clearInterval(this._auto); 
            this._auto = null; 
        }
    }

    enableAutoplay() {
        this._startAuto();
        
        this.track.addEventListener('mouseenter', () => this._stopAuto());
        this.track.addEventListener('mouseleave', () => this._startAuto());
    }

    disableAutoplay() {
        this._stopAuto();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.carousel = new Carousel('#carousel', 'prevBtn', 'nextBtn');

});

