import { validaNumero } from "./validaciones.js?v=4.9";
document.addEventListener("DOMContentLoaded", () => {
    // Asignar la fecha actual
    const inputFecha = document.getElementById("fecha");
    actualizarContadores();
    function establecerFechaActual() {
        const hoy = new Date();
        hoy.setMinutes(hoy.getMinutes() - hoy.getTimezoneOffset());

        const fechaFormateada = hoy.toISOString().split('T')[0];
        document.getElementById("fecha").value = fechaFormateada;
    }

    const idMiembro = document.getElementById('idMiembro');
    const precio = document.getElementById('precio');

    function limitarEntrada(event) {
        const input = event.target;
        const maxLength = 4;

        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value.length > maxLength) {
            input.value = input.value.slice(0, maxLength);
        }
    }

    // Agregar los eventos a los inputs
    idMiembro.addEventListener('input', limitarEntrada);
    precio.addEventListener('input', limitarEntrada);

    const modal = document.getElementById("miModal");
    modal.addEventListener("show.bs.modal", () => {
        cargarMembresias();
        establecerFechaActual();
       
        
    });


    listarAccesos();

    // Evento para el formulario de agregar acceso
    const formAgregar = document.querySelector("#formAgregarAcceso");
    if (formAgregar) {
        formAgregar.addEventListener("submit", (event) => {
            event.preventDefault();
            let errores = 0;
            let Precio = document.getElementById("precio");
            if (!validaNumero(Precio)) {
                errores++; // Sumar error si la validación falla
                return;  // Opcional: para evitar que siga ejecutándose el código
            }
            if (errores == 0) {
                agregarAcceso();
            }
        });
    }
    const fechayHora = () => {
        const fecha = new Date();

        const opcionesMes = { month: 'numeric', year: 'numeric' };
        let mesAño = fecha.toLocaleString("es-ES", opcionesMes);
        mesAño = mesAño.charAt(0).toUpperCase() + mesAño.slice(1); 

        const opcionesDia = { weekday: 'long', day: 'numeric' };
        let dia = fecha.toLocaleString("es-ES", opcionesDia);
        dia = dia.charAt(0).toUpperCase() + dia.slice(1);
        const opcionesHora = { hour: '2-digit', minute: '2-digit', hour12: true };
        const hora = fecha.toLocaleString("es-ES", opcionesHora);

        //document.getElementById("MesAct").textContent = mesAño;
        document.getElementById("DiaAct").textContent = dia+"/"+mesAño;
       // document.getElementById("horaAct").textContent = hora;
    };
    setInterval(fechayHora, 1000);
    fechayHora();
    // Evento para el botón de guardar acceso en el modal
    const btnGuardarAcceso = document.querySelector("#btnGuardarAcceso");
    if (btnGuardarAcceso) {
        btnGuardarAcceso.addEventListener("click", () => {
            event.preventDefault();
            let errores = 0;
            let Precio = document.getElementById("precio");
            if (!validaNumero(Precio)) {
                errores++; // Sumar error si la validación falla
                return;  // Opcional: para evitar que siga ejecutándose el código
            }
            if (errores == 0) {
                agregarAcceso();
            }
        });
    }


    // Buscar miembro en el modal
    const idMiembroInput = document.querySelector("#idMiembro");
    const nombreMiembroInput = document.querySelector("#nombreMiembro");

    idMiembroInput.addEventListener("input", () => {
        const idMiembro = idMiembroInput.value.trim();
        if (idMiembro === "") {
            nombreMiembroInput.value = "";
            return;
        }

        fetch('controlador/controladorAcceso.php', {
            method: 'POST',
            body: new URLSearchParams({ "ope": "BUSCAR_MIEMBRO", "ID_Miembro": idMiembro })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    nombreMiembroInput.value = `${data.miembro.Nombre} ${data.miembro.ApellidoP} ${data.miembro.ApellidoM}`;
                } else {
                    nombreMiembroInput.value = "No encontrado";
                }
            })
            .catch(error => {
                console.error("Error al buscar miembro:", error);
                nombreMiembroInput.value = "Error";
            });
    });

    // Buscar miembros con el input general de búsqueda
    const searchInput = document.querySelector("#searchInput");
    searchInput.addEventListener("input", () => {
        buscarMiembro(searchInput.value);
    });
});
//Limpiar filtros
document.querySelectorAll(".filter .close").forEach(button => {
    button.addEventListener("click", function (event) {
        event.stopPropagation();

        let filter = this.parentElement;
        filter.classList.remove("active");

        let inputs = filter.querySelectorAll("input, select");
        inputs.forEach(input => {
            input.classList.add("hidden");
            input.value = "";
        });

        document.getElementById("fechaInicio").value = "";
        document.getElementById("fechaFin").value = "";

        listarGastos();
    });
});
function actualizarCosto() {
    
    
    const costoInput = document.getElementById("precio");

    const precioMembresia = selectMembresia.selectedOptions[0]?.getAttribute("data-precio") || 0;
    

    // Calcular costo total
    costoInput.value = (precioMembresia);
}
// Función para listar accesos
function listarAccesos() {
    fetch('controlador/controladorAcceso.php', {
        method: 'POST',
        body: new URLSearchParams({ "ope": "LISTAR_ACCESOS" })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.querySelector("#tablaAccesos tbody");
                tbody.innerHTML = "";
                data.accesos.forEach(acceso => {
                    tbody.innerHTML += `
                <tr>
                    <td>${acceso.ID_Miembro}</td>
                    <td>${acceso.Nombre} ${acceso.ApellidoP} ${acceso.ApellidoM}</td>
                    <td>${acceso.Hora}</td>
                    <td>${acceso.Precio}</td>
                    <td>${acceso.Tipo}</td>
                   

                </tr>
                `;
                });
            } else {
                console.error("Error al listar accesos.");
            }
        })
        .catch(error => console.error("Error al listar accesos:", error));
}

// Función para agregar acceso
function agregarAcceso() {
    const form = document.querySelector("#formAgregarAcceso");
    const inputFecha = document.getElementById("fecha");
    const datos = new FormData(form);

    // Validar que todos los campos estén llenos
    let camposVacios = false;
    for (let valor of datos.values()) {
        if (valor.trim() === "") {
            camposVacios = true;
            break;
        }
    }

    if (camposVacios) {
        Swal.fire({
            title: "Campos incompletos",
            text: "Por favor, rellena todos los campos antes de continuar.",
            icon: "warning",
            confirmButtonColor: "#108d08"
        });
        return;
    }

    Swal.fire({
        title: "¿Los datos son correctos?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#108d08",
        cancelButtonColor: "#8d2e27",
        confirmButtonText: "Sí, agregar visita"
    }).then((result) => {
        if (result.isConfirmed) {
            datos.append("ope", "AGREGAR_ACCESO");

            fetch('controlador/controladorAcceso.php', {
                method: 'POST',
                body: datos
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Éxito", "Visita agregada correctamente", "success");
                        form.reset();
                        document.querySelector("#miModal .btn-close").click();
                        listarAccesos();
                        actualizarContadores();
                    } else {
                        Swal.fire("Error", "No se pudo agregar el acceso", "error");
                    }
                })
                .catch(error => console.error("Error al agregar acceso:", error));
        }
    });
}
function actualizarContadores() {
    fetch('controlador/controladorAcceso.php', {
        method: 'POST',
        body: new URLSearchParams({ "ope": "CONTAR_ACCESOS" })
    })
        .then(response => response.json())
        .then(data => {
            let visitas = 0;
            let membresias = 0;

            data.forEach(item => {
                if (item.Tipo === "Visita") {
                    visitas = item.cantidad;
                } else if (item.Tipo === "Miembro") {
                    membresias = item.cantidad;
                }
            });

            document.querySelector(".contadores .card:nth-child(1) .numbers").textContent = visitas;
            document.querySelector(".contadores .card:nth-child(2) .numbers").textContent = membresias;
        })
        .catch(error => console.error("Error al obtener conteo:", error));
}
function registrarAcceso() {
    const ID_Miembro = document.getElementById("miembroID").value;
    const Precio = document.getElementById("precio").value;
    const Tipo = document.getElementById("tipo").value;

    if (!ID_Miembro) {
        Swal.fire("Error", "No se encontró el ID del miembro", "error");
        return;
    }

    let datos = new FormData();
    datos.append("ope", "AGREGAR_ACCESOM");
    datos.append("ID_Miembro", ID_Miembro);
    datos.append("Precio", Precio);
    datos.append("Tipo", Tipo);

    fetch('controlador/controladorAcceso.php', {
        method: 'POST',
        body: datos
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Éxito", "Acceso registrado correctamente", "success");
                listarAccesos();
                actualizarContadores();
            } else {
                Swal.fire("Error", "No se pudo registrar el acceso", "error");
            }
        })
        .catch(error => console.error("Error al registrar acceso:", error));
}
function cargarMembresias() {
    fetch('controlador/controladorRelacionM.php', {
        method: 'POST',
        body: new URLSearchParams({ "ope": "OBTENERCLASESDIA" })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selectMembresias = document.getElementById("ID_Membresia");
                selectMembresias.innerHTML = "<option value=''>Seleccione una membresía</option>";

                data.membresias.forEach(membresia => {
                    const option = document.createElement("option");
                    option.value = membresia.ID_Membresia;
                    option.setAttribute("data-precio", membresia.Costo);
                    option.textContent = membresia.Tipo;
                    selectMembresias.appendChild(option);
                });

                selectMembresias.addEventListener("change", function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const precio = selectedOption.getAttribute("data-precio");
                    document.getElementById("precio").value = precio ? `${precio}` : "";
                });
            } else {
                Swal.fire("Error", "No se pudieron cargar las membresías", "error");
            }
        })
        .catch(error => {
            Swal.fire("Error", "No se pudo cargar la lista de membresías: " + error.message, "error");
        });
}
const searchInput = document.getElementById("searchInput");
const contenidoM = document.querySelector(".contenidoM");
const fotoM = document.querySelector(".fotoM");

searchInput.addEventListener("input", () => {
    if (searchInput.value.trim() === "") {
        contenidoM.innerHTML = "";
        fotoM.innerHTML = "";

        contenidoM.innerHTML = `<p>#ID de miembro</p>
                        <h3>Nombre</h2>
                            <p>Número: </p>
                            <div class="fechas">
                                <div class="fechaI">
                                    dd/mm/yyyy
                                </div>
                                <div class="fechaF">
                                    dd/mm/yyyy
                                </div>
                            </div>
                            <div class="estadoM">
                                Membresía Activa
                            </div>`;
    }
});
function buscarMiembro(id) {
    if (id.trim() === "") return;

    fetch('controlador/controladorAcceso.php', {
        method: 'POST',
        body: new URLSearchParams({ "ope": "BUSCAR_MIEMBROActivo", "ID_Miembro": id })
    })
        .then(response => response.json())
        .then(data => {
            const contenidoM = document.querySelector(".contenidoM");

            if (!data.miembro) {
             
                const fotoDiv = document.querySelector(".fotoM");
    fotoDiv.innerHTML = "";

                contenidoM.innerHTML = `
                <div class="error-container">
                    <h3>⚠ Membresía no activa</h3>
                    <p>¿Desea renovar?</p>
                    <br>
                    <a href="index.php?pag=relacion" class="btn-renovar">
                        Renovar Membresía
                    </a>
                </div>
            `;
                return;
            }

            const miembro = data.miembro;
            
            contenidoM.innerHTML = `
                <p>#${miembro.ID_Miembro}</p>
                <h3>${miembro.Nombre} ${miembro.ApellidoP} ${miembro.ApellidoM}</h3>
                <p>Teléfono: ${miembro.Telefono}</p>
                <p>Sexo: ${miembro.Sexo}</p>
                <div class="fechas">
                    <div class="fechaI">F.Inicio: ${miembro.FechaInicio}</div>
                    <div class="fechaF">F.Fin: ${miembro.FechaFin}</div>
                </div>
                <div class="estadoM">${data.success ? "Membresía Activa" : "<h3>Sin Membresía Activa</h3>"}</div>
                <input type="hidden" id="miembroID" value="${miembro.ID_Miembro}">
                <input type="hidden" id="precio" value="0">
                <input type="hidden" id="tipo" value="Miembro">
                ${data.success ? `<button id="btnRegistrarAcceso" class="btn btn-success">Registrar Acceso</button>` : ""}
            `;

            const fotoDiv = document.querySelector(".fotoM");
            if (miembro.Sexo === "F") {
                fotoDiv.innerHTML = '<img  width="130" height="130" src="../asset/images/m.gif"></img>';
            } else if (miembro.Sexo === "M") {
                fotoDiv.innerHTML = '<img  width="130" height="130" src="../asset/images/h.gif"></img>';
            } else {
                fotoDiv.innerHTML = '<i class="fas fa-user fa-4x" style="color: gray;"></i>'; // Por si acaso
            }

            if (data.success) {
                document.getElementById("btnRegistrarAcceso").addEventListener("click", registrarAcceso);
            }
        })
        .catch(error => console.error("Error al buscar miembro:", error));
}
