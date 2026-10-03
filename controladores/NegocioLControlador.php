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
        ob_start();
        foreach ($negocios as $negocio) {
            include __DIR__ . '/../vistas/partials/tarjeta_negocio.php';
        }
        $htmlResultados = ob_get_clean();
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