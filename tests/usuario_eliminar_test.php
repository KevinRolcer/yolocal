<?php
require_once __DIR__ . '/../config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function comprobarEliminacion($condicion, $mensaje)
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function solicitarEliminacion($idUsuario)
{
    $_POST = ['ope' => 'ELIMINAR', 'ID_Usuario' => $idUsuario];
    ob_start();
    try {
        require __DIR__ . '/../controladores/controladorUsuarios.php';
        return json_decode(ob_get_contents(), true, 512, JSON_THROW_ON_ERROR);
    } finally {
        ob_end_clean();
    }
}

try {
    $conexion = dbConectar();
    // Estas tablas temporales ocultan las reales solo durante esta conexión.
    $conexion->query('CREATE TEMPORARY TABLE usuarios (ID_Usuario INT PRIMARY KEY)');
    $conexion->query('CREATE TEMPORARY TABLE negocios (ID_Negocio INT PRIMARY KEY, ID_Usuario INT)');
    $conexion->query('INSERT INTO usuarios VALUES (1), (2)');
    $conexion->query('INSERT INTO negocios VALUES (10, 1), (11, 1)');
    chdir(__DIR__ . '/../controladores');

    $respuesta = solicitarEliminacion(1);
    comprobarEliminacion($respuesta['success'] === false, 'Debe impedir eliminar al usuario con negocios.');
    comprobarEliminacion(
        str_contains($respuesta['msg'] ?? '', 'negocios asignados'),
        'La respuesta debe explicar que el usuario tiene negocios asignados.'
    );
    comprobarEliminacion(
        $conexion->query('SELECT ID_Usuario FROM usuarios WHERE ID_Usuario = 1')->num_rows === 1,
        'El usuario con negocios debe conservarse.'
    );
    comprobarEliminacion(
        $conexion->query('SELECT ID_Negocio FROM negocios WHERE ID_Usuario = 1')->num_rows === 2,
        'Los negocios asignados deben conservarse.'
    );

    $respuesta = solicitarEliminacion(2);
    comprobarEliminacion($respuesta['success'] === true, 'Debe permitir eliminar un usuario sin negocios.');
    comprobarEliminacion(
        $conexion->query('SELECT ID_Usuario FROM usuarios WHERE ID_Usuario = 2')->num_rows === 0,
        'El usuario sin negocios debe eliminarse.'
    );
    echo "PASS: bloqueo con mensaje claro y eliminación de usuario sin negocios.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'FAIL: ' . $error->getMessage() . "\n");
    exit(1);
}
