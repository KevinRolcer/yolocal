/**
 * Abre un modal con los detalles completos del evento
 */
function abrirModalDetallesEvento(id) {
    const formData = new FormData();
    formData.append("ope", "OBTENER");
    formData.append("ID_Evento", id);

    fetch(
        typeof window.YL_controladorEventosUrl === "function"
            ? window.YL_controladorEventosUrl()
            : "/controladores/controladorEventos.php",
        { method: "POST", body: formData }
    )
    .then(response => response.json())
    .then(data => {
        if (!data.success || !data.evento) {
            Swal.fire('Error', 'No se pudo cargar el evento', 'error');
            return;
        }

        const ev = data.evento;
        const imagenUrl = ev.RutaImagenE ? 'imagenes/' + ev.RutaImagenE : 'assets/img/default-event.png';
        const horaFormato = ev.HoraE ? ev.HoraE.substring(0, 5) : 'No especificada';

        const contenido = `
            <div class="modal-detalles-evento">
                <!-- Header con imagen -->
                <div class="detalles-evento-header">
                    <img src="${imagenUrl}" alt="${ev.TituloE}" class="detalles-evento-imagen">
                    <div class="detalles-evento-precio">
                        <span>${ev.PrecioE ? '$' + ev.PrecioE : 'Gratis'}</span>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="detalles-evento-contenido">
                    <!-- Título y Categoría -->
                    <div class="detalles-evento-titulo-seccion">
                        <h2 class="detalles-evento-titulo">${ev.TituloE}</h2>
                        <span class="detalles-evento-categoria">${ev.NombreCategoria || 'Sin categoría'}</span>
                    </div>

                    <!-- Info Grid -->
                    <div class="detalles-evento-info-grid">
                        <div class="detalles-evento-info-item">
                            <div class="detalles-evento-info-icono">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <div>
                                <p class="detalles-evento-info-label">Fecha</p>
                                <p class="detalles-evento-info-valor">${ev.FechaE}</p>
                            </div>
                        </div>

                        <div class="detalles-evento-info-item">
                            <div class="detalles-evento-info-icono">
                                <i class="ri-time-line"></i>
                            </div>
                            <div>
                                <p class="detalles-evento-info-label">Hora</p>
                                <p class="detalles-evento-info-valor">${horaFormato}</p>
                            </div>
                        </div>

                        <div class="detalles-evento-info-item">
                            <div class="detalles-evento-info-icono">
                                <i class="ri-map-pin-2-line"></i>
                            </div>
                            <div>
                                <p class="detalles-evento-info-label">Ubicación</p>
                                <p class="detalles-evento-info-valor">${ev.UbicacionE}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="detalles-evento-descripcion-seccion">
                        <h3 class="detalles-evento-seccion-titulo">Acerca del evento</h3>
                        <p class="detalles-evento-descripcion">${ev.DescripcionE}</p>
                    </div>

                    <!-- Acciones para admin -->
                    ${window.usuarioTipo === 'admin' ? `
                    <div class="detalles-evento-acciones">
                        <button class="btn btn-primary" data-bs-dismiss="modal" onclick="abrirModalEditarEvento('${ev.ID_Evento}')">
                            <i class="ri-pencil-line me-1"></i> Editar
                        </button>
                        <button class="btn btn-outline-danger" onclick="eliminarEvento('${ev.ID_Evento}')">
                            <i class="ri-delete-bin-line me-1"></i> Eliminar
                        </button>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;

        const contenedorDetalles = document.getElementById('contenidoDetallesEvento');
        if (contenedorDetalles) {
            contenedorDetalles.innerHTML = contenido;
        }

        // Abrir modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetallesEvento'));
        modal.show();
    })
    .catch(error => {
        console.error("Error al cargar detalles del evento:", error);
        Swal.fire('Error', 'Ocurrió un error al cargar el evento', 'error');
    });
}
