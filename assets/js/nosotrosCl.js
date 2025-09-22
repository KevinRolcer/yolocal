document.addEventListener('DOMContentLoaded', function() {
            const infoPoints = document.querySelectorAll('.info-point');
            const centralImage = document.querySelector('.central-image');
            
            infoPoints.forEach(point => {
                point.addEventListener('mouseenter', function() {
                    this.classList.add('active');
                    centralImage.classList.add('highlight');
                    
                    const position = this.dataset.position;

                    centralImage.classList.remove(
                    'top-left', 'middle-left', 'bottom-left',
                    'top-right', 'middle-right', 'bottom-right'
                    );

                    centralImage.classList.add('highlight', position);

                });
                
                point.addEventListener('mouseleave', function() {
                    this.classList.remove('active');
                    centralImage.classList.remove('highlight');
                    centralImage.className = centralImage.className.replace(/\s(top-left|middle-left|bottom-left|top-right|middle-right|bottom-right)/g, '');
                });
            });
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, { threshold: 0.1 });
            
            infoPoints.forEach(point => {
                observer.observe(point);
            });
            
            observer.observe(centralImage);
        });