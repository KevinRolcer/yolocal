<!-- MODALS FOR JOBS AND EVENTS -->

<!-- Modal AGREGAR TRABAJO -->
<div class="modal fade" id="modalPromocion" tabindex="-1" aria-labelledby="modalPromocionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPromocionLabel">Cargar Trabajo</h5>
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
                            <textarea class="form-control" id="Descripcion" name="Descripcion" rows="3" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="Horario" class="form-label">Tipo de Horario</label>
                            <select class="form-control" id="Horario" name="Horario" required>
                                <option value="Turno Completo">Tiempo completo</option>
                                <option value="Matutino">Matutino</option>
                                <option value="Vespertino">Vespertino</option>
                                <option value="Horas">Por horas</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="Salario" class="form-label">Salario</label>
                            <input type="number" class="form-control" id="Salario" name="Salario">
                        </div>
                        <div class="col-md-6">
                            <label for="PerRequeridas" class="form-label">Personas Requeridas</label>
                            <input type="number" class="form-control" id="PerRequeridas" name="PerRequeridas">
                        </div>
                        <div class="col-md-12">
                            <label for="ID_Negocio" class="form-label">Negocio</label>
                            <select class="form-control" id="ID_Negocio" name="ID_Negocio" required></select>
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

<!-- Modal EDITAR TRABAJO -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar informacion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditar">
                    <input type="hidden" id="ID_Promocion" name="ID_Promocion">
                    <div class="col-md-12">
                        <label for="EditTitulo" class="form-label">T&iacute;tulo</label>
                        <input type="text" class="form-control" id="EditTitulo" name="EditTitulo" maxlength="100" required>
                    </div>
                    <div class="col-md-12">
                        <label for="EditDescripcion" class="form-label">Descripci&oacute;n</label>
                        <textarea class="form-control" id="EditDescripcion" name="EditDescripcion" rows="3" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="EditHorario" class="form-label">Tipo de Horario</label>
                        <select class="form-control" id="EditHorario" name="EditHorario" required>
                            <option value="Turno Completo">Tiempo completo</option>
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            <option value="Horas">Por horas</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="EditSalario" class="form-label">Salario</label>
                        <input type="number" class="form-control" id="EditSalario" name="EditSalario">
                    </div>
                    <div class="col-md-6">
                        <label for="EditPerRequeridas" class="form-label">Personas Requeridas</label>
                        <input type="number" class="form-control" id="EditPerRequeridas" name="EditPerRequeridas">
                    </div>
                    <div class="col-md-12">
                        <label for="ID_NegocioEdit" class="form-label">Negocio</label>
                        <select class="form-control" id="ID_NegocioEdit" name="ID_NegocioEdit" required></select>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal AGREGAR EVENTO -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEventoLabel">Cargar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formEvento">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="TituloE" class="form-label">T&iacute;tulo del Evento</label>
                            <input type="text" class="form-control" id="TituloE" name="TituloE" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ID_Categoria" class="form-label">Categor&iacute;a</label>
                            <select class="form-select" id="ID_Categoria" name="ID_Categoria" required></select>
                        </div>
                        <div class="col-md-12">
                            <label for="DescripcionE" class="form-label">Descripci&oacute;n</label>
                            <textarea class="form-control" id="DescripcionE" name="DescripcionE" rows="3" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="PrecioE" class="form-label">Precio (Opcional)</label>
                            <input type="text" class="form-control" id="PrecioE" name="PrecioE" placeholder="Ej: 50.00 o Gratis">
                        </div>
                        <div class="col-md-4">
                            <label for="FechaE" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="FechaE" name="FechaE" required>
                        </div>
                        <div class="col-md-4">
                            <label for="HoraE" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="HoraE" name="HoraE" required>
                        </div>
                        <div class="col-md-12">
                            <label for="UbicacionE" class="form-label">Ubicaci&oacute;n</label>
                            <input type="text" class="form-control" id="UbicacionE" name="UbicacionE" required>
                        </div>
                        <div class="col-md-12">
                            <label for="RutaImagenE" class="form-label">Imagen del Evento</label>
                            <input type="file" class="form-control" id="RutaImagenE" name="RutaImagenE" accept="image/*">
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">Guardar Evento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal EDITAR EVENTO -->
<div class="modal fade" id="modalEditarEvento" tabindex="-1" aria-labelledby="modalEditarEventoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarEventoLabel">Editar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body pb-2">
                <form id="formEditarEvento" enctype="multipart/form-data">
                    <input type="hidden" id="ID_Evento_Editar" name="ID_Evento">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="EditTituloE" class="form-label">T&iacute;tulo del Evento</label>
                            <input type="text" class="form-control" id="EditTituloE" name="TituloE" required>
                        </div>
                        <div class="col-md-6">
                            <label for="EditID_Categoria" class="form-label">Categor&iacute;a</label>
                            <select class="form-select" id="EditID_Categoria" name="ID_Categoria" required></select>
                        </div>
                        <div class="col-md-12">
                            <label for="EditDescripcionE" class="form-label">Descripci&oacute;n</label>
                            <textarea class="form-control" id="EditDescripcionE" name="DescripcionE" rows="3" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="EditPrecioE" class="form-label">Precio</label>
                            <input type="text" class="form-control" id="EditPrecioE" name="PrecioE">
                        </div>
                        <div class="col-md-4">
                            <label for="EditFechaE" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="EditFechaE" name="FechaE" required>
                        </div>
                        <div class="col-md-4">
                            <label for="EditHoraE" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="EditHoraE" name="HoraE" required>
                        </div>
                        <div class="col-md-12">
                            <label for="EditUbicacionE" class="form-label">Ubicaci&oacute;n</label>
                            <input type="text" class="form-control" id="EditUbicacionE" name="UbicacionE" required>
                        </div>
                        <div class="col-md-12">
                            <label for="EditTelefono" class="form-label">Tel&eacute;fono de contacto (opcional)</label>
                            <input type="tel" class="form-control" id="EditTelefono" name="Telefono" maxlength="15" placeholder="Ej: 2481234567">
                        </div>
                        <div class="col-md-12">
                            <label for="EditRutaImagenE" class="form-label">Imagen del Evento (Opcional)</label>
                            <input type="file" class="form-control" id="EditRutaImagenE" name="RutaImagenE" accept="image/*">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top flex-shrink-0">
                <button type="submit" class="btn btn-primary" form="formEditarEvento">Actualizar Evento</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal VER DETALLES DE EVENTO -->
<div class="modal fade" id="modalDetallesEvento" tabindex="-1" aria-labelledby="modalDetallesEventoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-2">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="contenidoDetallesEvento">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>
