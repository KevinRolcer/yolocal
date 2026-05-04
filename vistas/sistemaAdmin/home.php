<!DOCTYPE html>
<html lang="es">

<head>
    <title>Home - Yo Local</title>
    
    
    <?php
    require_once("config.php");
    $conexion = dbConectar();

    // Obtener contadores reales
    $resNegocios = $conexion->query("SELECT count(*) FROM negocios");
    $totalNegocios = $resNegocios ? $resNegocios->fetch_row()[0] : 0;

    $resPromos = $conexion->query("SELECT count(*) FROM promociones");
    $totalPromos = $resPromos ? $resPromos->fetch_row()[0] : 0;

    $resUsuarios = $conexion->query("SELECT count(*) FROM usuarios");
    $totalUsuarios = $resUsuarios ? $resUsuarios->fetch_row()[0] : 0;

    // Obtener visitas
    $archivoVisitas = __DIR__ . '/../../visitas.txt';
    $visitasTotales = file_exists($archivoVisitas) ? (int)file_get_contents($archivoVisitas) : 0;
    
    // M&eacute;trica: Clientes Satisfechos = Usuarios registrados + Visitas (o como convenga)
    $clientesSatisfechos = $totalUsuarios + $visitasTotales;

    $adminCssFiles = ["assets/css/principal.css"];
    include_once("head.php");
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/726544f644.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- =============== Barra de navegacion ================ -->
    <div class="navigation admin-sidebar">
        <?php
        include_once("encabezado.php")
        ?>
    </div>

    <!-- ========================= Contenido principal ==================== -->
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <i class="ri-menu-2-line admin-menu-icon" aria-hidden="true"></i>
            </div>

            


            <div class="contenedor">
                <div class="notificacion" onclick="toggleNotifi()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                    </svg>
                </div>
                <div class="usuario">
                    <img src="assets/img/descarga.gif" alt="">
                </div>
                <div class="notifi-box" id="box">
                    <p class="calendario"></p>
                    <div class="notifi-item">
                        <div class="text">
                            <h4>Notificaciones</h4>
                        </div>
                        <div class="calend">
                            <div class="calend">
                                <div class="calendar">
                                    <div class="calendar-header">
                                        <button id="prev">&lt;</button>
                                        <h3></h3>
                                        <button id="next">&gt;</button>
                                    </div>
                                    <ul class="weekdays">
                                        <li>Dom</li>
                                        <li>Lun</li>
                                        <li>Mar</li>
                                        <li>Mi&eacute;</li>
                                        <li>Jue</li>
                                        <li>Vie</li>
                                        <li>S&aacute;b</li>
                                    </ul>
                                    <ul class="dates"></ul>
                                </div>
                            </div>
                        </div>
                        <div class="noti">
                            <table>
                                <tr>
                                    <td>
                                        <h4>Sin notificaciones...<br></h4>
                                    </td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="content">
        <!-- Header superior -->
        <div class="header">
            <div class="header-left">
                <div class="header-text">
                    <h1>Bienvenido a<br><span class="purple-text">Yo Local</span></h1>
                    <p class="subtitle">Si es del barrio, es de todos</p>
                </div>
            </div>
           
        </div>

        <!-- Tarjetas de estad&iacute;sticas -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon bg-purple-solid">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-number" data-target="<?= $totalNegocios ?>">0</div>
                <div class="stat-label">Negocios Registrados</div>
                <div class="stat-bar">
                    <div class="stat-progress bg-purple-solid" style="width: 100%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-yellow-solid">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <div class="stat-number" data-target="<?= $totalPromos ?>">0</div>
                <div class="stat-label">Promociones Activas</div>
                <div class="stat-bar">
                    <div class="stat-progress bg-yellow-solid" style="width: 100%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-green-solid">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="stat-number" data-target="<?= $clientesSatisfechos ?>">0</div>
                <div class="stat-label">Clientes Satisfechos</div>
                <div class="stat-bar">
                    <div class="stat-progress bg-green-solid" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalOverlay" class="window-overlay"></div>


    <script src="assets/js/main.js"></script>

    <script>
        // Animaci&oacute;n de los n&uacute;meros del dashboard
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200; // a menor n&uacute;mero, m&aacute;s r&aacute;pida la animaci?n

        counters.forEach(counter => {
            const animate = () => {
                const value = +counter.getAttribute('data-target');
                const data = +counter.innerText;
                
                const time = value / speed;
                if(data < value) {
                    counter.innerText = Math.ceil(data + time);
                    setTimeout(animate, 20);
                } else {
                    counter.innerText = value;
                }
            }
            animate();
        });
    </script>

</body>

</html>
