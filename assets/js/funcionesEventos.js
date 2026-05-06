function fetchControladorEventosJson(formData) {
    var url =
        typeof window.YL_controladorEventosUrl === "function"
            ? window.YL_controladorEventosUrl()
            : "/controladores/controladorEventos.php";
    return fetch(url, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
    }).then(async (response) => {
        const textRaw = await response.text();
        const text = textRaw.replace(/^\uFEFF/, "").trim();
        if (!response.ok) {
            console.error(
                "[eventos] HTTP",
                response.status,
                response.statusText,
                "URL:",
                url,
                "Cuerpo:",
                text.substring(0, 800)
            );
            throw new Error(
                `HTTP ${response.status} al contactar el servidor. Revisa la URL o la sesión (${url}).`
            );
        }
        if (!text.length) {
            console.error("[eventos] Respuesta vacía. URL:", url);
            throw new Error(
                "El servidor devolvió respuesta vacía. Revisa errores PHP del controlador de eventos."
            );
        }
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error(
                "[eventos] Respuesta no JSON. URL:",
                url,
                "\n",
                text.substring(0, 1200)
            );
            const plain = text.replace(/<[^>]+>/g, " ").replace(/\s+/g, " ").trim();
            throw new Error(
                plain.slice(0, 240) ||
                    "El servidor no devolvió JSON válido. Comprueba la ruta y la consola de red."
            );
        }
    });
}

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

    const formEdEv =
        document.getElementById("formEditarEvento") ||
        document.getElementById("formEditar");
    if (formEdEv) {
        formEdEv.addEventListener("submit", (e) => {
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
    if (!selectAgregar && !selectEditar) return;

    const formData = new FormData();
    formData.append("ope", "CARGAR_CATEGORIAS");

    fetchControladorEventosJson(formData)
    .then(data => {
        if (!data.success) { return; }
        const optBase = '<option value="">Seleccione una categoría...</option>';
        if (selectAgregar) selectAgregar.innerHTML = optBase;
        if (selectEditar) selectEditar.innerHTML = optBase;
        data.lista.forEach(cat => {
            const option = `<option value="${cat.ID_Categoria}">${cat.Descripcion}</option>`;
            if (selectAgregar) selectAgregar.innerHTML += option;
            if (selectEditar) selectEditar.innerHTML += option;
        });
    })
    .catch(error => console.error("Error al cargar categorías:", error));
}

/**
 * Obtiene y muestra los eventos como tarjetas (CON CAMPO TELEFONO).
 */
function listarEventosEnTarjetas(filtro = '') {
    const formData = new FormData();
    formData.append("ope", "LISTAR");
    if (filtro) formData.append("buscar", filtro);
    fetchControladorEventosJson(formData)
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
            const telefonoInfo = evento.Telefono ? `<strong>📞 Teléfono:</strong> ${evento.Telefono}<br>` : '';
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
                        <p class="ev-telefono">${telefonoInfo}</p>
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
            const contenedorErr = document.getElementById("contenedorEventos");
            if (contenedorErr) {
                contenedorErr.innerHTML = "<p class='text-center text-danger'>Ocurrió un error al cargar los eventos.</p>";
            }
        });
}
function setInputValueIfPresent(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    el.value = value != null && value !== undefined ? String(value) : "";
}

function getModalEditarEventoEl() {
    return (
        document.getElementById("modalEditarEvento") ||
        document.getElementById("modalEditar")
    );
}

function abrirModalEditarEvento(id) {
    const formData = new FormData();
    formData.append("ope", "OBTENER");
    formData.append("ID_Evento", id);

    fetchControladorEventosJson(formData)
    .then(data => {
        if (!data.success) {
            Swal.fire("Error", "No se encontró el evento.", "error");
            return;
        }

        const evento = data.evento;
        setInputValueIfPresent("ID_Evento_Editar", evento.ID_Evento);
        setInputValueIfPresent("EditTituloE", evento.TituloE);
        setInputValueIfPresent("EditDescripcionE", evento.DescripcionE);
        setInputValueIfPresent("EditPrecioE", evento.PrecioE);
        setInputValueIfPresent("EditFechaE", evento.FechaE);
        setInputValueIfPresent("EditHoraE", evento.HoraE);
        setInputValueIfPresent("EditUbicacionE", evento.UbicacionE);
        setInputValueIfPresent("EditTelefono", evento.Telefono);
        setInputValueIfPresent("EditID_Categoria", evento.ID_Categoria);

        const modalEl = getModalEditarEventoEl();
        if (!modalEl) {
            Swal.fire("Error", "No se encontró el modal de edición en esta página.", "error");
            return;
        }
        new bootstrap.Modal(modalEl).show();
    });
}

/**
 * Envía los datos actualizados de un evento con SweetAlert.
 */
function editarEvento() {
    const form =
        document.getElementById("formEditarEvento") ||
        document.getElementById("formEditar");
    if (!form) {
        console.error("No se encontró el formulario de edición (formEditarEvento / formEditar).");
        return;
    }
    const formData = new FormData(form);
    formData.append("ope", "EDITAR");

    fetchControladorEventosJson(formData)
    .then(data => {
        if (data.success) {
            const modalEl = getModalEditarEventoEl();
            const inst = modalEl && bootstrap.Modal.getInstance(modalEl);
            if (inst) inst.hide();
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
                text: data.message || 'No se pudo actualizar el evento.' // Usa el mensaje del controlador
            });
        }
    })
    .catch(error => {
        console.error("Error en editarEvento:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'No se pudo comunicar con el servidor.'
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

            fetchControladorEventosJson(formData)
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
                        data.message || 'No se pudo eliminar el evento.', // Usa el mensaje del controlador
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
