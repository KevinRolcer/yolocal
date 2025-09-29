class ImageCarousel {
    constructor(slidesData) {
        this.currentSlide = 0;
        this.slidesData = slidesData;  // Ahora se pasan los datos dinámicos
        this.totalSlides = this.slidesData.length;
        this.track = document.getElementById('carouselTrack');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.dotsContainer = document.getElementById('carouselDots');
        
        // Llamamos al método init después de renderizar los slides
        this.init();
    }

    init() {
        this.renderSlides();  // Renderizamos los slides dinámicamente
        this.createDots();
        this.addEventListeners();
        this.updateCarousel();
        this.startAutoPlay();
    }

    // Generamos los slides dinámicamente a partir de los datos
    renderSlides() {
        if (!this.track) return;
        this.track.innerHTML = "";  // Limpiamos los slides anteriores
        
        this.slidesData.forEach((slideData, index) => {
            const slide = document.createElement('div');
            slide.classList.add('carousel-slide');
            slide.innerHTML = `
                <div class="slide-content">
                    <div class="slide-image">
                        <img src="${slideData.image || 'assets/img/default.jpg'}" alt="${slideData.title}">
                    </div>
                    <div class="slide-info">
                        <h2 class="slide-title">${slideData.title}</h2>
                        <p class="slide-description">${slideData.description || ''}</p>
                    </div>
                </div>
            `;
            this.track.appendChild(slide);
        });

        // Ahora actualizamos la variable de totalSlides
        this.slides = document.querySelectorAll('.carousel-slide');
        this.totalSlides = this.slides.length;
    }

    createDots() {
        for (let i = 0; i < this.totalSlides; i++) {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            dot.addEventListener('click', () => this.goToSlide(i));
            this.dotsContainer.appendChild(dot);
        }
    }

    addEventListeners() {
        this.prevBtn.addEventListener('click', () => this.prevSlide());
        this.nextBtn.addEventListener('click', () => this.nextSlide());

        let startX = 0;
        let endX = 0;

        this.track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        this.track.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            this.handleSwipe(startX, endX);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.prevSlide();
            } else if (e.key === 'ArrowRight') {
                this.nextSlide();
            }
        });

        const container = document.querySelector('.carousel-container');
        container.addEventListener('mouseenter', () => this.stopAutoPlay());
        container.addEventListener('mouseleave', () => this.startAutoPlay());
    }

    handleSwipe(startX, endX) {
        const threshold = 50;
        const diff = startX - endX;

        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                this.nextSlide();
            } else {
                this.prevSlide();
            }
        }
    }

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.updateCarousel();
    }

    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.updateCarousel();
    }

    goToSlide(index) {
        this.currentSlide = index;
        this.updateCarousel();
    }

    updateCarousel() {
        const translateX = -this.currentSlide * 100;
        this.track.style.transform = `translateX(${translateX}%)`;

        const dots = document.querySelectorAll('.dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentSlide);
        });

        this.animateSlideContent();
    }

    animateSlideContent() {
        this.slides.forEach(slide => {
            const elements = slide.querySelectorAll('.slide-info > *');
            elements.forEach(el => {
                el.style.animation = 'none';
            });
        });

        setTimeout(() => {
            const currentSlideElement = this.slides[this.currentSlide];
            const elements = currentSlideElement.querySelectorAll('.slide-info > *');

            elements.forEach((el, index) => {
                el.style.animation = `slideInRight 0.6s ease ${index * 0.1}s both`;
            });
        }, 50);
    }

    startAutoPlay() {
        this.autoPlayInterval = setInterval(() => {
            this.nextSlide();
        }, 5000);
    }

    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
        }
    }
}

// Estilo de la animación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', () => {
   
    fetch("../controladores/controladorNegocios.php", {
        method: "POST", 
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ "ope": "LISTAICONOSBanner" }).toString()  
    })
    .then(response => response.json())  
    .then(data => {
        if (data.success) {

            const slidesData = data.lista.map(item => ({
                image: item.Rutaicono || 'assets/img/default.jpg', 
                title: item.nombre_negocio,
                description: item.DescripcionN || 'Descripción no disponible'
            }));
    
            new ImageCarousel(slidesData);
        } else {
            console.error('No se pudieron cargar los negocios:', data.msg);
        
        }
    })
    .catch(error => {
        console.error('Error al conectar con el servidor:', error);
       
    });
});

