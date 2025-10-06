<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Yolocal</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
    <link rel="stylesheet" href="assets/css/inicioCl.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <script type="module" src="assets/js/pagina/funcionesHome1.js"></script>
    <script type="module" src="assets/js/pagina/funcionesHome.js"></script>
    <script defer src="assets/js/menuCl.js"></script>
    <script src="assets/js/carruselCl.js"></script>
    <script defer src="assets/js/carrusel2Cl.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <header class="encabezado">
        <nav class="navbar">
            <div class="logo">
                <img src="assets/img/LogoYolocal.png" alt="">
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
                <a href="controladores/NegocioLControlador.php" class="enlace" data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <span class="texto-menu">Negocios</span>
                </a>
                <a href="vistas/empleo.php" class="enlace " data-tooltip="Empleo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span class="texto-menu">Bolsa de trabajo</span>
                </a>


                <a href="vistas/cuponesPagina.php" class="enlace" data-tooltip="Promociones">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M2.25 12h19.5M6.375 17.25a4.875 4.875 0 0 0 4.875-4.875V12m6.375 5.25a4.875 4.875 0 0 1-4.875-4.875V12m-9 8.25h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5Zm12.621-9.44c-1.409 1.41-4.242 1.061-4.242 1.061s-.349-2.833 1.06-4.242a2.25 2.25 0 0 1 3.182 3.182ZM10.773 7.63c1.409 1.409 1.06 4.242 1.06 4.242S9 12.22 7.592 10.811a2.25 2.25 0 1 1 3.182-3.182Z" />
                    </svg>

                    <span class="texto-menu">Cupones</span>
                </a>

                <div class="submenu" data-tooltip="Nosotros">
                    <a href="vistas/nosotros.php" class="enlace">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                        <span class="texto-menu">Nosotros</span>
                        <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </a>
                    <div class="submenu-contenido">
                        <a href="vistas/nosotros.php">¿Quienes somos?</a>
                        <a href="https://wa.me/+522482694278">Contacto</a>
                        <a href="vistas/eventosPag.php">Patrocinadores</a>
                    </div>
                </div>

            </div>

            <div class="sesion">
                <a href="vistas/eventosPag.php" class="btn-prueba">
                    <span class="btn-text-full">Eventos</span>
                    <span class="btn-text-short">Eventos</span>
                </a>
                <a href="vistas/sistemaAdmin/login.php" class="btn-sesion">
                    <span class="btn-text-full">Iniciar sesión</span>
                    <span class="btn-text-short">Entrar</span>
                </a>
            </div>
        </nav>
    </header>

    <div class="principal">

        <div class="destacado">
            <div class="carousel-container">
                <div class="carousel-wrapper">
                    <div class="carousel-track" id="carouselTrack">

                    </div>
                </div>

                <button class="carousel-nav prev" id="prevBtn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button class="carousel-nav next" id="nextBtn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <div class="carousel-dots" id="carouselDots"></div>
            </div>
        </div>


        <div id="contenedorCupon" class="container">

            <h1 class="title">Cupones</h1>

            <p class="percentage">30%</p>
            <button onclick="location.href='vistas/cuponesPagina.php'" class="button">
                Ver cuponera
            </button>

            <img src="assets/img/LogoYolocal.png" class="logo logo-1 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-2 shadow-pink" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-3 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-4 shadow-pink" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-5 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-6 shadow-pink" alt="Logo">

            <img src="assets/img/LogoYolocal.png" class="logo logo-7 shadow-pink" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-8 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-9 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-10 shadow-pink" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-11 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-12 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-13 shadow-pink" alt="Logo">

            <img src="assets/img/LogoYolocal.png" class="logo logo-14 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-15 shadow-pink" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-16 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-17 shadow-pink" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-18 shadow-pink" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-19 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-20 shadow-yellow" alt="Logo">
            <img src="assets/img/LogoYolocal.png" class="logo logo-21 shadow-pink" alt="Logo">
        </div>


        <div class="contenedorC">

            <div class="descripcionC" id="Aliados">
                <h2>Aliados</h2>
                <p>Descubre a los negocios que forman parte de
                    <span>Yo <span id="span2">local</span></span>
                </p>

            </div>
            <div class="carousel-container2">
                <div class="carousel-inner2">
                    <div class="carousel-track2">
                    </div>
                </div>
            </div>
            <div class="encabezadoC" id="Populares">
                <div>
                    <h2>Populares</h2>
                    <p>Conoce a los negocios que mejor valorados de
                        <span>Texme<span id="span2">lucan</span></span>
                    </p>
                </div>

            </div>
            <div class="contenedorCarruselC">
                <div class="carruselC" id="carousel">
                    
                    <div class="tarjetaC">
                        <div class="imagenTarjetaC" style="background-image: ..."></div>
                        <div class="contenidoTarjetaC">
                            <h3 class="tituloTarjetaC">Título de negocio</h3>
                            <div class="ubicacionTarjetaC">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span>Ubicación</span>
                            </div>
                            <div class="calificacionTarjetaC">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                <span>Ver más</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="descripcionC" id="Conecta">
            <h2>Mapa de Negocios

            </h2>
            <p>Negocios que apoyan lo local en
                <span>Texme <span id="span2">lucan</span></span>
            </p>

        </div>

        <div id="map"></div>

        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="assets/js/pagina/funcionesMapa.js"></script>

        <div class="descripcionC" id="Conecta">
            <h2>Yo local Conecta

            </h2>
            <p>Encuentra todo lo que necesitas en
                <span>Yo <span id="span2">local</span></span>
            </p>

        </div>
        <div class="container">

            <div class="row row-3">
                <div class="card white">
                    <div class="card-header">
                        <div class="card-title">Eventos</div>
                        <div class="card-subtitle">Subtitulo</div>
                    </div>
                    <div class="card-content">
                        <div class="card-description">
                            Descripción
                        </div>
                        <div class="card-image">
                            <img src="assets/img/Cafeteria.jpg" alt="Cafeteria">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="card-button">Más detalles</button>
                    </div>
                </div>

                <div class="card purple">
                    <div class="card-header">
                        <div class="card-title">Bolsa de trabajo</div>
                        <div class="card-subtitle">Subtitulo</div>
                    </div>
                    <div class="card-content">
                        <div class="card-description">
                            Descripción
                        </div>
                        <div class="card-image">img</div>
                    </div>
                    <div class="card-footer">
                        <button class="card-button">Más detalles</button>
                    </div>
                </div>

                <div class="card white">
                    <div class="card-header">
                        <div class="card-title">Noticias</div>
                        <div class="card-subtitle">Subtitulo</div>
                    </div>
                    <div class="card-content">
                        <div class="card-description">
                            Descripción
                        </div>
                        <div class="card-image">
                            <img id="noticiasCl" src="assets/img/noticias.png" alt="Cafeteria">

                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="card-button">Más detalles</button>
                    </div>
                </div>
            </div>

            <div class="row row-2">
                <div class="card white">
                    <div class="card-header">
                        <div class="card-title">Contacto</div>
                        <div class="card-subtitle">Subtitulo</div>
                    </div>
                    <div class="card-content">
                        <div class="card-description">
                            Descripción
                        </div>
                        <div class="card-image">imagen</div>
                    </div>
                    <div class="card-footer">
                        <button class="card-button">Más detalles</button>
                    </div>
                </div>

                <div class="card yellow">
                    <div class="card-header">
                        <div class="card-title">Patrocinadores</div>
                        <div class="card-subtitle">Negocios</div>
                    </div>
                    <div class="card-content">
                        <div class="card-description">
                            Explora mas tipos de negocios que forman parte de Yolocal
                        </div>
                        <div id="cardLogo" class="card-image">
                            <img src="assets/img/LogoYolocal.png" alt="">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="card-button">Más detalles</button>
                    </div>
                </div>
            </div>
        </div>





    </div>

    <footer>
        <div class="footer-container">
            <div class="buttons-top">
                <a href="vistas/nosotros.php" class="btn btn-yellow">Conócenos</a>
                <a href="#" class="btn btn-white">Patrocinadores</a>
                <a href="vistas/eventosPag.php" class="btn btn-yellow">Eventos</a>
            </div>

            <div class="footer-top">
                <div class="footer-column">
                    <ul>
                        <li><a href="vistas/nosotros.php">Nosotros</a></li>
                        <li><a href="#Populares">Populares</a></li>
                        <li><a href="#Aliados">Aliados</a></li>
                        <li><a href="vistas/cuponesPagina.php">Cúpones</a></li>
                        <li><a href="#Conecta">Yo Local Conecta</a></li>

                    </ul>
                </div>

                <div class="footer-column">
                    <ul>
                        <li><a href="vistas/empleo.php">Bolsa de Trabajo</a></li>
                        <li><a href="vistas/eventosPag.php">Eventos</a></li>
                        <li><a href="https://wa.me/+522482694278">Contacto</a></li>
                        <li><a href="vistas/sistemaAdmin/login.php">Inicia Sesión</a></li>
                        <li><a href="vistas/mapa.php">Mapa</a></li>

                    </ul>
                </div>

                <div class="footer-column">
                    <div class="social-links">

                        <a href="https://www.facebook.com/YoLocalTex/" class="social-link">
                            <span class="social-icon"><i class="fab fa-facebook-f"></i></span>
                            <span>Facebook</span>
                        </a>
                        <a href="https://www.instagram.com/yolocal_tex?igsh=MW9peGVreTlpOWptcA==" class="social-link">
                            <span class="social-icon"><i class="fab fa-instagram"></i></span>
                            <span>Instagram</span>
                        </a>
                        <a href="https://www.tiktok.com/@yolocaltex?_t=ZS-908kLNRWj15&_r=1" class="social-link">
                            <span class="social-icon"><i class="fab fa-tiktok"></i></span>
                            <span>Tik Tok</span>
                        </a>
                    </div>
                </div>

                <div class="footer-right">
                    <div class="heart-logo">
                        <img src="assets/img/LogoYolocal.png" height="100" alt="Logo">
                    </div>
                    <div class="contact-info">
                        <p><strong>C. 16 de Septiembre 311, Col Centro</strong></p>
                        <p>74000 San Martín Texmelucan de Labastida, Pue.</p>
                        <p style="margin-top: 15px;">Tel: 2482694278</p>
                        <p><strong>Yo Local, 2025</strong></p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 Yo Local Texmelucan. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

</body>

</html>