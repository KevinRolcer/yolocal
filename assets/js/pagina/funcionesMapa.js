var map = L.map("map", { attributionControl: false }).setView(
  [19.2822, -98.4389],
  13
);

// Capa base
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

function cargarNegocios() {
  fetch("../controladores/controladorNegocios.php", {
    method: "POST",
    body: new URLSearchParams({ ope: "OBTENERCOORDENADAS" }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        data.coordenadas.forEach((coordenada) => {
          L.marker([coordenada.Latitud, coordenada.Longitud], {
            icon: purpleIcon,
          })
            .addTo(map)
            .bindTooltip("<b>" + coordenada.nombre_negocio + "</b>", {
              permanent: true, // 👈 siempre visible
              direction: "top", // 👈 posición de la etiqueta (arriba del marcador)
              offset: [0, -35],
            });
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
cargarNegocios();
