<?php 
//puto agustin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["sistema"]) || $_SESSION["sistema"] !== "YoLocal") {
 
    include_once("vistas/inicioCl.php"); 
    exit(); 
}


$tipoUsuario = $_SESSION["tipo"] ?? null;
$pag = $_GET["pag"] ?? "home"; // por defecto ir a home

$rutas = [
    "admin"     => ["admin"   => "vistas/SistemaAdmin/home.php"],
    "negocio"   => ["negocio" => "vistas/SistemaAdmin/home.php"],

    // Solo Admin
    "usuarios"  => ["admin" => "vistas/SistemaAdmin/usuarios.php"],
    "categorias"=> ["admin" => "vistas/SistemaAdmin/categorias.php"],
    "bolsa_trabajo"=> ["admin" => "vistas/SistemaAdmin/bolsa_trabajo.php"],

    "ventas"  => [
        "admin"   => "vistas/SistemaAdmin/negocios.php",
        "negocio" => "vistas/SistemaAdmin/negocios.php"
    ],
    "cupones"   => [
        "admin"   => "vistas/SistemaAdmin/cupones.php",
        "negocio" => "vistas/SistemaAdmin/cupones.php"
    ],
    "home"   => [
        "admin"   => "vistas/SistemaAdmin/home.php",
        "negocio" => "vistas/SistemaAdmin/home.php"
    ]
];

if ($pag && isset($rutas[$pag][$tipoUsuario])) {
    include_once($rutas[$pag][$tipoUsuario]);
} else {
    include_once("vistas/SistemaAdmin/acceso_denegado.php");
    exit();
}