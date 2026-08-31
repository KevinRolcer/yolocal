let paginaActual = 1;
const registrosPorPagina = 12;
let filtrosActuales = {
  busqueda: "",
  categoria: "",
  estado: "activos",
  orden: "recientes",
};
let debounceTimer = null;

document.addEventListener("DOMContentLoaded", () => {
  inicializarFiltros();
  cargarCategorias();
  listarPromociones();
});

function escaparHtml(valor) {
  return String(valor ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function formatearFecha(fecha) {
  if (!fecha) return "Sin fecha";
  const date = new Date(`${fecha}T00:00:00`);
  return date.toLocaleDateString("es-MX", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

function leerFiltros() {
  const inputBusqueda = document.getElementById("filtroBusqueda") || document.getElementById("filtroDescripcion");
  const categoria = document.getElementById("filtroCategoria");
  const orden = document.getElementById("filtroOrden");
  const estadoActivo = document.querySelector("#filtroEstado .chip.active");

  return {
    busqueda: inputBusqueda?.value.trim() || "",
    categoria: categoria?.value || "",
    orden: orden?.value || "recientes",
    estado: estadoActivo?.dataset.estado || "activos",
  };
}

function inicializarFiltros() {
  const inputBusqueda = document.getElementById("filtroBusqueda") || document.getElementById("filtroDescripcion");
  const categoria = document.getElementById("filtroCategoria");
  const orden = document.getElementById("filtroOrden");
  const limpiarBusqueda = document.getElementById("limpiarBusqueda") || document.querySelector(".search-bar button");
  const limpiarFiltros = document.getElementById("limpiarFiltros");

  if (inputBusqueda) {
    inputBusqueda.placeholder = "Buscar cupón, negocio o categoría";
    const barra = inputBusqueda.closest(".search-bar");
    if (barra && !barra.querySelector(".bi-search")) {
      inputBusqueda.insertAdjacentHTML("beforebegin", '<i class="bi bi-search"></i>');
    }
    if (limpiarBusqueda) {
      limpiarBusqueda.innerHTML = '<i class="bi bi-x-lg"></i>';
      limpiarBusqueda.setAttribute("aria-label", "Limpiar busqueda");
    }
  }

  inputBusqueda?.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => aplicarFiltros(), 300);
  });

  categoria?.addEventListener("change", aplicarFiltros);
  orden?.addEventListener("change", aplicarFiltros);

  document.querySelectorAll("#filtroEstado .chip").forEach((chip) => {
    chip.addEventListener("click", () => {
      document.querySelectorAll("#filtroEstado .chip").forEach((item) => item.classList.remove("active"));
      chip.classList.add("active");
      aplicarFiltros();
    });
  });

  limpiarBusqueda?.addEventListener("click", () => {
    if (!inputBusqueda) return;
    inputBusqueda.value = "";
    aplicarFiltros();
  });

  limpiarFiltros?.addEventListener("click", () => {
    if (inputBusqueda) inputBusqueda.value = "";
    if (categoria) categoria.value = "";
    if (orden) orden.value = "recientes";
    document.querySelectorAll("#filtroEstado .chip").forEach((item) => item.classList.remove("active"));
    document.querySelector('#filtroEstado .chip[data-estado="activos"]')?.classList.add("active");
    aplicarFiltros();
  });

  document.querySelector(".coupons-grid")?.addEventListener("click", (event) => {
    const boton = event.target.closest(".claim-button");
    if (!boton) return;
    const meta = {
      id: boton.dataset.id,
      titulo: boton.dataset.titulo,
      fecha: boton.dataset.fecha,
      descripcion: boton.dataset.descripcion,
      nombreNegocio: boton.dataset.nombre,
      direccionNegocio: boton.dataset.direccion,
      categoria: boton.dataset.categoria,
    };
    fetch("../controladores/controladorCupones.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ ope: "EMITIR_CUPON_PDF", ID_Promocion: boton.dataset.id }).toString(),
    })
      .then((r) => r.json())
      .then((data) => {
        if (!data.success) {
          alert(data.msg || "No se pudo generar el cupón.");
          return;
        }
        generarPDFPromocion({ ...meta, codigoCupon: data.codigo });
      })
      .catch(() => alert("Error de red."));
  });
}

function aplicarFiltros() {
  paginaActual = 1;
  filtrosActuales = leerFiltros();
  listarPromociones();
}

function cargarCategorias() {
  fetch("../controladores/controladorCupones.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ ope: "LISTAR_CATEGORIAS_CUPONES" }).toString(),
  })
    .then((response) => response.json())
    .then((data) => {
      const select = document.getElementById("filtroCategoria");
      if (!select || !data.success) return;
      data.lista.forEach((categoria) => {
        const option = document.createElement("option");
        option.value = categoria.ID_Categoria;
        option.textContent = categoria.Descripcion;
        select.appendChild(option);
      });
    })
    .catch((error) => console.error("Error al cargar categorias:", error));
}

export function listarPromociones(filtros = filtrosActuales) {
  filtrosActuales = filtros;
  const params = new URLSearchParams({
    ope: "LISTARPROMOCIONESPagina",
    pagina: paginaActual,
    registrosPorPagina,
    estado: filtros.estado || "activos",
    orden: filtros.orden || "recientes",
  });

  if (filtros.busqueda) params.append("busqueda", filtros.busqueda);
  if (filtros.categoria) params.append("categoria", filtros.categoria);

  renderizarCargando();

  fetch("../controladores/controladorCupones.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        renderizarEstado("No se pudieron cargar las promociones.");
        return;
      }
      renderizarPromociones(data.lista);
      actualizarPaginacion(Number(data.totalPaginas || 0));
      actualizarContador(Number(data.totalRegistros || 0));
    })
    .catch((error) => {
      console.error("Error al cargar promociones:", error);
      renderizarEstado("Error al conectarse con el servidor.");
    });
}

function renderizarCargando() {
  const contenedor = document.querySelector(".coupons-grid");
  if (!contenedor) return;
  contenedor.innerHTML = Array.from({ length: 4 }, () => '<div class="coupon-skeleton"></div>').join("");
}

function renderizarEstado(mensaje) {
  const contenedor = document.querySelector(".coupons-grid");
  if (!contenedor) return;
  contenedor.innerHTML = `
    <div class="empty-state">
      <i class="bi bi-ticket-perforated"></i>
      <h3>${escaparHtml(mensaje)}</h3>
      <p>Prueba cambiando la categoria o limpiando los filtros.</p>
    </div>
  `;
}

function renderizarPromociones(lista) {
  const contenedor = document.querySelector(".coupons-grid");
  if (!contenedor) return;

  if (!lista || lista.length === 0) {
    renderizarEstado("No encontramos cupones con esos filtros.");
    return;
  }

  contenedor.innerHTML = lista.map((promo) => {
    const stockClass = Number(promo.cantidad) <= 5 ? "low-stock" : "";
    const miercoles = String(promo.PromoMiercoles) === "1";

    return `
      <article class="coupon-card ${stockClass}">
        <div class="coupon-accent">
          <span>${escaparHtml(promo.categoria)}</span>
        </div>
        <div class="coupon-main">
          ${
            miercoles
              ? `<div class="coupon-meta"><span class="coupon-chip">PromoMiercoles</span></div>`
              : ""
          }
          <h3>${escaparHtml(promo.titulo)}</h3>
          <p>${escaparHtml(promo.descripcion || "Promocion disponible en negocio local.")}</p>
          <div class="coupon-business">
            <i class="bi bi-shop"></i>
            <span>${escaparHtml(promo.nombre_negocio)}</span>
          </div>
        </div>
        <div class="coupon-side">
          <div class="coupon-date">
            <span class="coupon-date-label">Válido hasta</span>
            <strong class="coupon-date-value">${formatearFecha(promo.fecha_fin)}</strong>
          </div>
          <div class="coupon-stock">
            <strong>${escaparHtml(promo.cantidad)}</strong>
            <span>disponibles</span>
          </div>
          <button class="claim-button"
            type="button"
            data-id="${escaparHtml(promo.ID_Promocion)}"
            data-titulo="${escaparHtml(promo.titulo)}"
            data-nombre="${escaparHtml(promo.nombre_negocio)}"
            data-descripcion="${escaparHtml(promo.descripcion || "Sin descripcion")}"
            data-fecha="${escaparHtml(promo.fecha_fin)}"
            data-direccion="${escaparHtml(promo.direccion_negocio || "")}"
            data-categoria="${escaparHtml(promo.categoria)}">
            <i class="bi bi-download"></i>
            Descargar
          </button>
        </div>
      </article>
    `;
  }).join("");
}

function actualizarContador(total) {
  const contador = document.getElementById("contadorCupones");
  if (!contador) return;
  contador.textContent = total === 1 ? "1 cupón activo" : `${total} cupones activos`;
}

function actualizarPaginacion(totalPaginas) {
  const paginacion = document.querySelector("#paginacion");
  if (!paginacion) return;
  paginacion.innerHTML = "";

  if (totalPaginas <= 1) return;

  const crearBoton = (texto, pagina, activo = false, deshabilitado = false) => {
    const boton = document.createElement("button");
    boton.type = "button";
    boton.className = activo ? "page-btn active" : "page-btn";
    boton.innerHTML = texto;
    boton.disabled = deshabilitado;
    boton.addEventListener("click", () => {
      paginaActual = pagina;
      listarPromociones();
      document.querySelector(".coupon-results-header")?.scrollIntoView({ behavior: "smooth", block: "start" });
    });
    return boton;
  };

  paginacion.appendChild(crearBoton("&laquo;", Math.max(1, paginaActual - 1), false, paginaActual === 1));

  const inicio = Math.max(1, paginaActual - 2);
  const fin = Math.min(totalPaginas, inicio + 4);
  for (let i = inicio; i <= fin; i++) {
    paginacion.appendChild(crearBoton(String(i), i, i === paginaActual));
  }

  paginacion.appendChild(crearBoton("&raquo;", Math.min(totalPaginas, paginaActual + 1), false, paginaActual === totalPaginas));
}

function fetchYolocalLogoDataUrl() {
  try {
    const logoUrl = new URL("../assets/img/LogoYolocal.png", window.location.href);
    return fetch(logoUrl.href, { credentials: "same-origin" })
      .then((r) => (r.ok ? r.blob() : Promise.reject()))
      .then(
        (blob) =>
          new Promise((resolve, reject) => {
            const fr = new FileReader();
            fr.onload = () => resolve(fr.result);
            fr.onerror = reject;
            fr.readAsDataURL(blob);
          })
      )
      .catch(() => null);
  } catch {
    return Promise.resolve(null);
  }
}

function publicAppBasePath() {
  let root = typeof window.__YL_PUBLIC_ROOT__ === "string" ? window.__YL_PUBLIC_ROOT__.trim() : "";
  if (root && root !== ".") {
    return root.startsWith("/") ? root : `/${root}`;
  }
  const path = window.location.pathname.replace(/\\/g, "/");
  const v = path.indexOf("/vistas/");
  if (v !== -1) {
    return path.slice(0, v) || "";
  }
  return "";
}

function canjePublicOrigin() {
  const o = typeof window.__YL_PUBLIC_ORIGIN__ === "string" ? window.__YL_PUBLIC_ORIGIN__.trim() : "";
  return o ? o.replace(/\/$/, "") : window.location.origin;
}

function urlCanjeCupon(id, codigoCupon) {
  const base = publicAppBasePath();
  const origin = canjePublicOrigin();
  let q = `id=${encodeURIComponent(String(id))}`;
  if (codigoCupon) {
    q += `&c=${encodeURIComponent(String(codigoCupon))}`;
  }
  return `${origin}${base}/canje.php?${q}`;
}

function crearQrDataUrl(texto, pixelSize = 260, opts = {}) {
  const colorDark = opts.colorDark ?? "#4c0682";
  const colorLight = opts.colorLight ?? "#ffffff";
  return new Promise((resolve) => {
    if (typeof QRCode === "undefined") {
      resolve(null);
      return;
    }
    const holder = document.createElement("div");
    holder.style.position = "fixed";
    holder.style.left = "-9999px";
    holder.style.width = `${pixelSize + 24}px`;
    holder.style.height = `${pixelSize + 24}px`;
    document.body.appendChild(holder);
    try {
      new QRCode(holder, {
        text: texto,
        width: pixelSize,
        height: pixelSize,
        colorDark,
        colorLight,
        correctLevel: QRCode.CorrectLevel.M,
      });
    } catch {
      if (holder.parentNode) holder.parentNode.removeChild(holder);
      resolve(null);
      return;
    }
    const leer = () => {
      const canvas = holder.querySelector("canvas");
      const img = holder.querySelector("img");
      let dataUrl = null;
      if (canvas) dataUrl = canvas.toDataURL("image/png");
      else if (img && img.src) dataUrl = img.src;
      if (holder.parentNode) holder.parentNode.removeChild(holder);
      resolve(dataUrl);
    };
    requestAnimationFrame(() => setTimeout(leer, 100));
  });
}

function generarPDFPromocion({ id, titulo, fecha, descripcion, nombreNegocio, direccionNegocio, categoria, codigoCupon }) {
  if (!codigoCupon) {
    return;
  }
  const canjeUrl = urlCanjeCupon(id, codigoCupon);
  Promise.all([
    crearQrDataUrl(canjeUrl, 400, { colorDark: "#4c0682", colorLight: "#ffffff" }), // QR con color morado de la marca
    fetchYolocalLogoDataUrl(),
  ]).then(([qrDataUrl, logoDataUrl]) => {
    const { jsPDF } = window.jspdf;
    // Nuevo formato panorámico tipo pase de abordar
    const doc = new jsPDF({ unit: "mm", orientation: "landscape", format: [210, 90] });
    const W = doc.internal.pageSize.getWidth();
    const H = doc.internal.pageSize.getHeight();

    // Paleta de colores
    const pageBg = [244, 244, 246]; // Gris muy claro para el fondo físico
    const paper = [255, 255, 255]; // Blanco del ticket
    const purpleStub = [76, 6, 130]; // Morado marca YoLocal
    const ink = [17, 24, 39]; // Texto oscuro principal
    const muted = [107, 114, 128]; // Texto secundario
    const label = [156, 163, 175]; // Etiquetas sutiles
    const accent = [234, 179, 8]; // Acentos

    // Fondo del PDF completo
    doc.setFillColor(...pageBg);
    doc.rect(0, 0, W, H, "F");

    // Coordenadas de la tarjeta
    const tx = 15;
    const ty = 12;
    const tw = W - 30;
    const th = H - 24;
    const wStub = 55; // Ancho del talón izquierdo
    const splitX = tx + wStub;

    // Sombra sutil de la tarjeta principal
    doc.setFillColor(220, 222, 225);
    doc.roundedRect(tx + 1.2, ty + 1.5, tw, th, 3, 3, "F");

    // Tarjeta principal (Blanca)
    doc.setFillColor(...paper);
    doc.roundedRect(tx, ty, tw, th, 3, 3, "F");

    // Talón izquierdo (Morado oscuro)
    doc.setFillColor(...purpleStub);
    doc.roundedRect(tx, ty, wStub, th, 3, 3, "F");
    doc.rect(tx + wStub - 4, ty, 4, th, "F"); // Para quitar el borde redondeado derecho del talón

    // Muescas (Cortes circulares arriba y abajo en la línea de división)
    doc.setFillColor(...pageBg);
    doc.circle(splitX, ty, 3.5, "F");
    doc.circle(splitX, ty + th, 3.5, "F");

    // Línea punteada de precorte
    doc.setDrawColor(200, 200, 205);
    doc.setLineWidth(0.4);
    const dashFn = doc.setLineDashPattern || doc.setLineDash;
    if (typeof dashFn === "function") dashFn.call(doc, [2, 2], 0);
    doc.line(splitX, ty + 4, splitX, ty + th - 4);
    if (typeof dashFn === "function") dashFn.call(doc, [], 0);

    // --- CONTENIDO DEL TALÓN IZQUIERDO ---
    const stubMid = tx + wStub / 2;
    let yStub = ty + 7;

    // Logotipo
    if (logoDataUrl) {
      try {
        const logoBox = 15; // Tamaño aumentado
        doc.addImage(logoDataUrl, "PNG", stubMid - logoBox / 2, yStub - 2, logoBox, logoBox);
        yStub += logoBox + 1; // Espaciado ajustado
      } catch { yStub += 6; }
    } else {
      yStub += 6;
    }

    // Código QR (con fondo blanco para resaltar)
    const qrSize = 27;
    const qx = stubMid - qrSize / 2;
    doc.setFillColor(255, 255, 255);
    doc.roundedRect(qx - 1.5, yStub - 1.5, qrSize + 3, qrSize + 3, 2, 2, "F");
    if (qrDataUrl) {
      try {
        doc.addImage(qrDataUrl, "PNG", qx, yStub, qrSize, qrSize);
      } catch {}
    }
    yStub += qrSize + 5;

    // Etiqueta y código manual
    doc.setFont("helvetica", "bold");
    doc.setFontSize(4.5);
    doc.setTextColor(200, 190, 240);
    doc.text("CÓDIGO DE CANJE", stubMid, yStub, { align: "center" });
    yStub += 3.5;
    
    doc.setFont("courier", "bold");
    doc.setFontSize(7.5);
    doc.setTextColor(255, 255, 255);
    doc.text(String(codigoCupon).trim(), stubMid, yStub, { align: "center" });
    
    doc.setFont("helvetica", "normal");
    doc.setFontSize(3.5);
    doc.setTextColor(150, 140, 180);
    doc.text(`Ref: ${id}`, stubMid, ty + th - 3, { align: "center" });

    // --- CONTENIDO DEL CUERPO PRINCIPAL ---
    const cx = splitX + 8;
    let cy = ty + 8;
    const contentW = (tw - wStub) - 16;

    // Encabezado "Vuelo" (Categoría ---> Negocio)
    doc.setFont("helvetica", "bold");
    doc.setFontSize(6.5);
    doc.setTextColor(...label);
    const catStr = String(categoria || "PROMOCIÓN").toUpperCase();
    doc.text(catStr, cx, cy);
    
    doc.setFont("helvetica", "bold");
    doc.setTextColor(...purpleStub); // Nombre del negocio en color morado oscuro
    const busStr = String(nombreNegocio || "NEGOCIO LOCAL").toUpperCase();
    doc.text(busStr, cx + contentW, cy, { align: "right" });
    
    // Línea punteada que conecta categoría y negocio
    const catW = doc.getTextWidth(catStr);
    const busW = doc.getTextWidth(busStr);
    doc.setDrawColor(220, 220, 225);
    doc.setLineWidth(0.4);
    if (typeof dashFn === "function") dashFn.call(doc, [1.5, 1.5], 0);
    doc.line(cx + catW + 3, cy - 2, cx + contentW - busW - 3, cy - 2);
    if (typeof dashFn === "function") dashFn.call(doc, [], 0);
    
    cy += 10;

    // Título de la promoción
    doc.setFont("helvetica", "bold");
    doc.setFontSize(15);
    doc.setTextColor(...ink);
    const tituloLineas = doc.splitTextToSize(String(titulo || "Promoción").trim(), contentW).slice(0, 2);
    doc.text(tituloLineas, cx, cy);
    cy += tituloLineas.length * 6.5 + 2;

    // Descripción
    doc.setFont("helvetica", "normal");
    doc.setFontSize(8);
    doc.setTextColor(...muted);
    const descLineas = doc.splitTextToSize(String(descripcion || "").trim(), contentW).slice(0, 2);
    doc.text(descLineas, cx, cy);
    cy += descLineas.length * 4 + 5;

    // Separador horizontal
    doc.setDrawColor(235, 235, 240);
    doc.setLineWidth(0.3);
    doc.line(cx, cy, cx + contentW, cy);
    cy += 6;

    // Información del pie (Válido hasta, Ubicación, Condiciones)
    const col1 = cx;
    const col2 = cx + contentW * 0.38;
    const col3 = cx + contentW * 0.76;

    // Títulos de las columnas inferiores
    doc.setFontSize(5);
    doc.setFont("helvetica", "bold");
    doc.setTextColor(...label);
    doc.text("VÁLIDO HASTA", col1, cy);
    doc.text("UBICACIÓN", col2, cy);
    doc.text("CONDICIONES", col3, cy);
    cy += 4;

    // Valores de las columnas inferiores
    doc.setFontSize(8);
    doc.setTextColor(...ink);
    doc.text(formatearFecha(fecha), col1, cy);
    
    doc.setFontSize(7);
    const locLines = doc.splitTextToSize(String(direccionNegocio || "En sucursal"), contentW * 0.35).slice(0, 2);
    doc.text(locLines, col2, cy);

    doc.setFontSize(6.5);
    doc.setTextColor(...muted);
    doc.text("Solo en tienda", col3, cy);
    doc.text("No transferible", col3, cy + 3.5);

    doc.save(`cupon_${id}_yolocal.pdf`);
  });
}
