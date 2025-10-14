

document.addEventListener("DOMContentLoaded", () => {
    cargarCategorias();
    listarEventosEnTarjetas();

    // Asignar el evento de envío al formulario de AGREGAR
    document.getElementById('formEvento').addEventListener('submit', (e) => {
        e.preventDefault();
        agregarEvento();
    });

    // Asignar el evento de envío al formulario de EDITAR
    document.getElementById('formEditar').addEventListener('submit', (e) => {
        e.preventDefault();
        editarEvento();
    });

    // Delegación de eventos para los botones de editar y eliminar
    const contenedor = document.getElementById('contenedor');
    contenedor.addEventListener('click', (e) => {
        if (e.target.matches('.btn-edit, .btn-edit *')) {
            const button = e.target.closest('.btn-edit');
            const id = button.dataset.id;
            abrirModalEditar(id);
        }
        if (e.target.matches('.btn-delete, .btn-delete *')) {
            const button = e.target.closest('.btn-delete');
            const id = button.dataset.id;
            eliminarEvento(id);
        }
    });
});

/**
 * Carga las categorías en los selectores de los modales.
 */
function cargarCategorias() {
    const formData = new FormData();
    formData.append("ope", "CARGAR_CATEGORIAS");

    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (!data.success) { return; }
        const selectAgregar = document.getElementById("ID_Categoria");
        const selectEditar = document.getElementById("EditID_Categoria");
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
function listarEventosEnTarjetas() {
    const formData = new FormData();
    formData.append("ope", "LISTAR");
    
    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        const contenedor = document.getElementById("contenedor");
        contenedor.innerHTML = "";

        if (!data.success || !data.lista || data.lista.length === 0) {
            contenedor.innerHTML = "<p class='text-center text-secondary'>No hay eventos registrados.</p>";
            return;
        }

        data.lista.forEach(evento => {
            // **CAMBIO IMPORTANTE**: Añadimos data-id a los botones
            const tarjeta = `
                <div class="promo-card">
                    <div class="promo-card-image">
                        <img src="${evento.RutaImagenE ? '../imagenes/' + evento.RutaImagenE : '../assets/img/default-event.png'}" alt="${evento.TituloE}">
                    </div>
                    <div class="promo-card-content">
                        <span class="badge bg-secondary mb-2">${evento.NombreCategoria}</span>
                        <h5 class="promo-card-title">${evento.TituloE}</h5>
                        <p class="promo-card-description">${evento.DescripcionE}</p>
                        <p class="promo-card-info">
                            <strong>📅 Fecha:</strong> ${evento.FechaE}<br>
                            <strong>⏰ Hora:</strong> ${evento.HoraE}<br>
                            <strong>📍 Lugar:</strong> ${evento.UbicacionE}<br>
                            <strong>💰 Precio:</strong> ${evento.PrecioE || 'Gratis'}
                        </p>
                        <div class="promo-card-actions">
                            <button class="btn-edit" data-id="${evento.ID_Evento}">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                            <button class="btn-delete" data-id="${evento.ID_Evento}">
                                <i class="bi bi-trash3-fill"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
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
 * Envía los datos del formulario para agregar un nuevo evento.
 */
function agregarEvento() {
    const form = document.getElementById("formEvento");
    const formData = new FormData(form);
    formData.append("ope", "AGREGAR");

    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ Evento agregado correctamente");
            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalEvento')).hide();
            listarEventosEnTarjetas();
        } else {
            alert("❌ " + (data.message || 'No se pudo agregar el evento.'));
        }
    });
}

/**
 * Obtiene los datos de un evento y abre el modal de edición.
 */
function abrirModalEditar(id) {
    const formData = new FormData();
    formData.append("ope", "OBTENER");
    formData.append("ID_Evento", id);

    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert("Error: No se encontró el evento.");
            return;
        }
        
        const evento = data.evento;
        // **CORRECCIÓN**: Asegúrate de que los IDs del formulario de edición son correctos
        document.getElementById("ID_Evento_Editar").value = evento.ID_Evento; // Campo oculto para el ID
        document.getElementById("EditTituloE").value = evento.TituloE;
        document.getElementById("EditDescripcionE").value = evento.DescripcionE;
        document.getElementById("EditPrecioE").value = evento.PrecioE;
        document.getElementById("EditFechaE").value = evento.FechaE;
        document.getElementById("EditHoraE").value = evento.HoraE;
        document.getElementById("EditUbicacionE").value = evento.UbicacionE;
        document.getElementById("EditID_Categoria").value = evento.ID_Categoria;

        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    });
}

/**
 * Envía los datos actualizados de un evento.
 */
function editarEvento() {
    const form = document.getElementById("formEditar");
    const formData = new FormData(form);
    formData.append("ope", "EDITAR");
    // **CORRECCIÓN**: El ID se obtiene del campo oculto correcto
    formData.append("ID_Evento", document.getElementById("ID_Evento_Editar").value);


    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ Evento actualizado correctamente");
            bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
            listarEventosEnTarjetas();
        } else {
            alert("❌ No se pudo actualizar el evento.");
        }
    });
}

/**
 * Elimina un evento previa confirmación.
 */
function eliminarEvento(id) {
    if (!confirm("¿Estás seguro de que deseas eliminar este evento? Esta acción no se puede deshacer.")) {
        return;
    }

    const formData = new FormData();
    formData.append("ope", "ELIMINAR");
    formData.append("ID_Evento", id);

    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("🗑️ Evento eliminado correctamente");
            listarEventosEnTarjetas();
        } else {
            alert("❌ No se pudo eliminar el evento.");
        }
    });
}