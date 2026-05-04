<!DOCTYPE html>
<html lang="es">

<head>
    <title>Categor&iacute;as - Yo Local</title>
    <?php
    $adminCssFiles = ["assets/css/categorias.css", "assets/css/paginacion.css", "assets/css/adminFormal.css"];
    include_once("head.php");
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script type="module" src="assets/js/funcionesCategorias.js"></script>
</head>

<body>
    <div class="navigation admin-sidebar">
        <?php include_once("encabezado.php") ?>
    </div>

    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <i class="ri-menu-2-line admin-menu-icon" aria-hidden="true"></i>
            </div>
            <div class="contenedor">
                <!-- Perfil / Notificaciones -->
                <div class="usuario">
                    <img src="assets/img/descarga.gif" alt="Usuario">
                </div>
            </div>
        </div>

        <main class="admin-users-page">
            <section class="users-hero">
                <div>
                    <span class="eyebrow">Administraci&oacute;n</span>
                    <h1 class="admin-page-title">Categor&iacute;as</h1>
                </div>
                <div class="hero-actions">
                    <button type="button" class="btn-new-business" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                        <i class="ri-plus-line"></i> Nueva Categor&iacute;a
                    </button>
                </div>
            </section>

            <section class="users-toolbar">
                <div class="search-filter-row">
                    <div class="search-input-wrapper">
                        <i class="ri-search-line search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Buscar categor&iacute;a..." data-filter-key="Nombre">
                        <button id="searchClear" class="search-clear" type="button">&times;</button>
                    </div>

                    <div class="filter-dropdown">
                        <button class="btn-filter-toggle" type="button" id="filterDropdownBtn">
                            <i class="ri-filter-3-line"></i>
                            <span>Filtrar</span>
                        </button>
                        <div class="filter-dropdown-menu" id="filterMenu">
                            <div class="filter-menu-title">Buscar por</div>
                            <button class="filter-option active" data-key="Nombre" data-placeholder="Buscar categor&iacute;a..." type="button">
                                <i class="ri-font-size"></i> Nombre de Categor&iacute;a
                                <i class="ri-check-line check-icon"></i>
                            </button>
                            <button class="filter-option" data-key="ID" data-placeholder="Buscar por ID (ej: 25)..." type="button">
                                <i class="ri-hashtag"></i> ID de Categor&iacute;a
                                <i class="ri-check-line check-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button class="btn-privacy-toggle" type="button" id="btnPrivacyToggle">
                        <i class="ri-eye-off-line"></i>
                        <span>Ocultar datos</span>
                    </button>

                    <button class="btn-sort-toggle" type="button" id="btnSortAz" data-sort="nombre">
                        <i class="ri-sort-alphabet-asc"></i>
                        <span>Az</span>
                    </button>

                    <button class="btn-sort-toggle" type="button" id="btnSortNum" data-sort="id">
                        <i class="ri-sort-number-asc"></i>
                        <span>1-9</span>
                    </button>

                    <button id="limpiarM" class="btn-clear-filters" type="button">
                        <i class="ri-refresh-line"></i> Limpiar
                    </button>
                </div>
            </section>

            <section class="users-list-panel">
                <div id="ListaMiembros" class="row g-4">
                    <!-- Tarjetas de categorías se cargan aquí -->
                    <div class="col-12 text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>

                <div id="paginacion" class="mt-5 d-flex justify-content-center"></div>
            </section>
        </main>

    </div>

    <!-- Modal AGREGAR -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarLabel">Nueva Categor&iacute;a</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgregar" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="Nombre" class="form-label">Nombre de categor&iacute;a</label>
                            <input type="text" class="form-control" id="Nombre" name="Nombre" maxlength="50" required placeholder="Ej: Restaurantes">
                        </div>
                        <div class="mb-3">
                            <label for="Color" class="form-label">Color Distintivo</label>
                            <div class="color-input-group">
                                <input type="color" class="form-control form-control-color" id="Color" name="Color" value="#7b68ee" title="Elige un color">
                                <span class="text-muted small">Este color aparecer&aacute; en la tarjeta.</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="Imagen" class="form-label">Icono / Imagen</label>
                            <input type="file" class="form-control" id="Imagen" name="Imagen" accept="image/*">
                            <div id="previewAgregar" class="preview-container">
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn-new-business w-100 justify-content-center">Guardar Categor&iacute;a</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal EDITAR -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Categor&iacute;a</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditar" enctype="multipart/form-data">
                        <input type="hidden" id="ID_Categoria" name="ID_Categoria">
                        <input type="hidden" id="RutaImagenActual" name="RutaImagenActual">
                        
                        <div class="mb-3">
                            <label for="NombreEdit" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="NombreEdit" name="NombreEdit" maxlength="50" required>
                        </div>
                        <div class="mb-3">
                            <label for="ColorEdit" class="form-label">Color Distintivo</label>
                            <div class="color-input-group">
                                <input type="color" class="form-control form-control-color" id="ColorEdit" name="ColorEdit" value="#7b68ee">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ImagenEdit" class="form-label">Cambiar Icono / Imagen</label>
                            <input type="file" class="form-control" id="ImagenEdit" name="ImagenEdit" accept="image/*">
                            <div id="previewEditar" class="preview-container">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn-new-business w-100 justify-content-center">Actualizar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal VER NEGOCIOS -->
    <div class="modal fade" id="modalVerNegocios" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 1.5rem;">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="tituloModalNegocios" style="font-size: 1.3rem;">Negocios en Categor&iacute;a</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div id="listaNegociosCategoria" class="d-flex flex-column gap-2">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal MOVER NEGOCIO -->
    <div class="modal fade" id="modalMoverNegocio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Mover Categor&iacute;a</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="moverNegocioId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-2">Nueva Categor&iacute;a</label>
                        <select id="selectNuevaCategoria" class="form-select rounded-3">
                            <!-- Se llena dinámicamente -->
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100 rounded-3 py-2 fw-bold" onclick="finalizarMoverNegocio()">Confirmar Movimiento</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>

</html>
