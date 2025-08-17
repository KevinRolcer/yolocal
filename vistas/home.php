<!DOCTYPE html>
<html lang="es">

<head>
    <title>Home-DragonGym</title>
    
    <link rel="stylesheet" href="../assets/css/principal.css">
    <?php
    include_once("head.php");
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="assets/js/acceso.js?v=1.9.1"></script>
    <script src="js/bodymovin.js" type="text/javascript"></script>
    <script src="https://kit.fontawesome.com/726544f644.js" crossorigin="anonymous"></script>
  
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
</head>

<body>
    <!-- =============== Barra de navegacion ================ -->
    <div class="navigation">
        <?php
        include_once("encabezado.php")
        ?>
    </div>

    <!-- ========================= Contenido principal ==================== -->
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </div>

            


            <div class="contenedor">
                <div class="notificacion" onclick="toggleNotifi()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                    </svg>
                </div>
                <div class="usuario">
                    <img src="https://i.pinimg.com/originals/a0/14/7a/a0147adf0a983ab87e86626f774785cf.gif" alt="">
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
                                        <li>Mié</li>
                                        <li>Jue</li>
                                        <li>Vie</li>
                                        <li>Sáb</li>
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

        <!-- Tarjetas de estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-hand-thumbs-up"></i>
                </div>
                <div class="stat-number">100</div>
                <div class="stat-label">Negocios Registrados</div>
                <div class="stat-bar">
                    <div class="stat-progress" style="width: 100%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-gift"></i>
                </div>
                <div class="stat-number">150</div>
                <div class="stat-label">Promociones Activas</div>
                <div class="stat-bar">
                    <div class="stat-progress" style="width: 100%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-emoji-smile"></i>
                </div>
                <div class="stat-number">250</div>
                <div class="stat-label">Clientes satisfechos</div>
                <div class="stat-bar">
                    <div class="stat-progress" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalOverlay" class="window-overlay"></div>

    <script src="../asset/js/scriptIA.js?v=2.3"></script>
    <!-- Enlace al script de AOS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    <script src="../assets/js/notificaciones.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/calendario.js"></script>

</body>

</html>