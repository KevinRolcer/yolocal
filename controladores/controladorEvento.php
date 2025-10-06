<?php



require_once '../config.php'; 
require_once '../modelos/evento.php'; 


$db = dbConectar();

$eventos = EventoModelo::obtenerTodos($db); 


$db->close();


require_once '../vistas/eventosPag.php'; 

?>