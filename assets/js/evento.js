document.addEventListener("DOMContentLoaded", function () {
  function irAEventoDesdeURL() {
    const params = new URLSearchParams(window.location.search);
    const eventoId = params.get("evento");
    if (!eventoId) return;
    const safeId =
      typeof CSS !== "undefined" && CSS.escape
        ? CSS.escape(eventoId)
        : eventoId.replace(/["\\]/g, "");
    const tarjeta = document.querySelector(
      `.event-card[data-evento-id="${safeId}"]`
    );
    if (!tarjeta) return;
    tarjeta.scrollIntoView({ behavior: "smooth", block: "center" });
    try {
      tarjeta.focus({ preventScroll: true });
    } catch (_) {
      /* sin foco programático en algunos entornos */
    }
  }

  document.querySelectorAll(".btn-share").forEach((boton) => {
    boton.addEventListener("click", function (event) {
      event.stopPropagation();
      const tarjeta = this.closest(".event-card");
      if (!tarjeta) return;
      const eventoId = tarjeta.dataset.eventoId;
      if (!eventoId) return;

      const urlParaCompartir = `${window.location.origin}${window.location.pathname}?evento=${encodeURIComponent(eventoId)}`;

      navigator.clipboard.writeText(urlParaCompartir).then(() => {
        Swal.fire({
          icon: "success",
          title: "¡Enlace copiado!",
          text: "Ya puedes compartirlo donde quieras.",
          timer: 2500,
        });
      }).catch((err) => {
        console.error("Error al copiar el enlace: ", err);
      });
    });
  });

  irAEventoDesdeURL();
});
