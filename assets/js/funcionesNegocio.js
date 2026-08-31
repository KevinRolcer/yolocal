import {
  validaCorreo,
  validaLargo,
 
  validaSoloLetras,
  validaContrasena,
} from "./validaciones.js?v=3.8.2";
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
  contenedorHorarios.classList.toggle("oculto");
  btn.classList.toggle("active", estaOculto);

  // Cargar horarios solo una vez cuando se muestre
  if (estaOculto && !contenedorHorarios.dataset.loaded) {
    listarHorarios(idNegocio, contenedorHorarios);
    contenedorHorarios.dataset.loaded = "true";
  }
});

// --- State Variables ---
let paginaActual = 1;
const registrosPorPagina = 10;
let filtrosActuales = {};
let filtroDebounceTimer = null;
let ocultarDatosSensibles = false;
let ultimaListaRenderizada = [];
let ultimoTotalRegistros = 0;
let ordenColumnaActual = 'ID_Negocio';
let ordenDireccionActual = 'DESC';
let sortSeleccionado = null; // null | 'az' | 'num'

function iniciarModuloNegocios() {
  buscarMiembroModal();
  cargarMembresias();
  cargarCategoriasFiltro();
  
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
      const target = event.target.closest(
        ".btn-editar-imagen, .btn-eliminar, .btn-crear-horario, .btn-editar, .btn-pagar, .btn-ver-galeria"
      );
      if (!target) return;

      const id = target.dataset.id;
      if (target.classList.contains("btn-ver-galeria")) {
        abrirGaleria(id);
      } else if (target.classList.contains("btn-editar-imagen")) {
        document.querySelector("#ID_NegocioImagenes").value = id;
      } else if (target.classList.contains("btn-eliminar")) {
        eliminarUsuario(id);
      } else if (target.classList.contains("btn-crear-horario")) {
        document.querySelector("#ID_NegocioHorario").value = id;
      } else if (target.classList.contains("btn-editar")) {
        cargarUsuario(id);
        document.querySelector("#ID_Negocio").value = id;
      }
    });
  }

  const formHorario = document.querySelector("#formHorario");
  if (formHorario) {
    formHorario.addEventListener("submit", (event) => {
      event.preventDefault();
      if (agregarHorario) agregarHorario();
    });
  }

  const formEditarUsuario = document.querySelector("#formEditar");
  if (formEditarUsuario) {
    formEditarUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      editarUsuario();
    });
  }

  // --- Filtros ---
  const searchInput = document.getElementById("searchInput");
  const filterOptions = document.querySelectorAll(".filter-option");
  const statusButtons = document.querySelectorAll(".status-btn");
  const clearAllButton = document.getElementById("limpiarM");

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      updateClearButton();
      clearTimeout(filtroDebounceTimer);
      filtroDebounceTimer = setTimeout(aplicarFiltros, 400);
    });
  }

  const filterDropdownBtn = document.getElementById("filterDropdownBtn");
  const filterDropdown = document.querySelector(".filter-dropdown");
  if (filterDropdownBtn && filterDropdown) {
    filterDropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      filterDropdown.classList.toggle("show");
    });
    document.addEventListener("click", (e) => {
      if (!filterDropdown.contains(e.target)) {
        filterDropdown.classList.remove("show");
      }
    });
  }

  filterOptions.forEach((opt) => {
    opt.addEventListener("click", () => {
      const key = opt.dataset.key;
      const placeholder = opt.dataset.placeholder;
      filterOptions.forEach((o) => o.classList.remove("active"));
      opt.classList.add("active");
      if (searchInput) {
        searchInput.dataset.filterKey = key;
        searchInput.placeholder = placeholder;
      }
      aplicarFiltros();
    });
  });

  statusButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      statusButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      aplicarFiltros();
    });
  });

  if (clearAllButton) {
    clearAllButton.addEventListener("click", () => {
      if (searchInput) {
        searchInput.value = "";
        searchInput.dataset.filterKey = "Nombre";
        searchInput.placeholder = "Buscar por nombre...";
      }
      updateClearButton();
      filterOptions.forEach((o) => o.classList.remove("active"));
      const defOpt = document.querySelector('.filter-option[data-key="Nombre"]');
      if (defOpt) defOpt.classList.add("active");
      statusButtons.forEach((b) => b.classList.remove("active"));
      const todosBtn = document.querySelector('.status-btn[data-status="todos"]');
      if (todosBtn) todosBtn.classList.add("active");
      const catCheckboxes = document.querySelectorAll(".cat-checkbox");
      catCheckboxes.forEach(cb => cb.checked = false);
      sortSeleccionado = null;
      ordenColumnaActual = "ID_Negocio";
      ordenDireccionActual = "DESC";
      updateSortButtons();
      aplicarFiltros();
    });
  }

  const btnSortAz = document.getElementById("btnSortAz");
  const btnSortNum = document.getElementById("btnSortNum");
  if (btnSortAz) {
    btnSortAz.addEventListener("click", () => {
      if (sortSeleccionado === "az" && ordenDireccionActual === "ASC") {
        ordenDireccionActual = "DESC";
      } else if (sortSeleccionado === "az" && ordenDireccionActual === "DESC") {
        sortSeleccionado = null;
        ordenColumnaActual = "ID_Negocio";
        ordenDireccionActual = "DESC";
      } else {
        sortSeleccionado = "az";
        ordenColumnaActual = "Nombre";
        ordenDireccionActual = "ASC";
      }
      updateSortButtons();
      paginaActual = 1;
      listarMiembros();
    });
  }
  if (btnSortNum) {
    btnSortNum.addEventListener("click", () => {
      if (sortSeleccionado === "num" && ordenDireccionActual === "ASC") {
        ordenDireccionActual = "DESC";
      } else if (sortSeleccionado === "num" && ordenDireccionActual === "DESC") {
        sortSeleccionado = null;
        ordenColumnaActual = "ID_Negocio";
        ordenDireccionActual = "DESC";
      } else {
        sortSeleccionado = "num";
        ordenColumnaActual = "id";
        ordenDireccionActual = "ASC";
      }
      updateSortButtons();
      paginaActual = 1;
      listarMiembros();
    });
  }

  listarMiembros();
}

// Expose functions to window for SPA navigation
window.listarMiembros = listarMiembros;

// Auto-init for AJAX or full load
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", iniciarModuloNegocios, { once: true });
} else {
  iniciarModuloNegocios();
}

function escapeHTML(str) {
  if (!str) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function resolverRutaPublica(ruta) {
  const valor = String(ruta || "").trim().replace(/\\/g, "/");
  if (!valor || /^(?:data:|https?:\/\/|\/\/)/i.test(valor)) return valor;

  const indiceAssets = valor.indexOf("assets/");
  if (indiceAssets !== -1) {
    const base = typeof window.YL_BASE_PATH === "string"
      ? window.YL_BASE_PATH.replace(/\/$/, "")
      : "";
    return `${base}/${valor.slice(indiceAssets)}`;
  }

  return valor;
}

function enmascararCorreo(correo) {
  if (!correo || !correo.includes("@")) return correo;
  const [local, dominio] = correo.split("@");
  if (local.length <= 2) return "**@" + dominio;
  return local[0] + local[1] + "***@" + dominio;
}

function enmascararTelefono(tel) {
  if (!tel || tel.length < 4) return tel;
  return tel.substring(0, 2) + "****" + tel.substring(tel.length - 2);
}

export function listarMiembros(filtros = filtrosActuales) {
  // Ensure global variables are available (for AJAX navigation safety)
  if (typeof window.usuarioId === 'undefined' || window.usuarioId === null || window.usuarioId === '') {
    console.warn("usuarioId not available yet, scheduling retry");
    setTimeout(() => listarMiembros(filtros), 100);
    return;
  }

  filtrosActuales = filtros;
  let params = new URLSearchParams();
  params.append("ope", "LISTAUSUARIOS");
  params.append("pagina", paginaActual);
  params.append("registrosPorPagina", registrosPorPagina);

  if (filtros.ID_Miembro) params.append("id", filtros.ID_Miembro);
  if (filtros.Nombre) params.append("nombre", filtros.Nombre);
  if (filtros.Apellidos) params.append("apellidos", filtros.Apellidos);
  if (filtros.Telefono) params.append("telefono", filtros.Telefono);
  if (filtros.Estatus && filtros.Estatus !== "todos") params.append("estatus", filtros.Estatus);
  
  if (filtros.Categorias && filtros.Categorias.length > 0) {
    params.append("categorias", filtros.Categorias.join(","));
  }

  // Sorting params
  params.append("ordenColumna", ordenColumnaActual);
  params.append("ordenDireccion", ordenDireccionActual);

  params.append("usuarioId", window.usuarioId);
  params.append("usuarioTipo", window.usuarioTipo);

  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        console.error("Error al cargar miembros:", data.msg);
        renderizarError("No se pudieron cargar los negocios.");
        return;
      }
      ultimaListaRenderizada = data.lista;
      ultimoTotalRegistros = data.totalRegistros;
      renderizarMiembros(data.lista, data.totalRegistros);
      actualizarPaginacion(data.totalPaginas);
    })
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      renderizarError("Error al conectarse con el servidor.");
    });
}

function renderizarMiembros(lista, total = 0) {
  const contenedor = document.querySelector("#ListaMiembros");
  const contador = document.getElementById("negociosContador");
  
  if (contador) {
    contador.textContent = `${total} negocios`;
  }

  contenedor.innerHTML = "";

  if (!lista || lista.length === 0) {
    contenedor.innerHTML = `
            <div class="no-results">
                <i class="bi bi-folder-x"></i>
                <p>No se encuentra ningún negocio con los filtros aplicados.</p>
            </div>
        `;
    return;
  }
  let htmlCompleto = "";

  lista.forEach((miembro) => {
    const id = miembro.ID_Negocio;
    const nombreNegocio = escapeHTML(miembro.nombre_negocio);
    const propietario = escapeHTML(`${miembro.Nombre} ${miembro.ApellidoP} ${miembro.ApellidoM}`);
    let correo = escapeHTML(miembro.CorreoN || "");
    let telefono = escapeHTML(miembro.Telefono || "");
    const categoria = escapeHTML(miembro.CategoriaNombre || "Sin categoría");
    const estado = miembro.estado == 1;

    // Privacy masking
    if (ocultarDatosSensibles) {
      correo = enmascararCorreo(correo);
      telefono = enmascararTelefono(telefono);
    }

    const estadoClase = estado ? "status-activo" : "status-inactivo";
    const estadoTexto = estado ? "Activo" : "Inactivo";
    const estadoIcono = estado ? "bi-check-circle-fill" : "bi-x-circle-fill";

    const rutaIcono = resolverRutaPublica(miembro.Rutaicono);
    const rutaIconoEsImagen = /\.(?:avif|gif|jpe?g|png|svg|webp)(?:[?#].*)?$/i.test(rutaIcono);
    const iconoNegocio = rutaIconoEsImagen
      ? `<img src="${escapeHTML(rutaIcono)}" class="folder-thumb" alt="Logo de ${nombreNegocio}" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><i class="bi bi-building folder-thumb-placeholder" hidden></i>`
      : `<i class="bi bi-building folder-thumb-placeholder"></i>`;

    htmlCompleto += `
    <article class="negocio-folder-card">
      <div class="folder-header">
        <div class="folder-icon-wrapper">
          <i class="bi bi-folder-fill folder-icon"></i>
          ${iconoNegocio}
        </div>
        <div class="folder-title-area">
          <span class="negocio-id">#${id}</span>
          <h3 class="negocio-name">${nombreNegocio}</h3>
          <span class="negocio-category-pill">${categoria}</span>
        </div>
        <div class="folder-actions-dropdown">
          <button class="btn-folder-actions" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><h6 class="dropdown-header">Acciones</h6></li>
            <li><button class="dropdown-item btn-ver-galeria" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalGaleria"><i class="bi bi-images me-2"></i> Ver Galería</button></li>
            <li><button class="dropdown-item btn-editar" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="bi bi-pencil me-2"></i> Editar Datos</button></li>
            <li><button class="dropdown-item btn-editar-imagen" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalImagenes"><i class="bi bi-camera me-2"></i> Gestionar Imágenes</button></li>
            <li><button class="dropdown-item btn-crear-horario" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalHorario"><i class="bi bi-clock me-2"></i> Horarios</button></li>
            <div class="dropdown-divider"></div>
            ${window.usuarioTipo === "admin" ? `
            <li><button class="dropdown-item btn-pagar" data-id="${id}"><i class="bi bi-cash me-2"></i> Registrar Pago</button></li>
            <li><button class="dropdown-item btn-toggle-status" data-id="${id}" data-status="${miembro.estado}"><i class="bi bi-power me-2"></i> ${estado ? 'Desactivar' : 'Activar'}</button></li>
            ` : ""}
            <li><button class="dropdown-item text-danger btn-eliminar" data-id="${id}"><i class="bi bi-trash me-2"></i> Eliminar</button></li>
          </ul>
        </div>
      </div>
      
      <div class="folder-body">
        <div class="info-row">
          <i class="bi bi-person"></i>
          <span>${propietario}</span>
        </div>
        <div class="info-row">
          <i class="bi bi-telephone"></i>
          <span>${telefono}</span>
        </div>
        <div class="info-row">
          <i class="bi bi-envelope"></i>
          <span>${correo}</span>
        </div>
        
        <div class="folder-footer">
          <div class="status-indicator ${estadoClase}">
            <i class="bi ${estadoIcono}"></i>
            <span>${estadoTexto}</span>
          </div>
          <button class="btn-view-hours toggle-horarios" data-id="${id}">
            <i class="bi bi-chevron-down"></i>
            Horarios
          </button>
        </div>

        <!-- Contenedor de horarios (mantiene funcionalidad original) -->
        <div class="negocio-horarios oculto mt-2" id="horarios-${id}"></div>
      </div>
    </article>
    `;
  });

  contenedor.innerHTML = htmlCompleto;
}

function renderizarError(mensaje) {
  const contenedor = document.querySelector("#ListaMiembros");
  contenedor.innerHTML = `
        <div class="error-message">
            <i class="bi bi-exclamation-triangle"></i>
            <p>${mensaje}</p>
        </div>
    `;
}

function actualizarPaginacion(totalPaginas) {
  const paginacion = document.querySelector("#paginacion");
  if (!paginacion) return;
  paginacion.innerHTML = "";

  const createBtn = (content, disabled, onClick, active = false) => {
    const btn = document.createElement("button");
    btn.className = `btn ${active ? 'btn-primary' : 'btn-outline-primary'} mx-1`;
    btn.innerHTML = content;
    btn.disabled = disabled;
    btn.addEventListener("click", onClick);
    return btn;
  };

  paginacion.appendChild(createBtn("&laquo;", paginaActual === 1, () => {
    paginaActual--;
    listarMiembros();
  }));

  for (let i = 1; i <= totalPaginas; i++) {
    if (i === 1 || i === totalPaginas || (i >= paginaActual - 1 && i <= paginaActual + 1)) {
      paginacion.appendChild(createBtn(i, false, () => {
        paginaActual = i;
        listarMiembros();
      }, i === paginaActual));
    }
  }

  paginacion.appendChild(createBtn("&raquo;", paginaActual === totalPaginas, () => {
    paginaActual++;
    listarMiembros();
  }));
}

function aplicarFiltros() {
  const searchInput = document.getElementById("searchInput");
  const filterKey = searchInput ? searchInput.dataset.filterKey : "Nombre";
  const searchValue = searchInput ? searchInput.value.trim() : "";

  const activeStatusBtn = document.querySelector(".status-btn.active");
  const status = activeStatusBtn ? activeStatusBtn.dataset.status : "todos";

  const filtros = {
    Estatus: status,
    Categorias: []
  };

  const checkedCats = document.querySelectorAll(".cat-checkbox:checked");
  checkedCats.forEach(cb => {
    filtros.Categorias.push(cb.value);
  });

  if (searchValue) {
    // Mapeamos las llaves de la UI a las que espera el controlador
    const mapKeys = {
      "Nombre": "Nombre",
      "Telefono": "Telefono", // En negocios este es el propietario (segun el HTML viejo)
      "ID_Miembro": "ID_Miembro"
    };
    filtros[mapKeys[filterKey] || filterKey] = searchValue;
  }

  paginaActual = 1;
  listarMiembros(filtros);
}

function updateClearButton() {
  const searchInput = document.getElementById("searchInput");
  const searchClear = document.getElementById("searchClear");
  if (!searchClear || !searchInput) return;
  const hasValue = searchInput.value.trim().length > 0;
  searchClear.style.opacity = hasValue ? "1" : "0";
  searchClear.style.pointerEvents = hasValue ? "auto" : "none";
}

function updateSortButtons() {
  const btnSortAz = document.getElementById("btnSortAz");
  const btnSortNum = document.getElementById("btnSortNum");
  if (btnSortAz) {
    const icon = btnSortAz.querySelector("i");
    const isActive = sortSeleccionado === "az";
    btnSortAz.classList.toggle("active", isActive);
    if (icon) {
      icon.className = (isActive && ordenDireccionActual === "DESC") ? "bi bi-sort-alpha-up" : "bi bi-sort-alpha-down";
    }
  }
  if (btnSortNum) {
    const icon = btnSortNum.querySelector("i");
    const isActive = sortSeleccionado === "num";
    btnSortNum.classList.toggle("active", isActive);
    if (icon) {
      icon.className = (isActive && ordenDireccionActual === "DESC") ? "bi bi-sort-numeric-up" : "bi bi-sort-numeric-down";
    }
  }
}


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

  const fileInput = form.querySelector("#IconoNegocioEdit");
  if (fileInput && fileInput.files.length === 0) {
    datos.delete("IconoNegocioEdit");
    const logoActual = document.querySelector("#RutaiconoEdit");
    if (logoActual && logoActual.value) {
      datos.append("Rutaicono", logoActual.value);
    }
  }

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

function abrirGaleria(id) {
  const contenedor = document.getElementById("contenedorGaleria");
  contenedor.innerHTML = `
    <div class="text-white text-center">
      <div class="spinner-border text-primary mb-2" role="status"></div>
      <p>Cargando galería...</p>
    </div>
  `;

  const formData = new FormData();
  formData.append("ope", "LISTAR_IMAGENES");
  formData.append("ID_Negocio", id);

  fetch("controladores/controladorImagenes.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.imagenes.length > 0) {
        const carouselId = `carousel-galeria-${id}`;
        let carouselHtml = `
        <div id="${carouselId}" class="carousel slide w-100 h-100" data-bs-ride="carousel">
          <div class="carousel-inner h-100">
            ${data.imagenes
              .map(
                (ruta, index) => `
              <div class="carousel-item h-100 ${index === 0 ? "active" : ""}">
                <div class="d-flex align-items-center justify-content-center h-100" style="min-height: 400px; background: #000;">
                  <img src="${ruta}" 
                       class="d-block mw-100 mh-100" 
                       style="object-fit: contain;" 
                       alt="Imagen negocio">
                </div>
              </div>
            `
              )
              .join("")}
          </div>
          ${data.imagenes.length > 1 ? `
          <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true" ></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
          ` : ""}
        </div>
      `;
        contenedor.innerHTML = carouselHtml;
        
        // Inicializar el carousel manualmente si es necesario
        const carouselElem = document.getElementById(carouselId);
        if (window.bootstrap && window.bootstrap.Carousel) {
          new bootstrap.Carousel(carouselElem);
        }
      } else {
        contenedor.innerHTML = `
          <div class="text-white text-center">
            <i class="bi bi-image-fill fs-1 mb-3 opacity-50"></i>
            <p>Este negocio aún no tiene imágenes en su galería.</p>
          </div>
        `;
      }
    })
    .catch(err => {
      contenedor.innerHTML = `<div class="text-danger">Error al cargar imágenes</div>`;
      console.error(err);
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
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".btn-pagar");
  if (!btn) return;

  const id = btn.dataset.id;

  Swal.fire({
    title: "¿Estás seguro?",
    text: "¿Confirmas que el negocio ha pagado su cuota?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, confirmar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("controladores/controladorNegocios.php", {
        method: "POST",
        body: new URLSearchParams({
          ope: "PAGAR",
          ID_Negocio: id,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Éxito", "Fecha de pago confirmada", "success");
            listarMiembros(); // refrescar la lista
          } else {
            Swal.fire(
              "Error",
              data.msg || "No se pudo confirmar el pago",
              "error"
            );
          }
        })
        .catch((error) => {
          Swal.fire(
            "Error",
            "Problema con el servidor: " + error.message,
            "error"
          );
        });
    }
  });
});

function cargarCategoriasFiltro() {
  const container = document.getElementById("categoryFilterList");
  if (!container) return;

  const params = new URLSearchParams();
  params.append("ope", "LISTAUSUARIOS");
  params.append("registrosPorPagina", 100);

  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        let html = `
          <label class="category-filter-item">
            <input type="checkbox" class="cat-checkbox" value="0">
            <span>Sin categor&iacute;a</span>
          </label>
        `;
        data.lista.forEach((cat) => {
          html += `
            <label class="category-filter-item">
              <input type="checkbox" class="cat-checkbox" value="${cat.ID_Categoria}">
              <span>${escapeHTML(cat.Descripcion)}</span>
            </label>
          `;
        });
        container.innerHTML = html;

        // Agregar listeners
        container.querySelectorAll(".cat-checkbox").forEach((cb) => {
          cb.addEventListener("change", () => {
            aplicarFiltros();
          });
        });
      } else {
        container.innerHTML = '<div class="filter-category-loading text-danger">Error al cargar</div>';
      }
    })
    .catch(() => {
      container.innerHTML = '<div class="filter-category-loading text-danger">Error de red</div>';
    });
}

