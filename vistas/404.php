<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<div class="error-container">
    
    <div class="error-content">
        <h1 class="error-code">
            <span class="num">4</span>
            <span class="num badge-zero">0</span>
            <span class="num">4</span>
        </h1>
        
        <h2 class="error-title">¡Ups! Nos salimos del barrio.</h2>
        <p class="error-text">No pudimos encontrar lo que buscabas. Tal vez el emprendedor se mudó o la página ya no existe.</p>
        
        <a href="index.php" class="btn-yolocal">Volver al inicio</a>
    </div>

</div>

<style>
    :root {
        --color-morado: #511c8a; /* Aproximado al fondo de tu web */
        --color-amarillo: #ffd500; /* Aproximado al botón amarillo */
        --color-blanco: #ffffff;
    }

    body, html { margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; /* Asumiendo que usas una fuente similar */ }

    .error-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: var(--color-morado);
        color: var(--color-blanco);
        text-align: center;
        overflow: hidden;
    }

    .error-code {
        font-size: 8rem;
        font-weight: 900;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        color: var(--color-amarillo);
    }

    .badge-zero {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 140px;
        height: 140px;
        background-color: var(--color-amarillo);
        color: var(--color-morado);
        border-radius: 50%;
        font-size: 7rem;
        border: 4px dashed var(--color-blanco); /* Toque similar a los bordes de tu logo */
    }

    .error-title {
        font-size: 2.5rem;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .error-text {
        font-size: 1.2rem;
        max-width: 500px;
        margin: 0 auto 40px auto;
        line-height: 1.5;
    }

    .btn-yolocal {
        display: inline-block;
        background-color: var(--color-amarillo);
        color: var(--color-morado);
        text-decoration: none;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 1.1rem;
        transition: transform 0.2s ease;
    }
    
    .btn-yolocal:hover {
        transform: scale(1.05);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Animación de entrada general (Fade In hacia arriba)
    anime({
        targets: '.error-content',
        translateY: [50, 0],
        opacity: [0, 1],
        duration: 1000,
        easing: 'easeOutExpo'
    });

    // 2. Animación del "0" (Cae, rebota y luego gira infinitamente)
    let badgeAnim = anime.timeline({
        loop: false
    });

    badgeAnim.add({
        targets: '.badge-zero',
        translateY: [-200, 0],
        scale: [0.5, 1],
        duration: 1500,
        easing: 'easeOutElastic(1, .5)', // Efecto de rebote
        complete: function() {
            // Cuando termina de rebotar, empieza a girar lento
            anime({
                targets: '.badge-zero',
                rotate: 360,
                duration: 10000, // Gira muy lento (10 segundos por vuelta)
                loop: true,
                easing: 'linear'
            });
        }
    });

    // 3. Pequeño rebote en el botón para llamar la atención
    anime({
        targets: '.btn-yolocal',
        scale: [1, 1.1, 1],
        duration: 1500,
        delay: 2000,
        loop: true,
        easing: 'easeInOutSine'
    });

});
</script>

