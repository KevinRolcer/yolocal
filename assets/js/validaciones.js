export function validaCorreo(elemento) {
    let validEmail = /^\w+([.-_+]?\w+)*@\w+([.-]?\w+)*(\.\w{2,10})+$/;
    
    if (elemento.value === "") {
        mostrarMensaje(elemento, "El campo es obligatorio", false);
        return false;
    } else if (validEmail.test(elemento.value)) {
        mostrarMensaje(elemento, "Correcto!", true);
        return true;
    } else {
        mostrarMensaje(elemento, "Correo no válido!", false);
        return false;
    }
}
export function validaNumero(elemento) {
    let numero = parseFloat(elemento.value.trim()); // Convertir a número

    if (elemento.value === "") {
        mostrarMensaje(elemento, "El campo es obligatorio", false);
        return false;
    } else if (isNaN(numero)) {
        mostrarMensaje(elemento, "Debe ser un número válido.", false);
        return false;
    } else if (numero <= 0 || numero >= 10000) {
        mostrarMensaje(elemento, "Debe ser mayor a 0 y menor a 10,000.", false);
        return false;
    } else {
        mostrarMensaje(elemento, "Número válido!", true);
        return true;
    }
}
export function validaTelefono(elemento) {
    let validTelefono = /^\d{10}$/; // Solo acepta 10 dígitos numéricos
    let repetidos = /^(\d)\1{9}$/; // Detecta números con todos los dígitos iguales

    if (elemento.value === "") {
        mostrarMensaje(elemento, "El campo es obligatorio", false);
        return false;
    } else if (!validTelefono.test(elemento.value.trim())) {
        mostrarMensaje(elemento, "El número debe tener exactamente 10 dígitos numéricos.", false);
        return false;
    } else if (repetidos.test(elemento.value.trim())) {
        mostrarMensaje(elemento, "El número no puede contener solo dígitos repetidos.", false);
        return false;
    } else {
        mostrarMensaje(elemento, "Número válido!", true);
        return true;
    }
}

export function validaPin(elemento) {
    let validTelefono = /^\d{4}$/; // Solo acepta 4 dígitos numéricos
    let repetidos = /^(\d)\1{9}$/; // Detecta números con todos los dígitos iguales

    if (elemento.value === "") {
        mostrarMensaje(elemento, "El campo es obligatorio", false);
        return false;
    } else if (!validTelefono.test(elemento.value.trim())) {
        mostrarMensaje(elemento, "El número debe tener exactamente 4 dígitos numéricos.", false);
        return false;
    } else if (repetidos.test(elemento.value.trim())) {
        mostrarMensaje(elemento, "El número no puede contener solo dígitos repetidos.", false);
        return false;
    } else {
        mostrarMensaje(elemento, "Número válido!", true);
        return true;
    }
}
export function validaLargo(elemento, largo) {
    if (elemento.value === "") {
        mostrarMensaje(elemento, "El campo es obligatorio", false);
        return false;
    } else if (elemento.value.length !== largo) {
        mostrarMensaje(elemento, `Debe ser de ${largo} caracteres!`, false);
        return false;
    } else {
        mostrarMensaje(elemento, "Correcto!", true);
        return true;
    }
}


export function validaSoloLetras(elemento) {
    let validLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    if (elemento.value === "") {
        mostrarMensaje(elemento, "El campo es obligatorio", false);
        return false;
    } else if (!validLetras.test(elemento.value)) {
        mostrarMensaje(elemento, "Solo se permiten letras!", false);
        return false;
    } else {
        mostrarMensaje(elemento, "Correcto!", true);
        return true;
    }
}
export function validaContrasena(elemento, min, max) {
    let validContrasena = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/;

    if (elemento.value === "") {
        mostrarMensaje(elemento, "El campo es obligatorio", false);
        return false;
    } else if (elemento.value.length < min || elemento.value.length > max) {
        mostrarMensaje(
            elemento,
            `Debe tener entre ${min} y ${max} caracteres!`,
            false
        );
        return false;
    } else if (!validContrasena.test(elemento.value)) {
        mostrarMensaje(
            elemento,
            "Debe contener al menos un carácter especial!",
            false
        );
        return false;
    } else {
        mostrarMensaje(elemento, "Correcto!", true);
        return true;
    }
}

function mostrarMensaje(elemento, mensaje, esValido) {
    let mensajeElemento = elemento.parentElement.querySelector(esValido ? ".valid-feedback" : ".invalid-feedback");
    mensajeElemento.innerText = mensaje;
    
    elemento.classList.remove("is-invalid", "is-valid");
    
    if (esValido) {
        elemento.classList.add("is-valid");
        
    } else {
        elemento.classList.add("is-invalid");
    }
}
