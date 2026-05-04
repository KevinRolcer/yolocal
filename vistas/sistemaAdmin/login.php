<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - YoLocal</title>
    <link href="../../assets/img/LogoYolocal.png" rel="icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/loginl.css">
    <script type="module" src="../../assets/js/funciones.js?v=3.1"></script>
</head>

<body class="login-page">
    <main class="login-card" aria-label="Acceso administrativo Yo Local">
        <div class="left-section">
            <div class="brand-panel">
                <img src="../../assets/img/LogoYolocal.png" alt="Yo Local" class="logo-icon">
            </div>
        </div>
        <div class="right-section">
            <form method="POST" id="login" class="login-form">
                <div class="login-heading">
                    <span class="login-eyebrow">Panel administrativo</span>
                    <h2>Inicio de Sesi&oacute;n</h2>
                    <p>Si es de aqu&iacute;, es de todos</p>
                </div>
                <div class="login-alert-slot" aria-live="polite">
                    <?php echo isset($alert) ? $alert : ""; ?>
                </div>
                <div class="input-group">
                    <label for="nombre"><i class="bi bi-person"></i> Usuario</label>
                    <input type="text" id="nombre" name="nombre" minlength="5" maxlength="50" pattern=".{5,50}" required>
                    <div class="invalid-feedback">Usuario inv&aacute;lido</div>
                    <div class="valid-feedback">Correcto</div>
                </div>
                <div class="input-group">
                    <label for="contra"><i class="bi bi-lock"></i> Contrase&ntilde;a</label>
                    <input type="password" id="contra" name="contra" required>
                    <div class="invalid-feedback">La contrase&ntilde;a es obligatoria</div>
                    <div class="valid-feedback">Correcto</div>
                </div>
                <a href="recuperacion.php" class="forgot-link">&iquest;Olvid&oacute; la contrase&ntilde;a?</a>
                <button type="submit" class="login-btn">
                    <span>Ingresar</span>
                </button>
            </form>
        </div>
    </main>
</body>

</html>
