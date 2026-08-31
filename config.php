<?php
define("RUTA", "/yolocal//");
date_default_timezone_set('America/Mexico_City');

function iniciarSesionYoLocal()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $rutaSesion = rtrim(RUTA, "/");
    if ($rutaSesion === "") {
        $rutaSesion = "/";
    }

    $directorioSesiones = __DIR__ . "/tmp/sessions";

    if (!is_dir($directorioSesiones)) {
        mkdir($directorioSesiones, 0755, true);
    }

    session_save_path($directorioSesiones);
    session_set_cookie_params(0, "/");
    session_start();
}

function dbConectar()
{
    static $conexion;

    if (!isset($conexion)) {
        $config = parse_ini_file('config.ini');
        $conexion = mysqli_connect($config['servidor'], $config['usuario'], $config['pass'], $config['bbdd']);
        $query = "set CHARSET 'utf8'";
        $conexion->query($query);
    }

    if ($conexion === false) {
        return mysqli_connect_error();
    }

    return $conexion;
}

?>