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
 * Obtiene y muestra los eventos como tarjetas (CON CAMPO TELEFONO).
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

            // Lógica condicional para mostrar el teléfono solo si no está vacío
            const telefonoInfo = evento.Telefono ? `<strong>📞 Teléfono:</strong> ${evento.Telefono}<br>` : ''; // Usa evento.Telefono

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
                            ${telefonoInfo} <strong>💰 Precio:</strong> ${evento.PrecioE || 'Gratis'}
                        </p>
                        <div class="promo-card-actions">
                            <button class="btn-edit" data-id="${evento.ID_Evento}" title="Editar Evento">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn-delete" data-id="${evento.ID_Evento}" title="Eliminar Evento">
                                <i class="bi bi-trash3-fill"></i>
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
 * Envía los datos del formulario para agregar un nuevo evento con SweetAlert.
 */
function agregarEvento() {
    const form = document.getElementById("formEvento");
    const formData = new FormData(form); // Recoge automáticamente el campo Telefono
    formData.append("ope", "AGREGAR");

    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
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
            form.reset();
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
 * Obtiene los datos de un evento y abre el modal de edición (CON CAMPO TELEFONO).
 */
function abrirModalEditar(id) {
    const formData = new FormData();
    formData.append("ope", "OBTENER");
    formData.append("ID_Evento", id);

    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
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
        document.getElementById("EditTelefono").value = evento.Telefono; // <-- Pone el valor del teléfono en el input
        document.getElementById("EditID_Categoria").value = evento.ID_Categoria;

        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    });
}

/**
 * Envía los datos actualizados de un evento con SweetAlert.
 */
function editarEvento() {
    const form = document.getElementById("formEditar");
    const formData = new FormData(form); // Recoge automáticamente el campo Telefono
    formData.append("ope", "EDITAR");
    formData.append("ID_Evento", document.getElementById("ID_Evento_Editar").value);

    fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
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

            fetch("../controladores/controladorEventos.php", { method: "POST", body: formData })
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