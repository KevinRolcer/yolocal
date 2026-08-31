<?php
    include_once("config.php");
    iniciarSesionYoLocal();
    session_unset();
    session_destroy();
    header("Location:".RUTA."/");
    exit;
?>
