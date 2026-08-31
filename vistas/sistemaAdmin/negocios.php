
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Negocios - Yo Local</title>
    <?php
    $adminCssFiles = ["assets/css/negociosAdmin.css", "assets/css/paginacion.css", "assets/css/adminFormal.css"];
    include_once("head.php");
    ?>
    <script>
        if (typeof usuarioId === 'undefined') {
            var usuarioId = <?= json_encode($_SESSION["ID_Usuario"]) ?>;
        }
        if (typeof usuarioTipo === 'undefined') {
            var usuarioTipo = <?= json_encode($_SESSION["tipo"]) ?>;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/index.js"></script>
    <script type="module" src="<?= htmlspecialchars(ylAssetUrl("assets/js/funcionesNegocio.js"), ENT_QUOTES, "UTF-8") ?>"></script>
</head>

<body>
    <div class="navigation admin-sidebar">
        <?php
        include_once("encabezado.php")
        ?>
    </div>
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
                    <img src="assets/img/descarga.gif"  alt="">
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
        <main class="admin-users-page">
            <section class="users-hero">
                <div>
                    <span class="eyebrow">Administraci&oacute;n</span>
                    <h1 class="admin-page-title">Negocios</h1>
                </div>
                <?php if ($_SESSION["tipo"] === "admin"): ?>
                    <div class="hero-actions">
                        <button type="button" class="btn-new-business" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                            <i class="bi bi-plus-lg"></i> Nuevo Negocio
                        </button>
                        <a href="controladores/ReporteNegocios.php" class="btn-reporte" target="_blank">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Reporte
                        </a>
                    </div>
                <?php endif; ?>
            </section>

            <section class="users-toolbar">
                <div class="search-filter-row">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Buscar por nombre..." data-filter-key="Nombre">
                        <button id="searchClear" class="search-clear" type="button">&times;</button>
                    </div>

                    <div class="filter-dropdown">
                        <button class="btn-filter-toggle" type="button" id="filterDropdownBtn">
                            <i class="bi bi-funnel"></i>
                            Filtrar
                        </button>
                        <div class="filter-dropdown-menu" id="filterMenu">
                            <div class="filter-menu-title">Filtrar por</div>
                            <button class="filter-option active" data-key="Nombre" data-placeholder="Buscar por nombre..." type="button">
                                <i class="bi bi-shop"></i> Nombre Negocio
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            <button class="filter-option" data-key="Telefono" data-placeholder="Buscar por propietario..." type="button">
                                <i class="bi bi-person"></i> Propietario
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            
                            <div class="filter-divider"></div>
                            
                            <div class="filter-menu-title">Categor&iacute;as</div>
                            <div class="filter-category-container" id="categoryFilterList">
                                <!-- Se llena dinámicamente con JS -->
                                <div class="filter-category-loading">Cargando...</div>
                            </div>

                            <div class="filter-divider"></div>
                            <div class="filter-status-group">
                                <span class="filter-status-label"><i class="bi bi-shield-check"></i> Estado</span>
                                <div class="status-toggle-group" id="statusToggle">
                                    <button class="status-btn active" data-status="todos" type="button">Todos</button>
                                    <button class="status-btn" data-status="1" type="button">Activo</button>
                                    <button class="status-btn" data-status="0" type="button">Inactivo</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="btn-privacy-toggle" type="button" id="btnPrivacyToggle">
                        <i class="bi bi-eye-slash"></i>
                        <span>Ocultar datos</span>
                    </button>

                    <button class="btn-sort-toggle" type="button" id="btnSortAz" title="Az">
                        <i class="bi bi-sort-alpha-down"></i>
                        <span>Az</span>
                    </button>
                    <button class="btn-sort-toggle" type="button" id="btnSortNum" title="1-9">
                        <i class="bi bi-sort-numeric-down"></i>
                        <span>1-9</span>
                    </button>

                    <button id="limpiarM" class="btn-clear-filters" type="button">
                        <i class="bi bi-x-circle"></i>
                        Limpiar
                    </button>
                </div>
            </section>

            <section class="users-list-panel">
                <div class="users-list-header">
                    <h2 class="admin-section-title">Directorio</h2>
                    <span class="results-counter" id="negociosContador">Cargando...</span>
                </div>
                <div id="ListaMiembros" class="users-grid"></div>
                <div class="pagination-footer">
                    <div id="paginacion" class="mt-4"></div>
                </div>
            </section>
        </main>


            <!-- Modal AGREGAR -->
            <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAgregarLabel">Agregar Negocio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregar">
                                <div class="row g-3">
                                    <div class="mb-3 d-flex align-items-center">
                                        <div class="me-3 flex-grow-1">
                                            <label for="ID_Usuario" class="form-label"># Usuario</label>
                                            <input type="number" class="form-control" id="ID_Usuario" name="ID_Usuario" placeholder="Escriba el n&uacute;mero" required>
                                        </div>
                                        <div class="flex-grow-2">
                                            <label for="nombreMiembro" class="form-label">Nombre del Usuario</label>
                                            <input type="text" class="form-control" id="nombreMiembro" placeholder="Nombre" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="Nombre" class="form-label">Nombre Negocio</label>
                                        <input type="text" class="form-control" id="Nombre" name="Nombre" maxlength="30" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ID_Categoria" class="form-label">Tipo de Negocio</label>
                                        <select class="form-control" id="ID_Categoria" name="ID_Categoria" required>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="CodigoCanje" class="form-label">C&oacute;digo de Canje (Secret)</label>
                                        <input type="text" class="form-control" id="CodigoCanje" name="CodigoCanje" maxlength="20" value="" placeholder="Ej. CAFE2025 (opcional)" autocomplete="off">
                                        <div class="form-text text-secondary">Si lo dejas vac&iacute;o, se genera uno al azar autom&aacute;ticamente. &Uacute;salo en <code>canje.php</code>.</div>
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

            <!-- Modal EDITAR -->
            <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarLabel">Editar Negocio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form id="formEditar">
                                <input type="hidden" id="ID_Negocio" name="ID_Negocio">

                                <div class="row g-3">

                                    <!-- Nombre -->
                                    <div class="col-md-6">
                                        <label for="NombreEdit" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre_negocioEdit" name="nombre_negocioEdit" maxlength="50" required>
                                    </div>

                                    <!-- Tel&eacute;fono -->
                                    <div class="col-md-6">
                                        <label for="TelefonoEdit" class="form-label">Tel&eacute;fono</label>
                                        <input type="tel" class="form-control" id="TelefonoEdit" name="TelefonoEdit" maxlength="15">
                                    </div>
                                    <!-- Direcci&oacute;n -->
                                    <div class="col-12">
                                        <label for="DireccionEdit" class="form-label">Direcci&oacute;n</label>
                                        <input type="text" class="form-control" id="DireccionEdit" name="DireccionEdit" maxlength="150">
                                    </div>

                                    <!-- Descripci&oacute;n (textarea grande) -->
                                    <div class="col-12">
                                        <label for="DescripcionNEdit" class="form-label">Descripci&oacute;n</label>
                                        <textarea class="form-control" id="DescripcionNEdit" name="DescripcionNEdit" rows="4" maxlength="500" placeholder="Ingrese la descripci&oacute;n del negocio"></textarea>
                                    </div>
                                    <!-- Correo -->
                                    <div class="col-md-6">
                                        <label for="CorreoNEdit" class="form-label">Correo</label>
                                        <input type="email" class="form-control" id="CorreoNEdit" name="CorreoNEdit" maxlength="50">
                                    </div>

                                    <!-- Sitio Web -->
                                    <div class="col-md-6">
                                        <label for="SitioWebEdit" class="form-label">Sitio Web</label>
                                        <input type="url" class="form-control" id="SitioWebEdit" name="SitioWebEdit" maxlength="100">
                                    </div>

                                    <!-- Facebook -->
                                    <div class="col-md-6">
                                        <label for="FacebookEdit" class="form-label">Facebook</label>
                                        <input type="url" class="form-control" id="FacebookEdit" name="FacebookEdit" maxlength="100">
                                    </div>

                                    <!-- Instagram -->
                                    <div class="col-md-6">
                                        <label for="InstagramEdit" class="form-label">Instagram</label>
                                        <input type="url" class="form-control" id="InstagramEdit" name="InstagramEdit" maxlength="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="TikTokEdit" class="form-label">TikTok</label>
                                        <input type="url" class="form-control" id="TikTokEdit" name="TikTokEdit" maxlength="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="IconoNegocioEdit" class="form-label">Logo del Negocio</label>
                                        <input type="file" class="form-control" id="IconoNegocioEdit" name="IconoNegocioEdit" accept="image/jpeg,image/png,image/webp,image/gif">
                                    </div>
                                    <input type="hidden" id="RutaiconoEdit" name="RutaiconoEdit">
                                    <div class="col-md-12">
                                        <label for="GoogleMapsEdit" class="form-label">Link de Google Maps</label>
                                        <input type="url" class="form-control" id="GoogleMapsEdit" name="GoogleMapsEdit" maxlength="100">
                                    </div>
                                     <div class="col-md-6">
                                        <label for="LatitudEdit" class="form-label">Latitud</label>
                                        <input type="text" class="form-control" id="LatitudEdit" name="LatitudEdit" maxlength="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="LongitudEdit" class="form-label">Longitud</label>
                                        <input type="text" class="form-control" id="LongitudEdit" name="LongitudEdit" maxlength="100">
                                    </div>
                                    <?php if ($_SESSION["tipo"] === "admin"): ?>
                                        <div class="col-12">
                                            <label for="RelevanciaEdit" class="form-label">Relevancia</label>
                                            <select name="RelevanciaEdit" id="RelevanciaEdit" class="form-select" required>
                                                <option value="">Seleccione una categor&iacute;a...</option>
                                                <option value="1">Normal</option>
                                                <option value="2">Destacado</option>
                                                <option value="3">Super Destacado</option>
                                                <option value="4">Patrocinadores</option>
                                            </select>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>



            <!-- Modal CREAR/EDITAR HORARIO -->
            <div class="modal fade" id="modalHorario" tabindex="-1" aria-labelledby="modalHorarioLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalHorarioLabel">Horario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form id="formHorario">



                                <input type="hidden" id="ID_NegocioHorario" name="ID_Negocio">
                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label for="dia_semana" class="form-label">D&iacute;a de la Semana</label>
                                        <select class="form-select" id="dia_semana" name="dia_semana" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Lunes">Lunes</option>
                                            <option value="Martes">Martes</option>
                                            <option value="Mi&eacute;rcoles">Mi&eacute;rcoles</option>
                                            <option value="Jueves">Jueves</option>
                                            <option value="Viernes">Viernes</option>
                                            <option value="S&aacute;bado">S&aacute;bado</option>
                                            <option value="Domingo">Domingo</option>
                                        </select>
                                        <div class="invalid-feedback">Seleccione un d&iacute;a v&aacute;lido</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="hora_apertura" class="form-label">Hora de Apertura</label>
                                        <input type="time" class="form-control" id="hora_apertura" name="hora_apertura" required>
                                        <div class="invalid-feedback">Ingrese una hora v&aacute;lida</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="hora_cierre" class="form-label">Hora de Cierre</label>
                                        <input type="time" class="form-control" id="hora_cierre" name="hora_cierre" required>
                                        <div class="invalid-feedback">Ingrese una hora v&aacute;lida</div>
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

            <!-- Modal Subir Im&aacute;genes -->
            <div class="modal fade" id="modalImagenes" tabindex="-1" aria-labelledby="modalImagenesLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalImagenesLabel">Subir Im&aacute;genes del Negocio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form id="formImagenes" enctype="multipart/form-data">
                                <input type="hidden" id="ID_NegocioImagenes" name="ID_NegocioImagenes">

                                <div id="dropzone" class="dropzone">
                                    <p>Arrastra tus im&aacute;genes aqu&iacute; o haz clic para seleccionarlas (m&aacute;x. 4)</p>
                                    <input type="file" id="fileInput" name="imagenes[]" accept="image/*" multiple hidden>
                                </div>

                                <!-- Vista previa -->
                                <div id="previewContainer" class="row mt-3 g-3"></div>

                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>



            <!-- Modal Galería de Imágenes -->
            <div class="modal fade" id="modalGaleria" tabindex="-1" aria-labelledby="modalGaleriaLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold" id="modalGaleriaLabel"><i class="bi bi-images me-2 text-primary"></i> Galería del Negocio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div id="contenedorGaleria" class="min-vh-50 d-flex align-items-center justify-content-center bg-dark" style="min-height: 400px;">
                                <div class="text-white text-center">
                                    <div class="spinner-border text-primary mb-2" role="status"></div>
                                    <p>Cargando imágenes...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Container -->
            <div class="container-fluid px-4 mt-4">
                <div id="ListaMiembros"></div>
                <div id="paginacion" class="mt-4"></div>
            </div>
        </div>
        <script src="assets/js/main.js"></script>
</body>

</html>
