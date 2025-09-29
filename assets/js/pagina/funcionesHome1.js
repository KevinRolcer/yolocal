  document.addEventListener("DOMContentLoaded", () => {
    listarNegociosBanner();
      listarMiembros();
      
  });

  export function listarMiembros() {
    
    let params = new URLSearchParams();
    params.append("ope", "LISTAICONOS");

    fetch("controladores/controladorNegocios.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params.toString(),
    })
      .then((response) => response.json())
      .then((data) => {
        if (!data.success) {
          console.error("Error al cargar negocios:", data.msg);
          renderizarError("No se pudieron cargar los negocios.");
          return;
        }
        renderizarMiembros(data.lista);
        
      })
      .catch((error) => {
        console.error("Error en la solicitud:", error);
        renderizarError("Error al conectarse con el servidor.");
      });
  }

  function renderizarMiembros(lista) {
    const contenedor = document.querySelector(".carousel-track2"); 
    contenedor.innerHTML = "";

    if (!lista || lista.length === 0) {
      contenedor.innerHTML = `
        <div class="no-results">
            <p>No se encuentra ningún negocio con los filtros aplicados.</p>
        </div>
      `;
      return;
    }

    let htmlItems = "";

    lista.forEach((miembro) => {
      htmlItems += `
      <a class="carousel-item" href="controladores/DetalleNegocioControlador.php?id=${miembro.ID_Negocio}" >
        
        
          <div class="icon-container">
            ${
              miembro.Rutaicono
                ? `<img src="${miembro.Rutaicono}" alt="icono ${miembro.nombre_negocio}" class="icon" />`
                : `<div class="icon">🏢</div>`
            }
          </div>
          <div class="business-name">${miembro.nombre_negocio}</div>
          </a>
        
    
      `;
    });

    // 🔁 Duplicamos para loop infinito
    contenedor.innerHTML = htmlItems + htmlItems;
  }
  function renderizarError(mensaje) {
    const contenedor = document.querySelector(".carousel-track2");
    contenedor.innerHTML = `
          <div class="error-message">
              <p>${mensaje}</p>
          </div>
      `;
  }
  export function listarNegociosBanner() {
    
    let params = new URLSearchParams();
    params.append("ope", "LISTAICONOSBanner");

    fetch("controladores/controladorNegocios.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params.toString(),
    })
      .then((response) => response.json())
      .then((data) => {
        if (!data.success) {
          console.error("Error al cargar negocios:", data.msg);
          renderizarError("No se pudieron cargar los negocios.");
          return;
        }
        renderizarNegociosBanner(data.lista);
        
      })
      .catch((error) => {
        console.error("Error en la solicitud:", error);
        renderizarError("Error al conectarse con el servidor.");
      });
  }

  function renderizarNegociosBanner(lista) {
  const track = document.getElementById("carouselTrack");
  if (!track) return;

  track.innerHTML = "";

  if (!lista || lista.length === 0) {
    track.innerHTML = `
      <div class="carousel-slide">
        <div class="slide-background bg-gradient-1">
          <div class="slide-content">
            <div class="slide-info">
              <h2 class="slide-title">Sin resultados</h2>
              <p class="slide-description">No se encontraron negocios disponibles.</p>
            </div>
          </div>
        </div>
      </div>`;
    return;
  }

  let htmlSlides = "";

  lista.forEach((miembro, i) => {
    htmlSlides += `
      <div class="carousel-slide">
        <div class="slide-background bg-gradient-${(i % 3) + 1}">
          <div class="slide-content">
            <div class="slide-image">
              <img src="${miembro.Rutaicono || 'assets/img/default.jpg'}" 
                   alt="${miembro.nombre_negocio}">
            </div>
            <div class="slide-info">
              <span class="discount-badge">Recomendado en ${miembro.nombre_categoria|| "Categoría desconocida"}</span>
              <h2 class="slide-title">${miembro.nombre_negocio}</h2>
              <p class="slide-description">${miembro.DescripcionN || "Negocio destacado"}</p>
              <a href="controladores/DetalleNegocioControlador.php?id=${miembro.ID_Negocio}">
                <button class="slide-button">Ver negocio</button>
              </a>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  track.innerHTML = htmlSlides;
}
