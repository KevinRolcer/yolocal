<?php
date_default_timezone_set("America/Mexico_City");

$GLOBALS["__yl_app_ini"] = parse_ini_file(__DIR__ . "/config.ini", true);
if ($GLOBALS["__yl_app_ini"] === false) {
    $GLOBALS["__yl_app_ini"] = [];
}
$__yl_ini = &$GLOBALS["__yl_app_ini"];

$__yl_host = $_SERVER["HTTP_HOST"] ?? "";
$__yl_site = $__yl_ini["site"] ?? [];
$__yl_origen = isset($__yl_site["origen"]) ? trim((string) $__yl_site["origen"], " \t\n\r/") : "";
$__yl_ruta_explicit = array_key_exists("ruta", $__yl_site) ? trim((string) $__yl_site["ruta"]) : null;

/* Producción: https://yolocaltexmelucan.com/ — mismo host o sección [site] en config.ini */
if ($__yl_origen === "" && $__yl_ruta_explicit === null && stripos($__yl_host, "yolocaltexmelucan.com") !== false) {
    $__yl_origen = "https://yolocaltexmelucan.com";
    $__yl_ruta_explicit = "/";
}

if ($__yl_ruta_explicit !== null) {
    $__r = trim($__yl_ruta_explicit, "/");
    define("RUTA", $__r === "" ? "/" : "/" . $__r . "/");
} else {
    define("RUTA", "/yolocal/");
}

define("YL_ORIGEN_PUBLICO", $__yl_origen);

function iniciarSesionYoLocal()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $rutaSesion = rtrim(RUTA, "/");
    if ($rutaSesion === "") {
        $rutaSesion = "/";
    }

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => $rutaSesion,
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    session_start();
}

function dbConectar()
{
    static $conexion;

    if (!isset($conexion)) {
        $ini = $GLOBALS["__yl_app_ini"] ?? [];
        $db = $ini["database"] ?? [];
        if (empty($db["servidor"]) && isset($ini["servidor"])) {
            $db = $ini;
        }
        $servidor = $db["servidor"] ?? "localhost";
        $usuario = $db["usuario"] ?? "root";
        $pass = $db["pass"] ?? "";
        $bbdd = $db["bbdd"] ?? "";

        $conexion = mysqli_connect($servidor, $usuario, $pass, $bbdd);
        $query = "set CHARSET 'utf8'";
        if ($conexion instanceof mysqli) {
            $conexion->query($query);
        }
    }
    if ($conexion === false) {
        return mysqli_connect_error();
    }
    return $conexion;
}
