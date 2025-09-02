import {
  validaCorreo,
  validaLargo,
  validaRango,
  validaSoloLetras,
  validaContrasena,
} from "./validaciones.js?v=3.8.1";
document.addEventListener("DOMContentLoaded", () => {
  buscarMiembroModal();

  cargarMembresias();
  // agregar usuario

  const formUsuario = document.querySelector("#formAgregar");
  if (formUsuario) {
    formUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      let errores = 0;

      if (errores == 0) agregarUsuario();
    });
  }

  //  editar y eliminar
  const listaUsuarios = document.querySelector("#ListaMiembros");
  if (listaUsuarios) {
    listaUsuarios.addEventListener("click", (event) => {
      event.preventDefault();
      const target = event.target;

      if (target.classList.contains("btn-editar")) {
        cargarUsuario(target.dataset.id);
      } else if (target.classList.contains("btn-eliminar")) {
        eliminarUsuario(target.dataset.id);
      } else if (event.target.classList.contains("btn-crear-horario")) {
        let userId = event.target.dataset.id;
        document.querySelector("#ID_NegocioHorario").value = userId;
      }
    });
  }

  const formHorario = document.querySelector("#formHorario");

  if (formHorario) {
    formHorario.addEventListener("submit", (event) => {
      event.preventDefault();
      let errores = 0;

      if (errores === 0) {
        agregarHorario();
      } else {
        alert("Por favor completa todos los campos.");
      }
    });
  }
  const formEditarUsuario = document.querySelector("#formEditar");
  if (formEditarUsuario) {
    formEditarUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      let erroresE = 0;
      let nombreE = document.querySelector("#NombreEdit");

      if (!validaSoloLetras(nombreE)) erroresE++;

      if (erroresE == 0) editarUsuario();
    });
  }

  listarMiembros();
});

// Función para listar usuarios
let paginaActual = 1;
const registrosPorPagina = 10;

export function listarMiembros(filtros = {}) {
  let params = new URLSearchParams();
  params.append("ope", "LISTAUSUARIOS");
  params.append("pagina", paginaActual);
  params.append("registrosPorPagina", registrosPorPagina);

  if (filtros.ID_Miembro) params.append("id", filtros.ID_Miembro);
  if (filtros.Nombre) params.append("nombre", filtros.Nombre);
  if (filtros.Apellidos) params.append("apellidos", filtros.Apellidos);
  if (filtros.Telefono) params.append("telefono", filtros.Telefono);

  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        console.error("Error al cargar miembros:", data.msg);
        renderizarError("No se pudieron cargar los miembros.");
        return;
      }
      renderizarMiembros(data.lista);
      actualizarPaginacion(data.totalPaginas);
    })
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      renderizarError("Error al conectarse con el servidor.");
    });
}

function renderizarMiembros(lista) {
  const contenedor = document.querySelector("#ListaMiembros");
  contenedor.innerHTML = "";

  if (!lista || lista.length === 0) {
    contenedor.innerHTML = `
            <div class="no-results">
                <p>No se encuentra ningún miembro con los filtros aplicados.</p>
            </div>
        `;
    return;
  }

  lista.forEach((miembro) => {
    contenedor.innerHTML += `
            <div class="gasto-card">
                <p># ${miembro.ID_Negocio}</p>
                <h3>${miembro.nombre_negocio} </h3>
                <p><strong>Propietario:</strong> ${miembro.Nombre} ${miembro.ApellidoP} ${miembro.ApellidoM}</p>
                
                <p><strong>Categoria:</strong> ${miembro.Descripcion}</p>
                <div class="card-buttons">
                    <button class="btn btn-warning btn-editar" data-id="${miembro.ID_Negocio}" data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
                    <button class="btn btn-danger btn-eliminar" data-id="${miembro.ID_Negocio}">Eliminar</button>
                    <button class="btn btn-success btn-crear-horario" 
        data-id="${miembro.ID_Negocio}" 
        data-bs-toggle="modal" 
        data-bs-target="#modalHorario">
  Crear Horario
</button>
                        
                </div>
            </div>
        `;
  });
}

function renderizarError(mensaje) {
  const contenedor = document.querySelector("#ListaMiembros");
  contenedor.innerHTML = `
        <div class="error-message">
            <p>${mensaje}</p>
        </div>
    `;
}

function actualizarPaginacion(totalPaginas) {
  const paginacion = document.querySelector("#paginacion");

  if (!paginacion) {
    console.error("Error: No se encontró el contenedor #paginacion.");
    return;
  }

  paginacion.innerHTML = "";

  for (let i = 1; i <= totalPaginas; i++) {
    let boton = document.createElement("button");
    boton.classList.add(
      "btn",
      i === paginaActual ? "btn-primary" : "btn-outline-primary"
    );
    boton.textContent = i;
    boton.addEventListener("click", () => {
      paginaActual = i;
      listarMiembros();
    });

    paginacion.appendChild(boton);
  }
}

function aplicarFiltros() {
  const filtros = {
    //ID_Miembro: document.getElementById("idM").value.trim(),
    Nombre: document.getElementById("nombreM").value.trim(),

    Telefono: document.getElementById("numM").value.trim(),
  };

  paginaActual = 1;
  listarMiembros(filtros);
}

document.addEventListener("DOMContentLoaded", () => {
  const filtersContainer = document.querySelector(".filter-container");

  if (!filtersContainer) {
    console.error("Error: No se encontró el contenedor de filtros.");
    return;
  }

  filtersContainer.addEventListener("input", aplicarFiltros);

  listarMiembros();
});

document.getElementById("limpiarM").addEventListener("click", function () {
  document.querySelectorAll(".filter input").forEach((input) => {
    input.value = "";
  });

  aplicarFiltros();
});

document.querySelectorAll(".filter").forEach((filter) => {
  filter.addEventListener("click", function (event) {
    let isActive = this.classList.contains("active");

    document.querySelectorAll(".filter").forEach((otherFilter) => {
      otherFilter.classList.remove("active");
      let inputs = otherFilter.querySelectorAll("input, select");
      inputs.forEach((input) => {
        input.classList.add("hidden");
        input.value = "";
      });
    });

    if (!isActive) {
      this.classList.add("active");
      let input = this.querySelector("input, select");
      if (input) input.classList.remove("hidden");
    }
  });
});

document.querySelectorAll(".filter input").forEach((input) => {
  input.addEventListener("click", function (event) {
    event.stopPropagation();
  });
});

document.querySelectorAll(".filter .close").forEach((button) => {
  button.addEventListener("click", function (event) {
    event.stopPropagation();
    let filter = this.parentElement;
    filter.classList.remove("active");

    let inputs = filter.querySelectorAll("input, select");
    inputs.forEach((input) => {
      input.classList.add("hidden");
      input.value = "";
    });
  });
});

document.getElementById("limpiarM").addEventListener("click", function () {
  document.querySelectorAll(".filter").forEach((filter) => {
    let input = filter.querySelector("input");
    input.classList.add("hidden");
    input.value = "";
    filter.classList.remove("active");
  });
});

function agregarUsuario() {
  const form = document.querySelector("#formAgregar");
  const datos = new FormData(form);
  datos.append("ope", "AGREGAR");

  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data.success) {
        Swal.fire("Éxito", "Negocio agregado correctamente", "success");
        form.reset();
        document.querySelector("#modalAgregar .btn-close").click();
        listarMiembros();
      } else {
        Swal.fire("Error", data.msg, "error");
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo agregar el usuario: " + error.message,
        "error"
      );
    });
}

function cargarUsuario(id) {
  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENER", ID_Negocio: id }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.querySelector("#ID_Usuario").value = data.usuario.ID_Negocio;
        document.querySelector("#NombreEdit").value =
          data.usuario.nombre_negocio;
      } else {
        Swal.fire(
          "Error",
          "No se pudo obtener la información del usuario",
          "error"
        );
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo obtener la información del usuario: " + error.message,
        "error"
      );
    });
}

function editarUsuario() {
  const form = document.querySelector("#formEditar");
  const datos = new FormData(form);
  datos.append("ope", "EDITAR");

  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire("Éxito", "Usuario actualizado correctamente", "success");
        document.querySelector("#modalEditar .btn-close").click();
        listarMiembros();
      } else {
        Swal.fire("Error", data.msg, "error");
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo actualizar el usuario: " + error.message,
        "error"
      );
    });
}
function buscarMiembroModal() {
  const idMiembroInput = document.querySelector("#ID_Usuario");
  const nombreMiembroInput = document.querySelector("#nombreMiembro");

  idMiembroInput.addEventListener("input", () => {
    const idMiembro = idMiembroInput.value.trim();
    if (idMiembro === "") {
      nombreMiembroInput.value = "";
      return;
    }

    fetch("controladores/controladorNegocios.php", {
      method: "POST",
      body: new URLSearchParams({
        ope: "BUSCAR_MIEMBRO",
        ID_Miembro: idMiembro,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          nombreMiembroInput.value = `${data.miembro.Nombre} ${data.miembro.ApellidoP} ${data.miembro.ApellidoM}`;
        } else {
          nombreMiembroInput.value = "No encontrado";
        }
      })
      .catch((error) => {
        console.error("Error al buscar miembro:", error);
        nombreMiembroInput.value = "Error";
      });
  });
}

function cargarMembresias() {
  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENERCLASESDIA" }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const selectMembresias = document.getElementById("ID_Categoria");
        selectMembresias.innerHTML =
          "<option value=''>Seleccione una categoria</option>";

        data.membresias.forEach((membresia) => {
          const option = document.createElement("option");
          option.value = membresia.ID_Categoria;
          option.setAttribute("data-precio", membresia.Descripcion);
          option.textContent = membresia.Descripcion;
          selectMembresias.appendChild(option);
        });
      } else {
        Swal.fire("Error", "No se pudieron cargar las membresías", "error");
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo cargar la lista de membresías: " + error.message,
        "error"
      );
    });
}
function eliminarUsuario(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡Esta acción no se puede deshacer!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("controladores/controladorNegocios.php", {
        method: "POST",
        body: new URLSearchParams({ ope: "ELIMINAR", ID_Negocio: id }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire(
              "Eliminado",
              "Negocio eliminado correctamente",
              "success"
            );
            listarMiembros();
          } else {
            Swal.fire("Error", data.msg, "error");
          }
        })
        .catch((error) => {
          Swal.fire(
            "Error",
            "No se pudo eliminar el usuario: " + error.message,
            "error"
          );
        });
    }
  });
}
document.addEventListener("DOMContentLoaded", function () {
  cargarMembresias();
});

function agregarHorario(id) {
  Swal.fire({
    title: "¿Guardar horario?",
    text: "Se creará un nuevo horario para este negocio.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, guardar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      const formHorario = document.querySelector("#formHorario");
      const datos = new FormData(formHorario);

      // Añadimos operación y el ID del negocio como extra seguridad
      datos.append("ope", "AGREGAR_HORARIO");
     

      fetch("controladores/controladorHorarios.php", {
        method: "POST",
        body: datos,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Guardado", "Horario creado correctamente", "success");
            location.reload(); // o refrescar tabla con listarHorarios()
          } else {
            Swal.fire("Error", data.message, "error");
          }
        })
        .catch((err) => {
          Swal.fire(
            "Error",
            "No se pudo guardar el horario: " + err.message,
            "error"
          );
        });
    }
  });
}
