// Menú responsive para Yolocal
class ResponsiveMenu {
    constructor() {
        this.menuToggle = document.getElementById('menuToggle');
        this.mainMenu = document.getElementById('mainMenu');
        this.submenus = document.querySelectorAll('.submenu');
        this.body = document.body;
        this.isMenuOpen = false;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.handleResize();
        this.setupTooltips();
    }
    
    setupEventListeners() {
        // Toggle del menú hamburguesa
        this.menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMenu();
        });
        
        // Cerrar menú al hacer click fuera
        document.addEventListener('click', (e) => {
            if (this.isMenuOpen && !this.mainMenu.contains(e.target) && !this.menuToggle.contains(e.target)) {
                this.closeMenu();
            }
        });
        
        // Manejar submenús en móvil
        this.submenus.forEach(submenu => {
            const link = submenu.querySelector('.enlace');
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    this.toggleSubmenu(submenu);
                }
            });
        });
        
        // Cerrar menú al cambiar tamaño de pantalla
        window.addEventListener('resize', () => {
            this.handleResize();
        });
        
        // Cerrar menú con tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMenuOpen) {
                this.closeMenu();
            }
        });
        
        // Prevenir scroll del body cuando el menú está abierto
        this.mainMenu.addEventListener('touchmove', (e) => {
            e.stopPropagation();
        });
    }
    
    toggleMenu() {
        if (this.isMenuOpen) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }
    
    openMenu() {
        this.mainMenu.classList.add('active');
        this.menuToggle.classList.add('active');
        this.body.style.overflow = 'hidden';
        this.isMenuOpen = true;
        
        // Añadir clase para animaciones escalonadas
        setTimeout(() => {
            this.mainMenu.style.visibility = 'visible';
        }, 10);
    }
    
    closeMenu() {
        this.mainMenu.classList.remove('active');
        this.menuToggle.classList.remove('active');
        this.body.style.overflow = '';
        this.isMenuOpen = false;
        
        // Cerrar todos los submenús
        this.submenus.forEach(submenu => {
            submenu.classList.remove('active');
        });
    }
    
    toggleSubmenu(submenu) {
        // Cerrar otros submenús
        this.submenus.forEach(otherSubmenu => {
            if (otherSubmenu !== submenu) {
                otherSubmenu.classList.remove('active');
            }
        });
        
        // Toggle el submenú actual
        submenu.classList.toggle('active');
    }
    
    handleResize() {
        if (window.innerWidth > 768) {
            this.closeMenu();
            this.body.style.overflow = '';
            
            // Resetear submenús
            this.submenus.forEach(submenu => {
                submenu.classList.remove('active');
            });
        }
    }
    
    setupTooltips() {
        // Crear tooltips para móvil
        const menuItems = document.querySelectorAll('[data-tooltip]');
        
        menuItems.forEach(item => {
            const tooltipText = item.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            
            item.style.position = 'relative';
            item.appendChild(tooltip);
            
            // Mostrar tooltip en hover (solo en pantallas táctiles)
            if ('ontouchstart' in window) {
                let touchTimer;
                
                item.addEventListener('touchstart', () => {
                    touchTimer = setTimeout(() => {
                        if (window.innerWidth <= 480) {
                            tooltip.style.opacity = '1';
                            tooltip.style.visibility = 'visible';
                        }
                    }, 500);
                });
                
                item.addEventListener('touchend', () => {
                    clearTimeout(touchTimer);
                    setTimeout(() => {
                        tooltip.style.opacity = '0';
                        tooltip.style.visibility = 'hidden';
                    }, 1500);
                });
            }
        });
    }
}

// Utilidades adicionales
class MenuUtils {
    static highlightActiveSection() {
        const sections = document.querySelectorAll('section[id]');
        const menuLinks = document.querySelectorAll('.enlace[href^="#"]');
        
        window.addEventListener('scroll', () => {
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                if (window.pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });
            
            menuLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    }
    
    static smoothScroll() {
        const menuLinks = document.querySelectorAll('.enlace[href^="#"]');
        
        menuLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const targetId = link.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    e.preventDefault();
                    
                    const offsetTop = targetSection.offsetTop - 80; 
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    
                    if (window.innerWidth <= 768) {
                        menu.closeMenu();
                    }
                }
            });
        });
    }
    
    static addScrollEffect() {
        const header = document.querySelector('.encabezado');
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', () => {
            const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (currentScrollTop > lastScrollTop && currentScrollTop > 100) {
                // Scrolling down
                header.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                header.style.transform = 'translateY(0)';
            }
            
            // Añadir sombra cuando hay scroll
            if (currentScrollTop > 10) {
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.boxShadow = 'none';
            }
            
            lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop;
        });
        
        // Añadir transición
        header.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
    }
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar menú responsive
    window.menu = new ResponsiveMenu();
    
    // Inicializar utilidades opcionales
    MenuUtils.smoothScroll();
    MenuUtils.addScrollEffect();
    
    // Si hay secciones en la página, activar highlight automático
    if (document.querySelectorAll('section[id]').length > 0) {
        MenuUtils.highlightActiveSection();
    }
    
    console.log('Menú Yolocal inicializado correctamente');
});

// Funciones globales de utilidad
window.YolocalMenu = {
    closeMenu: () => window.menu.closeMenu(),
    openMenu: () => window.menu.openMenu(),
    toggleMenu: () => window.menu.toggleMenu()
};