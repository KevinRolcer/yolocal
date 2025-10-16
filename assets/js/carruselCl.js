const predefinedBanners = [
  {
    type: "banner",
    srcDesktop: "../assets/img/banners/1.png",
    srcMobile: "../assets/img/banners/1_mobile.png",
  },
  {
    type: "banner",
    srcDesktop: "../assets/img/banners/2.png",
    srcMobile: "../assets/img/banners/2_mobile.png",
  },
  {
    type: "banner",
    srcDesktop: "../assets/img/banners/3.png",
    srcMobile: "../assets/img/banners/3_mobile.png",
  },
];

class ImageCarousel {
  constructor(slidesData) {
    this.currentSlide = 0;
    this.slidesData = slidesData;
    this.totalSlides = this.slidesData.length;
    this.track = document.getElementById("carouselTrack");
    this.prevBtn = document.getElementById("prevBtn");
    this.nextBtn = document.getElementById("nextBtn");
    this.dotsContainer = document.getElementById("carouselDots");
    this.isMobileView = window.matchMedia("(max-width: 767px)");

    // Prevenir errores si no se encuentran los elementos del DOM
    if (!this.track || !this.prevBtn || !this.nextBtn || !this.dotsContainer) {
      console.error("Faltan elementos esenciales del carrusel en el DOM.");
      return;
    }

    this.init();
  }

  init() {
    this.renderSlides();
    this.addEventListeners();
    this.updateCarousel(); 
    this.startAutoPlay();
  }

  renderSlides() {
    this.track.innerHTML = ""; 

    this.slidesData.forEach((slideData, index) => {
      const slide = document.createElement("div");
      slide.classList.add("carousel-slide");

      if (slideData.type === "banner") {
        slide.innerHTML = `
          <div class="slide-background-banner">
            <picture>
              <source media="(min-width: 768px)" srcset="${slideData.srcDesktop}">
              <source media="(max-width: 767px)" srcset="${slideData.srcMobile}">
              <img src="${slideData.srcDesktop}" alt="Anuncio" class="banner-image">
            </picture>
          </div>
        `;
      } else {
        // Plantilla para los slides que vienen de la base de datos
        slide.innerHTML = `
          <div class="slide-background bg-gradient-${(index % 3) + 1}">
            <div class="slide-content">
              <div class="slide-image">
                <img src="${slideData.Rutaicono || "assets/img/default.jpg"}" alt="${slideData.nombre_negocio}">
              </div>
              <div class="slide-info">
                <span class="discount-badge">Recomendado en ${slideData.nombre_categoria || "Categoría"}</span>
                <h2 class="slide-title">${slideData.nombre_negocio}</h2>
                <p class="slide-description">${slideData.DescripcionN || "Negocio destacado"}</p>
                <a href="controladores/DetalleNegocioControlador.php?id=${slideData.ID_Negocio}">
                  <button class="slide-button">Ver negocio</button>
                </a>
              </div>
            </div>
          </div>
        `;
      }
      this.track.appendChild(slide);
    });

    // Actualizamos la colección de slides y el total
    this.slides = document.querySelectorAll(".carousel-slide");
    this.totalSlides = this.slides.length;
  }

  updateDots() {
    this.dotsContainer.innerHTML = "";
    const maxVisibleDots = 11; 

    if (this.isMobileView.matches && this.totalSlides > maxVisibleDots) {
  
      let start = Math.max(0, this.currentSlide - Math.floor(maxVisibleDots / 2));
      let end = Math.min(this.totalSlides - 1, start + maxVisibleDots - 1);

      if (end - start + 1 < maxVisibleDots) {
        start = Math.max(0, end - maxVisibleDots + 1);
      }

      for (let i = start; i <= end; i++) {
        this._createSingleDot(i);
      }
    } else {
      for (let i = 0; i < this.totalSlides; i++) {
        this._createSingleDot(i);
      }
    }
  }

  _createSingleDot(index) {
    const dot = document.createElement("div");
    dot.classList.add("dot");
    if (index === this.currentSlide) {
      dot.classList.add("active"); 
    }
    dot.addEventListener("click", () => this.goToSlide(index));
    this.dotsContainer.appendChild(dot);
  }

  addEventListeners() {
    this.prevBtn.addEventListener("click", () => this.prevSlide());
    this.nextBtn.addEventListener("click", () => this.nextSlide());
    
    this.isMobileView.addEventListener("change", () => this.updateDots());

    let startX = 0;
    this.track.addEventListener("touchstart", (e) => { startX = e.touches[0].clientX; }, { passive: true });
    this.track.addEventListener("touchend", (e) => {
      const endX = e.changedTouches[0].clientX;
      this.handleSwipe(startX, endX);
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") this.prevSlide();
      if (e.key === "ArrowRight") this.nextSlide();
    });
    const container = this.track.closest(".carousel-container");
    container.addEventListener("mouseenter", () => this.stopAutoPlay());
    container.addEventListener("mouseleave", () => this.startAutoPlay());
  }
  handleSwipe(startX, endX) {
    const threshold = 50; 
    const diff = startX - endX;
    if (Math.abs(diff) > threshold) {
      diff > 0 ? this.nextSlide() : this.prevSlide();
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
    // Mover el carrusel
    const translateX = -this.currentSlide * 100;
    this.track.style.transform = `translateX(${translateX}%)`;

    this.updateDots();

    this.animateSlideContent();
  }

  animateSlideContent() {
    this.slides.forEach((slide) => {
      slide.querySelectorAll(".slide-info > *").forEach((el) => {
        el.style.animation = "none";
      });
    });

    setTimeout(() => {
      const currentSlideElement = this.slides[this.currentSlide];
      const elements = currentSlideElement.querySelectorAll(".slide-info > *");
      elements.forEach((el, index) => {
        el.style.animation = `slideInRight 0.6s ease ${index * 0.1}s both`;
      });
    }, 50); 
  }

  startAutoPlay() {
    this.stopAutoPlay(); 
    this.autoPlayInterval = setInterval(() => this.nextSlide(), 5000);
  }

  stopAutoPlay() {
    clearInterval(this.autoPlayInterval);
  }
}

const style = document.createElement("style");
style.textContent = `
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);

// Carga de datos e instanciación del carrusel
document.addEventListener("DOMContentLoaded", () => {
  fetch("../controladores/controladorNegocios.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ ope: "LISTAICONOSBanner" }).toString(),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const slidesFromDB = data.lista;
        let combinedData = [...slidesFromDB];

        if (combinedData.length > 1) combinedData.splice(2, 0, predefinedBanners[0]);
        if (combinedData.length > 5) combinedData.splice(6, 0, predefinedBanners[1]);
        if (combinedData.length > 10) combinedData.splice(10, 0, predefinedBanners[2]);
        
        new ImageCarousel(combinedData);
      } else {
        console.error("No se pudieron cargar los negocios:", data.msg);
      }
    })
    .catch((error) => console.error("Error al conectar con el servidor:", error));
});