<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros Yolocal</title>
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
    <link rel="stylesheet" href="../assets/css/nosotros.css">
    <script defer src="../assets/js/nosotrosCl.js"></script>
    <script defer src="../assets/js/menuCl.js"></script>
</head>
<body>
    <header class="encabezado">
        <nav class="navbar">
            <div class="logo">
                <img src="../assets/img/LogoYolocal.png" alt="">
            </div>
            
            <button class="menu-toggle" id="menuToggle">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
            
            <div class="menu" id="mainMenu">
                <a href="../index.php" class="enlace " data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="texto-menu">Inicio</span>
                </a>
                <a href="../controladores/NegocioLControlador.php" class="enlace" data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <span class="texto-menu">Negocios</span>
                </a>

                <a href="cuponesPagina.php" class="enlace" data-tooltip="Promociones">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M2.25 12h19.5M6.375 17.25a4.875 4.875 0 0 0 4.875-4.875V12m6.375 5.25a4.875 4.875 0 0 1-4.875-4.875V12m-9 8.25h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5Zm12.621-9.44c-1.409 1.41-4.242 1.061-4.242 1.061s-.349-2.833 1.06-4.242a2.25 2.25 0 0 1 3.182 3.182ZM10.773 7.63c1.409 1.409 1.06 4.242 1.06 4.242S9 12.22 7.592 10.811a2.25 2.25 0 1 1 3.182-3.182Z" />
                    </svg>

                    <span class="texto-menu">Cupones</span>
                </a>

                <div class="submenu" data-tooltip="Nosotros">
                    <a href="nosotros.php" class="enlace active">
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
           <a href="../controladores/controladorEvento.php" class="btn-prueba">
                    <span class="btn-text-full">Eventos</span>
                    <span class="btn-text-short">Eventos</span>
                </a>
                <a href="../vistas/sistemaAdmin/login.php" class="btn-sesion">
                    <span class="btn-text-full">Iniciar sesión</span>
                    <span class="btn-text-short">Entrar</span>
                </a>
            </div>
        </nav>
    </header>


    <div class="nosotros-container">
        <section class="hero" id="inicio">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>
                        Yo Local Texmelucan:
                        <span class="highlight">el impulso que nace del barrio</span>
                    </h1>
                    <p>
                        Conectamos, empoderamos y visibilizamos todo lo bueno de Texmelucan. 
                        Desde emprendedores hasta artistas, deportistas y creadores culturales.
                    </p>
                    <a href="#historia" class="cta-button">Conoce nuestra historia</a>
                </div>
                <div class="hero-image">
                    <div class="hero-logo-space">
                        <img src="../assets/img/LogoYolocal.png" alt="">
                    </div>
                </div>
            </div>
        </section>

        <section class="historia fade-in" id="historia">
            <h2 class="section-title">Nuestra Historia</h2>
            <div class="historia-content">
                <p>
                    Yo Local nació en plena pandemia, cuando un grupo de amigos vio cómo los negocios cerraban 
                    y nuestra comunidad necesitaba un empujón. No podíamos quedarnos quietos, así que creamos 
                    un espacio donde emprendedores, artistas y deportistas de Texmelucan pudieran conectarse, 
                    crecer y apoyarse entre todos, y donde se pudiera difundir lo bueno que pasa en nuestra ciudad: 
                    el arte, la cultura, el deporte y cada emprendimiento local.
                </p>
                <p>
                    Hoy, en 2025, esta iniciativa ya está en marcha, es independiente y está liderada desde hace 
                    cinco años por <strong>Marco Hernández</strong>, Director General de Yo Local, con el impulso 
                    de <strong>File Ramírez</strong>, para fortalecer lo local y dar visibilidad a todo el talento 
                    que hay en nuestra comunidad.
                </p>
                <div class="highlight-box">
                    <p style="font-size: 1.2rem; font-weight: 600; margin: 0;">
                        Yo Local no viene a reemplazar las acciones que los emprendedores hacen día a día, 
                        sino a sumar, complementar y darles un lugar donde brillar.
                    </p>
                </div>
            </div>
        </section>

        <section class="logo-significado fade-in">
            <h2 class="section-title">Lo que buscamos con nuestro logo</h2>
            <p style="text-align: center; color: var(--blanco); font-size: 1.2rem; max-width: 800px; margin: 0 auto 3rem;">
                Cuando veas el logo de Yo Local en un negocio, queremos que pienses en:
            </p>
            <div class="logo-cards">
                <div class="logo-card">
                    <div class="number">01</div>
                    <h3>Calidad</h3>
                    <p>Calidad en todo lo que hacen. Cada negocio con nuestro logo representa excelencia y compromiso con la comunidad.</p>
                </div>
                <div class="logo-card">
                    <div class="number">02</div>
                    <h3>Identidad Local</h3>
                    <p>Que es de aquí, que es de Texmelucan. Orgullo por nuestras raíces y lo que nos hace únicos como comunidad.</p>
                </div>
            </div>
            <p class="eslogan">"Yo Local, es de aquí, es de todos"</p>
        </section>

        <section class="vision fade-in" id="vision">
            <h2 class="section-title">Nuestra Visión</h2>
            <div class="vision-content">
                <div class="vision-text">
                    <h3>Visión 2025</h3>
                    <p>
                        Ser el espacio que conecte, empodere y visibilice todo lo bueno de Texmelucan, 
                        desde emprendedores hasta artistas, deportistas y creadores culturales, 
                        fomentando una comunidad unida y activa.
                    </p>
                </div>
                <div class="vision-image">
                    <img src="../assets/img/LogoYolocal.png" alt="">
                </div>
            </div>
        </section>

        <section class="valores fade-in" id="valores">
            <h2 class="section-title">Nuestros Valores</h2>
            <div class="valores-grid">
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </div>
                    <h3>Cercanía</h3>
                    <p>Somos comunidad, vecinos y aliados. Juntos construimos un Texmelucan más fuerte.</p>
                </div>
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    </div>
                    <h3>Orgullo Local</h3>
                    <p>Promovemos lo nuestro, lo que hace grande a Texmelucan y nos llena de identidad.</p>
                </div>
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                        </svg>
                    </div>
                    <h3>Empoderamiento</h3>
                    <p>Compartimos herramientas, consejos y apoyo para que cada emprendedor dé su siguiente paso.</p>
                </div>
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                        </svg>
                    </div>
                    <h3>Colaboración</h3>
                    <p>Juntos logramos más, apoyando y difundiendo los esfuerzos de cada quien.</p>
                </div>
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                    <h3>Compromiso</h3>
                    <p>Trabajamos de corazón para que lo local tenga voz, visibilidad y fuerza.</p>
                </div>
            </div>
        </section>
    </div>
    
</body>
</html>