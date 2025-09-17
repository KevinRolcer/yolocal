document.addEventListener("DOMContentLoaded", () => {
    listarMiembros();
});


  



export function listarMiembros() {
  
  let params = new URLSearchParams();
  params.append("ope", "LISTAICONOS");

  fetch("../controladores/controladorNegocios.php", {
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
      <div class="carousel-item">
        <div class="icon-container">
          ${
            miembro.Rutaicono
              ? `<img src="${miembro.Rutaicono}" alt="icono ${miembro.nombre_negocio}" class="icon" />`
              : `<div class="icon">🏢</div>`
          }
        </div>
        <div class="business-name">${miembro.nombre_negocio}</div>
      </div>
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