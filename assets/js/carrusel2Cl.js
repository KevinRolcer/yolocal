class InfiniteCarousel {
    constructor() {
        this.carousel = document.getElementById('carousel');
        
        if (!this.carousel) {
            console.warn('Carousel no encontrado');
            return;
        }

        this.originalCards = Array.from(this.carousel.querySelectorAll('.tarjetaC'));
        this.totalCards = this.originalCards.length;
        this.currentPosition = 0;
        this.isAnimating = false;
        this.isPaused = false;
        this.autoPlayInterval = null;
        this.resumeTimeout = null; 

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
        
        this.originalCards.forEach(card => {
            const clone = card.cloneNode(true);
            clone.classList.add('cloned');
            fragment.appendChild(clone);
        });
        
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
        this._clearResumeTimeout(); 
        
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
        
        // Reanudar después de 3 segundos incluso si se cancela el drag
        this._scheduleResume(3000);
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
            void this.carousel.offsetWidth; // Forzar repaint
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

    _stopAutoPlay() {
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

    stop() {
        this._stopAutoPlay();
        this._clearResumeTimeout(); 
    }

    _scheduleResume(delay) {
        this._clearResumeTimeout();
        this.resumeTimeout = setTimeout(() => {
            this._resumeAutoPlay();
        }, delay);
    }

    _clearResumeTimeout() {
        if (this.resumeTimeout) {
            clearTimeout(this.resumeTimeout);
            this.resumeTimeout = null;
        }
    }

    setSpeed(speed) {
        this.autoPlaySpeed = speed;
        this._stopAutoPlay();
        this._startAutoPlay();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.infiniteCarousel = new InfiniteCarousel();
});