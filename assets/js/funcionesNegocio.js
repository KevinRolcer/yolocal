import {
  validaCorreo,
  validaLargo,
  validaRango,
  validaSoloLetras,
  validaContrasena,
} from "./validaciones.js?v=3.8.1";
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".toggle-horarios");
  if (!btn) return;

  const idNegocio = btn.dataset.id;
  const contenedorHorarios = document.getElementById(`horarios-${idNegocio}`);
  const icono = btn.querySelector("i");

  if (!contenedorHorarios) return;

  // Verificar estado actual ANTES de hacer toggle
  const estaOculto = contenedorHorarios.classList.contains("oculto"); // Cambiar aquí

  // Alternar visibilidad
  contenedorHorarios.classList.toggle("oculto"); // Y aquí

  // Si ESTABA oculto (ahora se muestra), rotar ícono
  icono.classList.toggle("rotate-180", estaOculto);

  // Cargar horarios solo una vez cuando se muestre
  if (estaOculto && !contenedorHorarios.dataset.loaded) {
    listarHorarios(idNegocio, contenedorHorarios);
    contenedorHorarios.dataset.loaded = "true";
  }
});
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
      // Busca el botón más cercano que tenga alguna de las clases de acción
      const target = event.target.closest(
        ".btn-editar-imagen, .btn-eliminar, .btn-crear-horario, .btn-editar"
      );
      if (!target) return; // si no hay botón válido, salir

      if (target.classList.contains("btn-editar-imagen")) {
        let userIdI = target.dataset.id;
        document.querySelector("#ID_NegocioImagenes").value = userIdI;
      } else if (target.classList.contains("btn-eliminar")) {
        eliminarUsuario(target.dataset.id);
      } else if (target.classList.contains("btn-crear-horario")) {
        let userId = target.dataset.id;
        document.querySelector("#ID_NegocioHorario").value = userId;
      } else if (target.classList.contains("btn-editar")) {
        cargarUsuario(target.dataset.id);
        let userIde = target.dataset.id;
        document.querySelector("#ID_Negocio").value = userIde;
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
      let nombreE = document.querySelector("#nombre_negocioEdit");

      if (erroresE == 0) editarUsuario();
    });
  }

  listarMiembros();
});

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
  params.append("usuarioId", usuarioId);
  params.append("usuarioTipo", usuarioTipo);
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
  let htmlCompleto = "";

  lista.forEach((miembro) => {
    htmlCompleto += `
    <div class="negocio-card shadow-lg rounded-3 p-4 mb-4 bg-white">
      
      <!-- Encabezado -->
      <div class="negocio-header flex items-center justify-between mb-4">
        <h3 class="text-2xl font-bold text-gray-800">${miembro.nombre_negocio
      }</h3>
      </div>

      <!-- Información -->
      <div class="negocio-info text-sm text-gray-700 space-y-2 mb-4">
        <p><strong>Propietario:</strong> ${miembro.Nombre} ${miembro.ApellidoP
      } ${miembro.ApellidoM}</p>
        <p>${miembro.DescripcionN}</p>
        <p><strong>Teléfono:</strong> <a href="tel:${miembro.Telefono
      }" class="text-blue-600 hover:underline">${miembro.Telefono}</a></p>
        <p><a href="mailto:${miembro.Correo
      }" class="text-blue-600 hover:underline">${miembro.CorreoN}</a></p>
        <p><strong>Categoría:</strong> ${miembro.Descripcion}</p>
      </div>

      <!-- Redes Sociales -->
      <div class="negocio-social">
        ${miembro.SitioWeb
        ? `<a href="${miembro.SitioWeb}" target="_blank" class="social-btn"><i class="bi bi-globe"></i></a>`
        : ""
      }
        ${miembro.Facebook
        ? `<a href="${miembro.Facebook}" target="_blank" class="social-btn"><i class="bi bi-facebook"></i></a>`
        : ""
      }
        ${miembro.Instagram
        ? `<a href="${miembro.Instagram}" target="_blank" class="social-btn"><i class="bi bi-instagram"></i></a>`
        : ""
      }
      </div>

      <!-- Carrusel de imágenes -->
      <div class="negocio-imagenes mt-3 text-center" id="imagenes-${miembro.ID_Negocio
      }"></div>
      
      <!-- Botón Horarios -->
      <div class="toggle-horarios" data-id="${miembro.ID_Negocio}">
          <span> Ver horarios</span>
          <i class="bi bi-chevron-down"></i>
      </div>

      <!-- Contenedor de horarios -->
      <div class="negocio-horarios oculto mt-2" id="horarios-${miembro.ID_Negocio
      }"></div>
      
      <!-- Acciones -->
      <div class="negocio-actions flex justify-center gap-4">
        <button class="circle-btn bg-yellow-400 hover:bg-yellow-500 text-white btn-editar" 
                title="Editar"
                data-id="${miembro.ID_Negocio}" 
                data-bs-toggle="modal" 
                data-bs-target="#modalEditar">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="circle-btn bg-blue-400 hover:bg-blue-500 text-white btn-editar-imagen" 
                title="Subir Imagen"
                data-id="${miembro.ID_Negocio}" 
                data-bs-toggle="modal" 
                data-bs-target="#modalImagenes">
          <i class="bi bi-camera"></i>
        </button>
        <button class="circle-btn text-white btn-crear-horario" 
        style="background-color: #c084fc; border: none;" 
        title="Crear Horario"
        data-id="${miembro.ID_Negocio}" 
        data-bs-toggle="modal" 
        data-bs-target="#modalHorario">
  <i class="bi bi-clock"></i>
</button>


        ${usuarioTipo === "admin"
        ? `
        <button class="circle-btn btn-toggle ${miembro.estado == 1 ? "btn-green" : "btn-red"
        }" 
            data-id="${miembro.ID_Negocio}" 
            data-status="${miembro.estado}">
            <i class="bi bi-power"></i>
          </button>
        `
        : ""
      }
        
      </div>
    </div>
    `;
  });

  // Segundo: insertar todo el HTML de una vez
  contenedor.innerHTML = htmlCompleto;

  // Tercero: cargar las imágenes para todos los negocios
  lista.forEach((miembro) => {
    listarImagenes(miembro.ID_Negocio);
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
        // Recorremos todos los campos del formulario de edición
        const form = document.querySelector("#formEditar");

        for (const key in data.usuario) {
          // Buscamos el input que tenga el id igual al nombre del campo + "Edit"
          const input = form.querySelector(`#${key}Edit`);
          if (input) {
            input.value = data.usuario[key] ?? ""; // asigna valor o cadena vacía si es null
          }
        }

        // Si quieres mantener el campo oculto con el ID separado
        const hiddenId = form.querySelector("#ID_Negocio");
        if (hiddenId) hiddenId.value = data.usuario.ID_Negocio;
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

      datos.append("ope", "AGREGAR_HORARIO");

      fetch("controladores/controladorHorarios.php", {
        method: "POST",
        body: datos,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Guardado", "Horario creado correctamente", "success");
            formHorario.reset();
            document.querySelector("#modalHorario .btn-close").click();
            listarMiembros();
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
const dropzone = document.getElementById("dropzone");
const fileInput = document.getElementById("fileInput");
const previewContainer = document.getElementById("previewContainer");

let files = [];

dropzone.addEventListener("click", () => fileInput.click());

fileInput.addEventListener("change", (e) => {
  handleFiles(e.target.files);
});

dropzone.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropzone.classList.add("dragover");
});

dropzone.addEventListener("dragleave", () => {
  dropzone.classList.remove("dragover");
});

dropzone.addEventListener("drop", (e) => {
  e.preventDefault();
  dropzone.classList.remove("dragover");
  handleFiles(e.dataTransfer.files);
});

function handleFiles(selectedFiles) {
  if (files.length + selectedFiles.length > 4) {
    alert("Solo puedes subir un máximo de 4 imágenes.");
    return;
  }

  [...selectedFiles].forEach((file) => {
    files.push(file);

    const reader = new FileReader();
    reader.onload = (e) => {
      const col = document.createElement("div");
      col.classList.add("col-md-3");

      col.innerHTML = `
        <div class="preview-image">
          <img src="${e.target.result}" alt="preview">
          <button type="button" class="remove-btn">&times;</button>
        </div>
      `;

      col.querySelector(".remove-btn").addEventListener("click", () => {
        previewContainer.removeChild(col);
        files = files.filter((f) => f !== file);
      });

      previewContainer.appendChild(col);
    };
    reader.readAsDataURL(file);
  });
}

// Enviar formulario con imágenes
document.getElementById("formImagenes").addEventListener("submit", (e) => {
  e.preventDefault();

  const formData = new FormData();
  formData.append("ope", "SUBIR_IMAGENES");
  formData.append(
    "ID_Negocio",
    document.getElementById("ID_NegocioImagenes").value
  );

  files.forEach((file, i) => {
    formData.append(`imagen${i + 1}`, file);
  });

  fetch("controladores/controladorImagenes.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert("Imágenes guardadas correctamente");
        files = [];
        previewContainer.innerHTML = "";
        listarMiembros();
        document.querySelector("#modalImagenes .btn-close").click();
      } else {
        alert("Error: " + data.msg);
      }
    });
});

// Función para listar imágenes
function listarImagenes(idNegocio) {
  const formData = new FormData();
  formData.append("ope", "LISTAR_IMAGENES");
  formData.append("ID_Negocio", idNegocio);

  fetch("controladores/controladorImagenes.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.imagenes.length > 0) {
        const contenedor = document.getElementById(`imagenes-${idNegocio}`);
        contenedor.innerHTML = "";

        const carouselId = `carousel-${idNegocio}`;
        let carouselHtml = `
        <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            ${data.imagenes
            .map(
              (ruta, index) => `
              <div class="carousel-item ${index === 0 ? "active" : ""}">
  <img src="${ruta}" 
       class="d-block w-100" 
       style="height:300px; object-fit:contain; background-color:#f0f0f0;" 
       alt="Imagen negocio">
</div>
            `
            )
            .join("")}
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true" ></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </div>
      `;
        contenedor.innerHTML = carouselHtml;
      }
    });
}
function listarHorarios(idNegocio) {
  const formData = new FormData();
  formData.append("ope", "LISTAR_HORARIOS");
  formData.append("ID_Negocio", idNegocio);

  fetch("controladores/controladorHorarios.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.horarios.length > 0) {
        const contenedor = document.getElementById(`horarios-${idNegocio}`);
        contenedor.innerHTML = "";

        // 🔥 función para convertir hora 24h → AM/PM
        const convertirHora = (hora) => {
          if (!hora) return "";
          const [h, m] = hora.split(":");
          let horas = parseInt(h, 10);
          const minutos = m || "00";
          const ampm = horas >= 12 ? "PM" : "AM";
          horas = horas % 12 || 12;
          return `${horas}:${minutos} ${ampm}`;
        };

        let horariosHtml = `
        <table class="table table-sm table-bordered text-center">
          <thead class="table-light">
            <tr>
              <th>Día</th>
              <th>Apertura</th>
              <th>Cierre</th>
            </tr>
          </thead>
          <tbody>
            ${data.horarios
            .map(
              (horario) => `
              <tr>
                <td>${horario.dia_semana}</td>
                <td>${convertirHora(horario.hora_apertura)}</td>
                <td>${convertirHora(horario.hora_cierre)}</td>
              </tr>
            `
            )
            .join("")}
          </tbody>
        </table>
      `;

        contenedor.innerHTML = horariosHtml;
      }
    })
    .catch((err) => console.error("Error cargando horarios:", err));
}
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".btn-toggle");
  if (!btn) return;

  const id = btn.dataset.id;
  const estatus = btn.dataset.status == "1" ? 0 : 1; // si está en 1 lo pasamos a 0, y viceversa

  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: new URLSearchParams({
      ope: "CAMBIARESTATUS",
      ID_Negocio: id,
      estado: estatus,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire("Éxito", "Estatus actualizado", "success");
        listarMiembros(); // refrescar la lista
      } else {
        Swal.fire(
          "Error",
          data.msg || "No se pudo cambiar el estatus",
          "error"
        );
      }
    })
    .catch((error) => {
      Swal.fire("Error", "Problema con el servidor: " + error.message, "error");
    });
});
