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
   
        $negocios = NegocioLModelo::obtenerTodos($db);
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


    if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
        $termino = trim($_GET['busqueda']);
        $negocios = NegocioLModelo::buscarPorNombre($db, $termino);
    } elseif (isset($_GET['categoria']) && is_numeric($_GET['categoria'])) {
        $id_categoria = (int)$_GET['categoria'];
        $negocios = NegocioLModelo::obtenerPorCategoria($db, $id_categoria);
    } else {
        $negocios = NegocioLModelo::obtenerTodos($db);
    }

    $categorias = CategoriaModelo::obtenerTodas($db);
    require_once '../vistas/negociosL.php';
}
?>