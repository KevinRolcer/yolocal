<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($negocio['nombre_negocio']); ?> - Yolocal</title>
    <link rel="stylesheet" href="../assets/css/negociosCl.css">
    <link rel="stylesheet" href="../assets/css/negocioD.css">
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
                <a href="../index.php?pag=negocios" class="enlace" data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="texto-menu">Inicio</span>
                </a>
                <a href="../controladores/NegocioLControlador.php" class="enlace active" data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <span class="texto-menu">Negocios</span>
                </a>

                <a href="../vistas/cuponesPagina.php" class="enlace" data-tooltip="Promociones">
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
                <a href="../vistas/sistemaAdmin/login.php" class="btn-sesion">
                    <span class="btn-text-full">Iniciar sesión</span>
                    <span class="btn-text-short">Entrar</span>
                </a>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="detalle-card">

            <div class="banner-distribuidor">
                <img src="../assets/img/banner-yolocal2.png" alt="Yolocal - Tu directorio local">
            </div>


            <div class="detalle-contenido">
                <span class="categoria-tag"><?php echo htmlspecialchars($negocio['nombre_categoria']); ?></span>
                <h1><?php echo htmlspecialchars($negocio['nombre_negocio']); ?></h1>
                <p class="detalle-descripcion"><?php echo htmlspecialchars($negocio['DescripcionN']); ?></p>


                <?php if (!empty($imagenes)): ?>
                    <div class="swiper miCarrusel">
                        <div class="swiper-wrapper">
                            <?php foreach ($imagenes as $imagen): ?>
                                <div class="swiper-slide">
                                    <img src="<?php echo htmlspecialchars($imagen['ruta_imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($negocio['nombre_negocio']); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="imagen-limpia">
                        <img src="<?php echo htmlspecialchars($negocio['Rutaicono']); ?>" alt="Icono de <?php echo htmlspecialchars($negocio['nombre_negocio']); ?>">
                    </div>
                <?php endif; ?>

                <div class="info-seccion social-icons">
                    <?php
                    if (!empty($negocio['SitioWeb']) || !empty($negocio['Facebook']) || !empty($negocio['Instagram']) || !empty($negocio['TikTok'])) {
                    ?>
                        <h2>Redes Sociales</h2>
                        <div class="icon-container">
                            <?php if (!empty($negocio['SitioWeb'])): ?>
                                <a href="<?php echo htmlspecialchars($negocio['SitioWeb']); ?>" target="_blank" title="Sitio Web">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($negocio['Facebook'])): ?>
                                <a href="<?php echo htmlspecialchars($negocio['Facebook']); ?>" target="_blank" title="Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($negocio['Instagram'])): ?>
                                <a href="<?php echo htmlspecialchars($negocio['Instagram']); ?>" target="_blank" title="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($negocio['TikTok'])): ?>
                                <a href="<?php echo htmlspecialchars($negocio['TikTok']); ?>" target="_blank" title="TikTok">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php
                    } else {
                        echo '<h2>Este negocio no cuenta con redes sociales disponibles.</h2>';
                    }
                    ?>
                </div>


                <div class="info-seccion">
                    <h2>Horarios</h2>
                    <ul class="lista-horarios">
                        <?php if (!empty($horarios)): ?>
                            <?php foreach ($horarios as $horario): ?>
                                <li><strong><?php echo htmlspecialchars($horario['dia_semana']); ?>:</strong> <?php echo date("g:i a", strtotime($horario['hora_apertura'])) . ' - ' . date("g:i a", strtotime($horario['hora_cierre'])); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Horario no disponible.</li>
                        <?php endif; ?>
                    </ul>
                </div>


                <div class="info-seccion">
                    <h2>Contacto y Ubicación</h2>
                    <?php if (!empty($negocio['Direccion'])): ?>
                        <p><strong>Dirección:</strong>
                            <?php if (!empty($negocio['GoogleMaps'])): ?>
                                <a href="<?php echo htmlspecialchars($negocio['GoogleMaps'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                                    <?php echo htmlspecialchars($negocio['Direccion'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($negocio['Direccion'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <p><strong>Teléfono:</strong> <a href="tel:<?php echo htmlspecialchars($negocio['Telefono']); ?>"><?php echo htmlspecialchars($negocio['Telefono']); ?></a></p>
                </div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="../assets/js/pagina/negociosCl.js"></script>



</body>

</html>