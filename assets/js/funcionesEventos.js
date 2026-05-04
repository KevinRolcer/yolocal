// Expose functions to window
window.listarEventosEnTarjetas = listarEventosEnTarjetas;
window.filtrarEventos = (val) => {
    listarEventosEnTarjetas(val);
};

function iniciarModuloEventos() {
    cargarCategorias();
    listarEventosEnTarjetas();

    // Form AGREGAR
    const formEv = document.getElementById('formEvento');
    if (formEv) {
        formEv.addEventListener('submit', (e) => {
            e.preventDefault();
            agregarEvento();
        });
    }

    // Form EDITAR
    const formEdEv = document.getElementById('formEditarEvento');
    if (formEdEv) {
        formEdEv.addEventListener('submit', (e) => {
            e.preventDefault();
            editarEvento();
        });
    }

    // Delegation
    const contenedorEv = document.getElementById('contenedorEventos');
    if (contenedorEv) {
        contenedorEv.addEventListener('click', (e) => {
            const btnEdit = e.target.closest('.btn-edit');
            const btnDelete = e.target.closest('.btn-delete');
            const linkDetalles = e.target.closest('.ev-action-link');
            
            if (btnEdit) abrirModalEditarEvento(btnEdit.dataset.id);
            if (btnDelete) eliminarEvento(btnDelete.dataset.id);
            if (linkDetalles) abrirModalDetallesEvento(linkDetalles.dataset.id);
        });
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", iniciarModuloEventos, { once: true });
} else {
    iniciarModuloEventos();
}

window.cargarCategorias = cargarCategorias;

/**
 * Carga las categorías en los selectores de los modales.
 */
function cargarCategorias() {
    const selectAgregar = document.getElementById("ID_Categoria");
    const selectEditar = document.getElementById("EditID_Categoria");
    
    if (!selectAgregar || !selectEditar) return; // Guard clause

    const formData = new FormData();
    formData.append("ope", "CARGAR_CATEGORIAS");

    fetch("controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (!data.success) { return; }
        selectAgregar.innerHTML = '<option value="">Seleccione una categoría...</option>';
        selectEditar.innerHTML = '<option value="">Seleccione una categoría...</option>';
        data.lista.forEach(cat => {
            const option = `<option value="${cat.ID_Categoria}">${cat.Descripcion}</option>`;
            selectAgregar.innerHTML += option;
            selectEditar.innerHTML += option;
        });
    })
    .catch(error => console.error("Error al cargar categorías:", error));
}

/**
 * Obtiene y muestra los eventos como tarjetas.
 */
function listarEventosEnTarjetas(filtro = '') {
    const formData = new FormData();
    formData.append("ope", "LISTAR");
    if (filtro) formData.append("buscar", filtro);
    
    fetch("controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        const contenedor = document.getElementById("contenedorEventos");
        if (!contenedor) return;
        contenedor.innerHTML = "";

        if (!data.success || !data.lista || data.lista.length === 0) {
            contenedor.innerHTML = "<p class='text-center text-secondary'>No hay eventos registrados.</p>";
            return;
        }

        data.lista.forEach(evento => {
            const precio = evento.PrecioE ? `$${evento.PrecioE}` : 'Gratis';
            const imagen = evento.RutaImagenE ? `imagenes/${evento.RutaImagenE}` : 'assets/img/banner-yolocal.png';
            const tarjeta = `
                <article class="ev-stylized-card">
                    <div class="ev-image-wrapper">
                        <img src="${imagen}" alt="${evento.TituloE}">
                        <div class="ev-price-badge">${precio}</div>
                        <div class="ev-footer-promo">Evento Especial • YoLocal</div>
                    </div>
                    
                    <div class="ev-content">
                        <div class="ev-header-row">
                            <div>
                                <span class="ev-kicker">${evento.NombreCategoria}</span>
                                <h3 class="ev-title">${evento.TituloE}</h3>
                            </div>
                            <span class="ev-action-link" data-id="${evento.ID_Evento}">Detalles <i class="ri-arrow-right-up-line"></i></span>
                        </div>
                        
                        <div class="ev-info-grid">
                            <div class="ev-info-item">
                                <i class="ri-calendar-event-line"></i>
                                <span>${evento.FechaE}</span>
                            </div>
                            <div class="ev-info-item">
                                <i class="ri-map-pin-line"></i>
                                <span>${evento.UbicacionE}</span>
                            </div>
                        </div>
                        <p class="ev-description">${evento.DescripcionE || ''}</p>
                    </div>

                    <div class="ev-admin-actions">
                        <button class="btn-ev-admin btn-edit" data-id="${evento.ID_Evento}">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button class="btn-ev-admin btn-delete" data-id="${evento.ID_Evento}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </article>
            `;
            contenedor.innerHTML += tarjeta;
        });
    })
    .catch(error => {
        console.error("Error al listar eventos:", error);
        contenedor.innerHTML = "<p class='text-center text-danger'>Ocurrió un error al cargar los eventos.</p>";
    });
}

/**
 * Envía los datos del formulario para agregar un nuevo evento con SweetAlert.
 */
function agregarEvento() {
    const form = document.getElementById("formEvento");
    const formData = new FormData(form);
    formData.append("ope", "AGREGAR");

    fetch("controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
            Swal.fire({
                icon: 'success',
                title: '¡Evento Guardado!',
                text: 'El nuevo evento ha sido registrado correctamente.',
                timer: 2000,
                showConfirmButton: false
            });
            listarEventosEnTarjetas();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo agregar el evento.'
            });
        }
    })
    .catch(error => {
        console.error("Error en agregarEvento:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo comunicar con el servidor.'
        });
    });
}

/**
 * Obtiene los datos de un evento y abre el modal de edición.
 */
function abrirModalEditarEvento(id) {
    const formData = new FormData();
    formData.append("ope", "OBTENER");
    formData.append("ID_Evento", id);

    fetch("controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            Swal.fire("Error", "No se encontró el evento.", "error");
            return;
        }
        
        const evento = data.evento;
        document.getElementById("ID_Evento_Editar").value = evento.ID_Evento;
        document.getElementById("EditTituloE").value = evento.TituloE;
        document.getElementById("EditDescripcionE").value = evento.DescripcionE;
        document.getElementById("EditPrecioE").value = evento.PrecioE;
        document.getElementById("EditFechaE").value = evento.FechaE;
        document.getElementById("EditHoraE").value = evento.HoraE;
        document.getElementById("EditUbicacionE").value = evento.UbicacionE;
        document.getElementById("EditID_Categoria").value = evento.ID_Categoria;

        new bootstrap.Modal(document.getElementById('modalEditarEvento')).show();
    });
}

/**
 * Envía los datos actualizados de un evento con SweetAlert.
 */
function editarEvento() {
    const form = document.getElementById("formEditarEvento");
    const formData = new FormData(form);
    formData.append("ope", "EDITAR");
    formData.append("ID_Evento", document.getElementById("ID_Evento_Editar").value);

    fetch("controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalEditarEvento')).hide();
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'El evento ha sido modificado correctamente.',
                timer: 2000,
                showConfirmButton: false
            });
            listarEventosEnTarjetas();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo actualizar el evento.'
            });
        }
    })
    .catch(error => {
        console.error("Error en editarEvento:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo comunicar con el servidor.'
        });
    });
}

/**
 * Pide confirmación con SweetAlert para eliminar un evento.
 */
function eliminarEvento(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, ¡eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Si el usuario confirma, se elimina
            const formData = new FormData();
            formData.append("ope", "ELIMINAR");
            formData.append("ID_Evento", id);

            fetch("controladores/controladorEventos.php", { method: "POST", body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        '¡Eliminado!',
                        'El evento ha sido eliminado.',
                        'success'
                    );
                    listarEventosEnTarjetas(); // Actualiza la lista
                } else {
                    Swal.fire(
                        'Error',
                        data.message || 'No se pudo eliminar el evento.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error("Error en eliminarEvento:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo comunicar con el servidor.'
                });
            });
        }
    });
}
