// funcionesTrabajos.js
let paginaActual = 1;
const registrosPorPagina = 10;
let filtrosActuales = {};
let promocionesLocales = []; // Cache para detalles
let trabajoSeleccionadoId = null;

// Expose functions to window for SPA navigation
window.listarPromociones = listarPromociones;
window.buscarTrabajos = (val) => {
    paginaActual = 1;
    filtrosActuales.titulo = val;
    listarPromociones();
};

function iniciarModuloTrabajos() {
  // Configurar formularios
  const formPromocion = document.querySelector("#formPromocion");
  if (formPromocion) {
    formPromocion.addEventListener("submit", (event) => {
      event.preventDefault();
      agregarTrabajo();
    });
  }

  const formEditar = document.querySelector("#formEditar");
  if (formEditar) {
    formEditar.addEventListener("submit", (event) => {
      event.preventDefault();
      editarTrabajo();
    });
  }

  // Delegación para clicks en la lista
  const contenedor = document.querySelector("#contenedor");
  if (contenedor) {
    contenedor.addEventListener("click", (event) => {
      const card = event.target.closest(".job-horizontal-card");
      const btnEdit = event.target.closest(".btn-editar");
      const btnDelete = event.target.closest(".btn-delete");
      const btnToggle = event.target.closest(".btn-toggle");

      if (btnEdit) {
        event.preventDefault();
        event.stopPropagation();
        cargarTrabajoParaEditar(btnEdit.dataset.id);
        return;
      }
      if (btnDelete) {
        event.preventDefault();
        event.stopPropagation();
        eliminarTrabajo(btnDelete.dataset.id);
        return;
      }
      if (btnToggle) {
        event.preventDefault();
        event.stopPropagation();
        toggleEstatusTrabajo(btnToggle.dataset.id, btnToggle.dataset.status);
        return;
      }

      if (card) {
        // Remover clase activa de otros
        document.querySelectorAll(".job-horizontal-card").forEach(c => c.classList.remove("active-card"));
        card.classList.add("active-card");

        const id = card.dataset.id;
        trabajoSeleccionadoId = id;
        const trabajo = promocionesLocales.find(p => p.ID_Trabajo == id);
        if (trabajo) renderizarDetalle(trabajo);
      }
    });
  }

  const detailPanel = document.getElementById("jobDetailPanel");
  if (detailPanel) {
    detailPanel.addEventListener("click", (event) => {
      const editBtn = event.target.closest(".detail-edit-job");
      const toggleBtn = event.target.closest(".detail-toggle-job");

      if (editBtn) {
        event.preventDefault();
        cargarTrabajoParaEditar(editBtn.dataset.id);
      }

      if (toggleBtn) {
        event.preventDefault();
        toggleEstatusTrabajo(toggleBtn.dataset.id, toggleBtn.dataset.status);
      }
    });
  }

  // Inicializar filtros
  const searchInput = document.getElementById("globalSearchInput") || document.getElementById("searchInput");
  const jobPills = document.querySelectorAll(".job-pill");

  jobPills.forEach(pill => {
    pill.addEventListener("click", () => {
      if (pill.id === "limpiarFiltros") {
        if (searchInput) searchInput.value = "";
        jobPills.forEach(p => p.classList.remove("active"));
        const allPill = document.querySelector('.job-pill[data-filter="all"]');
        if (allPill) allPill.classList.add("active");
      } else {
        jobPills.forEach(p => p.classList.remove("active"));
        pill.classList.add("active");
      }
      aplicarFiltros();
    });
  });

  listarPromociones();
  cargarNegocios();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", iniciarModuloTrabajos, { once: true });
} else {
  iniciarModuloTrabajos();
}

export function listarPromociones(filtros = filtrosActuales) {
  filtrosActuales = filtros;
  let params = new URLSearchParams();
  params.append("ope", "LISTARPROMOCIONES");
  params.append("pagina", paginaActual);
  params.append("registrosPorPagina", registrosPorPagina);

  if (filtros.titulo) params.append("titulo", filtros.titulo);
  if (filtros.horario) params.append("horario", filtros.horario);
  params.append("usuarioId", usuarioId);
  params.append("usuarioTipo", usuarioTipo);

  fetch("controladores/controladorTrabajos.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then(r => r.json())
    .then(data => {
      if (!data.success) {
        renderizarError(data.msg || "Error al cargar trabajos.");
        return;
      }
      promocionesLocales = data.lista;
      renderizarPromociones(data.lista);
      actualizarPaginacion(data.totalPaginas);
      const contador = document.getElementById("jobsContador");
      if (contador) {
        const total = data.totalRegistros ?? data.lista.length;
        contador.textContent = `${total} vacantes`;
      }
      
      // Mantener seleccionado el trabajo actual; si ya no existe, usar el primero.
      if (data.lista.length > 0) {
        const trabajoActivo = data.lista.find(t => String(t.ID_Trabajo) === String(trabajoSeleccionadoId)) || data.lista[0];
        trabajoSeleccionadoId = trabajoActivo.ID_Trabajo;
        renderizarDetalle(trabajoActivo);
        setTimeout(() => {
            document.querySelectorAll(".job-horizontal-card").forEach(c => c.classList.remove("active-card"));
            const activeCard = document.querySelector(`.job-horizontal-card[data-id="${trabajoSeleccionadoId}"]`);
            if (activeCard) activeCard.classList.add("active-card");
        }, 100);
      }
    })
    .catch(e => {
      console.error(e);
      renderizarError("Error de conexión.");
    });
}

function renderizarPromociones(lista) {
  const contenedor = document.querySelector("#contenedor");
  contenedor.innerHTML = "";

  if (!lista || lista.length === 0) {
    contenedor.innerHTML = `<div class="text-center p-5 text-muted"><p>No hay resultados.</p></div>`;
    return;
  }

  lista.forEach(promo => {
    const inicial = promo.nombre_negocio ? promo.nombre_negocio.charAt(0).toUpperCase() : 'W';
    const salario = promo.Salario ? `$${parseFloat(promo.Salario).toLocaleString()}` : "Confidencial";
    const ubicacion = obtenerTextoUbicacionNegocio(promo);
    const horario = promo.Tipo_Horario && promo.Tipo_Horario.trim() ? promo.Tipo_Horario : "Sin turno";
    
    contenedor.innerHTML += `
    <article class="job-horizontal-card${String(promo.ID_Trabajo) === String(trabajoSeleccionadoId) ? ' active-card' : ''}" data-id="${promo.ID_Trabajo}">
      <div class="job-logo-wrapper"><span>${inicial}</span></div>
      <div class="job-info-main">
        <h3 class="job-title-h3">${promo.titulo}</h3>
        <span class="job-company-name">${promo.nombre_negocio}</span>
        <div class="job-tags-row">
          <span class="job-tag">${horario}</span>
          <span class="job-tag job-location-tag"><i class="ri-map-pin-line"></i>${ubicacion}</span>
        </div>
      </div>
      <div class="job-stats-area">
        <span class="job-salary-value">${salario}</span>
      </div>
      <div class="job-card-actions">
        <button class="btn-job-action btn-toggle" data-id="${promo.ID_Trabajo}" data-status="${promo.Estatus}">
          <i class="${promo.Estatus == 1 ? 'ri-eye-line' : 'ri-eye-off-line'}"></i>
        </button>
        <button class="btn-job-action btn-editar" data-id="${promo.ID_Trabajo}">
          <i class="ri-edit-2-line"></i>
        </button>
      </div>
    </article>`;
  });
}

function renderizarDetalle(t) {
  const panel = document.getElementById("jobDetailPanel");
  const salario = t.Salario ? `$${parseFloat(t.Salario).toLocaleString()}` : "No especificado";
  const vacantes = t.PerRequeridas || 1;
  const estatusTexto = t.Estatus == 1 ? "Publicado" : "Oculto";
  const ubicacion = obtenerUbicacionDetalle(t);
  const horario = t.Tipo_Horario && t.Tipo_Horario.trim() ? t.Tipo_Horario : "Sin turno";
  
  panel.innerHTML = `
    <div class="job-detail-header">
      <div class="job-detail-kicker">
        <span>${estatusTexto}</span>
        <span>${horario}</span>
      </div>
      <h2 class="job-detail-title">${t.titulo}</h2>
      <span class="job-detail-company">${t.nombre_negocio}</span>
      <div class="job-detail-summary">
        <div class="job-summary-card">
          <i class="ri-money-dollar-circle-line"></i>
          <span>Salario</span>
          <strong>${salario}</strong>
        </div>
        <div class="job-summary-card">
          <i class="ri-user-follow-line"></i>
          <span>Vacantes</span>
          <strong>${vacantes}</strong>
        </div>
        <div class="job-summary-card">
          <i class="ri-map-pin-line"></i>
          <span>Ubicación</span>
          ${ubicacion}
        </div>
      </div>
      <div class="job-detail-actions">
        <button class="btn btn-primary rounded-pill detail-edit-job" data-id="${t.ID_Trabajo}"><i class="ri-edit-2-line me-1"></i> Editar publicación</button>
        <button class="btn btn-outline-secondary rounded-pill detail-toggle-job" data-id="${t.ID_Trabajo}" data-status="${t.Estatus}"><i class="ri-eye-line me-1"></i> Cambiar estado</button>
      </div>
    </div>
    <div class="job-detail-body">
      <h4 class="job-detail-section-title">Descripción del puesto</h4>
      <p>${t.descripcion.replace(/\n/g, '<br>')}</p>
      
      <h4 class="job-detail-section-title">Información adicional</h4>
      <ul>
        <li><strong>Salario:</strong> ${salario} mensual</li>
        <li><strong>Horario:</strong> ${horario}</li>
        <li><strong>Empresa:</strong> ${t.nombre_negocio}</li>
      </ul>
    </div>
  `;
}

function obtenerTextoUbicacionNegocio(trabajo) {
  const desdeMaps = extraerMunicipioGoogleMaps(trabajo.google_maps_negocio || trabajo.GoogleMaps || "");
  if (desdeMaps) return desdeMaps;

  const direccion = limpiarTextoUbicacion(trabajo.direccion_negocio || trabajo.Direccion || "");
  if (!direccion) return "Sin ubicación";

  const partes = direccion.split(",").map(p => p.trim()).filter(Boolean);
  const candidata = partes.find(p => /texmelucan|puebla|tlaxcala|mexico|méxico/i.test(p));
  return limpiarTextoUbicacion(candidata || partes[Math.max(0, partes.length - 2)] || partes[0]);
}

function obtenerUbicacionDetalle(trabajo) {
  const mapsUrl = obtenerMapsUrl(trabajo.google_maps_negocio || trabajo.GoogleMaps || "");
  if (mapsUrl) {
    return `<a class="job-maps-link" href="${mapsUrl}" target="_blank" rel="noopener noreferrer">Ver en maps</a>`;
  }

  return `<strong>${obtenerTextoUbicacionNegocio(trabajo)}</strong>`;
}

function obtenerMapsUrl(valor) {
  const texto = String(valor || "").trim();
  if (!/^https?:\/\/\S+/i.test(texto)) return "";

  try {
    const url = new URL(texto);
    if (!/google\.[a-z.]+|goo\.gl|maps\.app\.goo\.gl/i.test(url.hostname)) return "";
    return url.href;
  } catch (error) {
    return "";
  }
}

function extraerMunicipioGoogleMaps(url) {
  if (!url || typeof url !== "string") return "";
  if (!/^https?:\/\//i.test(url.trim())) return "";

  let texto = url;
  try {
    const parsed = new URL(url);
    const query = parsed.searchParams.get("q") || parsed.searchParams.get("query");
    texto = query || parsed.pathname;
  } catch (error) {
    texto = url;
  }

  texto = decodificarUbicacion(texto);
  const placeMatch = texto.match(/\/place\/([^/@?]+)/i);
  if (placeMatch) texto = decodificarUbicacion(placeMatch[1]);

  texto = texto
    .replace(/^https?:\/\/[^/]+/i, "")
    .replace(/\/maps\/?|\/place\/?|@.*$/gi, " ")
    .replace(/[+_]/g, " ")
    .replace(/\s+/g, " ")
    .trim();

  const partes = texto.split(",").map(p => limpiarTextoUbicacion(p)).filter(Boolean);
  const municipio = partes.find(p => /texmelucan|puebla|tlaxcala|mexico|méxico/i.test(p));
  return municipio || partes.slice(-2, -1)[0] || partes[0] || "";
}

function limpiarTextoUbicacion(texto) {
  const limpio = String(texto || "")
    .replace(/\b\d{4,}\b/g, "")
    .replace(/\b[CP]\.? ?\d+\b/gi, "")
    .replace(/\s+/g, " ")
    .trim();

  if (/^\/[A-Za-z0-9_-]{8,}$/.test(limpio)) return "";
  return limpio;
}

function decodificarUbicacion(texto) {
  try {
    return decodeURIComponent(String(texto || "").replace(/\+/g, " "));
  } catch (error) {
    return String(texto || "").replace(/\+/g, " ");
  }
}

function renderizarError(msg) {
  const contenedor = document.querySelector("#contenedor");
  contenedor.innerHTML = `<div class="alert alert-danger m-3">${msg}</div>`;
}

function actualizarPaginacion(total) {
  const pag = document.getElementById("paginacion");
  if (!pag) return;
  pag.innerHTML = "";
  // Lógica de paginación simplificada o restaurada
}

function aplicarFiltros() {
  const searchInput = document.getElementById("globalSearchInput") || document.getElementById("searchInput");
  const activePill = document.querySelector(".job-pill.active");
  paginaActual = 1;
  listarPromociones({
    titulo: searchInput ? searchInput.value : "",
    horario: activePill && activePill.dataset.filter !== "all" ? activePill.dataset.filter : null
  });
}

// CRUD
function agregarTrabajo() {
  const form = document.querySelector("#formPromocion");
  const datos = new FormData(form);
  datos.append("ope", "AGREGAR");

  fetch("controladores/controladorTrabajos.php", {
    method: "POST",
    body: datos,
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        Swal.fire("Éxito", "Trabajo publicado", "success");
        form.reset();
        bootstrap.Modal.getInstance(document.getElementById('modalPromocion')).hide();
        listarPromociones();
      } else {
        Swal.fire("Error", data.msg, "error");
      }
    });
}

function cargarTrabajoParaEditar(id) {
  fetch("controladores/controladorTrabajos.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENER", ID_Promocion: id }),
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const t = data.usuario;
        document.querySelector("#ID_Promocion").value = t.ID_Trabajo;
        document.querySelector("#EditTitulo").value = t.Titulo;
        document.querySelector("#EditDescripcion").value = t.Descripcion;
        document.querySelector("#EditHorario").value = t.Tipo_Horario;
        document.querySelector("#EditSalario").value = t.Salario;
        document.querySelector("#EditPerRequeridas").value = t.PerRequeridas;
        document.querySelector("#ID_NegocioEdit").value = t.ID_Negocio;
        new bootstrap.Modal(document.getElementById('modalEditar')).show();
      }
    });
}

function editarTrabajo() {
  const form = document.querySelector("#formEditar");
  const datos = new FormData(form);
  datos.append("ope", "EDITAR");

  fetch("controladores/controladorTrabajos.php", {
    method: "POST",
    body: datos,
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        Swal.fire("Éxito", "Información actualizada", "success");
        bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
        listarPromociones();
      }
    });
}

function eliminarTrabajo(id) {
  Swal.fire({
    title: '¿Eliminar este trabajo?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("controladores/controladorTrabajos.php", {
        method: "POST",
        body: new URLSearchParams({ ope: "ELIMINAR", ID_Promocion: id }),
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            Swal.fire("Eliminado", "El trabajo ha sido borrado", "success");
            listarPromociones();
          }
        });
    }
  });
}

function toggleEstatusTrabajo(id, status) {
  const nuevoEstatus = status == "1" ? 0 : 1;
  trabajoSeleccionadoId = id;
  fetch("controladores/controladorTrabajos.php", {
    method: "POST",
    body: new URLSearchParams({
      ope: "CAMBIARESTATUS",
      ID_Promocion: id,
      estatus: nuevoEstatus
    }),
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        listarPromociones();
      } else {
        Swal.fire("Error", data.msg || "No se pudo cambiar el estado", "error");
      }
    })
    .catch(() => {
      Swal.fire("Error", "No se pudo conectar con el servidor", "error");
    });
}

function cargarNegocios() {
  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENERMEMBRESIAS" }),
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const selects = [document.getElementById("ID_Negocio"), document.getElementById("ID_NegocioEdit")];
        selects.forEach(s => {
          if (!s) return;
          s.innerHTML = "<option value=''>Seleccione un negocio</option>";
          data.negocios.forEach(n => {
            s.innerHTML += `<option value="${n.ID_Negocio}">${n.nombre_negocio}</option>`;
          });
        });
      }
    });
}

