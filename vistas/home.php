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

            <div class="search">
                <label>

                    <input type="text" id="searchInput" placeholder="Buscar miembro ">

                </label>
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

        <!-- ======================= Contadores ================== -->
        <div class="contadores">
            <div class="card d-flex align-items-center">
                <div class="row w-100">
                    <!-- Columna 1 -->
                    <div class="col d-flex flex-column">
                        <div class="numbers"></div>
                        <div class="cardName">Visitas</div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-auto d-flex align-items-center" id="iconOjo">
                        <div class="iconBx">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>

                        </div>
                    </div>
                </div>
            </div>


            <div class="card d-flex align-items-center">
                <div class="row w-100">
                    <!-- Columna 1 -->
                    <div class="col d-flex flex-column">
                        <div class="numbers"></div>
                        <div class="cardName">Miembros</div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-auto d-flex align-items-center" id="iconOjo">
                        <div class="iconBx">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>


            <div class="botonV">
                <button class="agregarV" id="agregarVisitaBtn2" data-bs-toggle="modal"
                    data-bs-target="#miModal"><span>Agregar visita</span></button>

            </div>

        </div>


        <!-- ================ Tabla de usuarios ================= -->
        <div class="details">
            <div class="registro">
                <div class="cardHeader">
                    <h2>Lista de accesos</h2>
                    <a href="#" id="DiaAct" class="btn">Gráfica</a>
                </div>
                <!-- ================ Modal de estadísticas ================= -->
                <dialog id="modalEstadisticas">
                    <div class="modal-content">
                        <!-- si-->
                        <canvas id="myChart"></canvas>
                        <div>
                            <label for="filtro">Filtrar por:</label>
                            <select id="filtro">
                                <option value="dia">Hoy</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
                            </select>
                            <button id="cargarDatos">Cargar Datos</button>
                        </div>
                    </div>
                </dialog>

                <table id="tablaAccesos">
                    <thead>
                        <tr>
                            <td>No_Miembro</td>
                            <td>Nombre</td>
                            <td>Hora Entrada</td>
                            <td>Precio</td>
                            <td>Tipo</td>

                        </tr>
                    </thead>

                    <tbody>

                    </tbody>

                </table>
            </div>

            <!-- ================= Miembros ================ -->
            <div class="miembros">
                <div class="titulo">
                    <h2>Miembros</h2>
                    <div class="huella">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                    </div>

                </div>
                <div class="fotoM">

                </div>
                <div class="contenidoM">
                    <p>#ID de miembro</p>
                    <h3>Nombre</h2>
                        <p>Número: </p>
                        <div class="fechas">
                            <div class="fechaI">
                                dd/mm/yyyy
                            </div>
                            <div class="fechaF">
                                dd/mm/yyyy
                            </div>
                        </div>
                        <div class="estadoM">
                            Membresía Activa
                        </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ===== Modal visita ===== -->
    <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Agregar Acceso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgregarAcceso">
                        <div class="mb-3 d-flex align-items-center">
                            <div class="me-3 flex-grow-1">
                                <label for="idMiembro" class="form-label"># Miembro</label>
                                <input type="number" class="form-control" id="idMiembro" name="ID_Miembro" placeholder="Escriba el número" required>
                            </div>
                            <div class="flex-grow-2">
                                <label for="nombreMiembro" class="form-label">Nombre del Miembro</label>
                                <input type="text" class="form-control" id="nombreMiembro" placeholder="Nombre" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="Fecha" required readonly>
                        </div>

                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="precio" name="Precio" placeholder="Ingrese el precio" min="0" max="300" required readonly>
                            <div class="invalid-feedback">
                                Password is required
                            </div>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ID_Membresia" class="form-label">Tipo de Membresía</label>
                            <select class="form-control" id="ID_Membresia" name="ID_Membresia" required>

                            </select>
                        </div>
                        <input type="hidden" value="Visita" id="Tipo" name="Tipo">
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCerrar">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarAcceso">Guardar</button>

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