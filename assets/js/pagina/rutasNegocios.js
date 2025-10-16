// rutasNegocios.js

// Inicializar mapa
let map;
let markers = [];
let routingControl = null;
let ubicacionUsuario = null;
let negociosData = [];

// Configuración de iconos personalizados
const purpleIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-violet.png',
    shadowUrl: 'https://unpkg.com/leaflet/dist/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const userIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
    shadowUrl: 'https://unpkg.com/leaflet/dist/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const selectedIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png',
    shadowUrl: 'https://unpkg.com/leaflet/dist/images/marker-shadow.png',
    iconSize: [30, 49],
    iconAnchor: [15, 49],
    popupAnchor: [1, -42],
    shadowSize: [41, 41]
});

function initMap() {
    map = L.map('map', { 
        attributionControl: false,
        zoomControl: false
    }).setView([19.2822, -98.4359], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: ''
    }).addTo(map);

    L.control.zoom({
        position: 'topright'
    }).addTo(map);

    obtenerUbicacionUsuario();
    
    cargarNegocios();
}

function obtenerUbicacionUsuario() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                ubicacionUsuario = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Centrar mapa en ubicación del usuario
                map.setView([ubicacionUsuario.lat, ubicacionUsuario.lng], 15);
                
                // Agregar marcador de usuario
                L.marker([ubicacionUsuario.lat, ubicacionUsuario.lng], {
                    icon: userIcon
                })
                .addTo(map)
                .bindPopup('<b>Tu ubicación</b>')
                .openPopup();
                
                // Recalcular distancias
                if (negociosData.length > 0) {
                    calcularDistancias();
                    renderizarNegocios();
                }
            },
            (error) => {
                console.error('Error al obtener ubicación:', error);
                // Usar ubicación por defecto (Puebla)
                ubicacionUsuario = { lat: 19.2822, lng: -98.4359 };
            }
        );
    } else {
        // Navegador no soporta geolocalización
        ubicacionUsuario = { lat: 19.2822, lng: -98.4359 };
    }
}

// Cargar negocios desde el servidor
function cargarNegocios() {
    fetch('../controladores/controladorNegocios.php', {
        method: 'POST',
        body: new URLSearchParams({ ope: 'OBTENERCOORDENADAS' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            negociosData = data.coordenadas.map(negocio => ({
                ...negocio,
                distancia: 0,
                abierto: calcularEstadoNegocio(negocio)
            }));
            
            calcularDistancias();
            renderizarNegocios();
            agregarMarcadores();
        } else {
            mostrarMensajeVacio('No se pudieron cargar los negocios');
        }
    })
    .catch(error => {
        console.error('Error al cargar negocios:', error);
        mostrarMensajeVacio('Error al cargar los negocios');
    });
}

// Calcular si un negocio está abierto
function calcularEstadoNegocio(negocio) {
    // Por ahora retornamos true/false aleatoriamente
    // Aquí deberías implementar la lógica real basada en horarios
    return Math.random() > 0.3;
}

// Calcular distancias desde ubicación del usuario
function calcularDistancias() {
    if (!ubicacionUsuario) return;
    
    negociosData.forEach(negocio => {
        negocio.distancia = calcularDistancia(
            ubicacionUsuario.lat,
            ubicacionUsuario.lng,
            negocio.Latitud,
            negocio.Longitud
        );
    });
}

// Fórmula de Haversine para calcular distancia
function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distancia = R * c;
    return distancia;
}

// Agregar marcadores al mapa
function agregarMarcadores() {
    // Limpiar marcadores anteriores
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    negociosData.forEach(negocio => {
        const marker = L.marker([negocio.Latitud, negocio.Longitud], {
            icon: purpleIcon
        })
        .addTo(map)
        .bindTooltip(`<b>${negocio.nombre_negocio}</b>`, {
            permanent: false,
            direction: 'top',
            offset: [0, -35]
        });
        
        marker.on('click', () => {
            seleccionarNegocio(negocio);
        });
        
        markers.push(marker);
        negocio.marker = marker;
    });
}

// Renderizar lista de negocios
function renderizarNegocios() {
  const listaNegocios = document.getElementById('listaNegocios');
  if (!listaNegocios) return;

  const filtroEstado = document.getElementById('filtroEstado')?.value || 'todos';
  const filtroOrden = document.getElementById('filtroOrden')?.value || 'cercano';
  const textoBusqueda = document.getElementById('inputBuscador')?.value.toLowerCase() || '';

  // Filtrar negocios según búsqueda y estado
  let negociosFiltrados = negociosData.filter(n => {
    const coincideNombre = n.nombre_negocio.toLowerCase().includes(textoBusqueda);
    const coincideEstado =
      filtroEstado === 'todos' ||
      (filtroEstado === 'abierto' && n.abierto) ||
      (filtroEstado === 'cerrado' && !n.abierto);
    return coincideNombre && coincideEstado;
  });

  negociosFiltrados.sort((a, b) => {
    if (filtroOrden === 'nombre') return a.nombre_negocio.localeCompare(b.nombre_negocio);
    return a.distancia - b.distancia; 
  });

  if (negociosFiltrados.length === 0) {
    mostrarMensajeVacio('No se encontraron negocios');
    return;
  }

  listaNegocios.innerHTML = '';

  // Crear las tarjetas de negocios
  negociosFiltrados.forEach(negocio => {
    const div = document.createElement('div');
    div.className = 'negocio-card';
    div.dataset.id = negocio.id_negocio;

    div.innerHTML = `
      <div class="negocio-header">
        <div class="negocio-logo">
          <img src="${negocio.logo || '../assets/img/LogoYolocal.png'}"
               alt="${negocio.nombre_negocio}"
               onerror="this.src='../assets/img/LogoYolocal.png'">
        </div>
        <div class="negocio-info">
          <div class="negocio-nombre">${negocio.nombre_negocio}</div>
          <div class="negocio-distancia">${negocio.distancia ? negocio.distancia.toFixed(1) + ' km' : ''}</div>
          <div class="negocio-estado ${negocio.abierto ? 'abierto' : 'cerrado'}">
            ${negocio.abierto ? '● Abierto ahora' : '● Cerrado'}
          </div>
        </div>
      </div>
    `;

    div.addEventListener('click', () => seleccionarNegocio(negocio));
    listaNegocios.appendChild(div);
  });
}



// Seleccionar negocio
function seleccionarNegocio(negocio) {
    // Restaurar iconos de todos los marcadores
    negociosData.forEach(n => {
        if (n.marker) {
            n.marker.setIcon(purpleIcon);
        }
    });
    
    // Marcar negocio seleccionado
    if (negocio.marker) {
        negocio.marker.setIcon(selectedIcon);
    }
    
    // Centrar mapa en el negocio
    map.setView([negocio.Latitud, negocio.Longitud], 16);
    
    // Mostrar info card
    mostrarInfoCard(negocio);
    
    // Marcar card como activo
    document.querySelectorAll('.negocio-card').forEach(card => {
        card.classList.remove('activo');
    });
    const cardSeleccionado = document.querySelector(`[data-id="${negocio.id_negocio}"]`);
    if (cardSeleccionado) {
        cardSeleccionado.classList.add('activo');
    }
}

// Mostrar info card
function mostrarInfoCard(negocio) {
    const infoCard = document.getElementById('infoCard');
    const contenido = infoCard.querySelector('.info-card-contenido');
    
    contenido.innerHTML = `
        <div class="info-card-header">
            <div class="info-card-logo">
                <img src="${negocio.logo || '../assets/img/LogoYolocal.png'}" alt="${negocio.nombre_negocio}" onerror="this.src='../assets/img/LogoYolocal.png'">
            </div>
            <div class="info-card-info">
                <h3>${negocio.nombre_negocio}</h3>
                <p style="display: flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    ${negocio.distancia.toFixed(2)} km de distancia
                </p>
                <p style="color: ${negocio.abierto ? '#10b981' : '#ef4444'}; font-weight: 600;">
                    ${negocio.abierto ? '● Abierto ahora' : '● Cerrado'}
                </p>
            </div>
        </div>
        <div class="info-card-acciones">
            <button class="btn-card-accion" onclick="llamarNegocio('${negocio.telefono || ''}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                Llamar
            </button>
            <button class="btn-card-accion primario" onclick="trazarRuta(event, ${negocio.Latitud}, ${negocio.Longitud}, '${negocio.nombre_negocio.replace(/'/g, "\\'")}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 11l19-9-9 19-2-8-8-2z"/>
                </svg>
                Cómo llegar
            </button>
        </div>
    `;
    
    infoCard.style.display = 'block';
}

function trazarRuta(event, lat, lng, nombre) {
  if (event) event.stopPropagation();

  if (!ubicacionUsuario) {
    alert('No se pudo obtener tu ubicación');
    return;
  }

  // Eliminar ruta anterior correctamente
  if (routingControl) {
    map.removeControl(routingControl);
    routingControl = null;
  }

  routingControl = L.Routing.control({
    waypoints: [
      L.latLng(ubicacionUsuario.lat, ubicacionUsuario.lng),
      L.latLng(lat, lng)
    ],
    routeWhileDragging: false,
    showAlternatives: false,
    lineOptions: { styles: [{ color: '#6613F0', weight: 5 }] },
    createMarker: () => null
  }).addTo(map);

  map.setView([lat, lng], 15);
}


function toggleFavorito(event, id) {
    event.stopPropagation();
    const btn = event.currentTarget;
    btn.classList.toggle('activo');
    
    console.log('Toggle favorito:', id);
}

// Ver detalles
function verDetalles(event, id) {
    event.stopPropagation();
    // Redirigir a página de detalles
    window.location.href = `../vistas/detalleNegocio.php?id=${id}`;
}

// Llamar negocio
function llamarNegocio(telefono) {
    if (telefono) {
        window.location.href = `tel:${telefono}`;
    } else {
        alert('Este negocio no tiene teléfono registrado');
    }
}

// Mostrar mensaje vacío
function mostrarMensajeVacio(mensaje) {
    const listaNegocios = document.getElementById('listaNegocios');
    listaNegocios.innerHTML = `
        <div class="mensaje-vacio">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <h3>No hay resultados</h3>
            <p>${mensaje}</p>
        </div>
    `;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    initMap();
    
    // Toggle panel móvil
    const btnTogglePanel = document.getElementById('btnTogglePanel');
    const panelLateral = document.getElementById('panelLateral');
    
    btnTogglePanel.addEventListener('click', () => {
        panelLateral.classList.toggle('activo');
    });
    
    // Buscador
    const inputBuscador = document.getElementById('inputBuscador');
    const btnLimpiar = document.getElementById('btnLimpiar');
    
    inputBuscador.addEventListener('input', function() {
        if (this.value) {
            btnLimpiar.classList.add('visible');
        } else {
            btnLimpiar.classList.remove('visible');
        }
        renderizarNegocios();
    });
    
    btnLimpiar.addEventListener('click', function() {
        inputBuscador.value = '';
        this.classList.remove('visible');
        renderizarNegocios();
    });
    
    // Filtros
    document.getElementById('filtroEstado').addEventListener('change', renderizarNegocios);
    document.getElementById('filtroOrden').addEventListener('change', renderizarNegocios);
    
    // Cerrar info card
    document.getElementById('btnCerrarCard').addEventListener('click', function() {
        document.getElementById('infoCard').style.display = 'none';
        
        // Restaurar iconos
        negociosData.forEach(n => {
            if (n.marker) {
                n.marker.setIcon(purpleIcon);
            }
        });
        
        // Quitar clase activo
        document.querySelectorAll('.negocio-card').forEach(card => {
            card.classList.remove('activo');
        });
    });
    
    // Cerrar panel al hacer click fuera en móvil
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!panelLateral.contains(e.target) && !btnTogglePanel.contains(e.target)) {
                panelLateral.classList.remove('activo');
            }
        }
    });
});