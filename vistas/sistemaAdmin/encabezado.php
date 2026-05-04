<?php
$paginaActual = $_GET["pag"] ?? "home";

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
    ]
    
    
];
?>

<div class="sidebar">
    <div class="logo-top">
        <span class="icon">
            <img src="assets/img/LogoYolocal.png" alt="Logo" class="logo">
        </span>
        <span class="title">Yo Local</span>
    </div>

    <ul class="main-menu">
        <?php foreach ($menus as $menu): ?>
            <?php if (in_array($_SESSION["tipo"], $menu["roles"])): ?>
                <?php
                    // Determinar si este menú es el activo
                    $esActivo = ($paginaActual === $menu["pag"]);
                    // bolsa_trabajo y eventos comparten el mismo ítem
                    if ($menu["pag"] === "bolsa_trabajo" && ($paginaActual === "bolsa_trabajo" || $paginaActual === "eventos")) {
                        $esActivo = true;
                    }
                ?>
                <li class="<?= $esActivo ? 'active' : '' ?>">
                    <a href="<?= $menu['link'] ?>">
                        <span class="icon"><i class="bi <?= $menu['icono'] ?>"></i></span>
                        <span class="title"><?= $menu['titulo'] ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-bottom">
        <a href="salir.php" class="exit-button">
            <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
            <span class="title">Salir</span>
        </a>
    </div>
</div>
