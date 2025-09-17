document.addEventListener("DOMContentLoaded", () => {
    const carrusel = document.getElementById("carruselC");
    const btnPrevio = document.getElementById("btnPrevio");
    const btnSiguiente = document.getElementById("btnSiguiente");

    let indice = 0;
    const totalTarjetas = carrusel.children.length;
    const anchoTarjeta = carrusel.children[0].offsetWidth + 24;

    function actualizarBotones() {
        btnPrevio.disabled = indice === 0;
        btnSiguiente.disabled = indice >= totalTarjetas - 3;
    }

    function moverCarrusel() {
        carrusel.style.transform = `translateX(${-indice * anchoTarjeta}px)`;
        actualizarBotones();
    }

    btnPrevio.addEventListener("click", () => {
        if (indice > 0) {
            indice--;
            moverCarrusel();
        }
    });

    btnSiguiente.addEventListener("click", () => {
        if (indice < totalTarjetas - 3) {
            indice++;
            moverCarrusel();
        }
    });

    actualizarBotones();
});
