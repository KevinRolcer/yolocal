<!DOCTYPE html>
<html lang="es">

<head>
    <title>Usuarios - Yo Local</title>
    <?php
    $adminCssFiles = ["assets/css/usuarios.css", "assets/css/paginacion.css", "assets/css/adminFormal.css"];
    include_once("head.php");
    ?>
    <script type="module" src="assets/js/funcionesUsu.js"></script>
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
                    <h1 class="admin-page-title">Usuarios</h1>
                </div>
                <button type="button" class="btn btn-primary btn-add-user" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="bi bi-person-plus"></i>
                    Nuevo usuario
                </button>
            </section>

            <section class="users-toolbar" aria-label="Filtros de usuarios">
                <div class="search-filter-row">
                    <!-- Dynamic single search input -->
                    <div class="search-input-wrapper" id="searchWrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Buscar por nombre..." autocomplete="off" data-filter-key="Nombre">
                        <button class="search-clear" type="button" id="searchClear" aria-label="Limpiar">&times;</button>
                    </div>

                    <!-- Filter dropdown -->
                    <div class="filter-dropdown" id="filterDropdown">
                        <button class="btn-filter-toggle" type="button" id="btnFilterToggle">
                            <i class="bi bi-funnel"></i>
                            <span>Filtrar</span>
                            <span class="filter-badge" id="filterBadge" style="display:none;">0</span>
                        </button>
                        <div class="filter-dropdown-menu" id="filterMenu">
                            <div class="filter-menu-title">Filtrar por</div>
                            <button class="filter-option active" data-key="Nombre" data-placeholder="Buscar por nombre..." type="button">
                                <i class="bi bi-person"></i> Nombre
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            <button class="filter-option" data-key="Apellidos" data-placeholder="Buscar por apellidos..." type="button">
                                <i class="bi bi-person-lines-fill"></i> Apellidos
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            <button class="filter-option" data-key="Telefono" data-placeholder="Buscar por correo..." type="button">
                                <i class="bi bi-envelope"></i> Correo
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            <button class="filter-option" data-key="ID_Miembro" data-placeholder="Buscar por n&uacute;mero..." type="button">
                                <i class="bi bi-hash"></i> N&ordm; de usuario
                                <i class="bi bi-check2 check-icon"></i>
                            </button>
                            <div class="filter-divider"></div>
                            <div class="filter-status-group">
                                <span class="filter-status-label"><i class="bi bi-shield-check"></i> Estado</span>
                                <div class="status-toggle-group" id="statusToggle">
                                    <button class="status-btn active" data-status="todos" type="button">Todos</button>
                                    <button class="status-btn" data-status="activo" type="button">Activo</button>
                                    <button class="status-btn" data-status="inactivo" type="button">Inactivo</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy toggle -->
                    <button class="btn-privacy-toggle" type="button" id="btnPrivacyToggle">
                        <i class="bi bi-eye-slash"></i>
                        <span>Ocultar datos</span>
                    </button>

                    <!-- Sort buttons -->
                    <button class="btn-sort-toggle" type="button" id="btnSortAz" title="Ordenar alfab&eacute;ticamente">
                        <i class="bi bi-sort-alpha-down"></i>
                        <span>Az</span>
                    </button>
                    <button class="btn-sort-toggle" type="button" id="btnSortNum" title="Ordenar por n&uacute;mero">
                        <i class="bi bi-sort-numeric-down"></i>
                        <span>1-9</span>
                    </button>

                    <!-- Clear all -->
                    <button id="limpiarM" class="btn btn-light btn-clear-filters" type="button">
                        <i class="bi bi-x-circle"></i>
                        Limpiar
                    </button>
                </div>
            </section>


            <!-- Modal AGREGAR -->
            <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAgregarLabel">Agregar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregar">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="Nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="Nombre" name="Nombre" maxlength="30" placeholder="Nombre" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ApellidoP" class="form-label">Ap. Paterno</label>
                                        <input type="text" class="form-control" id="ApellidoP" name="ApellidoP" maxlength="30" placeholder="Paterno" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ApellidoM" class="form-label">Ap. Materno</label>
                                        <input type="text" class="form-control" id="ApellidoM" name="ApellidoM" maxlength="30" placeholder="Materno" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback"></div>
                                    </div>

                                    <div class="col-md-8">
                                        <label for="NombreUsu" class="form-label">Correo</label>
                                        <input type="text" class="form-control" id="NombreUsu" name="NombreUsu" maxlength="50" placeholder="correo@ejemplo.com" required>
                                        <span id="errorNombreUsu"></span>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="usutip" class="form-label">Tipo</label>
                                        <select class="form-control" id="usutip" name="usutip">
                                            <option value="admin">Administrador</option>
                                            <option value="negocio">Negocio</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="Contra" class="form-label">Contrase&ntilde;a</label>
                                        <input type="password" class="form-control" id="Contra" name="Contra" maxlength="16" placeholder="••••••••" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ContraConfirmar" class="form-label">Confirmar</label>
                                        <input type="password" class="form-control" id="ContraConfirmar" name="ContraConfirmar" maxlength="16" placeholder="••••••••" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="valid-feedback"></div>
                                    </div>
                                </div>
                                <div class="text-end mt-2">
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
                            <h5 class="modal-title" id="modalEditarLabel">Editar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditar">
                                <input type="hidden" id="ID_Usuario" name="ID_Usuario">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="NombreEdit" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="NombreEdit" name="NombreEdit" maxlength="30" required>
                                        <div class="invalid-feedback">
                                            Password is required
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ApellidoPEdit" class="form-label">Apellido Paterno</label>
                                        <input type="text" class="form-control" id="ApellidoPEdit" name="ApellidoPEdit" maxlength="30" required>
                                        <div class="invalid-feedback">
                                            Password is required
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ApellidoMEdit" class="form-label">Apellido Materno</label>
                                        <input type="text" class="form-control" id="ApellidoMEdit" name="ApellidoMEdit" maxlength="30" required>
                                        <div class="invalid-feedback">
                                            Password is required
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="NombreUsuEdit" class="form-label">Correo</label>
                                        <input type="text" class="form-control" id="NombreUsuEdit" name="NombreUsuEdit" maxlength="50" required>
                                        <div class="invalid-feedback">
                                            Password is required
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="usutipEdit" class="form-label">Tipo de Usuario</label>
                                        <select class="form-control" id="usutipEdit" name="usutipEdit">
                                            <option value="admin">Administrador</option>
                                            <option value="negocio">Negocio</option>
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

            <!-- Modal para cambiar contrase&ntilde;a -->
            <div class="modal fade" id="modalEditarClave" tabindex="-1" aria-labelledby="modalEditarClaveLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarClaveLabel">Cambiar Contrase&ntilde;a</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditarClave">
                                <input type="hidden" id="ID_UsuarioClave" name="ID_Usuario">

                                <div class="mb-3">
                                    <label for="ClaveNueva" class="form-label">Nueva Contrase&ntilde;a</label>
                                    <input type="password" class="form-control" id="ClaveNueva" name="ClaveNueva" maxlength="16" required>
                                    <div class="invalid-feedback">
                                        Password is required
                                    </div>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="ConfirmarClave" class="form-label">Confirmar Contrase&ntilde;a</label>
                                    <input type="password" class="form-control" id="ConfirmarClave" name="ConfirmarClave" maxlength="16" required>
                                    <div class="invalid-feedback">
                                        Password is required
                                    </div>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <section class="users-list-panel">
                <div class="users-list-header">
                    <h2 class="admin-section-title">Directorio</h2>
                    <span class="results-counter" id="usuariosContador">Cargando...</span>
                </div>
                <div id="ListaMiembros" class="users-grid">
                </div>
                <div class="pagination-footer">
                    <div id="paginacion" class="mt-3 d-flex justify-content-center"></div>
                    <span class="per-page-label" id="porPaginaLabel"></span>
                </div>
            </section>

        </main>


    </div>
    <script src="assets/js/main.js"></script>
</body>

</html>
