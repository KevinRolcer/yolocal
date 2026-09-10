<?php
$paginaActual = $_GET["pag"] ?? "home";

// --- Datos de sesion para el modulo de pagos ---
if (!function_exists('usuarioTienePagoActivo')) {
    require_once __DIR__ . '/../../controladores/controladorPagos.php';
}

$rolSesion       = strtolower(trim((string)($_SESSION["tipo"] ?? '')));
$usuarioIdSesion = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);
$esDuenoSesion   = ($rolSesion === 'negocio');

// es_aliado: de la sesion; si no esta, se relee de la BD una sola vez.
$esAliadoSesion = strtolower((string)($_SESSION['es_aliado'] ?? '')) === 'si';
if (!isset($_SESSION['es_aliado']) && $usuarioIdSesion > 0 && function_exists('dbConectar')) {
    $__cn = dbConectar();
    if ($__cn instanceof mysqli) {
        $__st = $__cn->prepare("SELECT es_aliado FROM usuarios WHERE ID_Usuario = ? LIMIT 1");
        if ($__st) {
            $__st->bind_param('i', $usuarioIdSesion);
            $__st->execute();
            $__fila = $__st->get_result()->fetch_assoc();
            $__st->close();
            $esAliadoSesion = strtolower((string)($__fila['es_aliado'] ?? 'no')) === 'si';
            $_SESSION['es_aliado'] = $esAliadoSesion ? 'si' : 'no';
        }
    }
}

// Aliado sin pago vigente -> solo puede navegar a Inicio y Pagos.
$requierePagoEmpresa = $esDuenoSesion
    && $esAliadoSesion
    && $usuarioIdSesion > 0
    && function_exists('usuarioTienePagoActivo')
    && !usuarioTienePagoActivo($usuarioIdSesion);
$linksPermitidosSinPago = ['index.php?pag=home', 'index.php?pag=pagos'];

$menus = [
    [
        "titulo" => "Inicio",
        "icono"  => "bi-house",
        "link"   => "index.php?pag=home",
        "pag"    => "home",
        "roles"  => ["admin", "negocio"]
    ],
    [
        "titulo" => "Usuarios",
        "icono"  => "bi-person",
        "link"   => "index.php?pag=usuarios",
        "pag"    => "usuarios",
        "roles"  => ["admin"] // solo admin
    ],
    [
        "titulo" => "Negocios",
        "icono"  => "bi-shop",
        "link"   => "index.php?pag=ventas",
        "pag"    => "ventas",
        "roles"  => ["admin", "negocio"]
    ],
    [
        "titulo" => "Cupones",
        "icono"  => "bi-gift",
        "link"   => "index.php?pag=cupones",
        "pag"    => "cupones",
        "roles"  => ["admin", "negocio"]
    ],
    [
        "titulo" => "Categorías",
        "icono"  => "bi-grid",
        "link"   => "index.php?pag=categorias",
        "pag"    => "categorias",
        "roles"  => ["admin"] // solo admin
    ],
    [
        "titulo" => "Trabajos y eventos",
        "icono"  => "bi-briefcase",
        "link"   => "index.php?pag=bolsa_trabajo",
        "pag"    => "bolsa_trabajo",
        "roles"  => ["admin", "negocio"]
    ],
    // --- Modulo de pagos ---
    [
        "titulo" => "Administración de pagos",
        "icono"  => "bi-cash-stack",
        "link"   => "index.php?pag=pagos_admin",
        "pag"    => "pagos_admin",
        "roles"  => ["admin"]
    ],
    [
        "titulo" => "Pagos",
        "icono"  => "bi-credit-card",
        "link"   => "index.php?pag=pagos",
        "pag"    => "pagos",
        "roles"  => ["negocio"]
    ],
    [
        "titulo" => "Aliados Impulso",
        "icono"  => "bi-stars",
        "link"   => "index.php?pag=aportacion",
        "pag"    => "aportacion",
        "roles"  => ["negocio"]
    ],
    [
        "titulo" => "Mis pagos",
        "icono"  => "bi-receipt",
        "link"   => "index.php?pag=pagos_usuario",
        "pag"    => "pagos_usuario",
        "roles"  => ["negocio"]
    ]
];
?>

<div class="sidebar">
    <div class="logo-top">
        <span class="icon">
            <img src="<?= htmlspecialchars(ylAssetUrl("assets/img/LogoYolocal.png"), ENT_QUOTES, "UTF-8") ?>" alt="Logo" class="logo">
        </span>
        <span class="title">Yo Local</span>
    </div>

    <ul class="main-menu">
        <?php foreach ($menus as $menu): ?>
            <?php
                if (!in_array($rolSesion, $menu["roles"], true)) {
                    continue;
                }
                // El menu "Pagos" / "Aliados Impulso" solo para negocios aliados.
                if (!empty($menu["soloAliado"]) && !$esAliadoSesion) {
                    continue;
                }
                // Aliado sin pago vigente: se muestran los items pero deshabilitados,
                // salvo Inicio y Pagos.
                $bloqueadoSinPago = $requierePagoEmpresa
                    && !in_array($menu["link"], $linksPermitidosSinPago, true);

                $esActivo = ($paginaActual === $menu["pag"]);
                if ($menu["pag"] === "bolsa_trabajo" && ($paginaActual === "bolsa_trabajo" || $paginaActual === "eventos")) {
                    $esActivo = true;
                }
            ?>
            <li class="<?= $esActivo ? 'active' : '' ?><?= $bloqueadoSinPago ? ' menu-bloqueado' : '' ?>">
                <a href="<?= $bloqueadoSinPago ? 'index.php?pag=pagos' : $menu['link'] ?>"
                   <?= $bloqueadoSinPago ? 'title="Activa tu pago para usar esta sección"' : '' ?>>
                    <span class="icon"><i class="bi <?= $menu['icono'] ?>"></i></span>
                    <span class="title"><?= $menu['titulo'] ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-bottom">
        <a href="salir.php" class="exit-button">
            <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
            <span class="title">Salir</span>
        </a>
    </div>
</div>
