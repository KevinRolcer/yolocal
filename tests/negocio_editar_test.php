<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/negocios.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function comprobarEdicion($condicion, $mensaje)
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

try {
    $conexion = dbConectar();
    // La tabla temporal oculta la real solamente durante esta conexión.
    $conexion->query('CREATE TEMPORARY TABLE negocios (
        ID_Negocio INT PRIMARY KEY,
        nombre_negocio VARCHAR(50), DescripcionN TEXT, Direccion VARCHAR(150),
        Telefono VARCHAR(15), CorreoN VARCHAR(50), SitioWeb VARCHAR(100),
        Facebook VARCHAR(100), Instagram VARCHAR(100), TikTok VARCHAR(100),
        GoogleMaps VARCHAR(100), Latitud VARCHAR(100), Longitud VARCHAR(100),
        Relevancia INT NOT NULL, Rutaicono VARCHAR(255)
    )');
    $conexion->query("INSERT INTO negocios (ID_Negocio, nombre_negocio, Relevancia, Rutaicono)
        VALUES (1, 'Nombre anterior', 3, '../assets/uploads/iconos/original.png')");

    $datos = [
        'ID_Negocio' => 1,
        'nombre_negocioEdit' => 'Cafetería actualizada',
        'DescripcionN' => 'Café y pan artesanal',
        'Direccion' => 'Calle Centro 12',
        'Telefono' => '2481234567',
        'CorreoN' => 'contacto@example.com',
        'SitioWeb' => 'https://example.com',
        'Facebook' => 'https://facebook.com/cafeteria',
        'Instagram' => 'https://instagram.com/cafeteria',
        'TikTok' => 'https://tiktok.com/@cafeteria',
        'GoogleMaps' => 'https://maps.google.com/?q=19.28,-98.43',
        'Latitud' => '19.28',
        'Longitud' => '-98.43',
        'Relevancia' => null,
        'Icono' => '../assets/uploads/iconos/original.png',
    ];

    $modelo = new Negocios();
    comprobarEdicion($modelo->Editar($datos), 'La edición debe guardarse.');
    $negocio = $conexion->query('SELECT * FROM negocios WHERE ID_Negocio = 1')->fetch_assoc();
    $esperado = [
        'nombre_negocio' => 'Cafetería actualizada',
        'DescripcionN' => 'Café y pan artesanal',
        'Direccion' => 'Calle Centro 12',
        'Telefono' => '2481234567',
        'CorreoN' => 'contacto@example.com',
        'SitioWeb' => 'https://example.com',
        'Facebook' => 'https://facebook.com/cafeteria',
        'Instagram' => 'https://instagram.com/cafeteria',
        'TikTok' => 'https://tiktok.com/@cafeteria',
        'GoogleMaps' => 'https://maps.google.com/?q=19.28,-98.43',
        'Latitud' => '19.28',
        'Longitud' => '-98.43',
        'Relevancia' => '3',
        'Rutaicono' => '../assets/uploads/iconos/original.png',
    ];
    foreach ($esperado as $campo => $valor) {
        comprobarEdicion((string) $negocio[$campo] === $valor, "Valor incorrecto en $campo.");
    }

    $datos['Relevancia'] = '0';
    $datos['Telefono'] = '';
    comprobarEdicion($modelo->Editar($datos), 'La segunda edición debe guardarse.');
    $negocio = $conexion->query('SELECT Relevancia, Telefono FROM negocios WHERE ID_Negocio = 1')->fetch_assoc();
    comprobarEdicion((int) $negocio['Relevancia'] === 0, 'Debe permitir actualizar la relevancia a cero.');
    comprobarEdicion($negocio['Telefono'] === '', 'Debe permitir borrar un campo opcional.');

    iniciarSesionYoLocal();
    $_POST = [
        'ope' => 'EDITAR',
        'ID_Negocio' => '1',
        'nombre_negocioEdit' => 'Nombre desde formulario',
        'DescripcionNEdit' => 'Descripción desde formulario',
        'TelefonoEdit' => '2487654321',
        'RutaiconoEdit' => '../assets/uploads/iconos/original.png',
    ];
    foreach (['negocio', 'admin'] as $tipoSesion) {
        $_SESSION['tipo'] = $tipoSesion;
        if ($tipoSesion === 'admin') {
            $_POST['RelevanciaEdit'] = '2';
        }
        ob_start();
        require __DIR__ . '/../controladores/controladorNegocios.php';
        $respuesta = json_decode(ob_get_clean(), true, 512, JSON_THROW_ON_ERROR);
        comprobarEdicion($respuesta['success'] === true, "El controlador debe aceptar la edición de $tipoSesion.");
        $negocio = $conexion->query('SELECT * FROM negocios WHERE ID_Negocio = 1')->fetch_assoc();
        comprobarEdicion($negocio['nombre_negocio'] === 'Nombre desde formulario', 'El controlador debe guardar el nombre.');
        comprobarEdicion($negocio['DescripcionN'] === 'Descripción desde formulario', 'El controlador debe mapear la descripción.');
        comprobarEdicion($negocio['Telefono'] === '2487654321', 'El controlador debe mapear el teléfono.');
        comprobarEdicion($negocio['Rutaicono'] === '../assets/uploads/iconos/original.png', 'El controlador debe conservar el logo.');
        $relevanciaEsperada = $tipoSesion === 'admin' ? 2 : 0;
        comprobarEdicion((int) $negocio['Relevancia'] === $relevanciaEsperada, 'La relevancia debe respetar el formulario de cada rol.');
    }
    session_abort();
    echo "PASS: modelo y controlador; edición como negocio y admin, logo, relevancia y campos opcionales.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'FAIL: ' . $error->getMessage() . "\n");
    exit(1);
}
