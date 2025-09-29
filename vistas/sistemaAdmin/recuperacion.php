<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../assets/css/recuperacion.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación</title>
   
</head>

<body>
    <div class="encabezado">
        <div class="imagen">
            <img src="../../assets/img/LogoYolocal.png" alt="Logo" class="logo">
        </div>
    </div>

    <div class="main">
        <div class="titulo">
            <h2>Recuperación de contraseña</h2>
        </div>

        <div class="formulario">
            <form id="formRecuperacion">
                <div id="alerta"></div>

                <div class="contenidoIn">
                    <div class="input-group">
                        <input type="email" id="correo" name="correo" class="form-control" placeholder="Correo electrónico" required>
                    </div>
                </div>

                <div class="boton">
                    <button type="submit" class="pushable">
                        <span class="shadow"></span>
                        <span class="edge"></span>
                        <span class="front">Enviar correo</span>
                    </button>
                </div>
                <div class="regresar">
                    <a href="login.php" type="button" class="btn">Regresar</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/recuperacion.js?v=3.1"></script>
</body>

</html>