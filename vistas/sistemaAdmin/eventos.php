<!DOCTYPE html>
<html lang="es">

<head>
    <title>Cupones - Yo Local</title>
    <?php
    include_once("head.php");
    ?>
    <script type="module" src="assets/js/funcionesEventos.js?v=<?php echo time(); ?>"></script>
    <link rel="stylesheet" href="../assets/css/cupones.css">
    <link rel="stylesheet" href="../assets/css/paginacion.css">
    <link rel="stylesheet" href="../assets/css/pildora.css">
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
    <script>
        const usuarioId = <?= json_encode($_SESSION["ID_Usuario"]) ?>;
        const usuarioTipo = <?= json_encode($_SESSION["tipo"]) ?>;
    </script>
</head>

<body class="bg-light">
    <div class="navigation">
        <?php
        include_once("encabezado.php")
        ?>
    </div>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <svg class="svg" xmlns="ehttp://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
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
                    <img src="../assets/img/descarga.gif" alt="">
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
        <div class="pill-selector-container">
            <div class="pill-selector">
                <a href="index.php?pag=bolsa_trabajo" class="pill-option " id="opcion1">
                    Trabajos
                </a>
                <a href="index.php?pag=eventos" class="pill-option active" id="opcion2">
                    Eventos
                </a>
            </div>
        </div>
        <div class="container mt-5">
            <h1 class="text-start fw-bold">Eventos</h1>
            <h4 class="text-start text-secondary">Sección para administrar los eventos de YoLocal.</h4>
        </div>
        <div class="container mt-5">
            <div class="filter-container">

                <div class="filter" data-filter="titulo">
                    <span>Título</span>
                    <input type="text" id="filtroTitulo" class="hidden" placeholder="Escribe aquí..">
                    <button class="close">✖</button>
                </div>

                <div class="filter" data-filter="descripcion">
                    <span>Descripción</span>
                    <input type="text" id="filtroDescripcion" class="hidden" placeholder="Escribe aquí..">
                    <button class="close">✖</button>
                </div>
                <?php if ($_SESSION["tipo"] === "admin"): ?>
                    <div class="filter" data-filter="negocio">
                        <span>Negocio</span>
                        <input type="text" id="filtroNegocio" class="hidden" placeholder="Escribe aquí..">
                        <button class="close">✖</button>
                    </div>
                <?php endif; ?>


                <div class="filter-promociones">
                    <button id="limpiarFiltros" class="btn btn-secondary">Limpiar Filtros</button>
                </div>

            </div>
            <?php if ($_SESSION["tipo"] === "admin"): ?>
                <div class="d-flex justify-content-end gap-2 mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPromocion">
                        Cargar evento
                    </button>

                </div>
            <?php endif; ?>


            <!-- Modal AGREGAR -->
            <div class="modal fade" id="modalPromocion" tabindex="-1" aria-labelledby="modalPromocionLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPromocionLabel">Cargar evento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEvento" enctype="multipart/form-data">
                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label for="TituloE" class="form-label">Título del evento</label>
                                        <input type="text" class="form-control" id="TituloE" name="TituloE" maxlength="100" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="DescripcionE" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="DescripcionE" name="DescripcionE" rows="3" required></textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="PrecioE" class="form-label">Precio</label>
                                        <input type="number" step="0.01" class="form-control" id="PrecioE" name="PrecioE" placeholder="0.00">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="FechaE" class="form-label">Fecha del evento</label>
                                        <input type="date" class="form-control" id="FechaE" name="FechaE">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="HoraE" class="form-label">Hora</label>
                                        <input type="time" class="form-control" id="HoraE" name="HoraE">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="UbicacionE" class="form-label">Ubicación</label>
                                        <input type="text" class="form-control" id="UbicacionE" name="UbicacionE" maxlength="255">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="RutaImagenE" class="form-label">Imagen del evento</label>
                                        <input type="file" class="form-control" id="RutaImagenE" name="RutaImagenE" accept="image/*">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="Descripcion" class="form-label">Negocio</label>
                                        <select class="form-control" id="ID_Negocio" name="ID_Negocio" required>
                                        </select>
                                    </div>


                                    <!--<div class="col-md-12">
                                        <label for="ID_Categoria" class="form-label">Categoría</label>
                                        <select class="form-select" id="ID_Categoria" name="ID_Categoria" required>
                                            <option value="">Seleccionar...</option>
                                        </select>
                                    </div>
                                </div> -->

                                <div class="text-end mt-3">
                                    <button type="button" class="btn btn-primary" onclick="agregarEvento()">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal EDITAR -->
            <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarLabel">Editar evento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditar" enctype="multipart/form-data">
                                <input type="hidden" id="ID_Evento" name="ID_Evento">

                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label for="EditTituloE" class="form-label">Título del evento</label>
                                        <input type="text" class="form-control" id="EditTituloE" name="TituloE" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="EditDescripcionE" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="EditDescripcionE" name="DescripcionE" rows="3" required></textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="EditPrecioE" class="form-label">Precio</label>
                                        <input type="number" step="0.01" class="form-control" id="EditPrecioE" name="PrecioE">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="EditFechaE" class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="EditFechaE" name="FechaE">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="EditHoraE" class="form-label">Hora</label>
                                        <input type="time" class="form-control" id="EditHoraE" name="HoraE">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="EditUbicacionE" class="form-label">Ubicación</label>
                                        <input type="text" class="form-control" id="EditUbicacionE" name="UbicacionE">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="EditRutaImagenE" class="form-label">Nueva imagen (opcional)</label>
                                        <input type="file" class="form-control" id="EditRutaImagenE" name="RutaImagenE" accept="image/*">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="EditID_Categoria" class="form-label">Categoría</label>
                                        <select class="form-select" id="EditID_Categoria" name="ID_Categoria">
                                            <option value="">Seleccionar...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="button" class="btn btn-primary" onclick="editarEvento()">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Usuarios -->

        </div>


           <script src="../assets/js/main.js"></script>


</body>

</htm