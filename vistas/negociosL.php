<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negocios Yolocal</title>
   <link rel="stylesheet" href="../assets/css/negociosCl.css">
<link rel="stylesheet" href="../assets/css/negocioL.css">
   <header class="encabezado">
        <nav class="navbar">
            <div class="logo">
                <img src="../assets/img/LogoYolocal.png" alt="">
            </div>
            
            <!-- Menú móvil-->
            <button class="menu-toggle" id="menuToggle">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
            
            <div class="menu" id="mainMenu">
                <a href="..//index.php?pag=negocios" class="enlace" data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="texto-menu">Inicio</span>
                </a>
                <a href="../controladores/NegocioLControlador.php" class="enlace active" data-tooltip="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <span class="texto-menu">Negocios</span>
                </a>

                <a href="../vistas/cuponesPagina.php" class="enlace" data-tooltip="Promociones">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M2.25 12h19.5M6.375 17.25a4.875 4.875 0 0 0 4.875-4.875V12m6.375 5.25a4.875 4.875 0 0 1-4.875-4.875V12m-9 8.25h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5Zm12.621-9.44c-1.409 1.41-4.242 1.061-4.242 1.061s-.349-2.833 1.06-4.242a2.25 2.25 0 0 1 3.182 3.182ZM10.773 7.63c1.409 1.409 1.06 4.242 1.06 4.242S9 12.22 7.592 10.811a2.25 2.25 0 1 1 3.182-3.182Z" />
                    </svg>

                    <span class="texto-menu">Cupones</span>
                </a>

                <div class="submenu" data-tooltip="Nosotros">
                    <a href="#" class="enlace">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                        <span class="texto-menu">Nosotros</span>
                        <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </a>
                    <div class="submenu-contenido">
                        <a href="#">¿Quienes somos?</a>
                        <a href="#">Contacto</a>
                        <a href="#">Forma parte de Yolocal</a>
                    </div>
                </div>

            </div>

            <div class="sesion">
                <a href="#" class="btn-prueba">
                    <span class="btn-text-full">Unete a Yo local</span>
                    <span class="btn-text-short">Unirse</span>
                </a>
                <a href="vistas/login.php" class="btn-sesion">
                    <span class="btn-text-full">Iniciar sesión</span>
                    <span class="btn-text-short">Entrar</span>
                </a>
            </div>
        </nav>
    </header>

    <div class="principal">
        <div class="busqueda">
            <div class="buscador">
                <input type="text" placeholder="Buscar negocio" />
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="icono">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </a>
            </div>
            <div class="filtro">

            </div>
        </div>


        <div class="categoria-seccion">
    <div class="categoria">
        <a href="../controladores/NegocioLControlador.php">
            <div class="catalogo">
                <h3>Catálogo de negocios</h3>
            </div>
        </a>
    </div>

    <div class="categoria">
        <a href="../controladores/NegocioLControlador.php?categoria=1">
            <div class="negocio"><p>Cafeterias</p></div>
        </a>
    </div>

    <div class="categoria">
        <a href="../controladores/NegocioLControlador.php?categoria=2">
            <div class="negocio"><p>Ferreterias</p></div>
        </a>
    </div>
    
    <div class="categoria">
        <a href="../controladores/NegocioLControlador.php?categoria=3">
            <div class="negocio"><p>Papelerias</p></div>
        </a>
    </div>

    <div class="categoria">
        <a href="../controladores/NegocioLControlador.php?categoria=8">
            <div class="negocio"><p>Electricas</p></div>
        </a>
    </div>


    <div class="categoria">
        <a href="../controladores/NegocioLControlador.php?categoria=9">
            <div class="negocio"><p>Pinturas</p></div>
        </a>
    </div>

    <div class="categoria">
        <a href="../controladores/NegocioLControlador.php?categoria=10">
            <div class="negocio"><p>Zapaterias</p></div>
        </a>
    </div>
</div> 

   
    <div class="negocios-container">
        <?php if (!empty($negocios)): ?>
            <?php foreach ($negocios as $negocio): ?>
                <div class="negocio-card">
                   
                    <div class="categoria-tag <?php echo strtolower(str_replace(' ', '', $negocio['nombre_categoria'] ?? 'general')); ?>">
                        <?php echo htmlspecialchars($negocio['nombre_categoria'] ?? 'General'); ?>
                    </div>

                   <div class="negocio-content">
                    <div class="negocio-header">
                        <img src="<?php echo htmlspecialchars($negocio['Rutaicono']); ?>" 
                            alt="Icono de <?php echo htmlspecialchars($negocio['nombre_negocio']); ?>" 
                            class="negocio-icono">
                        
                        <h3 class="negocio-nombre">
                            <?php echo htmlspecialchars($negocio['nombre_negocio']); ?>
                        </h3>
                    </div>


                        <?php if (!empty($negocio['DescripcionN'])): ?>
                            <p class="negocio-descripcion">
                                <?php echo htmlspecialchars($negocio['DescripcionN']); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($negocio['Direccion'])): ?>
                            <div class="negocio-direccion">
                                <?php echo htmlspecialchars($negocio['Direccion']); ?>
                            </div>
                        <?php endif; ?>

                        <button class="btn-conocer-mas" onclick="verDetalle(<?php echo $negocio['ID_Negocio']; ?>)">
                            Conocer más
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <h3>No hay negocios disponibles</h3>
                <p>Próximamente se agregarán más negocios locales</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function verDetalle(idNegocio) {
            // Aquí puedes redirigir a la página de detalle del negocio
            window.location.href = `detalle-negocio.php?id=${idNegocio}`;
            // O abrir un modal con más información
            // console.log('Ver detalle del negocio:', idNegocio);
        }

        // Script para el menú móvil (si lo necesitas)
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            const menu = document.getElementById('mainMenu');
            menu.classList.toggle('active');
        });
    </script>

</body>
</html>