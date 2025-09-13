
document.addEventListener("DOMContentLoaded", () => {
 
  listarPromociones();
});

function esFechaExpirada(fechaFin) {
  const hoy = new Date();
  const fin = new Date(fechaFin);

  // Convertir ambas a formato YYYY-MM-DD (sin hora)
  const hoyStr = hoy.toISOString().split("T")[0];
  const finStr = fin.toISOString().split("T")[0];

  return finStr < hoyStr; // se desactiva solo si fue ANTES de hoy
}
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


  fetch("../controladores/controladorCupones.php", {
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
  const contenedor = document.querySelector(".coupons-grid");
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
    <div class="coupon-card">
      <div class="perforation-line"></div>
      <div class="coupon-content">
        <div class="main-content">
          <div class="limited-badge">${promo.categoria}</div>
          <div class="brand-name">${promo.nombre_negocio}</div>
          <div class="offer-description">${promo.descripcion ?? "Sin descripción"}</div>
          <div class="discount-section">
            <div class="discount"> ${promo.titulo}</div>
            <div class="validity">Válido hasta: ${promo.fecha_fin}</div>
          </div>
        </div>
        <div class="action-section">
          <!-- Botón siempre visible -->
          <button class="claim-button btn-tags" 
            data-id="${promo.ID_Promocion}" 
            data-titulo="${promo.titulo}"
            data-nombre="${promo.nombre_negocio}"
            data-descripcion="${promo.descripcion ?? "Sin descripción"}"
            data-fecha="${promo.fecha_fin}"
            data-direccion="${promo.direccion_negocio}"
            id="btnCupon_${promo.ID_Promocion}"
            ${promo.cantidad <= 0 || esFechaExpirada(promo.fecha_fin) ? "disabled" : ""}>
            Reclamar
          </button>

          <!-- Botones solo para admin -->
          
        </div>
      </div>
    </div>
  `;
});
  
}

function renderizarError(mensaje) {
  const contenedor = document.querySelector(".coupons-grid");
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
      listarPromociones(filtrosActuales);
    }
  });
  paginacion.appendChild(btnSiguiente);
}

function aplicarFiltros() {
  const filtros = {
  
    descripcion: document.getElementById("filtroDescripcion").value.trim(),
   
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
document.addEventListener('DOMContentLoaded', () => {

  // Escuchamos clics en todo el documento
  document.addEventListener('click', function(event) {
    
    // Verificamos si el elemento clickeado es un botón de reclamar
    if (event.target.matches('.claim-button')) {
      
      // Prevenimos cualquier comportamiento por defecto del botón (si lo tuviera)
      event.preventDefault(); 
      
      // Obtenemos el botón específico que fue presionado
      const boton = event.target;
      
      // Extraemos los datos del botón usando el `dataset`
      const idPromocion = boton.dataset.id;
      const titulo = boton.dataset.titulo;
      const fechaFin = boton.dataset.fecha;
      const descripcion = boton.dataset.descripcion;
      const nombreNegocio = boton.dataset.nombre;
      const direccionNegocio = boton.dataset.direccion;

      // Llamamos a la función que genera y descarga el PDF
      generarPDFPromocion(idPromocion, titulo, fechaFin, descripcion, nombreNegocio, direccionNegocio);
    }
  });
});

/**
 * Función que genera un PDF con los datos de la promoción y lo descarga.
 * @param {string} id - El ID de la promoción.
 * @param {string} titulo - El título de la promoción.
 * @param {string} fecha - La fecha de finalización.
 * @param {string} descripcion - La descripción de la promoción.
 * @param {string} nombreNegocio - El nombre del negocio.
 * @param {string} direccionNegocio - La dirección del negocio.
 */
function generarPDFPromocion(id, titulo, fecha, descripcion, nombreNegocio, direccionNegocio, categoria = "CAFETERIA") {
  const { jsPDF } = window.jspdf;

  // 📏 PDF tamaño boleto (90mm x 45mm)
  const doc = new jsPDF("l", "mm", [90, 45]);

  // 🎨 Colores
  const morado = "#6C4CCF";
  const textoNegro = "#333333";
  const gris = "#777777";

  // --- Contorno del cupón ---
  doc.setDrawColor(200);
  doc.setLineDash([2, 2]); 
  doc.roundedRect(5, 5, 80, 35, 3, 3); // borde ajustado al boleto

  // --- Categoría ---
  doc.setFillColor(255, 216, 77);
  doc.roundedRect(7, 7, 20, 6, 2, 2, "F"); 
  doc.setTextColor(textoNegro);
  doc.setFontSize(8);
  doc.text(categoria, 9, 11);

  // --- Nombre negocio ---
  doc.setFontSize(10);
  doc.setFont("helvetica", "bold");
  doc.text(nombreNegocio, 7, 18);

  // --- Descripción ---
  doc.setFont("helvetica", "normal");
  doc.setFontSize(8);
  doc.setTextColor(gris);
  doc.text(descripcion, 7, 24);

  // --- Título promoción ---
  doc.setFontSize(11);
  doc.setFont("helvetica", "bold");
  doc.setTextColor("#B45F06");
  doc.text(titulo, 7, 30);

  // --- Fecha ---
  doc.setFontSize(8);
  doc.setFont("helvetica", "normal");
  doc.setTextColor(gris);
  doc.text(`Válido hasta: ${fecha}`, 7, 36);

  // --- Botón Reclamar ---
  doc.setFillColor(morado);
  doc.roundedRect(65, 25, 18, 8, 3, 3, "F");
  doc.setTextColor("#FFFFFF");
  doc.setFontSize(8);
  doc.text("Reclamar", 74, 30, { align: "center" });

  // Guardar
  doc.save(`promocion_${id}.pdf`);
}




