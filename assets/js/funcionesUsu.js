import {
  validaCorreo,
  validaLargo,
  validaRango,
  validaSoloLetras,
  validaContrasena,
} from "./validaciones.js?v=3.8.1";
document.addEventListener("DOMContentLoaded", () => {
  NomUsuRep();

  // agregar usuario
  const formUsuario = document.querySelector("#formAgregar");
  if (formUsuario) {
    formUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      let errores = 0;

      let nombre = document.querySelector("#Nombre");
      let ApellidoP = document.querySelector("#ApellidoP");
      let ApellidoM = document.querySelector("#ApellidoM");
      let correo = document.querySelector("#NombreUsu");
      let clave = document.querySelector("#Contra");

      if (!validaSoloLetras(nombre)) errores++;
      if (!validaSoloLetras(ApellidoP)) errores++;
      if (!validaSoloLetras(ApellidoM)) errores++;
      if (!validaCorreo(correo)) errores++;
      if (!validaRango(clave, 5, 16)) errores++;
      if (!validaContrasena(clave)) errores++;

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
      } else if (event.target.classList.contains("btn-clave")) {
        let userId = event.target.dataset.id;
        document.querySelector("#ID_UsuarioClave").value = userId;
      }
    });
    document
      .querySelector("#formEditarClave")
      .addEventListener("submit", (event) => {
        event.preventDefault();
        let erroresC = 0;

        let claveC = document.querySelector("#ClaveNueva");
        let claveCC = document.querySelector("#ConfirmarClave");
        if (!validaRango(claveC, 8, 16)) erroresC++;
        if (!validaRango(claveCC, 8, 16)) erroresC++;
        if (!validaContrasena(claveC)) erroresC++;
        if (!validaContrasena(claveCC)) erroresC++;
        if (erroresC == 0) actualizarClave();
      });
  }

  const formEditarUsuario = document.querySelector("#formEditar");
  if (formEditarUsuario) {
    formEditarUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      let erroresE = 0;
      let nombreE = document.querySelector("#NombreEdit");
      let ApellidoPE = document.querySelector("#ApellidoPEdit");
      let ApellidoME = document.querySelector("#ApellidoMEdit");
      let correoE = document.querySelector("#NombreUsuEdit");

      if (!validaSoloLetras(nombreE)) erroresE++;
      if (!validaSoloLetras(ApellidoPE)) erroresE++;
      if (!validaSoloLetras(ApellidoME)) erroresE++;
      if (!validaCorreo(correoE)) erroresE++;

      if (erroresE == 0) editarUsuario();
    });
  }

  listarMiembros();
});
function NomUsuRep() {
  document.getElementById("NombreUsu").addEventListener("blur", function () {
    let nombreUsu = this.value.trim();
    let mensajeError = document.getElementById("errorNombreUsu");

    if (nombreUsu === "") {
      mensajeError.textContent = ""; // Limpia el mensaje si el campo está vacío
      return;
    }

    fetch("controladores/controladorUsuarios.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        ope: "VERIFICAR_NOMBREUSU",
        nombreUsu: nombreUsu,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          if (data.existe) {
            mensajeError.textContent =
              "⚠️ El nombre de usuario ya está en uso.";
            mensajeError.style.color = "red";
          } else {
            mensajeError.textContent = "";
          }
        } else {
          console.error("Error en la validación:", data.msg);
        }
      })
      .catch((error) => console.error("Error en la solicitud:", error));
  });
}
function CorreoUsuRep() {
  document.getElementById("CorreoUsu").addEventListener("blur", function () {
    let correoUsu = this.value.trim();
    let mensajeError = document.getElementById("errorCorreoUsu");

    if (correoUsu === "") {
      mensajeError.textContent = ""; // Limpia el mensaje si el campo está vacío
      return;
    }

    fetch("controladores/controladorUsuarios.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        ope: "VERIFICAR_CORREOUSU",
        correoUsu: correoUsu,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          if (data.existe) {
            mensajeError.textContent = "El correo ya está en uso.";
            mensajeError.style.color = "red";
          } else {
            mensajeError.textContent = "";
          }
        } else {
          console.error("Error en la validación:", data.msg);
        }
      })
      .catch((error) => console.error("Error en la solicitud:", error));
  });
}
// Función para listar usuarios
let paginaActual = 1;
const registrosPorPagina = 10;
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

  fetch("controladores/controladorUsuarios.php", {
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
    <p># ${miembro.ID_Usuario}</p>
    <h3>${miembro.Nombre} ${miembro.ApellidoP} ${miembro.ApellidoM}</h3>
    <p><strong>Correo:</strong> ${miembro.Correo}</p>
    <p><strong>Tipo:</strong> ${miembro.tipo_usuario}</p>

    <div class="card-buttons">
        <button class="icon-btn btn-editar" data-id="${miembro.ID_Usuario}" data-bs-toggle="modal" data-bs-target="#modalEditar">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="icon-btn btn-eliminar" data-id="${miembro.ID_Usuario}">
            <i class="bi bi-trash"></i>
        </button>
        <button class="icon-btn btn-clave" data-id="${miembro.ID_Usuario}" data-bs-toggle="modal" data-bs-target="#modalEditarClave">
            <i class="bi bi-key-fill"></i>
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

  // Ajuste si estamos cerca del final
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
    Apellidos: document.getElementById("apeP").value.trim(),
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

  fetch("controladores/controladorUsuarios.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data.success) {
        Swal.fire("Éxito", "Usuario agregado correctamente", "success");
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
  fetch("controladores/controladorUsuarios.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENER", ID_Usuario: id }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.querySelector("#ID_Usuario").value = data.usuario.ID_Usuario;
        document.querySelector("#NombreEdit").value = data.usuario.Nombre;
        document.querySelector("#ApellidoPEdit").value = data.usuario.ApellidoP;
        document.querySelector("#ApellidoMEdit").value = data.usuario.ApellidoM;

        document.querySelector("#NombreUsuEdit").value = data.usuario.Correo;

        document.querySelector("#usutipEdit").value = data.usuario.tipo_usuario;
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
function actualizarClave() {
  let form = document.querySelector("#formEditarClave");
  let formData = new FormData(form);

  if (formData.get("ClaveNueva") !== formData.get("ConfirmarClave")) {
    Swal.fire("Error", "Las contraseñas no coinciden", "error");
    return;
  }

  formData.append("ope", "CAMBIAR_CLAVE");

  fetch("controladores/controladorUsuarios.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire("Éxito", "Contraseña actualizada correctamente", "success");
        form.reset();
        let modal = bootstrap.Modal.getInstance(
          document.querySelector("#modalEditarClave")
        );
        modal.hide();
      } else {
        Swal.fire("Error", data.msg, "error");
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo actualizar la contraseña: " + error.message,
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
      fetch("controladores/controladorUsuarios.php", {
        method: "POST",
        body: new URLSearchParams({ ope: "ELIMINAR", ID_Usuario: id }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire(
              "Eliminado",
              "Usuario eliminado correctamente",
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
