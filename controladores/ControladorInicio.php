<?php

require_once __DIR__ . '/../modelos/Carrucel.php';


$carrucelModel = new Carrucel();
$idCategoriaCafeteria = 1;


$cafeteriasDestacadas = $carrucelModel->obtenerNegociosRelevantesPorCategoria($idCategoriaCafeteria);
?>