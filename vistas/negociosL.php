<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negocios Yolocal</title>
    <link rel="stylesheet" href="../assets/css/negociosCl.css">
    <link rel="stylesheet" href="../assets/css/negocioL.css">
</head>

<body>
    <header class="encabezado">
        <?php include_once("header.php"); ?>
    </header>

    <div class="seccion-filtros">
        <div class="busqueda-seccion">
            <label for="busqueda">Buscar negocio:</label>
            <input type="text" id="busqueda" name="busqueda" placeholder="Nombre del negocio..."
                onkeyup="buscarEnTiempoReal()"
                value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>">
        </div>

        <div class="filtro-seccion">
            <label for="filtroCategoria">O filtrar por categoría:</label>
            <select name="categoria" id="filtroCategoria" onchange="filtrarPorCategoria()">
                <option value="">-- Ver Todas --</option>
                <?php
                if (!empty($categorias)):
                    $idCategoriaActual = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
                    foreach ($categorias as $cat):
                        $selected = ($cat['ID_Categoria'] == $idCategoriaActual) ? 'selected' : '';
                ?>
                        <option value="<?php echo $cat['ID_Categoria']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($cat['Descripcion']); ?>
                        </option>
                <?php
                    endforeach;
                endif;
                ?>
            </select>
        </div>
    </div>

    <div class="negocios-container">
        <?php if (!empty($negocios)): ?>
            <?php foreach ($negocios as $negocio): ?>
                <div class="negocio-card">

                    <div class="categoria-tag <?php echo strtolower(str_replace(' ', '', $negocio['nombre_categoria'] ?? 'general')); ?>">
                        <?php echo htmlspecialchars($negocio['nombre_categoria'] ?? 'General'); ?>
                    </div>

                    <div class="negocio-content">
                        <div class="negocio-header">
                            <img src="<?php echo htmlspecialchars($negocio['Rutaicono']); ?>"
                                alt="Icono de <?php echo htmlspecialchars($negocio['nombre_negocio']); ?>"
                                class="negocio-icono">

                            <h3 class="negocio-nombre">
                                <?php echo htmlspecialchars($negocio['nombre_negocio']); ?>
                            </h3>
                        </div>

                        <?php if (!empty($negocio['DescripcionN'])): ?>
                            <p class="negocio-descripcion">
                                <?php echo htmlspecialchars($negocio['DescripcionN']); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($negocio['Direccion'])): ?>
                            <div class="negocio-direccion">
                                <?php if (!empty($negocio['GoogleMaps'])): ?>
                                    <a href="<?php echo htmlspecialchars($negocio['GoogleMaps'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                                        <?php echo htmlspecialchars($negocio['Direccion'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($negocio['Direccion'], ENT_QUOTES, 'UTF-8'); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <button class="btn-conocer-mas" onclick="verDetalle(<?php echo $negocio['ID_Negocio']; ?>)">
                            Conocer más
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <h3>No hay negocios disponibles</h3>
                <p>Intenta con otra búsqueda o categoría.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($negocios) && isset($total_paginas) && $total_paginas > 1): ?>
        <div class="paginacion">
            <?php

            $parametros_url = [];
            if (!empty($_GET['categoria'])) {
                $parametros_url['categoria'] = $_GET['categoria'];
            }
            ?>
            <?php if ($pagina_actual > 1): ?>
                <?php $parametros_url['pagina'] = $pagina_actual - 1; ?>
                <a href="?<?php echo http_build_query($parametros_url); ?>">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <?php $parametros_url['pagina'] = $i; ?>
                <a href="?<?php echo http_build_query($parametros_url); ?>" class="<?php echo ($pagina_actual == $i) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
                <?php $parametros_url['pagina'] = $pagina_actual + 1; ?>
                <a href="?<?php echo http_build_query($parametros_url); ?>">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <script src="../assets/js/negociosL.js"></script>
</body>

</html>