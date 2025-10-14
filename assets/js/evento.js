
document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('modal-evento');
    if (!modal) return; 

    const modalCerrar = modal.querySelector('.modal-cerrar');
    const modalImg = document.getElementById('modal-img');
    const modalTitulo = document.getElementById('modal-titulo');
    const modalCategoria = document.getElementById('modal-categoria');
    const modalPrecio = document.getElementById('modal-precio');
    const modalDescripcion = document.getElementById('modal-descripcion');
    const modalFecha = document.getElementById('modal-fecha');
    const modalHora = document.getElementById('modal-hora');
    const modalUbicacion = document.getElementById('modal-ubicacion');

   
    const botonesInfo = document.querySelectorAll('.btn-secondary, .btn-primary');


    botonesInfo.forEach(boton => {
        boton.addEventListener('click', function() {
           
            const tarjeta = this.closest('.event-card');


            const imagenSrc = tarjeta.querySelector('.card-image').src;
            const titulo = tarjeta.querySelector('h3').textContent;
            const categoria = tarjeta.querySelector('.card-tag').textContent;
            const precio = tarjeta.querySelector('.card-price').textContent;
            const descripcion = tarjeta.querySelector('.card-description').textContent;
        
            const fechaHTML = tarjeta.querySelector('.card-details .detail-item:nth-child(1)').innerHTML;
            const horaHTML = tarjeta.querySelector('.card-details .detail-item:nth-child(2)').innerHTML;
            const ubicacionHTML = tarjeta.querySelector('.card-details .detail-item:nth-child(3)').innerHTML;

         
            modalImg.src = imagenSrc;
            modalTitulo.textContent = titulo;
            modalCategoria.textContent = categoria;
            modalPrecio.textContent = precio;
            modalDescripcion.textContent = descripcion;
            modalFecha.innerHTML = fechaHTML;
            modalHora.innerHTML = horaHTML;
            modalUbicacion.innerHTML = ubicacionHTML;
            
                   modal.style.display = 'flex';
        });
    });


    function cerrarModal() {
        modal.style.display = 'none';
    }

    modalCerrar.addEventListener('click', cerrarModal);

   
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            cerrarModal();
        }
    });

}); 