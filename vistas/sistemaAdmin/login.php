<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - YoLocal</title>
    <link rel="stylesheet" href="../../assets/css/loginl.css">
    <link href="../../assets/images/logo.jpg" rel="icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
   
    <?php include_once("head.php"); ?>
    <script type="module" src="../../assets/js/funciones.js?v=3.1"></script>
</head>

<body>
    <div class="container">
        <div class="left-section">
            <img src="../../assets/img/LogoYolocal.png" alt="Ilustración" class="logo-icon">
        </div>
        <div class="right-section">
            <form method="POST" id="login" class="login-form">
                <h2>Inicio de Sesión</h2>
                <p>Si es de aquí, es de todos</p>
                <div>
                    <?php echo isset($alert) ? $alert : ""; ?>
                </div>
                <div class="input-group">
                    <label for="nombre"><i class="fas fa-user"></i> Usuario</label>
                    <input type="text" id="nombre" name="nombre" minlength="5" maxlength="50" pattern=".{5,50}" required>
                    <div class="invalid-feedback">Usuario inválido</div>
                    <div class="valid-feedback">Correcto</div>
                </div>
                <div class="input-group">
                    <label for="contra"><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" id="contra" name="contra" required>
                    <div class="invalid-feedback">La contraseña es obligatoria</div>
                    <div class="valid-feedback">Correcto</div>
                </div>
                <a href="recuperacion.php" class="forgot-link">¿Olvidó la contraseña?</a>
                <button type="submit" class="login-btn">
                    <span>Ingresar</span>
                </button>
            </form>
        </div>
    </div>
</body>

</html>
