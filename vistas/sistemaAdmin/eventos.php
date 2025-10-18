<!DOCTYPE html>
<html lang="es">

<head>
    <title>Eventos - Yo Local</title>
    <?php
    // Asegúrate de que este archivo inicie la sesión y contenga las configuraciones necesarias
    include_once("head.php");
    ?>
    <script type="module" src="assets/js/funcionesEventos.js?v=<?php echo time(); ?>"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/bolsaTrabajoAdmin.css">
    <link rel="stylesheet" href="../assets/css/paginacion.css">
    <link rel="stylesheet" href="../assets/css/pildora.css">
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="../assets/css/eventosL.css">
    <link rel="stylesheet" href="../assets/css/eventos2.css">
    <script>    
        const usuarioId = <?= json_encode($_SESSION["ID_Usuario"] ?? null) ?>;
        const usuarioTipo = <?= json_encode($_SESSION["tipo"] ?? null) ?>;
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
                <svg class="svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
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
            </div>
        </div>

        <div class="pill-selector-container">
            <div class="pill-selector">
                <a href="index.php?pag=bolsa_trabajo" class="pill-option" id="opcion1">
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
                <div class="filter" data-filter="categoria">
                    <span>Categoría</span>
                    <input type="text" id="filtroCategoria" class="hidden" placeholder="Escribe aquí..">
                    <button class="close">✖</button>
                </div>
                <?php endif; ?>

                <div class="filter-promociones">
                    <button id="limpiarFiltros" class="btn btn-secondary">Limpiar Filtros</button>
                </div>
            </div>

            <?php if ($_SESSION["tipo"] === "admin"): ?>
            <div class="d-flex justify-content-end gap-2 mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEvento">
                    Cargar Evento
                </button>
            </div>
            <?php endif; ?>

            <div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEventoLabel">Cargar Evento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEvento" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="TituloE" class="form-label">Título del Evento</label>
                                        <input type="text" class="form-control" id="TituloE" name="TituloE" maxlength="50" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="DescripcionE" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="DescripcionE" name="DescripcionE" rows="3" maxlength="500" required></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="PrecioE" class="form-label">Precio ($)</label>
                                        <input type="text" class="form-control" id="PrecioE" name="PrecioE" placeholder="Ej: 150.00 o Gratis" maxlength="6">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="FechaE" class="form-label">Fecha del Evento</label>
                                        <input type="date" class="form-control" id="FechaE" name="FechaE" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="HoraE" class="form-label">Hora del Evento</label>
                                        <input type="time" class="form-control" id="HoraE" name="HoraE" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="UbicacionE" class="form-label">Ubicación</label>
                                        <input type="text" class="form-control" id="UbicacionE" name="UbicacionE" maxlength="200" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="Telefono" class="form-label">Teléfono de Contacto (Opcional)</label>
                                        <input type="tel" class="form-control" id="Telefono" name="Telefono" maxlength="15" placeholder="Ej: 2481234567">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="RutaImagenE" class="form-label">Imagen del Evento</label>
                                        <input type="file" class="form-control" id="RutaImagenE" name="RutaImagenE" accept="image/*">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="ID_Categoria" class="form-label">Categoría</label>
                                        <select class="form-control" id="ID_Categoria" name="ID_Categoria" required>
                                            </select>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

           <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarLabel">Editar Evento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditar" enctype="multipart/form-data">
                                <input type="hidden" id="ID_Evento_Editar" name="ID_Evento">

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="EditTituloE" class="form-label">Título del Evento</label>
                                        <input type="text" class="form-control" id="EditTituloE" name="TituloE" maxlength="50" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="EditDescripcionE" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="EditDescripcionE" name="DescripcionE" rows="3" maxlength="500" required></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="EditPrecioE" class="form-label">Precio ($)</label>
                                        <input type="text" class="form-control" id="EditPrecioE" name="PrecioE" maxlength="6">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="EditFechaE" class="form-label">Fecha del Evento</label>
                                        <input type="date" class="form-control" id="EditFechaE" name="FechaE" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="EditHoraE" class="form-label">Hora del Evento</label>
                                        <input type="time" class="form-control" id="EditHoraE" name="HoraE" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="EditUbicacionE" class="form-label">Ubicación</label>
                                        <input type="text" class="form-control" id="EditUbicacionE" name="UbicacionE" maxlength="200" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="EditTelefono" class="form-label">Teléfono de Contacto (Opcional)</label>
                                        <input type="tel" class="form-control" id="EditTelefono" name="Telefono" maxlength="15" placeholder="Ej: 2481234567">
                                    </div>
                                     <div class="col-md-12">
                                        <label for="EditRutaImagenE" class="form-label">Nueva Imagen (Opcional)</label>
                                        <input type="file" class="form-control" id="EditRutaImagenE" name="RutaImagenE" accept="image/*">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="EditID_Categoria" class="form-label">Categoría</label>
                                        <select class="form-control" id="EditID_Categoria" name="ID_Categoria" required>
                                            </select>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <div id="contenedor" class="promo-grid">
                    </div>
                <div id="paginacion" class="mt-3 d-flex justify-content-center"></div>
            </div>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>

</html>