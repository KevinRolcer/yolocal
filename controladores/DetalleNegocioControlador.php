<?php


require_once '../modelos/NegocioLModelo.php';
require_once '../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: NegocioLControlador.php');
    exit();
}

$id_negocio = (int)$_GET['id'];
$db = dbConectar();

$negocio = NegocioLModelo::obtenerPorId($db, $id_negocio);
$horarios = NegocioLModelo::obtenerHorariosPorIdNegocio($db, $id_negocio);
$imagenes = NegocioLModelo::obtenerImagenesPorIdNegocio($db, $id_negocio); 

$db->close();

if (!$negocio) {
    echo "<h1>Error 404: Negocio no encontrado</h1>";
    exit();
}


require_once '../vistas/detalle_negocio_vista.php';



?>