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
    "admin"     => ["admin"   => "vistas/sistemaAdmin/home.php"],
    "negocio"   => ["negocio" => "vistas/sistemaAdmin/home.php"],

    // Solo Admin
    "usuarios"  => ["admin" => "vistas/sistemaAdmin/usuarios.php"],
    "categorias"=> ["admin" => "vistas/sistemaAdmin/categorias.php"],
    "bolsa_trabajo"=> ["admin" => "vistas/sistemaAdmin/bolsa_trabajo.php"],

    "ventas"  => [
        "admin"   => "vistas/sistemaAdmin/negocios.php",
        "negocio" => "vistas/sistemaAdmin/negocios.php"
    ],
    "cupones"   => [
        "admin"   => "vistas/sistemaAdmin/cupones.php",
        "negocio" => "vistas/sistemaAdmin/cupones.php"
    ],
    "home"   => [
        "admin"   => "vistas/sistemaAdmin/home.php",
        "negocio" => "vistas/sistemaAdmin/home.php"
    ]
];

if ($pag && isset($rutas[$pag][$tipoUsuario])) {
    include_once($rutas[$pag][$tipoUsuario]);
} else {
    include_once("vistas/sistemaAdmin/acceso_denegado.php");
    exit();
}