<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            background: linear-gradient(to bottom, #000000 0%, #0a0a0a 70%, #1a237e 100%);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .earth {
            position: fixed;
            bottom: -20%;
            left: 0;
            width: 100%;
            height: 40%;
            background: linear-gradient(to bottom, 
                rgba(100, 181, 246, 0.3) 0%,
                rgba(66, 165, 245, 0.5) 30%,
                rgba(41, 182, 246, 0.7) 100%);
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            z-index: 1;
            filter: blur(2px);
        }

        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s infinite;
        }

        .imagen{
            width: 120px;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        .container {
            text-align: center;
            z-index: 10;
            position: relative;
            padding: 20px;
            max-width: 600px;
        }

        .astronaut {
            font-size: clamp(60px, 15vw, 120px);
            animation: float 6s ease-in-out infinite;
            display: inline-block;
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.3));
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(-5deg);
            }
            25% {
                transform: translate(10px, -20px) rotate(5deg);
            }
            50% {
                transform: translate(-5px, -10px) rotate(-3deg);
            }
            75% {
                transform: translate(-10px, -25px) rotate(3deg);
            }
        }

        .oops {
            font-size: clamp(24px, 5vw, 40px);
            font-weight: 300;
            letter-spacing: 8px;
            margin-bottom: 10px;
            animation: fadeIn 1s ease-in;
            text-transform: uppercase;
        }

        .error-code {
            font-size: clamp(80px, 25vw, 200px);
            font-weight: bold;
            line-height: 1;
            margin: 20px 0;
            background: linear-gradient(45deg, #fff, #64b5f6, #fff);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient 3s ease infinite, scaleIn 0.8s ease-out;
            letter-spacing: 10px;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.5);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .message {
            font-size: clamp(14px, 3vw, 18px);
            line-height: 1.6;
            margin: 20px auto;
            max-width: 500px;
            animation: fadeIn 1.5s ease-in;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 300;
        }

        @media (max-width: 768px) {
            .container {
                padding: 40px 20px;
            }

            .error-code {
                letter-spacing: 5px;
            }

            .oops {
                letter-spacing: 4px;
            }
        }

        @media (max-width: 480px) {
            .astronaut {
                margin-bottom: 10px;
            }

            .message {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
    <div class="stars" id="stars"></div>
    <div class="earth"></div>
    
    <div class="container">
        <div class="astronaut">
            <img class="imagen" src="../assets/img/LogoYolocal.png" alt="">
        </div>
        <div class="oops">Oops!</div>
        <div class="error-code">404</div>
        <div class="message">
            Tu página está actualmente bajo mantenimiento<br>
            Por favor, intenta de nuevo más tarde.
        </div>
    </div>

    <script>
        const starsContainer = document.getElementById('stars');
        for (let i = 0; i < 100; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.width = Math.random() * 3 + 'px';
            star.style.height = star.style.width;
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 3 + 's';
            starsContainer.appendChild(star);
        }
    </script>
</body>
</html>