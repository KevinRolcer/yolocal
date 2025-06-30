var box = document.getElementById('box'); 
var activa = false;

function toggleNotifi() {
    if (activa) {
        box.style.height = '0px';
        box.style.opacity = '0';
        activa = false;
    } else {
        box.style.height = '510px';
        box.style.opacity = '1';
        activa = true;
    }
}

document.addEventListener('click', function(event) {
    if (activa && !box.contains(event.target) && !event.target.closest('.notificacion')) {
        box.style.height = '0px';
        box.style.opacity = '0';
        activa = false;
    }
});
