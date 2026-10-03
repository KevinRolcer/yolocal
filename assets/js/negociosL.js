
let debounceTimer;

function activarFallbackIconos(contenedor) {
    contenedor.querySelectorAll('.negocio-icono').forEach((imagen) => {
        const mostrarFallback = () => {
            const fallback = imagen.parentElement.querySelector('.negocio-icono-fallback');
            if (!fallback) return;
            imagen.hidden = true;
            fallback.hidden = false;
        };

        if (imagen.complete && imagen.naturalWidth === 0) {
            mostrarFallback();
        } else {
            imagen.addEventListener('error', mostrarFallback, { once: true });
        }
    });
}

document.addEventListener('DOMContentLoaded', (event) => {
    const menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            const menu = document.getElementById('mainMenu');
            menu.classList.toggle('active');
        });
    }
    activarFallbackIconos(document);
});



function verDetalle(idNegocio) {
    window.location.href = `../controladores/DetalleNegocioControlador.php?id=${idNegocio}`;
}

function filtrarPorCategoria() {
    const idCategoria = document.getElementById('filtroCategoria').value;
    const urlBase = '../controladores/NegocioLControlador.php';

    if (idCategoria) {
       
        window.location.href = `${urlBase}?categoria=${idCategoria}`;
    } else {
        window.location.href = urlBase;
    }
}


function buscarEnTiempoReal() {
 
    clearTimeout(debounceTimer);

   
    debounceTimer = setTimeout(() => {
        const terminoBusqueda = document.getElementById('busqueda').value;
        const contenedorResultados = document.querySelector('.negocios-container');
        
        
        contenedorResultados.innerHTML = "<div class='empty-state'><p>Buscando...</p></div>";
        
       
        const url = `../controladores/NegocioLControlador.php?busqueda=${encodeURIComponent(terminoBusqueda)}&ajax=1`;

     
        fetch(url)
            .then(response => response.text()) 
            .then(html => {
               
                contenedorResultados.innerHTML = html;
                activarFallbackIconos(contenedorResultados);
            })
            .catch(error => {
            
                console.error('Error en la búsqueda:', error);
                contenedorResultados.innerHTML = "<div class='empty-state'><p>Ocurrió un error al buscar.</p></div>";
            });
    }, 300); 
}