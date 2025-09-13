
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
          <div class="limited-badge">${promo.titulo}</div>
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
            data-cantidad="${promo.cantidad}"
            data-fecha="${promo.fecha_fin}"
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

document
  .getElementById("limpiarFiltros")
  .addEventListener("click", function () {
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

document
  .getElementById("limpiarFiltros")
  .addEventListener("click", function () {
    document.querySelectorAll(".filter").forEach((filter) => {
      let input = filter.querySelector("input");
      input.classList.add("hidden");
      input.value = "";
      filter.classList.remove("active");
    });
  });


