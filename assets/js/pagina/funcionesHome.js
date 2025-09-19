document.addEventListener("DOMContentLoaded", () => {
    listarMiembros();
});

export function listarMiembros() {
    let params = new URLSearchParams();
    params.append("ope", "LISTAICONOS");

    fetch("../controladores/controladorNegocios.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params.toString(),
    })
    .then((response) => response.json())
    .then((data) => {
        if (!data.success) {
            console.error("Error al cargar negocios:", data.msg);
            renderizarError("No se pudieron cargar los negocios.");
            return;
        }
        renderizarMiembros(data.lista);
        // Inicializar el carrusel después de cargar los datos
        inicializarCarrusel();
    })
    .catch((error) => {
        console.error("Error en la solicitud:", error);
        renderizarError("Error al conectarse con el servidor.");
    });
}

function renderizarMiembros(lista) {
    const contenedor = document.getElementById("carousel");
    contenedor.innerHTML = "";

    if (!lista || lista.length === 0) {
        contenedor.innerHTML = `
            <div class="no-results">
                <p>No se encuentra ningún negocio disponible.</p>
            </div>
        `;
        return;
    }

    let htmlItems = "";

    lista.forEach((miembro) => {
        // Obtener la URL de la imagen o usar imagen por defecto
        const imagenUrl = miembro.Rutaicono ? miembro.Rutaicono : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMzAgODBIMTcwVjEyMEgxMzBWODBaIiBmaWxsPSIjOUIzOUY0Ii8+Cjx0ZXh0IHg9IjE1MCIgeT0iMTUwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNjc3Mjg5IiBmb250LXNpemU9IjE0Ij7wn48iPC90ZXh0Pgo8L3N2Zz4=';
        
        // Obtener la dirección o mostrar texto por defecto
        const direccion = miembro.Direccion ? miembro.Direccion : 'Dirección no disponible';

        htmlItems += `
            <div class="tarjetaC">
                <div class="imagenTarjetaC" style="background-image: url('${imagenUrl}')"></div>
                <div class="contenidoTarjetaC">
                    <h3 class="tituloTarjetaC">${miembro.nombre_negocio}</h3>
                    <div class="ubicacionTarjetaC">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <span>${direccion}</span>
                    </div>
                    <div class="calificacionTarjetaC">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                        <span>Ver más</span>
                    </div>
                </div>
            </div>
        `;
    });

    contenedor.innerHTML = htmlItems;
}

function renderizarError(mensaje) {
    const contenedor = document.getElementById("carousel");
    contenedor.innerHTML = `
        <div class="error-message" style="padding: 2rem; text-align: center; color: #ef4444;">
            <p>${mensaje}</p>
        </div>
    `;
}

function inicializarCarrusel() {
    // Pequeño delay para asegurar que el DOM esté actualizado
    setTimeout(() => {
        // Verificar si ya existe una instancia del carrusel
        if (window.infiniteCarousel) {
            // Si existe, detenerla y crear una nueva
            window.infiniteCarousel.stop();
        }
        
        // Crear nueva instancia del carrusel infinito
        window.infiniteCarousel = new InfiniteCarousel();
    }, 100);
}

// Clase del carrusel infinito (la misma que ya tienes)
class InfiniteCarousel {
    constructor() {
        this.carousel = document.getElementById('carousel');
        
        if (!this.carousel) {
            console.warn('Carousel no encontrado');
            return;
        }

        this.originalCards = Array.from(this.carousel.querySelectorAll('.tarjetaC'));
        this.totalCards = this.originalCards.length;
        
        // Solo inicializar si hay tarjetas
        if (this.totalCards === 0) {
            console.warn('No hay tarjetas para mostrar en el carrusel');
            return;
        }
        
        this.currentPosition = 0;
        this.isAnimating = false;
        this.isPaused = false;
        this.autoPlayInterval = null;

        this._drag = {
            active: false,
            startX: 0,
            startY: 0,
            currentX: 0,
            startTranslate: 0
        };

        this.cardWidth = 0;
        this.speed = 50;
        this.autoPlaySpeed = 3000;

        this._init();
    }

    _init() {
        this.carousel.style.display = 'flex';
        this.carousel.style.cursor = 'grab';
        
        this._duplicateCards();
        this._calculateSizes();
        this._bindEvents();
        this._startAutoPlay();
    }

    _duplicateCards() {
        const fragment = document.createDocumentFragment();
        
        // Agregar copias al final
        this.originalCards.forEach(card => {
            const clone = card.cloneNode(true);
            clone.classList.add('cloned');
            fragment.appendChild(clone);
        });
        
        // Agregar copias al principio
        this.originalCards.slice().reverse().forEach(card => {
            const clone = card.cloneNode(true);
            clone.classList.add('cloned');
            this.carousel.insertBefore(clone, this.carousel.firstChild);
        });
        
        this.carousel.appendChild(fragment);
        
        this.allCards = Array.from(this.carousel.querySelectorAll('.tarjetaC'));
        this.startIndex = this.totalCards;
        this.currentPosition = this.startIndex;
    }

    _calculateSizes() {
        if (this.allCards.length === 0) return;

        const firstCard = this.allCards[0];
        const cardStyle = window.getComputedStyle(firstCard);
        
        this.cardWidth = firstCard.offsetWidth + parseInt(cardStyle.marginRight || '0');
        this._setPosition(this.currentPosition, true);
    }

    _bindEvents() {
        this.carousel.addEventListener('mousedown', (e) => this._onDragStart(e));
        this.carousel.addEventListener('touchstart', (e) => this._onDragStart(e), { passive: true });
        
        document.addEventListener('mousemove', (e) => this._onDragMove(e));
        document.addEventListener('touchmove', (e) => this._onDragMove(e), { passive: false });
        
        document.addEventListener('mouseup', (e) => this._onDragEnd(e));
        document.addEventListener('touchend', (e) => this._onDragEnd(e));

        this.carousel.addEventListener('mouseenter', () => this._pauseAutoPlay());
        this.carousel.addEventListener('mouseleave', () => this._resumeAutoPlay());

        this.carousel.addEventListener('selectstart', (e) => e.preventDefault());

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this._calculateSizes();
            }, 150);
        });
    }

    _onDragStart(e) {
        this._pauseAutoPlay();
        
        this._drag.active = true;
        this._drag.startX = this._getEventX(e);
        this._drag.startY = this._getEventY(e);
        this._drag.currentX = this._drag.startX;
        this._drag.startTranslate = -this.currentPosition * this.cardWidth;
        
        this.carousel.style.transition = 'none';
        this.carousel.style.cursor = 'grabbing';
        
        document.body.style.userSelect = 'none';
    }

    _onDragMove(e) {
        if (!this._drag.active) return;
        
        this._drag.currentX = this._getEventX(e);
        const deltaX = this._drag.currentX - this._drag.startX;
        const deltaY = this._getEventY(e) - this._drag.startY;
        
        if (Math.abs(deltaY) > Math.abs(deltaX) && Math.abs(deltaY) > 10) {
            this._cancelDrag();
            return;
        }
        
        if (Math.abs(deltaX) > 10) {
            e.preventDefault();
        }
        
        const newTranslate = this._drag.startTranslate + deltaX;
        this.carousel.style.transform = `translateX(${newTranslate}px)`;
    }

    _onDragEnd(e) {
        if (!this._drag.active) return;
        
        this._drag.active = false;
        this.carousel.style.transition = 'transform 0.3s ease';
        this.carousel.style.cursor = 'grab';
        document.body.style.userSelect = '';
        
        const deltaX = this._drag.currentX - this._drag.startX;
        const threshold = this.cardWidth * 0.2;
        
        if (Math.abs(deltaX) > threshold) {
            if (deltaX < 0) {
                this._moveToNext();
            } else {
                this._moveToPrevious();
            }
        } else {
            this._setPosition(this.currentPosition);
        }
        
        setTimeout(() => {
            this._resumeAutoPlay();
        }, 1000);
    }

    _cancelDrag() {
        this._drag.active = false;
        this.carousel.style.transition = 'transform 0.3s ease';
        this.carousel.style.cursor = 'grab';
        document.body.style.userSelect = '';
        this._setPosition(this.currentPosition);
        this._resumeAutoPlay();
    }

    _getEventX(e) {
        return e.type.includes('touch') ? e.touches[0]?.clientX || e.changedTouches[0]?.clientX : e.clientX;
    }

    _getEventY(e) {
        return e.type.includes('touch') ? e.touches[0]?.clientY || e.changedTouches[0]?.clientY : e.clientY;
    }

    _setPosition(position, immediate = false) {
        const translateX = -position * this.cardWidth;
        
        if (immediate) {
            this.carousel.style.transition = 'none';
            this.carousel.style.transform = `translateX(${translateX}px)`;
            void this.carousel.offsetWidth;
            this.carousel.style.transition = 'transform 0.3s ease';
        } else {
            this.carousel.style.transform = `translateX(${translateX}px)`;
        }
    }

    _moveToNext() {
        this.currentPosition++;
        this._setPosition(this.currentPosition);
        this._checkInfiniteLoop();
    }

    _moveToPrevious() {
        this.currentPosition--;
        this._setPosition(this.currentPosition);
        this._checkInfiniteLoop();
    }

    _checkInfiniteLoop() {
        if (this.currentPosition >= this.totalCards * 2) {
            setTimeout(() => {
                this.currentPosition = this.totalCards;
                this._setPosition(this.currentPosition, true);
            }, 300);
        }
        else if (this.currentPosition <= 0) {
            setTimeout(() => {
                this.currentPosition = this.totalCards;
                this._setPosition(this.currentPosition, true);
            }, 300);
        }
    }

    _startAutoPlay() {
        this.autoPlayInterval = setInterval(() => {
            if (!this.isPaused && !this._drag.active) {
                this._moveToNext();
            }
        }, this.autoPlaySpeed);
    }

    _pauseAutoPlay() {
        this.isPaused = true;
    }

    _resumeAutoPlay() {
        this.isPaused = false;
    }

    stop() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }

    pause() {
        this._pauseAutoPlay();
    }

    resume() {
        this._resumeAutoPlay();
    }

    setSpeed(speed) {
        this.autoPlaySpeed = speed;
        this.stop();
        this._startAutoPlay();
    }
}