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
                                <?php echo htmlspecialchars($negocio['Direccion']); ?>
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

  <script src="../assets/js/negociosL.js"></script>
</body>
</html>