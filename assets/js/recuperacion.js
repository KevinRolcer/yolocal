document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#formRecuperacion");

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const correo = document.querySelector("#correo").value.trim();
        const alertaDiv = document.querySelector("#alerta");

        if (correo === "") {
            alertaDiv.innerHTML = '<div class="alert alert-danger">Por favor, ingresa un correo.</div>';
            return;
        }

        fetch("../../../controladores/RecuperacionController.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `correo=${encodeURIComponent(correo)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertaDiv.innerHTML = '<div class="alert alert-success">Correo enviado con éxito.</div>';
            } else {
                alertaDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            alertaDiv.innerHTML = '<div class="alert alert-danger">Error al procesar la solicitud.</div>';
        });
    });
});