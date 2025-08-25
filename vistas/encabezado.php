<?php
$menus = [
    [
        "titulo" => "Inicio",
        "icono"  => "bi-house",
        "link"   => "index.php?pag=home",
        "roles"  => ["admin", "negocio"] 
    ],
    [
        "titulo" => "Usuarios",
        "icono"  => "bi-person",
        "link"   => "index.php?pag=usuarios",
        "roles"  => ["admin"] // solo admin
    ],
    [
        "titulo" => "Negocios",
        "icono"  => "bi-shop",
        "link"   => "index.php?pag=ventas",
        "roles"  => ["admin", "negocio"]
    ],
    [
        "titulo" => "Cupones",
        "icono"  => "bi-gift",
        "link"   => "index.php?pag=cupones",
        "roles"  => ["admin", "negocio"]
    ],
    [
        "titulo" => "Categorías",
        "icono"  => "bi-grid",
        "link"   => "index.php?pag=categorias",
        "roles"  => ["admin"] // solo admin
    ]
];
?>

<div class="sidebar">
    <div class="logo-bottom">
        <span class="title">Yo Local</span>
    </div>

    <ul class="main-menu">
        <?php foreach ($menus as $menu): ?>
            <?php if (in_array($_SESSION["tipo"], $menu["roles"])): ?>
                <li>
                    <a href="<?= $menu['link'] ?>">
                        <span class="icon"><i class="bi <?= $menu['icono'] ?>"></i></span>
                        <span class="title"><?= $menu['titulo'] ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-bottom">
        <a href="#" class="logo-bottom">
            <span class="icon">
                <img src="../assets/img/LogoYolocal.png" alt="Logo" class="logo">
            </span>
        </a>
        <a href="salir.php" class="exit-button">
            <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
            <span class="title">Salir</span>
        </a>
    </div>
</div>
