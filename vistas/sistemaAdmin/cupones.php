<!DOCTYPE html>
<html lang="es">

<head>
    <title>Cupones - Yo Local</title>
    <?php
    $adminCssFiles = ["assets/css/negociosAdmin.css", "assets/css/cupones.css", "assets/css/paginacion.css", "assets/css/adminFormal.css"];
    include_once("head.php");
    ?>
    <?php
    if (!defined('RUTA')) {
        require_once dirname(dirname(__DIR__)) . '/config.php';
    }
    $ylScriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $ylPublicRoot = rtrim(str_replace('\\', '/', dirname(dirname(dirname($ylScriptName)))), '/');
    if ($ylPublicRoot === '' || $ylPublicRoot === '.') {
        $ylPublicRoot = rtrim(RUTA, '/');
    }
    ?>
    <script>
        if (typeof usuarioId === 'undefined') {
            var usuarioId = <?= json_encode($_SESSION["ID_Usuario"]) ?>;
        }
        if (typeof usuarioTipo === 'undefined') {
            var usuarioTipo = <?= json_encode($_SESSION["tipo"]) ?>;
        }
        window.__YL_PUBLIC_ROOT__ = <?= json_encode($ylPublicRoot, JSON_UNESCAPED_SLASHES) ?>;
        window.__YL_PUBLIC_ORIGIN__ = <?= json_encode(defined("YL_ORIGEN_PUBLICO") ? YL_ORIGEN_PUBLICO : "", JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script type="module" src="<?= htmlspecialchars(ylAssetUrl("assets/js/funcionesCupones.js"), ENT_QUOTES, "UTF-8") ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
                  <img src="assets/img/descarga.gif"   alt="">
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
                    <h1 class="admin-page-title">Cupones</h1>
                </div>
                <div class="hero-actions">
                    <?php if ($_SESSION["tipo"] === "admin" || $_SESSION["tipo"] === "negocio"): ?>
                    <button type="button" class="btn-new-business" data-bs-toggle="modal" data-bs-target="#modalPromocion">
                        <i class="bi bi-plus-lg"></i> Nueva Promoci&oacute;n
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION["tipo"] === "admin"): ?>
                    <a href="controladores/ReporteCupones.php" class="btn-reporte" target="_blank">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Reporte
                    </a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="users-toolbar">
                <div class="search-filter-row">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Buscar por t&iacute;tulo..." data-filter-key="titulo">
                        <button id="searchClear" class="search-clear" type="button">&times;</button>
                    </div>

                    <div class="filter-dropdown">
                        <button class="btn-filter-toggle" type="button" id="filterDropdownBtn">
                            <i class="bi bi-funnel"></i>
                            Filtrar
                        </button>
                        <div class="filter-dropdown-menu" id="filterMenu">
                            <div class="filter-menu-title">Filtrar por</div>
                            <button class="filter-option active" data-key="titulo" data-placeholder="Buscar por t&iacute;tulo..." type="button">
                                <i class="bi bi-card-text"></i> T&iacute;tulo
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            <button class="filter-option" data-key="descripcion" data-placeholder="Buscar por descripci&oacute;n..." type="button">
                                <i class="bi bi-justify-left"></i> Descripci&oacute;n
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            <button class="filter-option" data-key="negocio" data-placeholder="Buscar por negocio..." type="button">
                                <i class="bi bi-shop"></i> Negocio
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            
                            <div class="filter-divider"></div>
                            <div class="filter-status-group">
                                <span class="filter-status-label"><i class="bi bi-shield-check"></i> Estado</span>
                                <div class="status-toggle-group" id="statusToggle">
                                    <button class="status-btn active" data-status="todos" type="button">Todos</button>
                                    <button class="status-btn" data-status="activo" type="button">Activos</button>
                                    <button class="status-btn" data-status="expirado" type="button">Expirados</button>
                                </div>
                            </div>
                            
                            <div class="filter-divider"></div>
                            <button class="btn-clear-filters w-100 mt-2" type="button" id="limpiarFiltros">
                                <i class="bi bi-x-circle"></i> Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="users-list-panel">
                <div class="users-list-header">
                    <h2 class="admin-section-title">Cat&aacute;logo de Cupones</h2>
                    <span class="results-counter" id="cuponesContador">Cargando...</span>
                </div>
                <div id="contenedor" class="promo-grid"></div>
                <div class="pagination-footer">
                    <div id="paginacion" class="mt-4"></div>
                </div>
            </section>
        </main>


            <!-- Modal AGREGAR -->
            <div class="modal fade" id="modalPromocion" tabindex="-1" aria-labelledby="modalPromocionLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPromocionLabel">Agregar Promoci&oacute;n</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formPromocion">
                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label for="Titulo" class="form-label">T&iacute;tulo</label>
                                        <input type="text" class="form-control" id="Titulo" name="Titulo" maxlength="100" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="Descripcion" class="form-label">Descripci&oacute;n</label>
                                        <textarea class="form-control" id="Descripcion" name="Descripcion" rows="3" maxlength="190" required></textarea>
                                    </div>


                                    <div class="col-md-6">
                                        <label for="FechaFin" class="form-label">Fecha Fin</label>
                                        <input type="date" class="form-control" id="FechaFin" name="FechaFin" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="Titulo" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="Cantidad" name="Cantidad" maxlength="100" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label d-block">&iquest;PromoMi&eacute;rcoles?</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="promoMiercoles" id="promoNo" value="0" required>
                                            <label class="form-check-label" for="promoNo">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="promoMiercoles" id="promoSi" value="1">
                                            <label class="form-check-label" for="promoSi">S&iacute;</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="ID_Negocio" class="form-label">Negocio</label>
                                        <select class="form-select" id="ID_Negocio" name="ID_Negocio" required>
                                            <option value="">Cargando negocios...</option>
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

            <!-- Modal EDITAR -->
            <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarLabel">Editar Promoci&oacute;n</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditar">
                                <input type="hidden" id="ID_Promocion" name="ID_Promocion">
                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label for="EditTitulo" class="form-label">T&iacute;tulo</label>
                                        <input type="text" class="form-control" id="EditTitulo" name="EditTitulo" maxlength="100" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="EditDescripcion" class="form-label">Descripci&oacute;n</label>
                                        <textarea class="form-control" id="EditDescripcion" name="EditDescripcion" rows="3" maxlength="190" required></textarea>
                                    </div>


                                    <div class="col-md-6">
                                        <label for="EditFechaFin" class="form-label">Fecha Fin</label>
                                        <input type="date" class="form-control" id="EditFechaFin" name="EditFechaFin" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="EditCantidad" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="EditCantidad" name="EditCantidad" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="ID_NegocioEdit" class="form-label">Negocio</label>
                                        <select class="form-select" id="ID_NegocioEdit" name="ID_NegocioEdit" required>
                                            <option value="">Cargando negocios...</option>
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

            <!-- Modal AGREGAR CUPONES (SUMAR) -->
            <div class="modal fade" id="modalAgregarC" tabindex="-1" aria-labelledby="modalAgregarCLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAgregarCLabel">Agregar Cupones</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregarC">
                                <input type="hidden" id="ID_PromocionC" name="ID_PromocionC">
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Cantidad a sumar</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" required min="1">
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Sumar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</body>

</html>
