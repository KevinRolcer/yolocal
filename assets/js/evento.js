document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modal-evento");
  if (!modal) return;

  const modalCerrar = modal.querySelector(".modal-cerrar");
  const modalImg = document.getElementById("modal-img");
  const modalTitulo = document.getElementById("modal-titulo");
  const modalCategoria = document.getElementById("modal-categoria");
  const modalPrecio = document.getElementById("modal-precio");
  const modalDescripcion = document.getElementById("modal-descripcion");
  const modalFecha = document.getElementById("modal-fecha");
  const modalHora = document.getElementById("modal-hora");
  const modalUbicacion = document.getElementById("modal-ubicacion");

  function abrirModal(tarjeta) {
    if (!tarjeta) return;

    const eventoId = tarjeta.dataset.eventoId;

    modalImg.src = tarjeta.querySelector(".card-image").src;
    modalTitulo.textContent = tarjeta.querySelector("h3").textContent;
    modalCategoria.textContent = tarjeta.querySelector(".card-tag").textContent;
    modalPrecio.textContent = tarjeta.querySelector(".card-price").textContent;
    modalDescripcion.textContent =
      tarjeta.querySelector(".card-description").textContent;
    modalFecha.innerHTML = tarjeta.querySelector(
      ".card-details .detail-item:nth-child(1)"
    ).innerHTML;
    modalHora.innerHTML = tarjeta.querySelector(
      ".card-details .detail-item:nth-child(2)"
    ).innerHTML;
    modalUbicacion.innerHTML = tarjeta.querySelector(
      ".card-details .detail-item:nth-child(3)"
    ).innerHTML;

    modal.style.display = "flex";

    if (eventoId) {
      window.location.hash = "evento-" + eventoId;
    }
  }

  function cerrarModal() {
    modal.style.display = "none";

    history.pushState(
      "",
      document.title,
      window.location.pathname + window.location.search
    );
  }

  function abrirModalDesdeURL() {
    const hash = window.location.hash;
    if (hash.startsWith("#evento-")) {
      const eventoId = hash.substring("#evento-".length);
      const tarjeta = document.querySelector(
        `.event-card[data-evento-id="${eventoId}"]`
      );
      if (tarjeta) {
        // Si encontramos la tarjeta, llamamos a nuestra función central
        abrirModal(tarjeta);
      }
    }
  }

  const botonesInfo = document.querySelectorAll(".btn-primary");
  botonesInfo.forEach((boton) => {
    boton.addEventListener("click", function () {
      const tarjeta = this.closest(".event-card");
      abrirModal(tarjeta); 
    });
  });

  // Lógica para el botón de compartir (esta parte estaba bien)
  const botonesCompartir = document.querySelectorAll(".btn-share");
  botonesCompartir.forEach((boton) => {
    boton.addEventListener("click", function (event) {
      event.stopPropagation();
      const tarjeta = this.closest(".event-card");
      const eventoId = tarjeta.dataset.eventoId;
      if (!eventoId) return;

      const urlParaCompartir = `${window.location.origin}${window.location.pathname}#evento-${eventoId}`;

      navigator.clipboard
        .writeText(urlParaCompartir)
        .then(() => {
         
          Swal.fire({
            icon: "success",
            title: "¡Enlace Copiado al portapapeles!",
            text: urlParaCompartir,
            confirmButtonColor: "#3085d6", 
            timer: 2000, 
          });
        })
        .catch((err) => {
          console.error("Error al copiar el enlace: ", err);

          Swal.fire({
            icon: "error",
            title: "¡Oops!",
            text: "No se pudo copiar el enlace.",
          });
        });
    });
  });

  // Eventos para cerrar el modal
  modalCerrar.addEventListener("click", cerrarModal);
  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      cerrarModal();
    }
  });

  abrirModalDesdeURL();
});
