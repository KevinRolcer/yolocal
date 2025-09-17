class Carousel {
    constructor() {
        this.currentSlide = 0;
        this.track = document.getElementById('carousel');
        this.slides = this.track.querySelectorAll('.tarjetaC');
        this.totalSlides = this.slides.length;
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.dots = []; // si luego quieres dots, puedes agregarlos aquí
        this.isAnimating = false;

        this.init();
    }

    init() {
        this.updateCarousel();
        this.bindEvents();
        this.startAutoPlay();
    }

    bindEvents() {
        this.prevBtn.addEventListener('click', () => this.goToPrevious());
        this.nextBtn.addEventListener('click', () => this.goToNext());

        // Swipe táctil
        let startX = 0;
        let currentX = 0;
        let isDragging = false;
        let startY = 0;
        let currentY = 0;

        this.track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isDragging = true;
            this.stopAutoPlay();
        }, { passive: true });

        this.track.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
            currentY = e.touches[0].clientY;

            const deltaX = Math.abs(startX - currentX);
            const deltaY = Math.abs(startY - currentY);

            if (deltaX > deltaY) {
                e.preventDefault();
            }
        }, { passive: false });

        this.track.addEventListener('touchend', () => {
            if (!isDragging) return;

            const deltaX = startX - currentX;
            const deltaY = Math.abs(startY - currentY);

            if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > deltaY) {
                if (deltaX > 0) {
                    this.goToNext();
                } else {
                    this.goToPrevious();
                }
            }

            isDragging = false;
            this.startAutoPlay();
        });

        // Navegación con teclado
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this.goToPrevious();
            }
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                this.goToNext();
            }
        });

        // Pausar autoplay al hacer hover
        this.track.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.track.addEventListener('mouseleave', () => this.startAutoPlay());
    }

    goToSlide(slideIndex) {
        if (this.isAnimating || slideIndex === this.currentSlide) return;

        this.isAnimating = true;
        this.currentSlide = slideIndex;
        this.updateCarousel();

        setTimeout(() => {
            this.isAnimating = false;
        }, 500);
    }

    goToNext() {
        if (this.isAnimating) return;
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.updateCarousel();
    }

    goToPrevious() {
        if (this.isAnimating) return;
        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.updateCarousel();
    }

    updateCarousel() {
        const translateX = -this.currentSlide * 100;
        this.track.style.transform = `translateX(${translateX}%)`;

        // Actualizar dots si existen
        this.dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentSlide);
        });
    }

    startAutoPlay() {
        this.stopAutoPlay();
        this.autoPlayInterval = setInterval(() => {
            this.goToNext();
        }, 4000);
    }

    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }
}

// Inicializar el carrusel al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    window.carousel = new Carousel();
});
