<?php

require_once '../modelos/NegocioLModelo.php'; 
require_once '../config.php'; 

$db = dbConectar(); 


if (isset($_GET['categoria']) && is_numeric($_GET['categoria'])) {
    
    $id_categoria = (int)$_GET['categoria'];
    
    $negocios = NegocioLModelo::obtenerPorCategoria($db, $id_categoria);

} else {
    
  
    $negocios = NegocioLModelo::obtenerTodos($db);
}


require_once '../vistas/negociosL.php';

?>