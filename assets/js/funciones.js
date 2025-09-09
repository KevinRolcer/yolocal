import { validaCorreo, validaLargo, validaRango } from "./validaciones.js";
document.addEventListener("DOMContentLoaded", (event)=>
    {
        //Formulario de Login
        if(document.querySelector("#login"))
        {
            const login = document.querySelector("#login");
        
            login.addEventListener ("submit", (event) => {
                event.preventDefault();
                let errores = 0; 
                
                let correo = document.querySelector("#nombre");
                let clave = document.querySelector("#contra");
               
                if(!validaRango(clave,8,20))
                    errores++;
               
                if(errores==0)
                {                    
                    let info = new FormData(login);
                    info.append("ope","LOGIN");

                    fetch('../controladores/controladorLogin.php', {
                        method: 'POST',
                        body: info 
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if(data.success)
                        {
                            localStorage.setItem("tipo_usuario", data.tipo);
                            Swal.fire({
                                title: "¡Bienvenid@! a Yo local",
                                text: "La página se actualizará automáticamente, favor de esperar...",
                                imageUrl: "../assets/img/LogoYolocal.png", 
                                imageWidth: 100,
                                imageHeight: 100,
                                allowOutsideClick: false,
                                showConfirmButton: false, 
                                didOpen: () => {
                                  setTimeout(() => {
                                    window.location.href = data.ruta;
                                  }, 1000);
                                }
                              });
                              
                            
                        }
                        else
                        {
                            Swal.fire({
                                title: "Error!",
                                text: data.msg,
                                icon: "error"
                              });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: "Error",
                            text: "El Servidor ha presentado un error interno, favor de intentarlo más tarde o informar! ",
                            icon: "error"
                          });
                    });
                }
                
                //Evento de Limpieza
                login.addEventListener("keydown", (event) => {
                    let elemento = event.target;
                    elemento.classList.remove("is-valid");
                    elemento.classList.remove("is-invalid");
                });        
            });
        }

        
    });
    