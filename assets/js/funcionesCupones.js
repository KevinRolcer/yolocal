import {
  validaCorreo,
  validaLargo,
  validaRango,
  validaSoloLetras,
  validaContrasena,
} from "./validaciones.js?v=3.8.1";
document.addEventListener("DOMContentLoaded", () => {


  // agregar usuario
  const formUsuario = document.querySelector("#formPromocion");
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
       const target = event.target.closest("button"); // busca el botón aunque pulses en el <i>
  if (!target) return;

  if (target.classList.contains("btn-editar")) {
    cargarUsuario(target.dataset.id);
  } else if (target.classList.contains("btn-eliminar")) {
    eliminarUsuario(target.dataset.id);
  } else if (target.classList.contains("btn-tags")) {
    verDetalles(target.dataset.id);
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

  listarPromociones();
});


// Función para listar usuarios
let paginaActual = 1;
const registrosPorPagina = 10;
let filtrosActuales = {};

export function listarPromociones(filtros = filtrosActuales) {
  filtrosActuales = filtros;

  let params = new URLSearchParams();
  params.append("ope", "LISTARPROMOCIONES");
  params.append("pagina", paginaActual);
  params.append("registrosPorPagina", registrosPorPagina);

  // Filtros disponibles
  if (filtros.titulo) params.append("titulo", filtros.titulo);
  if (filtros.descripcion) params.append("descripcion", filtros.descripcion);
  if (filtros.negocio) params.append("negocio", filtros.negocio);


  fetch("controladores/controladorCupones.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        console.error("Error al cargar promociones:", data.msg);
        renderizarError("No se pudieron cargar las promociones.");
        return;
      }
      renderizarPromociones(data.lista);
      actualizarPaginacion(data.totalPaginas);
    })
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      renderizarError("Error al conectarse con el servidor.");
    });
}

function renderizarPromociones(lista) {
  const contenedor = document.querySelector("#ListaMiembros");
  contenedor.innerHTML = "";

  if (!lista || lista.length === 0) {
    contenedor.innerHTML = `
      <div class="no-results">
          <p>No se encontró ninguna promoción con los filtros aplicados.</p>
      </div>
    `;
    return;
  }

  contenedor.innerHTML = ""; // limpiar antes de renderizar

lista.forEach((promo) => {
  contenedor.innerHTML += `
    <div class="promo-card">
      <div class="promo-info">
        <h3 class="promo-negocio">${promo.nombre_negocio}</h3>
        <p class="promo-titulo">${promo.titulo}</p>
        <p class="promo-descripcion">${promo.descripcion ?? "Sin descripción"}</p>
        <p class="promo-titulo">Cupones Restantes: ${promo.cantidad }</p>
      </div>
      <div class="promo-actions">
        <button class="icon-btn purple btn-tags" data-id="${promo.ID_Promocion}">
  <i class="bi bi-tags"></i>
</button>

<button class="icon-btn blue btn-agregar" data-id="${promo.ID_Promocion}">
  <i class="bi bi-plus"></i>
</button>

<button class="icon-btn green btn-eliminar" data-id="${promo.ID_Promocion}">
  <i class="bi bi-power"></i>
</button>
<button class="icon-btn yellow btn-editar" data-id="${promo.ID_Promocion}" 
        data-bs-toggle="modal" data-bs-target="#modalEditar">
  <i class="bi bi-pencil"></i>
</button>
      </div>
    </div>
  `;
});
}

function renderizarError(mensaje) {
  const contenedor = document.querySelector("#ListaPromociones");
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
      listarPromociones(filtrosActuales);
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
      listarPromociones(filtrosActuales);
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
    titulo: document.getElementById("filtroTitulo").value.trim(),
    descripcion: document.getElementById("filtroDescripcion").value.trim(),
    negocio: document.getElementById("filtroNegocio").value.trim(),

  };

  paginaActual = 1; // Reiniciar a la primera página al aplicar filtros
  listarPromociones(filtros);
}

document.addEventListener("DOMContentLoaded", () => {
  const filtersContainer = document.querySelector(".filter-container");

  if (!filtersContainer) {
    console.error("Error: No se encontró el contenedor de filtros.");
    return;
  }

  filtersContainer.addEventListener("input", aplicarFiltros);

  listarPromociones();
});

document.getElementById("limpiarFiltros").addEventListener("click", function () {
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

document.getElementById("limpiarFiltros").addEventListener("click", function () {
  document.querySelectorAll(".filter").forEach((filter) => {
    let input = filter.querySelector("input");
    input.classList.add("hidden");
    input.value = "";
    filter.classList.remove("active");
  });
});

function agregarUsuario() {
  const form = document.querySelector("#formPromocion");
  const datos = new FormData(form);
  datos.append("ope", "AGREGAR");

  fetch("controladores/controladorCupones.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data.success) {
        Swal.fire("Éxito", "Usuario agregado correctamente", "success");
        form.reset();
        document.querySelector("#modalPromocion .btn-close").click();
        listarPromociones();
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
  fetch("controladores/controladorCupones.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENER", ID_Promocion: id }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.querySelector("#ID_Promocion").value = data.usuario.ID_Promocion;
        document.querySelector("#EditTitulo").value = data.usuario.titulo;
        document.querySelector("#EditDescripcion").value = data.usuario.descripcion;
        document.querySelector("#EditFechaFin").value = data.usuario.fecha_fin;

        document.querySelector("#EditCantidad").value = data.usuario.cantidad;

       
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
        listarPromociones();
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
            listarPromociones();
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
function cargarNegocios() {
    fetch('controladores/controladorNegocios.php', {
        method: 'POST',
        body: new URLSearchParams({ "ope": "OBTENERMEMBRESIAS" })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selectNegocios = document.getElementById("ID_Negocio");
                selectNegocios.innerHTML = "<option value=''>Seleccione un negocio</option>";  // Opción por defecto

                data.negocios.forEach(negocio => {
                    const option = document.createElement("option");
                    option.value = negocio.ID_Negocio;
                    option.textContent = negocio.nombre_negocio;
                    selectNegocios.appendChild(option);
                });
            } else {
                Swal.fire("Error", "No se pudieron cargar los negocios", "error");
            }
        })
        .catch(error => {
            Swal.fire("Error", "No se pudo cargar la lista de negocios: " + error.message, "error");
        });
}
// Llamar a la función cuando se cargue el formulario
document.addEventListener('DOMContentLoaded', function () {
    cargarNegocios();
});
