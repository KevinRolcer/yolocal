<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<div class="error-container">
    <div class="error-content">
        <h1 class="error-code">
            <span class="num glitch-num">5</span>
            <span class="num gear gear-left">0</span>
            <span class="num gear gear-right">0</span>
        </h1>
        
        <h2 class="error-title">¡Cortocircuito en el local!</h2>
        <p class="error-text">Nuestros servidores están teniendo un pequeño problema técnico. Estamos trabajando para arreglar los engranajes del barrio lo antes posible.</p>
        
        <a href="index.php" class="btn-yolocal">Intentar de nuevo</a>
    </div>
</div>

<style>
    :root {
        --color-morado: #511c8a; 
        --color-amarillo: #ffd500; 
        --color-blanco: #ffffff;
    }

    body, html { margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; }

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
        gap: 10px;
        color: var(--color-amarillo);
    }

    .gear {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 120px;
        height: 120px;
        background-color: transparent;
        color: var(--color-amarillo);
        border-radius: 50%;
        font-size: 6rem;
        border: 10px dashed var(--color-amarillo); 
    }

    .error-title {
        font-size: 2.5rem;
        margin-top: 30px;
        margin-bottom: 10px;
    }

    .error-text {
        font-size: 1.2rem;
        max-width: 550px;
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
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .btn-yolocal:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(255, 213, 0, 0.4);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    anime({
        targets: '.glitch-num',
        translateX: [
            { value: -4, duration: 50 },
            { value: 4, duration: 50 },
            { value: -4, duration: 50 },
            { value: 4, duration: 50 },
            { value: 0, duration: 50 }
        ],
        opacity: [
            { value: 0.8, duration: 50 },
            { value: 1, duration: 50 }
        ],
        delay: anime.random(2000, 4000), // Tiembla de forma aleatoria cada 2-4 segundos
        loop: true,
        easing: 'easeInOutSine'
    });

    // 2. Animación del primer "0" (Engranaje girando hacia la derecha)
    anime({
        targets: '.gear-left',
        rotate: 360,
        duration: 6000, // Tarda 6 segundos en dar una vuelta completa
        loop: true,
        easing: 'linear' // Movimiento constante, sin acelerar ni frenar
    });

    // 3. Animación del segundo "0" (Engranaje girando hacia la izquierda)
    // Para que parezca un mecanismo real, el engranaje contiguo debe girar al revés
    anime({
        targets: '.gear-right',
        rotate: -360, 
        duration: 6000,
        loop: true,
        easing: 'linear'
    });

    // 4. Animación de entrada suave para todo el contenido
    anime({
        targets: '.error-content',
        opacity: [0, 1],
        translateY: [20, 0],
        duration: 1200,
        easing: 'easeOutCubic'
    });

});
</script>