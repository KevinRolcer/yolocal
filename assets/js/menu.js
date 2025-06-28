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
        this.menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMenu();
        });
        
        document.addEventListener('click', (e) => {
            if (this.isMenuOpen && !this.mainMenu.contains(e.target) && !this.menuToggle.contains(e.target)) {
                this.closeMenu();
            }
        });
        
        this.submenus.forEach(submenu => {
            const link = submenu.querySelector('.enlace');
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    this.toggleSubmenu(submenu);
                }
            });
        });
        
        window.addEventListener('resize', () => {
            this.handleResize();
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMenuOpen) {
                this.closeMenu();
            }
        });
        
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
        
        setTimeout(() => {
            this.mainMenu.style.visibility = 'visible';
        }, 10);
    }
    
    closeMenu() {
        this.mainMenu.classList.remove('active');
        this.menuToggle.classList.remove('active');
        this.body.style.overflow = '';
        this.isMenuOpen = false;
        
        this.submenus.forEach(submenu => {
            submenu.classList.remove('active');
        });
    }
    
    toggleSubmenu(submenu) {
        this.submenus.forEach(otherSubmenu => {
            if (otherSubmenu !== submenu) {
                otherSubmenu.classList.remove('active');
            }
        });
        
        submenu.classList.toggle('active');
    }
    
    handleResize() {
        if (window.innerWidth > 768) {
            this.closeMenu();
            this.body.style.overflow = '';
            
            this.submenus.forEach(submenu => {
                submenu.classList.remove('active');
            });
        }
    }
    
    setupTooltips() {
        const menuItems = document.querySelectorAll('[data-tooltip]');
        
        menuItems.forEach(item => {
            const tooltipText = item.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            
            item.style.position = 'relative';
            item.appendChild(tooltip);
            
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
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            
            if (currentScrollTop > 10) {
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.boxShadow = 'none';
            }
            
            lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop;
        });
        
        header.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.menu = new ResponsiveMenu();
    
    MenuUtils.smoothScroll();
    MenuUtils.addScrollEffect();
    
    if (document.querySelectorAll('section[id]').length > 0) {
        MenuUtils.highlightActiveSection();
    }
    
    console.log('Menú Yolocal inicializado correctamente');
});

window.YolocalMenu = {
    closeMenu: () => window.menu.closeMenu(),
    openMenu: () => window.menu.openMenu(),
    toggleMenu: () => window.menu.toggleMenu()
};