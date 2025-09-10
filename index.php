<?php 



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["sistema"]) || $_SESSION["sistema"] !== "YoLocal") {
 
    include_once("vistas/inicioCl.php"); 
    exit(); 
}
include_once("config.php");  
include_once("vistas/inicioCl.php"); 

$tipoUsuario = $_SESSION["tipo"] ?? null;
$pag = $_GET["pag"] ?? "home"; // por defecto ir a home

$rutas = [
    "admin"     => ["admin"   => "vistas/home.php"],
    "negocio"   => ["negocio" => "vistas/home.php"],

    // Solo Admin
    "usuarios"  => ["admin" => "vistas/usuarios.php"],
    "categorias"=> ["admin" => "vistas/categorias.php"],

    "ventas"  => [
        "admin"   => "vistas/negocios.php",
        "negocio" => "vistas/negocios.php"
    ],
    "cupones"   => [
        "admin"   => "vistas/cupones.php",
        "negocio" => "vistas/cupones.php"
    ],
    "home"   => [
        "admin"   => "vistas/home.php",
        "negocio" => "vistas/home.php"
    ]
];

if ($pag && isset($rutas[$pag][$tipoUsuario])) {
    include_once($rutas[$pag][$tipoUsuario]);
} else {
    include_once("vistas/acceso_denegado.php");
    exit();
}