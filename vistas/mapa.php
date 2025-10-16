<!DOCTYPE html>
<html lang="es">
<head>
  <title>Mapa de Negocios</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
    :root {
      --primary-yellow: #FFD700; 
      --secondary-purple: #5A1F9C; 
      --text-dark: #2c3e50;
      --text-light: #ffffff; 
      --shadow-color: rgba(0, 0, 0, 0.15);
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif; 
      background-color: var(--primary-yellow); 
      color: var(--text-dark);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 2rem 1rem; /* Padding en los lados para móvil */
      overflow-x: hidden; 
      position: relative;
    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');

    /* --- Elementos Decorativos de Fondo (como en tu imagen) --- */
    .bg-element {
      position: absolute;
      font-size: 4rem;
      opacity: 0.15; 
      color: var(--secondary-purple);
      pointer-events: none; 
      transform: rotate(var(--rotation, 0deg));
      z-index: 0; 
    }

    .bg-element:nth-child(1) { top: 5%; left: 10%; --rotation: 20deg; }
    .bg-element:nth-child(2) { top: 15%; right: 5%; --rotation: -10deg; }
    .bg-element:nth-child(3) { top: 30%; left: 2%; --rotation: 45deg; }
    .bg-element:nth-child(4) { bottom: 20%; right: 15%; --rotation: -30deg; }
    .bg-element:nth-child(5) { bottom: 5%; left: 20%; --rotation: 60deg; }
    .bg-element:nth-child(6) { top: 40%; right: 25%; --rotation: 10deg; }
    .bg-element:nth-child(7) { top: 60%; left: 5%; --rotation: -25deg; }
    .bg-element:nth-child(8) { bottom: 10%; right: 5%; --rotation: 35deg; }


    h2 {
      font-size: 3rem;
      color: var(--secondary-purple);
      text-align: center;
      margin-bottom: 2rem;
      font-weight: 800; 
      letter-spacing: -0.05em; 
    
      position: relative;
      z-index: 1; 
    }

    #map {
      height: 65vh; 
      width: 95%;
      max-width: 1000px; 
      border-radius: 20px; 
      box-shadow: 0 15px 35px var(--shadow-color); 
      border: 5px solid var(--text-light);
      position: relative;
      z-index: 1; 
    }
    @media (max-width: 768px) {
      h2 {
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
      }
      #map {
        width: 100%;
        height: 70vh;
        border-radius: 15px;
      }
      .bg-element {
        font-size: 3rem;
      }
    }
  </style>
</head>
<body>
  <span class="bg-element"><img src="../assets/img/LogoYolocal.png" alt="" srcset="" height="150px"></span>
  <span class="bg-element"><img src="../assets/img/LogoYolocal.png" alt="" srcset="" height="150px"></span>
  <span class="bg-element"><img src="../assets/img/LogoYolocal.png" alt="" srcset="" height="150px"></span>
  <span class="bg-element"><img src="../assets/img/LogoYolocal.png" alt="" srcset="" height="150px"></span>
  <span class="bg-element"><img src="../assets/img/LogoYolocal.png" alt="" srcset="" height="150px"></span>
  <span class="bg-element"><img src="../assets/img/LogoYolocal.png" alt="" srcset="" height="150px"></span>
  <span class="bg-element"><img src="../assets/img/LogoYolocal.png" alt="" srcset="" height="150px"></span>

  <h2>Encuentra los mejores negocios en Yo Local</h2>
  
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="../assets/js/pagina/funcionesMapa.js"></script>

</body>
</html>