import {
  validaCorreo,
  validaLargo,
  validaSoloLetras,
  validaContrasena,
} from "./validaciones.js?v=3.8.2";
document.addEventListener("DOMContentLoaded", () => {
  // agregar usuario
  const formUsuario = document.querySelector("#formAgregar");
  if (formUsuario) {
    formUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      let errores = 0;

      let nombre = document.querySelector("#Nombre");

      if (!validaSoloLetras(nombre)) errores++;

      if (errores == 0) agregarUsuario();
    });
  }

  //  editar y eliminar
  const listaUsuarios = document.querySelector("#ListaMiembros");
  if (listaUsuarios) {
    listaUsuarios.addEventListener("click", (event) => {
      event.preventDefault();
      const target = event.target.closest("button"); // busca el botón aunque pulses en el <i>
      if (!target) return;

      if (target.classList.contains("btn-editar")) {
        cargarUsuario(target.dataset.id);
      } else if (target.classList.contains("btn-eliminar")) {
        eliminarUsuario(target.dataset.id);
      } else if (event.target.classList.contains("btn-clave")) {
        let userId = event.target.dataset.id;
        document.querySelector("#ID_UsuarioClave").value = userId;
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
const registrosPorPagina = 5;
let filtrosActuales = {};

export function listarMiembros(filtros = filtrosActuales) {
  filtrosActuales = filtros; 

  let params = new URLSearchParams();
  params.append("ope", "LISTAUSUARIOS"); 
  params.append("pagina", paginaActual);
  params.append("registrosPorPagina", registrosPorPagina);

  if (filtros.ID_Miembro) params.append("id", filtros.ID_Miembro);
  if (filtros.Nombre) params.append("nombre", filtros.Nombre);
  if (filtros.Apellidos) params.append("apellidos", filtros.Apellidos);
  if (filtros.Telefono) params.append("telefono", filtros.Telefono);

  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        console.error("Error al cargar categorias:", data.msg);
        renderizarError("No se pudieron cargar los categorias.");
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
        <div class="categoria-card d-flex justify-content-between align-items-center p-3 mb-3 rounded">
            <h3 class="mb-0 ">${miembro.Descripcion}</h3>
            <div class="card-buttons d-flex gap-2">
                <button class="btn btn-warning btn-editar" data-id="${miembro.ID_Categoria}" data-bs-toggle="modal" data-bs-target="#modalEditar">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-eliminar" data-id="${miembro.ID_Categoria}">
                    <i class="bi bi-trash"></i>
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

  // Botón anterior
  let btnAnterior = document.createElement("button");
  btnAnterior.classList.add("btn", "btn-outline-primary");
  btnAnterior.innerHTML = "&laquo;"; // «
  btnAnterior.disabled = paginaActual === 1;
  btnAnterior.addEventListener("click", () => {
    if (paginaActual > 1) {
      paginaActual--;
      listarMiembros(filtrosActuales);
    }
  });
  paginacion.appendChild(btnAnterior);

  // Botones de páginas
  let maxVisible = 5;
  let inicio = Math.max(1, paginaActual - Math.floor(maxVisible / 2));
  let fin = Math.min(totalPaginas, inicio + maxVisible - 1);

  if (fin - inicio + 1 < maxVisible) {
    inicio = Math.max(1, fin - maxVisible + 1);
  }

  for (let i = inicio; i <= fin; i++) {
    let boton = document.createElement("button");
    boton.classList.add(
      "btn",
      i === paginaActual ? "btn-primary" : "btn-outline-primary",
      "mx-1"
    );
    boton.textContent = i;
    boton.addEventListener("click", () => {
      paginaActual = i;
      listarMiembros(filtrosActuales);
    });
    paginacion.appendChild(boton);
  }

  // Botón siguiente
  let btnSiguiente = document.createElement("button");
  btnSiguiente.classList.add("btn", "btn-outline-primary");
  btnSiguiente.innerHTML = "&raquo;"; // »
  btnSiguiente.disabled = paginaActual === totalPaginas;
  btnSiguiente.addEventListener("click", () => {
    if (paginaActual < totalPaginas) {
      paginaActual++;
      listarMiembros(filtrosActuales);
    }
  });
  paginacion.appendChild(btnSiguiente);
}

function aplicarFiltros() {
  const filtros = {
    //ID_Miembro: document.getElementById("idM").value.trim(),
    Nombre: document.getElementById("nombreM").value.trim(),
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

  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data.success) {
        Swal.fire("Éxito", "Categoria agregado correctamente", "success");
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
  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENER", ID_Usuario: id }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.querySelector("#ID_Usuario").value = data.usuario.ID_Categoria;
        document.querySelector("#NombreEdit").value = data.usuario.Descripcion;
     
      } else {
        Swal.fire(
          "Error",
          "No se pudo obtener la información de la categoria",
          "error"
        );
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo obtener la información de la categoria: " + error.message,
        "error"
      );
    });
}

function editarUsuario() {
  const form = document.querySelector("#formEditar");
  const datos = new FormData(form);
  datos.append("ope", "EDITAR");

  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire("Éxito", "Dato actualizado correctamente", "success");
        document.querySelector("#modalEditar .btn-close").click();
        listarMiembros();
      } else {
        Swal.fire("Error", data.msg, "error");
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo actualizar la categoria: " + error.message,
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
      fetch("controladores/controladorCategorias.php", {
        method: "POST",
        body: new URLSearchParams({ ope: "ELIMINAR", ID_Usuario: id }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire(
              "Eliminado",
              "Categoria eliminada correctamente",
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
