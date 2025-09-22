<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros Yolocal</title>
    <link rel="stylesheet" href="../assets/css/nosotros.css">
    <script defer src="../assets/js/nosotrosCl.js"></script>

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
                        <a href="#">Contacto</a>
                        <a href="#">Forma parte de Yolocal</a>
                    </div>
                </div>

            </div>

            <div class="sesion">
                <a href="#" class="btn-prueba">
                    <span class="btn-text-full">Unete a Yo local</span>
                    <span class="btn-text-short">Unirse</span>
                </a>
                <a href="../vistas/login.php" class="btn-sesion">
                    <span class="btn-text-full">Iniciar sesión</span>
                    <span class="btn-text-short">Entrar</span>
                </a>
            </div>
        </nav>
    </header>


    <div class="principal">
        <div class="container">
            <div class="content">
                <div class="left-content">
                    <h1 class="main-title">Yo Local<br>Si es del barrio,<br><span class="highlight">es de todos</span></h1>
                    
                    <p class="subtitle">
                        Descripción de nosotros
                    </p>
                    
                    
                    <div class="info-card">
                        <div class="card-image">
                            <img src="../assets/img/LogoYolocal.png" alt="Flores blancas">
                        </div>
                        <div class="card-content">
                            <h3>Ubicación</h3>
                            <p>Ubicación</p>
                        </div>
                    </div>
                </div>
                
                <div class="right-content">
                    <div class="image-container">
                        <div class="decorative-bg"></div>
                        <img src="../assets/img/LogoYolocal.png"  class="main-contenedor">
                    </div>
                </div>
            </div>
        </div>

        <section class="interactive-section">
            <div class="containerCl">
                <h2 class="section-title">Nosotros<br>Misión y visión</h2>
                
                <div class="interactive-content">
                    <div class="text-left">
                        <div class="info-point top-left" data-position="top-left">
                            <h3>Titulo</h3>
                            <p>Descrioción</p>
                            <div class="point-indicator"></div>
                        </div>
                        
                        <div class="info-point middle-left" data-position="middle-left">
                            <h3>Titulo</h3>
                            <p> Descripción</p>
                            <div class="point-indicator"></div>
                        </div>
                        
                        <div class="info-point bottom-left" data-position="bottom-left">
                            <h3>Titulo</h3>
                            <p>Descripción</p>
                            <div class="point-indicator"></div>
                        </div>
                    </div>
                    
                    <div class="central-image">
                        <div class="image-containerCl">
                            <div class="decorative-circle"></div>
                            <img src="../assets/img/LogoYolocal.png" alt="Logo Yolocal" class="main-tarjeta">
                            <div class="floating-particles">
                                <div class="particle particle-1"></div>
                                <div class="particle particle-2"></div>
                                <div class="particle particle-3"></div>
                                <div class="particle particle-4"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="info-point top-right" data-position="top-right">
                            <h3>Titulo</h3>
                            <p>Descripción</p>
                            <div class="point-indicator"></div>
                        </div>
                        
                        <div class="info-point middle-right" data-position="middle-right">
                            <h3>Titulo</h3>
                            <p>Descripción</p>
                            <div class="point-indicator"></div>
                        </div>
                        
                        <div class="info-point bottom-right" data-position="bottom-right">
                            <h3>Titulo</h3>
                            <p>Descripción</p>
                            <div class="point-indicator"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    
</body>
</html>