// --- State Variables ---
let paginaActual = 1;
const registrosPorPagina = 9;
let filtrosActuales = {};
let filtroDebounceTimer = null;
let searchKey = "Nombre"; // Por defecto buscar por nombre
let ordenAz = "ASC";
let ordenNum = ""; // Filtro 1-9 (vacío por defecto)
let datosOcultos = false;

function iniciarModuloCategorias() {
  // --- Modals & Forms ---
  const formAgregar = document.querySelector("#formAgregar");
  if (formAgregar) {
    formAgregar.addEventListener("submit", (e) => {
      e.preventDefault();
      agregarCategoria();
    });
  }

  const formEditar = document.querySelector("#formEditar");
  if (formEditar) {
    formEditar.addEventListener("submit", (e) => {
      e.preventDefault();
      editarCategoria();
    });
  }

  // --- Card Actions (Delegation) ---
  const contenedor = document.querySelector("#ListaMiembros");
  if (contenedor) {
    contenedor.addEventListener("click", (e) => {
      const target = e.target.closest("button");
      if (!target) return;

      const id = target.dataset.id;
      if (target.classList.contains("btn-editar")) {
        cargarCategoria(id);
      } else if (target.classList.contains("btn-eliminar")) {
        eliminarCategoria(id);
      }
    });

    // Delegación para "Ver negocios"
    contenedor.addEventListener("click", (e) => {
      const target = e.target.closest(".btn-card-action");
      if (!target) return;
      e.preventDefault();
      const id = target.dataset.id;
      const nombre = target.dataset.nombre;
      verNegocios(id, nombre);
    });
  }

  // --- Toolbar Logic ---
  const searchInput = document.getElementById("searchInput");
  const searchClear = document.getElementById("searchClear");
  const btnLimpiar = document.getElementById("limpiarM");
  const btnSortAz = document.getElementById("btnSortAz");
  const btnSortNum = document.getElementById("btnSortNum"); // Nuevo ID
  const btnPrivacy = document.getElementById("btnPrivacyToggle");
  const filterBtn = document.getElementById("filterDropdownBtn");
  const filterMenu = document.getElementById("filterMenu");
  const filterOptions = document.querySelectorAll(".filter-option");

  // --- Dropdown Logic ---
  if (filterBtn && filterMenu) {
    filterBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      filterBtn.parentElement.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {
      if (!filterMenu.contains(e.target) && !filterBtn.contains(e.target)) {
        filterBtn.parentElement.classList.remove("show");
      }
    });

    filterOptions.forEach(opt => {
      opt.addEventListener("click", () => {
        filterOptions.forEach(o => o.classList.remove("active"));
        opt.classList.add("active");
        searchKey = opt.dataset.key;
        if (searchInput) {
          searchInput.placeholder = opt.dataset.placeholder;
          searchInput.value = "";
        }
        filterBtn.parentElement.classList.remove("show");
        aplicarFiltros();
      });
    });
  }

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      if (searchClear) searchClear.style.display = searchInput.value ? "block" : "none";
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

  if (btnLimpiar) {
    btnLimpiar.addEventListener("click", () => {
      if (searchInput) {
        searchInput.value = "";
        searchInput.placeholder = "Buscar categor&iacute;a...";
        if (searchClear) searchClear.style.display = "none";
      }
      searchKey = "Nombre";
      filterOptions.forEach(o => o.classList.toggle("active", o.dataset.key === "Nombre"));
      ordenAz = "ASC";
      ordenNum = "";
      if (btnSortAz) btnSortAz.classList.remove("active");
      if (btnSortNum) btnSortNum.classList.remove("active");
      aplicarFiltros();
    });
  }

  if (btnSortAz) {
    btnSortAz.addEventListener("click", () => {
      ordenNum = ""; // Desactivar el otro
      if (btnSortNum) btnSortNum.classList.remove("active");
      
      ordenAz = (ordenAz === "ASC") ? "DESC" : "ASC";
      btnSortAz.classList.toggle("active", ordenAz === "DESC");
      aplicarFiltros();
    });
  }

  if (btnSortNum) {
    btnSortNum.addEventListener("click", () => {
      ordenAz = "ASC"; // Reset el otro
      if (btnSortAz) btnSortAz.classList.remove("active");

      ordenNum = (ordenNum === "" || ordenNum === "DESC") ? "ASC" : "DESC";
      btnSortNum.classList.toggle("active", ordenNum !== "");
      aplicarFiltros();
    });
  }

  if (btnPrivacy) {
    btnPrivacy.addEventListener("click", () => {
      datosOcultos = !datosOcultos;
      btnPrivacy.classList.toggle("active", datosOcultos);
      document.querySelectorAll(".categoria-id").forEach(el => {
        el.style.display = datosOcultos ? "none" : "block";
      });
    });
  }

  // --- Image Previews ---
  handleImagePreview("#Imagen", "#previewAgregar");
  handleImagePreview("#ImagenEdit", "#previewEditar");

  listarCategorias();
}

function handleImagePreview(inputSelector, previewSelector) {
  const input = document.querySelector(inputSelector);
  const preview = document.querySelector(previewSelector);
  if (input && preview) {
    input.addEventListener("change", () => {
      const file = input.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
      }
    });
  }
}

export function listarCategorias(filtros = null) {
  if (!filtros) {
    const searchInput = document.getElementById("searchInput");
    const val = searchInput ? searchInput.value.trim() : "";
    filtros = { Nombre: val };
  }
  filtrosActuales = filtros;

  let params = new URLSearchParams();
  params.append("ope", "LISTAUSUARIOS");
  params.append("pagina", paginaActual);
  params.append("registrosPorPagina", registrosPorPagina);
  params.append("orden", ordenAz);
  params.append("ordenNum", ordenNum);
  params.append("searchKey", searchKey); // Enviamos qué campo estamos buscando
  if (filtros.Nombre) params.append("nombre", filtros.Nombre);

  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((r) => {
      if (!r.ok) throw new Error("Error HTTP: " + r.status);
      return r.text(); // Get as text first to debug
    })
    .then((text) => {
      try {
        const data = JSON.parse(text);
        if (!data.success) {
          renderizarError(data.msg || "No se pudieron cargar las categorías.");
          return;
        }
        renderizarCategorias(data.lista);
        actualizarPaginacion(data.totalPaginas);
      } catch (err) {
        console.error("Error parseando JSON:", err, "Texto recibido:", text);
        renderizarError("Error en el formato de datos del servidor.");
      }
    })
    .catch((err) => {
      console.error("Error en fetch:", err);
      renderizarError("Error de conexión: " + err.message);
    });
}

function renderizarCategorias(lista) {
  const contenedor = document.querySelector("#ListaMiembros");
  if (!contenedor) return;
  contenedor.innerHTML = "";

  if (!lista || lista.length === 0) {
    contenedor.innerHTML = `
      <div class="no-results">
        <i class="bi bi-folder-x"></i>
        <p>No se encontraron categor&iacute;as.</p>
      </div>
    `;
    return;
  }

  lista.forEach((cat) => {
    const color = cat.Color || "#5e0a9e";
    const colorSoft = color + "15"; // Add 15 for ~8% opacity
    
    const imgHtml = cat.Imagen 
      ? `<img src="${cat.Imagen}" class="folder-thumb">` 
      : `<i class="ri-price-tag-3-line folder-thumb-placeholder"></i>`;

    contenedor.innerHTML += `
      <article class="categoria-folder-card" style="--category-color: ${color}; --category-color-soft: ${colorSoft}">
        <div class="folder-header">
          <div class="folder-icon-wrapper">
            ${imgHtml}
          </div>
          <div class="folder-actions-dropdown">
            <button class="btn-folder-actions" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ri-more-2-fill"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
              <li><button class="dropdown-item btn-editar" data-id="${cat.ID_Categoria}" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="ri-pencil-line me-2"></i> Editar</button></li>
              <li><hr class="dropdown-divider"></li>
              <li><button class="dropdown-item text-danger btn-eliminar" data-id="${cat.ID_Categoria}"><i class="ri-delete-bin-line me-2"></i> Eliminar</button></li>
            </ul>
          </div>
        </div>
        
        <div class="folder-title-area">
          <span class="categoria-id" style="display: ${datosOcultos ? 'none' : 'block'}">ID: #${cat.ID_Categoria}</span>
          <h3 class="categoria-name">${escapeHTML(cat.Descripcion)}</h3>
        </div>

        <div class="folder-footer">
          <button type="button" class="btn-card-action" data-id="${cat.ID_Categoria}" data-nombre="${escapeHTML(cat.Descripcion)}">
            Ver negocios <i class="ri-arrow-right-s-line ms-1"></i>
          </button>
        </div>
      </article>
    `;
  });
}

// --- Business Management Functions ---
async function verNegocios(idCategoria, nombreCategoria) {
  const modal = new bootstrap.Modal(document.getElementById('modalVerNegocios'));
  const contenedor = document.getElementById('listaNegociosCategoria');
  const titulo = document.getElementById('tituloModalNegocios');
  
  titulo.innerText = `Negocios en: ${nombreCategoria}`;
  contenedor.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary spinner-border-sm"></div></div>';
  
  modal.show();

  const formData = new FormData();
  formData.append("ope", "LISTARNEGOCIOS");
  formData.append("ID_Categoria", idCategoria);

  try {
    const res = await fetch("controladores/controladorCategorias.php", { method: "POST", body: formData });
    const data = await res.json();

    if (data.success) {
      if (data.lista.length === 0) {
        contenedor.innerHTML = '<div class="text-center p-4 text-muted small">No hay negocios asignados.</div>';
      } else {
        contenedor.innerHTML = data.lista.map(neg => `
          <div class="business-list-item">
            <span class="business-name-modal">${escapeHTML(neg.nombre_negocio)}</span>
            <button type="button" class="btn-move-business" onclick="abrirMoverNegocio(${neg.ID_Negocio}, '${escapeHTML(neg.nombre_negocio)}', ${idCategoria})">
              Mover
            </button>
          </div>
        `).join('');
      }
    }
  } catch (error) {
    contenedor.innerHTML = '<div class="alert alert-danger p-2 small">Error al cargar negocios.</div>';
  }
}

let categoriasCargadas = []; // Cache para el selector

async function abrirMoverNegocio(idNegocio, nombreNegocio, idCatActual) {
  const modalElem = document.getElementById('modalMoverNegocio');
  const modal = new bootstrap.Modal(modalElem);
  document.getElementById('moverNegocioId').value = idNegocio;
  const select = document.getElementById('selectNuevaCategoria');
  
  // Mostrar qué negocio estamos moviendo
  modalElem.querySelector('.modal-title').innerText = `Mover: ${nombreNegocio}`;
  
  select.innerHTML = '<option value="">Cargando categorías...</option>';
  modal.show();

  // Si ya tenemos las categorías en cache, usarlas
  if (categoriasCargadas.length === 0) {
    const formData = new FormData();
    formData.append("ope", "LISTAUSUARIOS");
    formData.append("registrosPorPagina", 100);
    const res = await fetch("controladores/controladorCategorias.php", { method: "POST", body: formData });
    const data = await res.json();
    if (data.success) {
      categoriasCargadas = data.lista;
    }
  }

  select.innerHTML = categoriasCargadas
    .filter(c => c.ID_Categoria != idCatActual)
    .map(c => `<option value="${c.ID_Categoria}">${escapeHTML(c.Descripcion)}</option>`)
    .join('');
}

async function finalizarMoverNegocio() {
  const idNegocio = document.getElementById('moverNegocioId').value;
  const idNuevaCat = document.getElementById('selectNuevaCategoria').value;

  if (!idNuevaCat) {
    alert("Selecciona una categoría.");
    return;
  }

  const formData = new FormData();
  formData.append("ope", "MOVERNEGOCIO");
  formData.append("ID_Negocio", idNegocio);
  formData.append("ID_Categoria", idNuevaCat);

  try {
    const res = await fetch("controladores/controladorCategorias.php", { method: "POST", body: formData });
    const data = await res.json();

    if (data.success) {
      // Cerrar modales y refrescar
      bootstrap.Modal.getInstance(document.getElementById('modalMoverNegocio')).hide();
      bootstrap.Modal.getInstance(document.getElementById('modalVerNegocios')).hide();
      listarCategorias();
      Swal.fire({ icon: 'success', title: '¡Movido!', text: 'El negocio ha cambiado de categoría.', timer: 1500 });
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo mover el negocio.' });
  }
}

// Attach to window to fix ReferenceError from onclick
window.verNegocios = verNegocios;
window.abrirMoverNegocio = abrirMoverNegocio;
window.finalizarMoverNegocio = finalizarMoverNegocio;


/**
 * Determina si el texto debe ser blanco o negro según el fondo
 * @param {string} hexcolor 
 * @returns {string} #ffffff o #1e293b
 */
function getContrastYIQ(hexcolor) {
  if (!hexcolor || hexcolor === "null" || hexcolor === "undefined") return "#1e293b";
  hexcolor = hexcolor.replace("#", "");
  if (hexcolor.length === 3) {
    hexcolor = hexcolor.split('').map(s => s + s).join('');
  }
  const r = parseInt(hexcolor.substr(0, 2), 16);
  const g = parseInt(hexcolor.substr(2, 2), 16);
  const b = parseInt(hexcolor.substr(4, 2), 16);
  const yiq = (r * 299 + g * 587 + b * 114) / 1000;
  return yiq >= 128 ? "#1e293b" : "#ffffff";
}


function aplicarFiltros() {
  paginaActual = 1;
  listarCategorias();
}

function agregarCategoria() {
  const form = document.querySelector("#formAgregar");
  const datos = new FormData(form);
  datos.append("ope", "AGREGAR");

  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    body: datos,
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        Swal.fire("¡Éxito!", "Categoría agregada correctamente.", "success");
        form.reset();
        document.querySelector("#previewAgregar").innerHTML = '<i class="bi bi-image text-muted" style="font-size: 2rem;"></i>';
        bootstrap.Modal.getInstance(document.querySelector("#modalAgregar")).hide();
        listarCategorias();
      } else {
        Swal.fire("Error", data.msg || "No se pudo agregar la categoría.", "error");
      }
    });
}

function cargarCategoria(id) {
  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENER", ID_Categoria: id }),
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        document.querySelector("#formEditar #ID_Categoria").value = data.usuario.ID_Categoria;
        document.querySelector("#NombreEdit").value = data.usuario.Descripcion;
        document.querySelector("#ColorEdit").value = data.usuario.Color || "#7b68ee";
        document.querySelector("#RutaImagenActual").value = data.usuario.Imagen || "";
        
        const preview = document.querySelector("#previewEditar");
        if (data.usuario.Imagen) {
          preview.innerHTML = `<img src="${data.usuario.Imagen}" alt="Current">`;
        } else {
          preview.innerHTML = '<i class="bi bi-image text-muted" style="font-size: 2rem;"></i>';
        }
      }
    });
}

function editarCategoria() {
  const form = document.querySelector("#formEditar");
  const datos = new FormData(form);
  datos.append("ope", "EDITAR");

  fetch("controladores/controladorCategorias.php", {
    method: "POST",
    body: datos,
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        Swal.fire("¡Actualizado!", "Categoría actualizada correctamente.", "success");
        bootstrap.Modal.getInstance(document.querySelector("#modalEditar")).hide();
        listarCategorias();
      } else {
        Swal.fire("Error", data.msg || "No se pudo actualizar.", "error");
      }
    });
}

function eliminarCategoria(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Los negocios en esta categoría se quedarán sin clasificación.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("controladores/controladorCategorias.php", {
        method: "POST",
        body: new URLSearchParams({ ope: "ELIMINAR", ID_Categoria: id }),
      })
        .then((r) => r.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Eliminada", "La categoría ha sido eliminada.", "success");
            listarCategorias();
          } else {
            Swal.fire("Error", data.msg || "No se pudo eliminar.", "error");
          }
        });
    }
  });
}

function renderizarError(msg) {
  const contenedor = document.querySelector("#ListaMiembros");
  if (contenedor) {
    contenedor.innerHTML = `<div class="col-12 text-center text-danger p-5"><i class="bi bi-exclamation-circle me-2"></i> ${msg}</div>`;
  }
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
    listarCategorias();
  }));

  for (let i = 1; i <= totalPaginas; i++) {
    if (i === 1 || i === totalPaginas || (i >= paginaActual - 1 && i <= paginaActual + 1)) {
      paginacion.appendChild(createBtn(i, false, () => {
        paginaActual = i;
        listarCategorias();
      }, i === paginaActual));
    }
  }

  paginacion.appendChild(createBtn("&raquo;", paginaActual === totalPaginas, () => {
    paginaActual++;
    listarCategorias();
  }));
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

// Auto-init
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", iniciarModuloCategorias, { once: true });
} else {
  iniciarModuloCategorias();
}
