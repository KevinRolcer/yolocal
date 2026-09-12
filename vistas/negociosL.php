<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negocios Yolocal</title>
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
    <link rel="stylesheet" href="../assets/css/negociosCl.css">
    <link rel="stylesheet" href="../assets/css/negocioL.css?v=<?= filemtime(__DIR__ . '/../assets/css/negocioL.css') ?>">
    <script defer src="../assets/js/menuCl.js"></script>
</head>

<body class="catalogo-negocios">
    <header class="encabezado">
        <?php include_once("header.php"); ?>
    </header>
    <div class="principal">
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
            <?php
            $paginaActual = (int) $pagina_actual;
            $totalPaginas = (int) $total_paginas;
            $parametrosPagina = array_intersect_key($_GET, array_flip(['categoria', 'busqueda']));
            $enlacePagina = static function ($pagina) use ($parametrosPagina) {
                return '?' . htmlspecialchars(http_build_query(array_merge($parametrosPagina, ['pagina' => $pagina])), ENT_QUOTES, 'UTF-8');
            };
            if ($totalPaginas <= 5) {
                $paginasVisibles = range(1, $totalPaginas);
            } else {
                $inicioVentana = max(2, min($paginaActual - 1, $totalPaginas - 2));
                $finVentana = min($totalPaginas - 1, max($paginaActual + 1, 3));
                $paginasVisibles = array_merge([1], range($inicioVentana, $finVentana), [$totalPaginas]);
            }
            ?>
            <nav class="paginacion" aria-label="Páginas de negocios">
                <p class="paginacion-resumen">Página <strong><?= $paginaActual ?></strong> de <?= $totalPaginas ?></p>
                <div class="paginacion-controles">
                    <?php if ($paginaActual > 1): ?>
                        <a class="paginacion-direccion" href="<?= $enlacePagina($paginaActual - 1) ?>" rel="prev">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m14 6-6 6 6 6" /></svg>
                            <span>Anterior</span>
                        </a>
                    <?php else: ?>
                        <span class="paginacion-direccion" aria-disabled="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m14 6-6 6 6 6" /></svg>
                            <span>Anterior</span>
                        </span>
                    <?php endif; ?>

                    <div class="paginacion-numeros">
                        <?php $paginaAnterior = 0; ?>
                        <?php foreach ($paginasVisibles as $numeroPagina): ?>
                            <?php if ($paginaAnterior && $numeroPagina > $paginaAnterior + 1): ?>
                                <span class="paginacion-puntos" aria-hidden="true">&hellip;</span>
                            <?php endif; ?>
                            <?php if ($numeroPagina === $paginaActual): ?>
                                <span class="paginacion-numero active" aria-current="page" aria-label="Página <?= $numeroPagina ?>, actual"><?= $numeroPagina ?></span>
                            <?php else: ?>
                                <a class="paginacion-numero" href="<?= $enlacePagina($numeroPagina) ?>" aria-label="Ir a la página <?= $numeroPagina ?>"><?= $numeroPagina ?></a>
                            <?php endif; ?>
                            <?php $paginaAnterior = $numeroPagina; ?>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a class="paginacion-direccion" href="<?= $enlacePagina($paginaActual + 1) ?>" rel="next">
                            <span>Siguiente</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m10 6 6 6-6 6" /></svg>
                        </a>
                    <?php else: ?>
                        <span class="paginacion-direccion" aria-disabled="true">
                            <span>Siguiente</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m10 6 6 6-6 6" /></svg>
                        </span>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>
    </div>
    
    <script src="../assets/js/negociosL.js"></script>
</body>

</html>
