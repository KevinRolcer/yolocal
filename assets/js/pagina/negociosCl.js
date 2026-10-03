document.querySelectorAll(".miCarrusel img, .imagen-limpia img").forEach((imagen) => {
    const mostrarFallback = () => {
        const contenedor = imagen.closest(".swiper-slide, .imagen-limpia");
        const fallback = contenedor?.querySelector(".imagen-fallback");
        if (!fallback) return;
        imagen.hidden = true;
        fallback.hidden = false;
    };

    if (imagen.complete && imagen.naturalWidth === 0) {
        mostrarFallback();
    } else {
        imagen.addEventListener("error", mostrarFallback, { once: true });
    }
});

var swiper = new Swiper(".miCarrusel", {
           
            loop: true,

       
            autoplay: {
              delay: 3000, 
              disableOnInteraction: false, 
            },
            
          
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },

          
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });