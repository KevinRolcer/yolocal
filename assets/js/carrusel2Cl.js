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
        this.targetPosition = 0; 
        this.isAnimating = false;
        this.isPaused = false;
        this.autoPlayInterval = null;
        this.resumeTimeout = null;
        this.animationFrame = null; 

        this._drag = {
            active: false,
            startX: 0,
            startY: 0,
            currentX: 0,
            startTranslate: 0,
            isDragging: false,
            velocity: 0,
            lastX: 0,
            lastTime: 0,
            momentum: 0
        };

        this.cardWidth = 0;
        this.containerWidth = 0;
        this.visibleCards = 1;
        this.speed = 50;
        this.autoPlaySpeed = 3000;
        this.smoothness = 0.08; 

        this._init();
    }

    _init() {
        this.carousel.style.display = 'flex';
        this.carousel.style.cursor = 'grab';
        this.carousel.style.willChange = 'transform';
        
        this._duplicateCards();
        this._calculateSizes();
        this._bindEvents();
        this._startAutoPlay();
        this._startSmoothAnimation();
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
        this.targetPosition = this.startIndex;
    }

    _calculateSizes() {
        if (this.allCards.length === 0) return;

        const firstCard = this.allCards[0];
        const cardStyle = window.getComputedStyle(firstCard);
        const containerStyle = window.getComputedStyle(this.carousel.parentElement);
        
        this.cardWidth = firstCard.offsetWidth + parseInt(cardStyle.marginRight || '0');
        this.containerWidth = this.carousel.parentElement.offsetWidth;
        this.visibleCards = Math.floor(this.containerWidth / this.cardWidth);
        
        this._setPositionImmediate(this.currentPosition);
    }

    _bindEvents() {
        this.carousel.addEventListener('mousedown', (e) => this._onDragStart(e));
        this.carousel.addEventListener('touchstart', (e) => this._onDragStart(e), { passive: true });
        
        document.addEventListener('mousemove', (e) => this._onDragMove(e));
        document.addEventListener('touchmove', (e) => this._onDragMove(e), { passive: false });
        
        document.addEventListener('mouseup', (e) => this._onDragEnd(e));
        document.addEventListener('touchend', (e) => this._onDragEnd(e));

        this.carousel.addEventListener('mouseenter', () => {
            this._pauseAutoPlay();
            this._clearResumeTimeout();
        });
        
        this.carousel.addEventListener('mouseleave', () => {
            if (!this._drag.active) {
                this._scheduleResume(2000);
            }
        });

        this.carousel.addEventListener('selectstart', (e) => e.preventDefault());
        this.carousel.addEventListener('dragstart', (e) => e.preventDefault());

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
        
        const eventX = this._getEventX(e);
        const eventY = this._getEventY(e);
        
        this._drag.active = true;
        this._drag.isDragging = false;
        this._drag.startX = eventX;
        this._drag.startY = eventY;
        this._drag.currentX = eventX;
        this._drag.lastX = eventX;
        this._drag.lastTime = Date.now();
        this._drag.velocity = 0;
        this._drag.startTranslate = -this.currentPosition * this.cardWidth;
        
        this.carousel.style.cursor = 'grabbing';
        document.body.style.userSelect = 'none';
        
        this.targetPosition = this.currentPosition;
    }

    _onDragMove(e) {
        if (!this._drag.active) return;
        
        const eventX = this._getEventX(e);
        const eventY = this._getEventY(e);
        const deltaX = eventX - this._drag.startX;
        const deltaY = eventY - this._drag.startY;
        
        if (!this._drag.isDragging && Math.abs(deltaY) > Math.abs(deltaX) && Math.abs(deltaY) > 15) {
            this._cancelDrag();
            return;
        }
        
        if (!this._drag.isDragging && Math.abs(deltaX) > 10) {
            this._drag.isDragging = true;
            e.preventDefault?.();
        }
        
        if (this._drag.isDragging) {
            e.preventDefault?.();
            
            const now = Date.now();
            const timeDelta = now - this._drag.lastTime;
            if (timeDelta > 0) {
                this._drag.velocity = (eventX - this._drag.lastX) / timeDelta;
            }
            this._drag.lastX = eventX;
            this._drag.lastTime = now;
            
            let resistance = 1;
            const position = this._drag.startTranslate + deltaX;
            const maxTranslate = 0;
            const minTranslate = -(this.allCards.length - 1) * this.cardWidth;
            
            if (position > maxTranslate) {
                resistance = Math.max(0.1, 1 - ((position - maxTranslate) / (this.cardWidth * 0.5)));
            } else if (position < minTranslate) {
                resistance = Math.max(0.1, 1 - ((minTranslate - position) / (this.cardWidth * 0.5)));
            }
            
            const newTranslate = this._drag.startTranslate + (deltaX * resistance);
            this.currentPosition = -newTranslate / this.cardWidth;
            
            this._updateTransform(newTranslate);
        }
        
        this._drag.currentX = eventX;
    }

    _onDragEnd(e) {
        if (!this._drag.active) return;
        
        this._drag.active = false;
        this.carousel.style.cursor = 'grab';
        document.body.style.userSelect = '';
        
        if (!this._drag.isDragging) {
            this._scheduleResume(2000);
            return;
        }
        
        const deltaX = this._drag.currentX - this._drag.startX;
        const velocity = this._drag.velocity * 1000;
        
        let momentum = velocity * 0.3;
        momentum = Math.max(-this.cardWidth * 2, Math.min(this.cardWidth * 2, momentum));
        
        const currentFloatPosition = this.currentPosition;
        let targetPosition;
        
        if (Math.abs(velocity) > 0.5 || Math.abs(deltaX) > this.cardWidth * 0.3) {
            if (deltaX + momentum < 0) {
                targetPosition = Math.ceil(currentFloatPosition);
            } else {
                targetPosition = Math.floor(currentFloatPosition);
            }
        } else {
            targetPosition = Math.round(currentFloatPosition);
        }
        
        targetPosition = Math.max(0, Math.min(this.allCards.length - 1, targetPosition));
        
        this.targetPosition = targetPosition;
        this._scheduleResume(3000);
    }

    _cancelDrag() {
        this._drag.active = false;
        this._drag.isDragging = false;
        this.carousel.style.cursor = 'grab';
        document.body.style.userSelect = '';
        this.targetPosition = this.currentPosition;
        this._scheduleResume(2000);
    }

    _getEventX(e) {
        return e.type.includes('touch') ? e.touches[0]?.clientX || e.changedTouches[0]?.clientX : e.clientX;
    }

    _getEventY(e) {
        return e.type.includes('touch') ? e.touches[0]?.clientY || e.changedTouches[0]?.clientY : e.clientY;
    }

    _startSmoothAnimation() {
        const animate = () => {
            if (Math.abs(this.currentPosition - this.targetPosition) > 0.01) {
                this.currentPosition += (this.targetPosition - this.currentPosition) * this.smoothness;
                const translateX = -this.currentPosition * this.cardWidth;
                this._updateTransform(translateX);
            } else if (this.currentPosition !== this.targetPosition) {
                this.currentPosition = this.targetPosition;
                const translateX = -this.currentPosition * this.cardWidth;
                this._updateTransform(translateX);
            }
            
            this._checkInfiniteLoop();
            this.animationFrame = requestAnimationFrame(animate);
        };
        
        animate();
    }

    _updateTransform(translateX) {
        this.carousel.style.transform = `translateX(${translateX}px)`;
    }

    _setPositionImmediate(position) {
        this.currentPosition = position;
        this.targetPosition = position;
        const translateX = -position * this.cardWidth;
        this._updateTransform(translateX);
    }

    _moveToNext() {
        if (Math.abs(this.currentPosition - this.targetPosition) < 0.1) {
            this.targetPosition = Math.round(this.targetPosition) + 1;
        }
    }

    _moveToPrevious() {
        if (Math.abs(this.currentPosition - this.targetPosition) < 0.1) {
            this.targetPosition = Math.round(this.targetPosition) - 1;
        }
    }

    _checkInfiniteLoop() {
        const roundedPosition = Math.round(this.currentPosition);
        
        if (roundedPosition >= this.totalCards * 2) {
            const offset = roundedPosition - this.totalCards * 2;
            this._setPositionImmediate(this.totalCards + offset);
        } else if (roundedPosition < this.totalCards) {
            const offset = this.totalCards - roundedPosition;
            this._setPositionImmediate(this.totalCards * 2 - offset);
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

    pause() {
        this._pauseAutoPlay();
    }

    resume() {
        this._resumeAutoPlay();
    }

    stop() {
        this._stopAutoPlay();
        this._clearResumeTimeout();
        if (this.animationFrame) {
            cancelAnimationFrame(this.animationFrame);
        }
    }

    setSpeed(speed) {
        this.autoPlaySpeed = speed;
        this._stopAutoPlay();
        this._startAutoPlay();
    }

    goToSlide(index) {
        const targetIndex = this.totalCards + (index % this.totalCards);
        this.targetPosition = targetIndex;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.infiniteCarousel = new InfiniteCarousel();
});