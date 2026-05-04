// --- State Variables ---
let paginaActual = 1;
const registrosPorPagina = 10;
let filtrosActuales = {};
let filtroDebounceTimer = null;

function iniciarModuloCupones() {
  const formUsuario = document.querySelector("#formPromocion");
  if (formUsuario) {
    formUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      agregarUsuario();
    });
  }

  const formACupon = document.querySelector("#formAgregarC");
  if (formACupon) {
    formACupon.addEventListener("submit", (event) => {
      event.preventDefault();
      // El submit se maneja abajo con un listener directo al form
    });
  }

  const contenedorLista = document.querySelector("#contenedor");
  if (contenedorLista) {
    contenedorLista.addEventListener("click", (event) => {
      const target = event.target.closest("button, .qr-code");
      if (!target) return;

      const id = target.dataset.id;

      if (target.classList.contains("btn-editar")) {
        cargarUsuario(id);
      } else if (target.classList.contains("btn-eliminar")) {
        eliminarUsuario(id);
      } else if (target.classList.contains("btn-agregar")) {
        cargarCupones(id);
      } else if (target.classList.contains("btn-toggle")) {
        const estatus = target.dataset.status == "1" ? 0 : 1;
        cambiarEstatusCupon(id, estatus);
      }
    });
  }

  const formEditarUsuario = document.querySelector("#formEditar");
  if (formEditarUsuario) {
    formEditarUsuario.addEventListener("submit", (event) => {
      event.preventDefault();
      editarUsuario();
    });
  }

  // --- Toolbar & Filters Logic ---
  const searchInput = document.getElementById("searchInput");
  const searchClear = document.getElementById("searchClear");
  const filterDropdownBtn = document.getElementById("filterDropdownBtn");
  const filterDropdown = document.querySelector(".filter-dropdown");
  const filterOptions = document.querySelectorAll(".filter-option");
  const btnLimpiar = document.getElementById("limpiarFiltros");

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      if (searchClear) searchClear.style.display = searchInput.value ? "flex" : "none";
      clearTimeout(filtroDebounceTimer);
      filtroDebounceTimer = setTimeout(aplicarFiltros, 400);
    });
  }

  if (searchClear) {
    searchClear.addEventListener("click", () => {
      searchInput.value = "";
      searchClear.style.display = "none";
      aplicarFiltros();
    });
  }

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

  if (btnLimpiar) {
    btnLimpiar.addEventListener("click", () => {
      // 1. Reset Search Input
      if (searchInput) {
        searchInput.value = "";
        searchInput.dataset.filterKey = "titulo";
        searchInput.placeholder = "Buscar por Título...";
      }
      
      // 2. Hide Clear Button
      if (searchClear) searchClear.style.display = "none";
      
      // 3. Reset Category/Key Filters
      filterOptions.forEach((o) => o.classList.remove("active"));
      const defOpt = document.querySelector('.filter-option[data-key="titulo"]');
      if (defOpt) defOpt.classList.add("active");
      
      // 4. Reset Status Filters
      const statusBtnsList = document.querySelectorAll(".status-btn");
      statusBtnsList.forEach(b => b.classList.remove("active"));
      const todosBtn = document.querySelector('.status-btn[data-status="todos"]');
      if (todosBtn) todosBtn.classList.add("active");

      // 5. Close Dropdown
      if (filterDropdown) filterDropdown.classList.remove("show");

      // 6. Refresh Results
      aplicarFiltros();
    });
  }

  const statusBtns = document.querySelectorAll(".status-btn");
  statusBtns.forEach(btn => {
      btn.addEventListener("click", () => {
          statusBtns.forEach(b => b.classList.remove("active"));
          btn.classList.add("active");
          aplicarFiltros();
      });
  });

  cargarNegocios();
  listarPromociones();
}

// Expose functions to window for SPA navigation
window.listarPromociones = listarPromociones;
window.cargarNegocios = cargarNegocios;

// Auto-init for AJAX or full load
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", iniciarModuloCupones, { once: true });
} else {
  iniciarModuloCupones();
}

function esFechaExpirada(fechaFin) {
  const hoy = new Date();
  const fin = new Date(fechaFin);
  const hoyStr = hoy.toISOString().split("T")[0];
  const finStr = fin.toISOString().split("T")[0];
  return finStr < hoyStr;
}

export function listarPromociones(filtros = null) {
  // Ensure global variables are available (for AJAX navigation safety)
  if (typeof window.usuarioId === 'undefined' || window.usuarioId === null || window.usuarioId === '') {
    console.warn("usuarioId not available yet:", { usuarioId: window.usuarioId, typeof: typeof window.usuarioId });
    setTimeout(() => listarPromociones(filtros), 100);
    return;
  }

  if (!filtros) {
    const searchInput = document.getElementById("searchInput");
    const key = searchInput ? searchInput.dataset.filterKey : "titulo";
    const val = searchInput ? searchInput.value.trim() : "";
    
    const statusBtn = document.querySelector(".status-btn.active");
    const estado = statusBtn ? statusBtn.dataset.status : "todos";

    filtros = {
      titulo: key === "titulo" ? val : "",
      descripcion: key === "descripcion" ? val : "",
      negocio: key === "negocio" ? val : "",
      estado: estado
    };
  }
  
  filtrosActuales = filtros;

  let params = new URLSearchParams();
  params.append("ope", "LISTARPROMOCIONES");
  params.append("pagina", paginaActual);
  params.append("registrosPorPagina", registrosPorPagina);

  if (filtros.titulo) params.append("titulo", filtros.titulo);
  if (filtros.descripcion) params.append("descripcion", filtros.descripcion);
  if (filtros.negocio) params.append("negocio", filtros.negocio);
  if (filtros.estado) params.append("estado", filtros.estado);

  params.append("usuarioId", window.usuarioId);
  params.append("usuarioTipo", window.usuarioTipo);

  fetch("controladores/controladorCupones.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (!data.success) {
        renderizarError("No se pudieron cargar las promociones.");
        return;
      }
      renderizarPromociones(data.lista);
      actualizarPaginacion(data.totalPaginas);
      
      const contador = document.getElementById("cuponesContador");
      if (contador) {
          contador.textContent = `${data.totalRegistros} cupones encontrados`;
      }
    })
    .catch((error) => {
      console.error("Fetch error en listarPromociones:", error);
      renderizarError("Error al conectarse con el servidor.");
    });
}

function renderizarPromociones(lista) {
  const contenedor = document.querySelector("#contenedor");
  if (!contenedor) return;
  contenedor.innerHTML = "";

  if (!lista || lista.length === 0) {
    contenedor.innerHTML = `
      <div class="no-results w-100 text-center py-5">
          <i class="bi bi-search mb-3 d-block text-secondary" style="font-size: 3rem; opacity: 0.3;"></i>
          <p class="text-secondary fw-medium">No se encontr&oacute; ning&uacute;n cup&oacute;n con los filtros aplicados.</p>
      </div>
    `;
    return;
  }

  lista.forEach((promo) => {
    const expirado = esFechaExpirada(promo.fecha_fin);
    const statusClass = expirado ? "status-expired" : (promo.Estatus == 1 ? "status-active" : "status-inactive");
    const statusText = expirado ? "Expirado" : (promo.Estatus == 1 ? "Activo" : "Inactivo");
    
    const ticketHTML = `
    <article class="ticket" data-id="${promo.ID_Promocion}">
      
      <div class="ticket-actions">
        ${(window.usuarioTipo === "admin" || window.usuarioTipo === "negocio") ? `
          <button class="action-btn add btn-agregar" title="Agregar Cupones" data-id="${promo.ID_Promocion}" data-bs-toggle="modal" data-bs-target="#modalAgregarC">
            <i class="bi bi-plus"></i>
          </button>
          <button class="action-btn toggle btn-toggle" title="Alternar Estado" data-id="${promo.ID_Promocion}" data-status="${promo.Estatus}">
            <i class="bi bi-power"></i>
          </button>
          <button class="action-btn edit btn-editar" title="Editar" data-id="${promo.ID_Promocion}" data-bs-toggle="modal" data-bs-target="#modalEditar">
            <i class="bi bi-pencil-fill"></i>
          </button>
        ` : ""}
        <button class="action-btn delete btn-eliminar" title="Eliminar" data-id="${promo.ID_Promocion}">
          <i class="bi bi-trash-fill"></i>
        </button>
      </div>

      <div class="ticket-top">
        <div class="ticket-header">
          <div class="ticket-business">
            <div class="business-icon"><i class="bi bi-shop"></i></div>
            <div class="ticket-business-meta">
              <span class="business-name">${promo.nombre_negocio}</span>
              <div class="status-badge ${statusClass}">${statusText}</div>
            </div>
          </div>
          <div class="ticket-header-right">
            <div class="ticket-date">${promo.fecha_fin}</div>
          </div>
        </div>
        
        <div class="ticket-body">
          <h3 class="ticket-title">${promo.titulo}</h3>
          <p class="ticket-desc">${promo.descripcion ?? "Disfruta de esta promoci&oacute;n exclusiva en nuestro local."}</p>
        </div>
      </div>

      <div class="ticket-divider"></div>

      <div class="ticket-bottom">
        <div class="ticket-info">
          <div class="info-grid">
            <div class="info-item">
              <label>Cantidad</label>
              <span>${promo.cantidad}</span>
            </div>
            <div class="info-item">
              <label>Canjeados</label>
              <span>${promo.Canjeados}</span>
            </div>
          </div>
        </div>
        
        <div class="ticket-qr-container">
          <div class="qr-code" id="qr-${promo.ID_Promocion}" data-id="${promo.ID_Promocion}" title="Escanear para canjear"></div>
          <span class="qr-label">ESCANEAR</span>
        </div>
      </div>
    </article>
    `;
    contenedor.insertAdjacentHTML("beforeend", ticketHTML);

    const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
    const qrUrl = `${baseUrl}/canje.php?id=${promo.ID_Promocion}`;
    
    new QRCode(document.getElementById(`qr-${promo.ID_Promocion}`), {
      text: qrUrl,
      width: 80,
      height: 80,
      colorDark: "#0f172a",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });
  });
}

function renderizarError(mensaje) {
  const contenedor = document.querySelector("#contenedor");
  if (contenedor) {
      contenedor.innerHTML = `<div class="error-message p-4 text-center text-danger"><p>${mensaje}</p></div>`;
  }
}

function actualizarPaginacion(totalPaginas) {
  const paginacion = document.querySelector("#paginacion");
  if (!paginacion) return;
  paginacion.innerHTML = "";

  let btnAnterior = document.createElement("button");
  btnAnterior.classList.add("btn", "btn-outline-primary");
  btnAnterior.innerHTML = "&laquo;";
  btnAnterior.disabled = paginaActual === 1;
  btnAnterior.addEventListener("click", () => {
    if (paginaActual > 1) {
      paginaActual--;
      listarPromociones(filtrosActuales);
    }
  });
  paginacion.appendChild(btnAnterior);

  let maxVisible = 5;
  let inicio = Math.max(1, paginaActual - Math.floor(maxVisible / 2));
  let fin = Math.min(totalPaginas, inicio + maxVisible - 1);
  if (fin - inicio + 1 < maxVisible) inicio = Math.max(1, fin - maxVisible + 1);

  for (let i = inicio; i <= fin; i++) {
    let boton = document.createElement("button");
    boton.classList.add("btn", i === paginaActual ? "btn-primary" : "btn-outline-primary", "mx-1");
    boton.textContent = i;
    boton.addEventListener("click", () => {
      paginaActual = i;
      listarPromociones(filtrosActuales);
    });
    paginacion.appendChild(boton);
  }

  let btnSiguiente = document.createElement("button");
  btnSiguiente.classList.add("btn", "btn-outline-primary");
  btnSiguiente.innerHTML = "&raquo;";
  btnSiguiente.disabled = paginaActual === totalPaginas;
  btnSiguiente.addEventListener("click", () => {
    if (paginaActual < totalPaginas) {
      paginaActual++;
      listarPromociones(filtrosActuales);
    }
  });
  paginacion.appendChild(btnSiguiente);
}

function aplicarFiltros() {
    paginaActual = 1;
    listarPromociones();
}

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
      if (data.success) {
        Swal.fire("Exito", "Promoci&oacute;n agregada correctamente", "success");
        form.reset();
        document.querySelector("#modalPromocion .btn-close").click();
        listarPromociones();
      } else {
        Swal.fire("Error", data.msg, "error");
      }
    })
    .catch((error) => {
      Swal.fire("Error", "No se pudo agregar la promoci&oacute;n", "error");
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
        document.querySelector("#ID_NegocioEdit").value = data.usuario.ID_Negocio;
      }
    });
}

function editarUsuario() {
  const form = document.querySelector("#formEditar");
  const datos = new FormData(form);
  datos.append("ope", "EDITAR");

  fetch("controladores/controladorCupones.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire("Exito", "Promoci&oacute;n actualizada correctamente", "success");
        document.querySelector("#modalEditar .btn-close").click();
        listarPromociones();
      } else {
        Swal.fire("Error", data.msg, "error");
      }
    });
}

function eliminarUsuario(id) {
  Swal.fire({
    title: "&iquest;Est&aacute;s seguro?",
    text: "¡Esta acci&oacute;n no se puede deshacer!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "S&iacute;, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("controladores/controladorCupones.php", {
        method: "POST",
        body: new URLSearchParams({ ope: "ELIMINAR", ID_Usuario: id }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Eliminado", "Promoci&oacute;n eliminada", "success");
            listarPromociones();
          }
        });
    }
  });
}

function cargarCupones(id) {
  document.getElementById("ID_PromocionC").value = id;
}

const formCupon = document.querySelector("#formAgregarC");
if (formCupon) {
  formCupon.addEventListener("submit", (event) => {
    event.preventDefault();
    const formData = new FormData(formCupon);
    formData.append("ope", "AGREGARCUPON");
    fetch("controladores/controladorCupones.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          Swal.fire("Exito", "Cupones sumados", "success");
          formCupon.reset();
          listarPromociones();
          document.querySelector("#modalAgregarC .btn-close").click();
        }
      });
  });
}

function cargarNegocios() {
  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENERMEMBRESIAS" }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const selects = [
          document.getElementById("ID_Negocio"),
          document.getElementById("ID_NegocioEdit"),
        ];
        selects.forEach((select) => {
          if (!select) return;
          select.innerHTML = "<option value=''>Seleccione un negocio</option>";
          data.negocios.forEach((negocio) => {
            const option = document.createElement("option");
            option.value = negocio.ID_Negocio;
            option.textContent = negocio.nombre_negocio;
            select.appendChild(option);
          });
        });
      }
    });
}

function cambiarEstatusCupon(id, estatus) {
  fetch("controladores/controladorCupones.php", {
    method: "POST",
    body: new URLSearchParams({
      ope: "CAMBIARESTATUS",
      ID_Promocion: id,
      estatus: estatus
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        listarPromociones();
      }
    });
}
