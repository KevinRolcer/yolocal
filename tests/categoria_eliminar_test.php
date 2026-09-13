<?php
require_once __DIR__ . '/../config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conexion = dbConectar();
$conexion->query('CREATE TEMPORARY TABLE categorias (ID_Categoria INT PRIMARY KEY)');
$conexion->query('CREATE TEMPORARY TABLE negocios (ID_Categoria INT)');
$conexion->query('CREATE TEMPORARY TABLE eventos (ID_Categoria INT)');
$conexion->query('INSERT INTO categorias VALUES (1), (2), (3)');
$conexion->query('INSERT INTO negocios VALUES (1)');
$conexion->query('INSERT INTO eventos VALUES (2)');
chdir(__DIR__ . '/../controladores');
foreach ([1 => false, 2 => false, 3 => true] as $id => $permitido) {
    $_POST = ['ope' => 'ELIMINAR', 'ID_Categoria' => $id];
    ob_start();
    require 'controladorCategorias.php';
    $respuesta = json_decode(ob_get_clean(), true, 512, JSON_THROW_ON_ERROR);
    if ($respuesta['success'] !== $permitido || (!$permitido && !str_contains($respuesta['msg'] ?? '', 'en uso'))) {
        throw new RuntimeException('Resultado incorrecto al eliminar categoría ' . $id);
    }
    $existe = $conexion->query('SELECT 1 FROM categorias WHERE ID_Categoria = ' . $id)->num_rows > 0;
    if ($existe === $permitido) throw new RuntimeException('Estado incorrecto de categoría ' . $id);
}
echo "PASS: categorías en negocios y eventos protegidas; categoría libre eliminada.\n";
