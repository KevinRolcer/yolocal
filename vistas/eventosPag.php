<?php
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_MX.UTF-8', 'spanish');
date_default_timezone_set('America/Mexico_City');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - Yolocal</title>
    <link href="../assets/img/LogoYolocal.png" rel="icon" />

    <link rel="stylesheet" href="../assets/css/negociosCl.css">
    <link rel="stylesheet" href="../assets/css/eventos.css">
</head>


<body>

    <header class="encabezado">

        <nav class="navbar">
            <div class="logo">
                <img src="../assets/img/LogoYolocal.png" alt="Logo YoLocal">
            </div>

            <button class="menu-toggle" id="menuToggle">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            <div class="menu" id="mainMenu">
                <a href="../index.php" class="enlace" data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="texto-menu">Inicio</span>
                </a>
                <a href="../controladores/NegocioLControlador.php" class="enlace " data-tooltip="Negocios">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <span class="texto-menu">Negocios</span>
                </a>
                <a href="../vistas/empleo.php" class="enlace " data-tooltip="Empleo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span class="texto-menu">Bolsa de trabajo</span>
                </a>
                <a href="../vistas/cuponesPagina.php" class="enlace" data-tooltip="Promociones">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M2.25 12h19.5M6.375 17.25a4.875 4.875 0 0 0 4.875-4.875V12m6.375 5.25a4.875 4.875 0 0 1-4.875-4.875V12m-9 8.25h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5Zm12.621-9.44c-1.409 1.41-4.242 1.061-4.242 1.061s-.349-2.833 1.06-4.242a2.25 2.25 0 0 1 3.182 3.182ZM10.773 7.63c1.409 1.409 1.06 4.242 1.06 4.242S9 12.22 7.592 10.811a2.25 2.25 0 1 1 3.182-3.182Z" />
                    </svg>
                    <span class="texto-menu">Cupones</span>
                </a>

                <div class="submenu" data-tooltip="Nosotros">
                    <a href="../vistas/nosotros.php" class="enlace">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                        <span class="texto-menu">Nosotros</span>
                        <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </a>
                    <div class="submenu-contenido">
                        <a href="../vistas/nosotros.php">¿Quienes somos?</a>
                        <a href="https://wa.me/+522482694278">Contacto</a>
                        <a href="../vistas/nosotros.php">Patrocinadores</a>
                    </div>
                </div>
            </div>
            <div class="sesion">
                <a href="controladorEvento.php" class="btn-prueba">
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

    <main class="container">
        <div class="event-section">
            <header class="section-header">
                <h1>Próximos Eventos</h1>
                <p>Descubre los mejores eventos en Texmelucan</p>
            </header>

            <div class="event-grid">

                <?php if (!empty($eventos)): ?>
                    <?php foreach ($eventos as $evento): ?>
                        <div class="event-card" data-evento-id="<?php echo htmlspecialchars($evento['ID_Evento']); ?>">
                            <div class="card-image-container">
                                <img src="../imagenes/<?php echo htmlspecialchars($evento['RutaImagenE']); ?>" alt="<?php echo htmlspecialchars($evento['TituloE']); ?>" class="card-image">
                                <div class="card-tag categoria-default">Evento</div>
                                <div class="card-price">$<?php echo htmlspecialchars($evento['PrecioE']); ?> MXN</div>
                            </div>
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($evento['TituloE']); ?></h3>
                                <p class="card-description"><?php echo htmlspecialchars($evento['DescripcionE']); ?></p>
                                <div class="card-details">
                                    <div class="detail-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-blue">
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg>
                                        <span><?php echo strftime("%e de %B de %Y", strtotime($evento['FechaE'])); ?></span>


                                    </div>
                                    <div class="detail-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-green">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        <span><?php echo date("g:i A", strtotime($evento['HoraE'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-red">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($evento['UbicacionE']); ?></span>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-primary">Mas información</button>
                                    <button class="btn btn-secondary btn-share" title="Compartir evento">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15 14 20 9 15 4"></polyline>
                                            <path d="M4 20v-7a4 4 0 0 1 4-4h12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No se encontraron eventos próximos. ¡Vuelve pronto!</p>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <div id="modal-evento" class="modal-overlay">
        <div class="modal-contenido">
            <span class="modal-cerrar">&times;</span>
            <img id="modal-img" src="" alt="Imagen del evento">
            <div class="modal-info">
                <h2 id="modal-titulo"></h2>
                <div id="modal-tags">
                    <span id="modal-categoria" class="card-tag"></span>
                    <span id="modal-precio" class="card-price" style="background-color: #f1f1f1; color: #333; font-weight: bold;"></span>
                </div>
                <p id="modal-descripcion"></p>
                <div class="modal-details">
                    <div class="detail-item" id="modal-fecha"></div>
                    <div class="detail-item" id="modal-hora"></div>
                    <div class="detail-item" id="modal-ubicacion"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/evento.js?version=1.2"></script>
    <script src="../assets/js/menuCl.js"></script>

</body>

</html>