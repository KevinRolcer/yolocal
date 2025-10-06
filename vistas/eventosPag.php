
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
     <link rel="stylesheet" href="../assets/css/eventos.css"> </head>

<body>

     <header class="encabezado">
        <?php include_once("header.php"); ?>
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
                        <div class="event-card">
                            <div class="card-image-container">
                                <img src="<?php echo htmlspecialchars($evento['RutaImagenE']); ?>" alt="<?php echo htmlspecialchars($evento['TituloE']); ?>" class="card-image">
                                <div class="card-tag categoria-default">Evento</div> 
                                <div class="card-price">$<?php echo htmlspecialchars($evento['PrecioE']); ?> MXN</div>
                            </div>
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($evento['TituloE']); ?></h3>
                                <p class="card-description"><?php echo htmlspecialchars($evento['DescripcionE']); ?></p>
                                <div class="card-details">
                                    <div class="detail-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-blue"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                        <span><?php echo strftime("%e de %B de %Y", strtotime($evento['FechaE'])); ?></span>


                                    </div>
                                    <div class="detail-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-green"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span><?php echo date("g:i A", strtotime($evento['HoraE'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-red"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span><?php echo htmlspecialchars($evento['UbicacionE']); ?></span>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-primary">Comprar Boletos</button>
                                    <button class="btn btn-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
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
    <script src="/yolocal/assets/js/evento.js"></script>
</body>
</html>