<!DOCTYPE html>
<html>
<head>
  <title>Mapa de Negocios</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    #map { height: 500px; width: 100%; }
  </style>
</head>
<body>
  <h2>Negocios registrados en YoLocal</h2>
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Inicializa el mapa centrado en México
    var map = L.map('map').setView([23.6345, -102.5528], 5);

    // Capa base (OpenStreetMap gratuito)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Ejemplo: negocios con lat/lng
    var negocios = [
      { nombre: "Taquería El Buen Sabor", lat: 19.4326, lng: -99.1332 },
      { nombre: "Panadería San Juan", lat: 20.6597, lng: -103.3496 }
    ];

    // Agregar marcadores
    negocios.forEach(n => {
      L.marker([n.lat, n.lng])
        .addTo(map)
        .bindPopup("<b>" + n.nombre + "</b>");
    });
  </script>
</body>
</html>
