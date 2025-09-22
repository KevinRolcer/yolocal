<?php
require_once '../modelos/NegocioLModelo.php'; 
require_once '../modelos/CategoriaModelo.php';
require_once '../config.php'; 

$db = dbConectar();


if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $negocios = []; 
    if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
        $termino = trim($_GET['busqueda']);
        $negocios = NegocioLModelo::buscarPorNombre($db, $termino);
    } else {
        $negocios = NegocioLModelo::obtenerTodos($db, 6); // Limita a 6 para no sobrecargar
    }

    $htmlResultados = '';
    
    if (!empty($negocios)) {
        foreach ($negocios as $negocio) {
            $nombreCategoriaLimpio = strtolower(str_replace(' ', '', $negocio['nombre_categoria'] ?? 'general'));
            $nombreCategoria = htmlspecialchars($negocio['nombre_categoria'] ?? 'General');
            $rutaIcono = htmlspecialchars($negocio['Rutaicono']);
            $nombreNegocio = htmlspecialchars($negocio['nombre_negocio']);
            $descripcionN = htmlspecialchars($negocio['DescripcionN']);
            $direccion = htmlspecialchars($negocio['Direccion']);
            $idNegocio = $negocio['ID_Negocio'];

            $htmlResultados .= "
                <div class='negocio-card'>
                    <div class='categoria-tag {$nombreCategoriaLimpio}'>{$nombreCategoria}</div>
                    <div class='negocio-content'>
                        <div class='negocio-header'>
                            <img src='{$rutaIcono}' alt='Icono de {$nombreNegocio}' class='negocio-icono'>
                            <h3 class='negocio-nombre'>{$nombreNegocio}</h3>
                        </div>
                        <p class='negocio-descripcion'>{$descripcionN}</p>
                        <div class='negocio-direccion'>{$direccion}</div>
                        <button class='btn-conocer-mas' onclick='verDetalle({$idNegocio})'>Conocer más</button>
                    </div>
                </div>
            ";
        }
    } else {
        $htmlResultados = "<div class='empty-state'><h3>No se encontraron negocios</h3><p>Intenta con otro término de búsqueda.</p></div>";
    }

    echo $htmlResultados;
    exit();

} else {  

    $negocios_por_pagina = 21;

    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    if ($pagina_actual < 1) {
        $pagina_actual = 1;
    }

    
    $inicio = ($pagina_actual - 1) * $negocios_por_pagina;

    $id_categoria = isset($_GET['categoria']) && is_numeric($_GET['categoria']) ? (int)$_GET['categoria'] : null;
    $termino_busqueda = isset($_GET['busqueda']) && !empty(trim($_GET['busqueda'])) ? trim($_GET['busqueda']) : null;
    
    
    $total_negocios = NegocioLModelo::contarTodos($db, $id_categoria, $termino_busqueda);

    
    $total_paginas = ceil($total_negocios / $negocios_por_pagina);

    
    $negocios = NegocioLModelo::obtenerTodosPaginados($db, $inicio, $negocios_por_pagina, $id_categoria, $termino_busqueda);

  
    $categorias = CategoriaModelo::obtenerTodas($db);

   
    require_once '../vistas/negociosL.php';
}
?>