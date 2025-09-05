<!DOCTYPE html>
<html lang="es">

<head>
    <title>Negocios - Yo Local</title>
    <?php
    include_once("head.php");
    ?>
    <script type="module" src="assets/js/funcionesNegocio.js?v=1.3.7"></script>
    <link rel="stylesheet" href="../assets/css/negocios.css">
    <link rel="stylesheet" href="../assets/css/paginacion.css">
    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/index.js"></script>
    <link href="../assets/img/LogoYolocal.png" rel="icon" />
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
            <h1 class="text-start fw-bold">Negocios</h1>
            <h4 class="text-start text-secondary">Sección para administrar negocios aliados de YoLocal.</h4>
        </div>
        <div class="container mt-5">
            <div class="filter-container">
                <!--
            <div class="filter" data-filter="id">
                <span>ID</span><input type="number" id="idM" class="hidden" placeholder="Escribe aquí.."> <button class="close"></button> <button class="close">✖</button>
            </div>
            -->
                <div class="filter" data-filter="nombre">
                    <span>Nombre Negocio</span> <input type="text" id="nombreM" class="hidden" placeholder="Escribe aquí.."> <button class="close"></button> <button class="close">✖</button>
                </div>

                <div class="filter" data-filter="numero">
                    <span>Propietario</span> <input type="text" id="numM" class="hidden" placeholder="Escribe aquí.."> <button class="close"></button> <button class="close">✖</button>
                </div>
                <div class="filter-miembros">
                    <button id="limpiarM" class="btn btn-secondary">Limpiar Filtros</button>
                </div>
            </div>

            <div class="gB">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    Nuevo Negocio
                </button>
            </div>


            <!-- Modal AGREGAR -->
            <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAgregarLabel">Agregar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregar">
                                <div class="row g-3">
                                    <div class="mb-3 d-flex align-items-center">
                                        <div class="me-3 flex-grow-1">
                                            <label for="ID_Usuario" class="form-label"># Miembro</label>
                                            <input type="number" class="form-control" id="ID_Usuario" name="ID_Usuario" placeholder="Escriba el número" required>
                                        </div>
                                        <div class="flex-grow-2">
                                            <label for="nombreMiembro" class="form-label">Nombre del Miembro</label>
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
                                        <label for="ID_Categoria" class="form-label">Tipo de Membresía</label>
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

            <!-- Teléfono -->
            <div class="col-md-6">
              <label for="TelefonoEdit" class="form-label">Teléfono</label>
              <input type="tel" class="form-control" id="TelefonoEdit" name="TelefonoEdit" maxlength="15">
            </div>
             <!-- Dirección -->
            <div class="col-12">
              <label for="DireccionEdit" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="DireccionEdit" name="DireccionEdit" maxlength="150">
            </div>

            <!-- Descripción (textarea grande) -->
            <div class="col-12">
              <label for="DescripcionNEdit" class="form-label">Descripción</label>
              <textarea class="form-control" id="DescripcionNEdit" name="DescripcionNEdit" rows="4" maxlength="500" placeholder="Ingrese la descripción del negocio"></textarea>
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
                                        <label for="dia_semana" class="form-label">Día de la Semana</label>
                                        <select class="form-select" id="dia_semana" name="dia_semana" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Lunes">Lunes</option>
                                            <option value="Martes">Martes</option>
                                            <option value="Miércoles">Miércoles</option>
                                            <option value="Jueves">Jueves</option>
                                            <option value="Viernes">Viernes</option>
                                            <option value="Sábado">Sábado</option>
                                            <option value="Domingo">Domingo</option>
                                        </select>
                                        <div class="invalid-feedback">Seleccione un día válido</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="hora_apertura" class="form-label">Hora de Apertura</label>
                                        <input type="time" class="form-control" id="hora_apertura" name="hora_apertura" required>
                                        <div class="invalid-feedback">Ingrese una hora válida</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="hora_cierre" class="form-label">Hora de Cierre</label>
                                        <input type="time" class="form-control" id="hora_cierre" name="hora_cierre" required>
                                        <div class="invalid-feedback">Ingrese una hora válida</div>
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

            <!-- Modal Subir Imágenes -->
            <div class="modal fade" id="modalImagenes" tabindex="-1" aria-labelledby="modalImagenesLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalImagenesLabel">Subir Imágenes del Negocio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form id="formImagenes" enctype="multipart/form-data">
                                <input type="hidden" id="ID_NegocioImagenes" name="ID_NegocioImagenes">

                                <div id="dropzone" class="dropzone">
                                    <p>Arrastra tus imágenes aquí o haz clic para seleccionarlas (máx. 4)</p>
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

           

            <!-- Tabla de Usuarios -->
            <div class="mt-3">
                <h4 class="text-center">Lista de Negocios</h4>
                <div class="row" id="ListaMiembros">

                </div>
                <script>

                </script>
                <div id="paginacion" class="mt-3"></div>
            </div>

        </div>


</body>

</html>