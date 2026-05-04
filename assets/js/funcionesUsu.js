import {
  validaCorreo,
  validaLargo,
  
  validaSoloLetras,
  validaContrasena,
} from "./validaciones.js?v=3.8.2";

function iniciarModuloUsuarios() {
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
      
    
      if (!validaContrasena(clave, 8, 16)) errores++;

      if (errores == 0) agregarUsuario();
    });
  }

  //  editar y eliminar
  const listaUsuarios = document.querySelector("#ListaMiembros");
  if (listaUsuarios) {
    listaUsuarios.addEventListener("click", (event) => {
      event.preventDefault();
      const target = event.target.closest("button"); // 👈 aquí debe ser "event"
    if (!target) return;
      if (target.classList.contains("btn-editar")) {
        cargarUsuario(target.dataset.id);
      } else if (target.classList.contains("btn-eliminar")) {
        eliminarUsuario(target.dataset.id);
      } else if (target.classList.contains("btn-clave")) {
        let userId = target.dataset.id;
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
  initUsersFilters();
}
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
            mensajeError.innerHTML =
              '<i class="bi bi-exclamation-circle"></i> El nombre de usuario ya está en uso.';
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
let filtroDebounceTimer = null;
let ocultarDatosSensibles = false;
let ultimaListaRenderizada = [];
let ultimoTotalRegistros = 0;
let ordenColumnaActual = 'ID_Usuario';
let ordenDireccionActual = 'DESC';

function enmascararCorreo(correo) {
  if (!correo || !correo.includes("@")) return correo;
  const [local, dominio] = correo.split("@");
  if (local.length <= 2) return "**@" + dominio;
  return local[0] + local[1] + "***@" + dominio;
}

function enmascararApellido(apellido) {
  if (!apellido || apellido.length <= 2) return apellido;
  return apellido[0] + "*".repeat(apellido.length - 2) + apellido[apellido.length - 1];
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", iniciarModuloUsuarios, { once: true });
} else {
  iniciarModuloUsuarios();
}

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
  if (filtros.Estatus && filtros.Estatus !== "todos") params.append("estatus", filtros.Estatus);

  // Sorting params
  params.append("ordenColumna", ordenColumnaActual);
  params.append("ordenDireccion", ordenDireccionActual);
  
  console.log(`Listando con orden: ${ordenColumnaActual} ${ordenDireccionActual}`);

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
      renderizarMiembros(data.lista, data.totalRegistros);
      // Store for re-rendering on privacy toggle
      ultimaListaRenderizada = data.lista;
      ultimoTotalRegistros = data.totalRegistros;
      actualizarPaginacion(data.totalPaginas);
      // Update per-page label
      const perPageLabel = document.getElementById("porPaginaLabel");
      if (perPageLabel) {
        perPageLabel.textContent = `Mostrando ${data.lista ? data.lista.length : 0} por página`;
      }
    })
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      renderizarError("Error al conectarse con el servidor.");
    });
}

function renderizarMiembrosLegacy(lista) {
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

function renderizarMiembros(lista, totalRegistros) {
  const contenedor = document.querySelector("#ListaMiembros");
  const contador = document.querySelector("#usuariosContador");
  contenedor.innerHTML = "";

  if (!lista || lista.length === 0) {
    if (contador) contador.textContent = "0 usuarios";
    contenedor.innerHTML = `
            <div class="no-results">
                <i class="bi bi-search"></i>
                <h3>Sin resultados</h3>
                <p>No se encontr&oacute; ning&uacute;n usuario con los filtros aplicados.</p>
            </div>
        `;
    return;
  }

  if (contador) {
    const total = totalRegistros != null ? totalRegistros : lista.length;
    contador.textContent = `${total} ${total === 1 ? "usuario" : "usuarios"}`;
  }

  lista.forEach((miembro) => {
    const id = escapeHTML(miembro.ID_Usuario);
    const nombre = escapeHTML(miembro.Nombre);
    let apellidoP = escapeHTML(miembro.ApellidoP);
    let apellidoM = escapeHTML(miembro.ApellidoM);
    let correo = escapeHTML(miembro.Correo);
    const tipo = escapeHTML(miembro.tipo_usuario);
    const estatus = miembro.Estatus || "activo";

    // Apply masking if privacy mode is on
    if (ocultarDatosSensibles) {
      correo = enmascararCorreo(correo);
      apellidoP = enmascararApellido(apellidoP);
      apellidoM = enmascararApellido(apellidoM);
    }

    const nombreCompleto = `${nombre} ${apellidoP} ${apellidoM}`.trim();
    const iniciales = obtenerIniciales(miembro.Nombre, miembro.ApellidoP);
    const rolClase = tipo.toLowerCase() === "admin" ? "role-admin" : "role-business";
    const estatusTexto = estatus === "activo" ? "Cuenta activa" : "Cuenta inactiva";
    const estatusIcono = estatus === "activo" ? "bi-shield-check" : "bi-shield-x";
    const estatusClase = estatus === "activo" ? "status-activo" : "status-inactivo";

    contenedor.innerHTML += `
            <article class="usuario-card">
                <div class="usuario-card-header">
                    <div class="usuario-avatar" aria-hidden="true">${iniciales}</div>
                    <div class="usuario-card-title">
                        <span class="usuario-id">#${id}</span>
                        <h3>${nombreCompleto}</h3>
                    </div>
                    <span class="role-pill ${rolClase}">${tipo}</span>
                </div>
                <div class="usuario-card-body">
                    <p class="usuario-email"><i class="bi bi-envelope"></i>${correo}</p>
                    <div class="usuario-meta">
                        <span class="${estatusClase}"><i class="bi ${estatusIcono}"></i>${estatusTexto}</span>
                    </div>
                </div>
                <div class="card-buttons" aria-label="Acciones de ${nombreCompleto}">
                    <button class="icon-btn btn-editar" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalEditar" title="Editar usuario" aria-label="Editar ${nombreCompleto}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="icon-btn btn-clave" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalEditarClave" title="Cambiar contrase&ntilde;a" aria-label="Cambiar contrase&ntilde;a de ${nombreCompleto}">
                        <i class="bi bi-key"></i>
                    </button>
                    <button class="icon-btn btn-eliminar" data-id="${id}" title="Eliminar usuario" aria-label="Eliminar ${nombreCompleto}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </article>
        `;
  });
}

function escapeHTML(valor) {
  return String(valor ?? "").replace(/[&<>"']/g, (caracter) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  }[caracter]));
}

function obtenerIniciales(nombre = "", apellido = "") {
  const primera = String(nombre).trim().charAt(0);
  const segunda = String(apellido).trim().charAt(0);
  return `${primera}${segunda}`.toUpperCase() || "US";
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
  const searchInput = document.getElementById("searchInput");
  const filterKey = searchInput ? searchInput.dataset.filterKey : "Nombre";
  const searchValue = searchInput ? searchInput.value.trim() : "";

  const filtros = {};
  if (searchValue) {
    filtros[filterKey] = searchValue;
  }

  // Get current status filter
  const activeStatus = document.querySelector("#statusToggle .status-btn.active");
  if (activeStatus) {
    filtros.Estatus = activeStatus.dataset.status;
  }

  paginaActual = 1;
  listarMiembros(filtros);
}

function programarAplicacionFiltros() {
  if (filtroDebounceTimer) {
    clearTimeout(filtroDebounceTimer);
  }
  filtroDebounceTimer = setTimeout(() => {
    aplicarFiltros();
  }, 260);
}

function initUsersFilters() {
  const searchInput = document.getElementById("searchInput");
  const searchClear = document.getElementById("searchClear");
  const searchWrapper = document.getElementById("searchWrapper");
  const btnToggle = document.getElementById("btnFilterToggle");
  const filterMenu = document.getElementById("filterMenu");
  const filterDropdown = document.getElementById("filterDropdown");
  const clearAllButton = document.getElementById("limpiarM");
  const filterBadge = document.getElementById("filterBadge");

  if (!searchInput || !btnToggle || !filterMenu) return;

  // --- Search input events ---
  searchInput.addEventListener("input", () => {
    updateClearButton();
    programarAplicacionFiltros();
  });

  searchInput.addEventListener("focus", () => {
    searchWrapper.classList.add("focused");
  });

  searchInput.addEventListener("blur", () => {
    searchWrapper.classList.remove("focused");
  });

  // --- Clear single input ---
  if (searchClear) {
    searchClear.addEventListener("mousedown", (e) => e.preventDefault());
    searchClear.addEventListener("click", () => {
      searchInput.value = "";
      updateClearButton();
      aplicarFiltros();
      searchInput.focus();
    });
  }

  // --- Toggle dropdown ---
  btnToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    filterDropdown.classList.toggle("open");
  });

  // --- Close dropdown on outside click ---
  document.addEventListener("click", (e) => {
    if (!filterDropdown.contains(e.target)) {
      filterDropdown.classList.remove("open");
    }
  });

  // --- Filter option selection ---
  const filterOptions = filterMenu.querySelectorAll(".filter-option");
  filterOptions.forEach((option) => {
    option.addEventListener("click", () => {
      // Remove active from all, set on this one
      filterOptions.forEach((o) => o.classList.remove("active"));
      option.classList.add("active");

      // Update search input
      const key = option.dataset.key;
      const placeholder = option.dataset.placeholder;
      searchInput.dataset.filterKey = key;
      searchInput.placeholder = placeholder;
      searchInput.value = "";
      searchInput.focus();
      updateClearButton();

      // Close dropdown
      filterDropdown.classList.remove("open");

      aplicarFiltros();
    });
  });

  // --- Status toggle ---
  const statusButtons = document.querySelectorAll("#statusToggle .status-btn");
  statusButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      statusButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      aplicarFiltros();
    });
  });

  // --- Clear all ---
  if (clearAllButton) {
    clearAllButton.addEventListener("click", () => {
      searchInput.value = "";
      searchInput.dataset.filterKey = "Nombre";
      searchInput.placeholder = "Buscar por nombre...";
      updateClearButton();

      // Reset filter option selection
      filterOptions.forEach((o) => o.classList.remove("active"));
      const defaultOption = filterMenu.querySelector('[data-key="Nombre"]');
      if (defaultOption) defaultOption.classList.add("active");

      // Reset status
      statusButtons.forEach((b) => b.classList.remove("active"));
      const todosBtn = document.querySelector('#statusToggle [data-status="todos"]');
      if (todosBtn) todosBtn.classList.add("active");

      // Reset Sort
      sortSeleccionado = null;
      ordenColumnaActual = "ID_Usuario";
      ordenDireccionActual = "DESC";
      updateSortButtons();

      // Reset Privacy (optional, but cleaner)
      ocultarDatosSensibles = false;
      if (privacyBtn) {
        privacyBtn.classList.remove("active");
        const pIcon = privacyBtn.querySelector("i");
        const pLabel = privacyBtn.querySelector("span");
        if (pIcon) pIcon.className = "bi bi-eye-slash";
        if (pLabel) pLabel.textContent = "Ocultar datos";
      }

      aplicarFiltros();
    });
  }

  function updateClearButton() {
    if (!searchClear) return;
    const hasValue = searchInput.value.trim().length > 0;
    searchClear.style.opacity = hasValue ? "1" : "0";
    searchClear.style.pointerEvents = hasValue ? "auto" : "none";
  }

  updateClearButton();

  // --- Privacy toggle ---
  const privacyBtn = document.getElementById("btnPrivacyToggle");
  if (privacyBtn) {
    privacyBtn.addEventListener("click", () => {
      ocultarDatosSensibles = !ocultarDatosSensibles;
      privacyBtn.classList.toggle("active", ocultarDatosSensibles);
      const icon = privacyBtn.querySelector("i");
      const label = privacyBtn.querySelector("span");
      if (icon) {
        icon.className = ocultarDatosSensibles ? "bi bi-eye" : "bi bi-eye-slash";
      }
      if (label) {
        label.textContent = ocultarDatosSensibles ? "Mostrar datos" : "Ocultar datos";
      }
      // Re-render with stored data
      if (ultimaListaRenderizada.length > 0) {
        renderizarMiembros(ultimaListaRenderizada, ultimoTotalRegistros);
      }
    });
  }

  // --- Sort toggles ---
  const btnSortAz = document.getElementById("btnSortAz");
  const btnSortNum = document.getElementById("btnSortNum");
  
  // El estado inicial es el default del sistema
  let sortSeleccionado = null; // null | 'az' | 'num'

  if (btnSortAz) {
    btnSortAz.addEventListener("click", () => {
      if (sortSeleccionado === "az" && ordenDireccionActual === "ASC") {
        ordenDireccionActual = "DESC";
      } else if (sortSeleccionado === "az" && ordenDireccionActual === "DESC") {
        // 3er click: Apagar
        sortSeleccionado = null;
        ordenColumnaActual = "ID_Usuario";
        ordenDireccionActual = "DESC";
      } else {
        // 1er click
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
        // 3er click: Apagar
        sortSeleccionado = null;
        ordenColumnaActual = "ID_Usuario";
        ordenDireccionActual = "DESC";
      } else {
        // 1er click
        sortSeleccionado = "num";
        ordenColumnaActual = "ID_Usuario";
        ordenDireccionActual = "ASC";
      }
      updateSortButtons();
      paginaActual = 1;
      listarMiembros();
    });
  }

  function updateSortButtons() {
    if (btnSortAz) {
      const icon = btnSortAz.querySelector("i");
      const isActive = sortSeleccionado === "az";
      btnSortAz.classList.toggle("active", isActive);
      if (icon) {
        icon.className = (isActive && ordenDireccionActual === "DESC") 
          ? "bi bi-sort-alpha-up" 
          : "bi bi-sort-alpha-down";
      }
    }
    if (btnSortNum) {
      const icon = btnSortNum.querySelector("i");
      const isActive = sortSeleccionado === "num";
      btnSortNum.classList.toggle("active", isActive);
      if (icon) {
        icon.className = (isActive && ordenDireccionActual === "DESC") 
          ? "bi bi-sort-numeric-up" 
          : "bi bi-sort-numeric-down";
      }
    }
  }

  // Llamada inicial para reflejar estado
  updateSortButtons();
}

function agregarUsuario() {
  const form = document.querySelector("#formAgregar");
  const datos = new FormData(form);
  if (datos.get("Contra") !== datos.get("ContraConfirmar")) {
    Swal.fire("Error", "Las contraseñas no coinciden", "error");
    return;
  }

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

  fetch("controladores/controladorUsuarios.php", {
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
