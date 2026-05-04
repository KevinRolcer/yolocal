import { validaCorreo, validaLargo, validaContrasena } from "./validaciones.js?v=3.8.3";

document.addEventListener("DOMContentLoaded", () => {
    if (document.querySelector("#login")) {
        const login = document.querySelector("#login");

        const swalClasses = {
            popup: "login-swal-popup",
            title: "login-swal-title",
            htmlContainer: "login-swal-text"
        };

        login.addEventListener("submit", (event) => {
            event.preventDefault();
            let errores = 0;

            if (errores == 0) {
                let info = new FormData(login);
                info.append("ope", "LOGIN");

                fetch("../../controladores/controladorLogin.php", {
                    method: "POST",
                    body: info
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            localStorage.setItem("tipo_usuario", data.tipo);
                            Swal.fire({
                                title: "Acceso confirmado",
                                text: "Estamos preparando tu panel administrativo...",
                                imageUrl: "../../assets/img/LogoYolocal.png",
                                imageWidth: 96,
                                imageHeight: 96,
                                heightAuto: false,
                                scrollbarPadding: false,
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                customClass: swalClasses,
                                didOpen: () => {
                                    setTimeout(() => {
                                        window.location.href = data.ruta;
                                    }, 1000);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: data.msg,
                                icon: "error",
                                heightAuto: false,
                                scrollbarPadding: false,
                                customClass: swalClasses
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            title: "Error",
                            text: "El servidor presento un error interno. Intenta de nuevo mas tarde.",
                            icon: "error",
                            heightAuto: false,
                            scrollbarPadding: false,
                            customClass: swalClasses
                        });
                    });
            }
        });

        login.addEventListener("keydown", (event) => {
            let elemento = event.target;
            elemento.classList.remove("is-valid");
            elemento.classList.remove("is-invalid");
        });
    }
});
