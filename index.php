<?php
include_once("config.php");
iniciarSesionYoLocal();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["sistema"]) || $_SESSION["sistema"] !== "YoLocal") {

    include_once("vistas/inicioCl.php");
    exit();
}

if (!function_exists('usuarioTienePagoActivo')) {
    require_once __DIR__ . '/controladores/controladorPagos.php';
}

$tipoUsuario    = strtolower(trim((string)($_SESSION["tipo"] ?? '')));
$pag            = $_GET["pag"] ?? "home";
$usuarioId      = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);
$esAliadoSesion = strtolower(trim((string)($_SESSION['es_aliado'] ?? 'no'))) === 'si';

// Paginas que un negocio aliado no puede usar mientras no tenga un pago vigente.
$paginasRestringidasSinPago = [
    'ventas', 'negocios', 'cupones', 'bolsa_trabajo',
    'trabajos', 'trabajo', 'eventos', 'evento', 'aportacion'
];
$esDueno = $tipoUsuario === 'negocio' || $tipoUsuario === 'dueño' || $tipoUsuario === 'dueno';
$requierePago = $esDueno
    && $esAliadoSesion
    && $usuarioId > 0
    && !usuarioTienePagoActivo($usuarioId)
    && in_array($pag, $paginasRestringidasSinPago, true);

if ($requierePago) {
    header('Location: index.php?pag=home&bloqueo_pago=1');
    exit();
}

$rutas = [
    "admin"     => ["admin"   => "vistas/sistemaAdmin/home.php"],
    "negocio"   => ["negocio" => "vistas/sistemaAdmin/home.php"],

    // Solo Admin
    "usuarios"  => ["admin" => "vistas/sistemaAdmin/usuarios.php"],
    "categorias"=> ["admin" => "vistas/sistemaAdmin/categorias.php"],
    "bolsa_trabajo"=> [
        "admin"   => "vistas/sistemaAdmin/bolsa_trabajo.php",
        "negocio" => "vistas/sistemaAdmin/bolsa_trabajo.php"
    ],
    "eventos"=> ["admin" => "vistas/sistemaAdmin/eventos.php"],

    "ventas"  => [
        "admin"   => "vistas/sistemaAdmin/negocios.php",
        "negocio" => "vistas/sistemaAdmin/negocios.php"
    ],
    "cupones"   => [
        "admin"   => "vistas/sistemaAdmin/cupones.php",
        "negocio" => "vistas/sistemaAdmin/cupones.php"
    ],

    // --- Modulo de pagos ---
    "pagos"             => ["negocio" => "vistas/sistemaAdmin/pagos.php"],
    "pagos_admin"       => ["admin"   => "vistas/sistemaAdmin/pagos_admin.php"],
    "recibo_pago_admin" => ["admin"   => "vistas/sistemaAdmin/recibo_pago_admin.php"],
    "pagos_usuario"     => ["negocio" => "vistas/sistemaAdmin/pagos_usuario.php"],
    "aportacion"        => ["negocio" => "vistas/sistemaAdmin/aportacion.php"],
    "notificaciones"    => [
        "admin"   => "vistas/sistemaAdmin/notificaciones.php",
        "negocio" => "vistas/sistemaAdmin/notificaciones.php"
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

// Notificaciones automaticas de vencimiento de aportacion (se auto-limita a 1 vez/dia).
if (function_exists('verificarVencimientoAportaciones')) {
    verificarVencimientoAportaciones();
}
