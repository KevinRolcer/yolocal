// Funcionalidad del header fijo
document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('header');
    let lastScrollTop = 0;
    
    // Función para manejar el scroll
    function handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Agregar clase 'scrolled' cuando se haga scroll
        if (scrollTop > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
    }
    
    // Escuchar el evento scroll con throttling para mejor rendimiento
    let isScrolling = false;
    window.addEventListener('scroll', function() {
        if (!isScrolling) {
            window.requestAnimationFrame(function() {
                handleScroll();
                isScrolling = false;
            });
            isScrolling = true;
        }
    });
    
    // Funcionalidad para botones de corazón
    const heartButtons = document.querySelectorAll('.heart-btn');
    heartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('filled');
            
            // Animación de "me gusta"
            this.style.transform = 'scale(1.3)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
    
    // Funcionalidad para botones de carrito
    const cartButtons = document.querySelectorAll('.cart-btn');
    const cartCount = document.querySelector('.cart-count');
    let currentCount = 2; // Valor inicial
    
    cartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Incrementar contador
            currentCount++;
            cartCount.textContent = currentCount;
            
            // Animación del botón
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
            
            // Animación del contador del carrito
            cartCount.style.transform = 'scale(1.3)';
            cartCount.style.background = '#28a745';
            setTimeout(() => {
                cartCount.style.transform = 'scale(1)';
                cartCount.style.background = '#dc3545';
            }, 300);
            
            // Mostrar mensaje de confirmación
            showNotification('Producto agregado al carrito');
        });
    });
    
    // Funcionalidad para botón de agregar (+)
    const addButtons = document.querySelectorAll('.add-btn');
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Incrementar contador del carrito
            currentCount++;
            cartCount.textContent = currentCount;
            
            // Animación del botón
            this.style.transform = 'rotate(90deg) scale(1.2)';
            setTimeout(() => {
                this.style.transform = 'rotate(0deg) scale(1)';
            }, 300);
            
            // Animación del contador del carrito
            cartCount.style.transform = 'scale(1.3)';
            cartCount.style.background = '#28a745';
            setTimeout(() => {
                cartCount.style.transform = 'scale(1)';
                cartCount.style.background = '#dc3545';
            }, 300);
            
            showNotification('Producto agregado al carrito');
        });
    });
    
    // Función para mostrar notificaciones
    function showNotification(message) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 1001;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        `;
        
        document.body.appendChild(notification);
        
        // Mostrar notificación
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Ocultar notificación después de 3 segundos
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Funcionalidad para botones principales del hero
    const heroButtons = document.querySelectorAll('.hero-buttons .btn');
    heroButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const isSecondary = this.classList.contains('btn-secondary');
            const message = isSecondary ? 'Explorando productos...' : 'Redirigiendo a compra...';
            
            // Animación del botón
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            showNotification(message);
        });
    });
    
    // Funcionalidad para smooth scroll en links del menú
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Solo prevenir el comportamiento por defecto si es un enlace interno
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                
                // Remover clase active de todos los links
                navLinks.forEach(nl => nl.classList.remove('active'));
                
                // Agregar clase active al link clickeado
                this.classList.add('active');
            }
        });
    });
    
    // Funcionalidad para el botón de explorar en trend card
    const exploreBtn = document.querySelector('.explore-btn');
    if (exploreBtn) {
        exploreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Animación de rotación
            this.style.transform = 'rotate(180deg)';
            setTimeout(() => {
                this.style.transform = 'rotate(0deg)';
            }, 300);
            
            showNotification('Explorando productos trending...');
        });
    }
    
    // Funcionalidad para iconos del navbar
    const iconButtons = document.querySelectorAll('.icon-btn');
    iconButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Animación de pulso
            this.style.transform = 'scale(1.1)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
            
            // Determinar qué icono se clickeó
            const svg = this.querySelector('svg');
            const path = svg.querySelector('path');
            
            if (path && path.getAttribute('d').includes('M20.84 4.61a5.5 5.5')) {
                showNotification('Lista de favoritos');
            } else if (svg.querySelector('circle[cx="11"]')) {
                showNotification('Función de búsqueda');
            } else if (path && path.getAttribute('d').includes('M20 21v-2a4 4')) {
                showNotification('Perfil de usuario');
            }
        });
    });
    

});