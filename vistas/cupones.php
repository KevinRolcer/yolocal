<!DOCTYPE html>
<html lang="es">

<head>
    <title>Usuarios-DragonGym</title>
    <?php
    include_once("head.php");
    ?>
    <script type="module" src="assets/js/funcionesCupones.js?v=2.9"></script>
    <link rel="stylesheet" href="../assets/css/cupones.css">
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
        <div class="container mt-5">
            <h1 class="text-start">Cupones</h1>
            <h4 class="text-start">Sección para administrar cupones de YoLocal.</h4>
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

                <div class="filter" data-filter="negocio">
                    <span>Negocio</span>
                    <input type="text" id="filtroNegocio" class="hidden" placeholder="Escribe aquí..">
                    <button class="close">✖</button>
                </div>


                <div class="filter-promociones">
                    <button id="limpiarFiltros" class="btn btn-secondary">Limpiar Filtros</button>
                </div>

            </div>

            <div class="gB">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPromocion">
                    Nueva Promoción
                </button>
            </div>


            <!-- Modal AGREGAR -->
            <div class="modal fade" id="modalPromocion" tabindex="-1" aria-labelledby="modalPromocionLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPromocionLabel">Agregar Promoción</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formPromocion">
                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label for="Titulo" class="form-label">Título</label>
                                        <input type="text" class="form-control" id="Titulo" name="Titulo" maxlength="100" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="Descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="Descripcion" name="Descripcion" rows="3" required></textarea>
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
                                        <label class="form-label d-block">¿PromoMiércoles?</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="promoMiercoles" id="promoNo" value="0" required>
                                            <label class="form-check-label" for="promoNo">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="promoMiercoles" id="promoSi" value="1">
                                            <label class="form-check-label" for="promoSi">Sí</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="Descripcion" class="form-label">Negocio</label>
                                        <select class="form-control" id="ID_Negocio" name="ID_Negocio" required>
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
                            <h5 class="modal-title" id="modalEditarLabel">Editar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditar">
                                <input type="hidden" id="ID_Promocion" name="ID_Promocion">
                                <div class="col-md-12">
                                    <label for="Titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="EditTitulo" name="EditTitulo" maxlength="100" required>
                                </div>

                                <div class="col-md-12">
                                    <label for="Descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="EditDescripcion" name="EditDescripcion" rows="3" required></textarea>
                                </div>


                                <div class="col-md-6">
                                    <label for="FechaFin" class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="EditFechaFin" name="EditFechaFin" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="Titulo" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="EditCantidad" name="EditCantidad" maxlength="100" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="ID_NegocioEdit" class="form-label">Negocio</label>
                                    <select class="form-control" id="ID_NegocioEdit" name="ID_NegocioEdit" required>
                                    </select>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para cambiar contraseña -->
            <div class="modal fade" id="modalAgregarC" tabindex="-1" aria-labelledby="modalEditarClaveLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarClaveLabel">Cambiar Contraseña</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregarC">
                                <input type="hidden" id="ID_PromocionC" name="ID_PromocionC">

                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Agregar mas cupones</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" maxlength="16" required>
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
            <div class="mt-3">

                <div id="ListaMiembros">
                    <div id="contenedor" class="promo-grid"></div>

                </div>
                <div id="paginacion" class="mt-3 d-flex justify-content-center"></div>
            </div>

        </div>


        <script src="../assets/js/main.js"></script>
</body>

</html>