const esPantallaMovil = window.matchMedia("(max-width: 768px)").matches;

var map = L.map("map", {
  attributionControl: false,
  scrollWheelZoom: false,
  dragging: !esPantallaMovil,
  tap: true,
  touchZoom: !esPantallaMovil,
}).setView(
  [19.2822, -98.4359],
  17
);


L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "",
}).addTo(map);
var purpleIcon = new L.Icon({
  iconUrl:
    "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-violet.png",
  shadowUrl: "https://unpkg.com/leaflet/dist/images/marker-shadow.png",
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

function escaparHtml(valor) {
  return String(valor ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function obtenerUrlNegocio(coordenada) {
  const id = Number(coordenada.ID_Negocio);
  return Number.isFinite(id) && id > 0
    ? `controladores/DetalleNegocioControlador.php?id=${id}`
    : "controladores/NegocioLControlador.php";
}

function cargarMapas() {
  fetch("controladores/controladorNegocios.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENERCOORDENADAS" }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        data.coordenadas.forEach((coordenada) => {
          const nombreNegocio = escaparHtml(coordenada.nombre_negocio || "Negocio local");
          const urlNegocio = obtenerUrlNegocio(coordenada);
          const marcador = L.marker([coordenada.Latitud, coordenada.Longitud], {
            icon: purpleIcon,
          })
            .addTo(map)
            .bindTooltip(`<span>${nombreNegocio}</span>`, {
              permanent: true, 
              direction: "top",
              offset: [0, -35],
              className: "yolocal-map-label",
            });

          marcador.bindPopup(
            `<div class="yolocal-map-popup">
              <strong>${nombreNegocio}</strong>
              <a href="${urlNegocio}" class="yolocal-map-more">Ver mas</a>
            </div>`,
            {
              closeButton: false,
              autoPan: true,
              className: "yolocal-map-popup-shell",
            }
          );
        });
      } else {
        Swal.fire("Error", "No se pudieron cargar los negocios", "error");
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error",
        "No se pudo cargar la lista de negocios: " + error.message,
        "error"
      );
    });
}
cargarMapas();
