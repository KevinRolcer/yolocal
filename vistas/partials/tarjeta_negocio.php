<?php
$nombreCategoria = $negocio['nombre_categoria'] ?? 'General';
$claseCategoria = strtolower(str_replace(' ', '', $nombreCategoria));
?>
<div class="negocio-card">
    <div class="categoria-tag <?php echo htmlspecialchars($claseCategoria, ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo htmlspecialchars($nombreCategoria, ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <div class="negocio-content">
        <div class="negocio-header">
            <span class="negocio-icono-marco">
                <img src="<?php echo htmlspecialchars($negocio['Rutaicono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    alt="Icono de <?php echo htmlspecialchars($negocio['nombre_negocio'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="negocio-icono">
                <span class="negocio-icono-fallback" role="status" hidden>Imagen no disponible</span>
            </span>

            <h3 class="negocio-nombre">
                <?php echo htmlspecialchars($negocio['nombre_negocio'], ENT_QUOTES, 'UTF-8'); ?>
            </h3>
        </div>

        <?php if (!empty($negocio['DescripcionN'])): ?>
            <p class="negocio-descripcion">
                <?php echo htmlspecialchars($negocio['DescripcionN'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($negocio['Direccion'])): ?>
            <div class="negocio-direccion">
                <?php if (!empty($negocio['GoogleMaps'])): ?>
                    <a href="<?php echo htmlspecialchars($negocio['GoogleMaps'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo htmlspecialchars($negocio['Direccion'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php else: ?>
                    <?php echo htmlspecialchars($negocio['Direccion'], ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <button class="btn-conocer-mas" onclick="verDetalle(<?php echo (int) $negocio['ID_Negocio']; ?>)">
            Conocer más
        </button>
    </div>
</div>
