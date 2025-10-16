<?php
require_once __DIR__ . '/../modelos/Carrucel.php';

$negocio = null;
if (isset($_GET['id'])) {
    $idNegocio = intval($_GET['id']);
    $carrucel = new Carrucel();
    $negocio = $carrucel->obtenerNegocioPorId($idNegocio);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($negocio['nombre_negocio']); ?> - Yolocal</title>
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
    <link rel="stylesheet" href="../assets/css/negociosCl.css">
    <link rel="stylesheet" href="../assets/css/negocioD.css">
    <script defer src="../assets/js/menuCl.js"></script>
</head>

<body>
    <header class="encabezado">
        <?php include_once("header.php"); ?>
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
    <script src="../assets/js/menuCl.js"></script>



</body>

</html>