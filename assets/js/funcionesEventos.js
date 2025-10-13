document.addEventListener("DOMContentLoaded", () => {
    listarEventos();
});

// LISTAR EVENTOS
function listarEventos(pagina = 1) {
    const formData = new FormData();
    formData.append("ope", "LISTAR");
    formData.append("pagina", pagina);
    formData.append("registrosPorPagina", 20);

    fetch("../controlador/controladorEventos.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const tabla = document.getElementById("tablaEventos");
            tabla.innerHTML = "";

            if (!data.success || data.lista.length === 0) {
                tabla.innerHTML = "<tr><td colspan='10'>No hay eventos registrados</td></tr>";
                return;
            }

            data.lista.forEach(evento => {
                const fila = `
                    <tr>
                        <td>${evento.ID_Evento}</td>
                        <td>${evento.TituloE}</td>
                        <td>${evento.DescripcionE}</td>
                        <td>${evento.PrecioE ?? ""}</td>
                        <td>${evento.FechaE ?? ""}</td>
                        <td>${evento.HoraE ?? ""}</td>
                        <td>${evento.UbicacionE ?? ""}</td>
                        <td>${evento.RutaImagenE ? `<img src="../imagenes/${evento.RutaImagenE}" width="80">` : "Sin imagen"}</td>
                        <td>${evento.ID_Categoria}</td>
                        <td>
                            <button onclick="obtenerEvento(${evento.ID_Evento})">✏️</button>
                            <button onclick="eliminarEvento(${evento.ID_Evento})">🗑️</button>
                        </td>
                    </tr>
                `;
                tabla.innerHTML += fila;
            });
        })
        .catch(error => console.error("Error al listar eventos:", error));
}

//AGREGAR EVENTO
function agregarEvento() {
    const form = document.getElementById("formEvento");
    const formData = new FormData(form);
    formData.append("ope", "AGREGAR");

    fetch("../controlador/controladorEventos.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("✅ Evento agregado correctamente");
                form.reset();
                listarEventos();
            } else {
                alert("❌ No se pudo agregar el evento");
            }
        })
        .catch(error => console.error("Error al agregar:", error));
}

// OBTENER EVENTO PARA EDITAR
function obtenerEvento(id) {
    const formData = new FormData();
    formData.append("ope", "OBTENER");
    formData.append("ID_Evento", id);

    fetch("../controlador/controladorEventos.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert("No se encontró el evento");
                return;
            }

            const evento = data.evento;
            document.getElementById("ID_Evento").value = evento.ID_Evento;
            document.getElementById("TituloE").value = evento.TituloE;
            document.getElementById("DescripcionE").value = evento.DescripcionE;
            document.getElementById("PrecioE").value = evento.PrecioE;
            document.getElementById("FechaE").value = evento.FechaE;
            document.getElementById("HoraE").value = evento.HoraE;
            document.getElementById("UbicacionE").value = evento.UbicacionE;
            document.getElementById("ID_Categoria").value = evento.ID_Categoria;

            document.getElementById("btnGuardar").style.display = "none";
            document.getElementById("btnActualizar").style.display = "inline-block";
        })
        .catch(error => console.error("Error al obtener evento:", error));
}

// EDITAR EVENTO
function editarEvento() {
    const form = document.getElementById("formEvento");
    const formData = new FormData(form);
    formData.append("ope", "EDITAR");

    fetch("../controlador/controladorEventos.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("✅ Evento actualizado correctamente");
                form.reset();
                listarEventos();

                document.getElementById("btnGuardar").style.display = "inline-block";
                document.getElementById("btnActualizar").style.display = "none";
            } else {
                alert("❌ No se pudo actualizar el evento");
            }
        })
        .catch(error => console.error("Error al editar evento:", error));
}

// ELIMINAR EVENTO
function eliminarEvento(id) {
    if (!confirm("¿Seguro que deseas eliminar este evento?")) return;

    const formData = new FormData();
    formData.append("ope", "ELIMINAR");
    formData.append("ID_Evento", id);

    fetch("../controlador/controladorEventos.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("🗑️ Evento eliminado correctamente");
                listarEventos();
            } else {
                alert("❌ No se pudo eliminar el evento");
            }
        })
        .catch(error => console.error("Error al eliminar evento:", error));
}
