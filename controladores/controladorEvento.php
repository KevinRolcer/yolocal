<?php



require_once '../config.php'; 
require_once '../modelos/evento.php'; 


$db = dbConectar();

$eventos = EventoModelo::obtenerTodos($db); 
$ogTitle = "Descubre Nuestros Eventos";
$ogDescription = "Encuentra los mejores eventos y actividades en nuestro sitio.";
$ogImage = "https://yolocaltexmelucan.com/assets/img/517130134_1300489448747756_547402111643965829_n.jpg"; 
$ogUrl = "https://yolocaltexmelucan.com/controladores/controladorEvento.php";
$eventoEspecifico = null;
if (isset($_GET['evento']) && is_numeric($_GET['evento'])) {
    
    $eventoId = (int)$_GET['evento']; 
    
    $eventoEspecifico = EventoModelo::obtenerPorId($db, $eventoId);

    if ($eventoEspecifico) {
        $ogTitle = htmlspecialchars($eventoEspecifico['TituloE']);
        $ogDescription = htmlspecialchars(substr($eventoEspecifico['DescripcionE'], 0, 155)) . '...';
        $ogImage = "https://www.yolocaltexmelucan.com/imagenes/" . htmlspecialchars($eventoEspecifico['RutaImagenE']); 
        $ogUrl = "https://www.yolocaltexmelucan.com/controladores/controladorEvento.php?evento=" . $eventoId;
    }
    
}
$eventos = EventoModelo::obtenerTodos($db);

$db->close();


require_once '../vistas/eventosPag.php'; 

?>