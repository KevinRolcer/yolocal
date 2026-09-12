<nav class="navbar">
    <div class="logo">
        <img src="../assets/img/LogoYolocal.png" alt="local">
    </div>
    
    <!-- Menú móvil-->
    <button class="menu-toggle" id="menuToggle">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
    </button>
    
    <div class="menu" id="mainMenu">
        <a href="#" class="enlace active" data-tooltip="Inicio">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="texto-menu">Inicio</span>
        </a>
<a href="../controladores/controladorEvento.php" class="enlace" data-tooltip="Eventos">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><rect x="3" y="5" width="18" height="16" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M3 11h18M8 15h2M14 15h2" /></svg>
            <span class="texto-menu">Eventos</span>
        </a>

        <a href="#" class="enlace" data-tooltip="Promociones">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M2.25 12h19.5M6.375 17.25a4.875 4.875 0 0 0 4.875-4.875V12m6.375 5.25a4.875 4.875 0 0 1-4.875-4.875V12m-9 8.25h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5Zm12.621-9.44c-1.409 1.41-4.242 1.061-4.242 1.061s-.349-2.833 1.06-4.242a2.25 2.25 0 0 1 3.182 3.182ZM10.773 7.63c1.409 1.409 1.06 4.242 1.06 4.242S9 12.22 7.592 10.811a2.25 2.25 0 1 1 3.182-3.182Z" />
            </svg>

            <span class="texto-menu">Cupones</span>
        </a>

        <div class="submenu" data-tooltip="Nosotros">
            <a href="#" class="enlace">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
                <span class="texto-menu">Nosotros</span>
                <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                </svg>
            </a>
            <div class="submenu-contenido">
                <a href="#">¿Quienes somos?</a>
                <a href="https://wa.me/+522482694278">Contacto</a>
                <a href="#">Patrocinadores</a>
            </div>
        </div>

    </div>

    <div class="sesion">
                <a href="../controladores/NegocioLControlador.php" class="btn-prueba">
                    <span class="btn-text-full">Aliados</span>
                    <span class="btn-text-short">Aliados</span>
                </a>
        <a href="#" class="btn-sesion">
            <span class="btn-text-full">Iniciar sesión</span>
            <span class="btn-text-short">Entrar</span>
        </a>
    </div>
</nav>
