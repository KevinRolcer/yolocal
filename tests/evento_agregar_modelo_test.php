<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/eventos.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conexion = dbConectar();
try {
    $columnas = $conexion->query('SHOW COLUMNS FROM eventos')->fetch_all(MYSQLI_ASSOC);
    $definiciones = [];
    foreach ($columnas as $columna) {
        $definicion = '`' . $columna['Field'] . '` ' . $columna['Type'];
        $definicion .= $columna['Null'] === 'YES' ? ' NULL' : ' NOT NULL';
        if ($columna['Default'] !== null) {
            $definicion .= " DEFAULT '" . $conexion->real_escape_string($columna['Default']) . "'";
        }
        if (str_contains($columna['Extra'], 'auto_increment')) {
            $definicion .= ' AUTO_INCREMENT PRIMARY KEY';
        }
        $definiciones[] = $definicion;
    }
    $conexion->query('CREATE TEMPORARY TABLE eventos (' . implode(', ', $definiciones) . ')');
    $modelo = new ModeloEventos($conexion);
    $guardado = $modelo->agregarEvento('Evento de prueba', 'Descripción de prueba', 'Gratis', '2026-10-10', '18:00', 'Centro', '2481234567', null, 1);
    $evento = $modelo->obtenerEventoPorId($conexion->insert_id);
    if (!$guardado || !$evento || $evento['TituloE'] !== 'Evento de prueba' || $evento['Telefono'] !== '2481234567') {
        throw new RuntimeException('El evento no se guardó con los datos esperados.');
    }
    echo "PASS: alta en tabla temporal con la estructura real de eventos.\n";
} finally {
    $conexion->close();
}
