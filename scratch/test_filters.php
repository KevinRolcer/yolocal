<?php
include_once("modelos/cupones.php");
include_once("config.php");

session_start();
$usu = new Cupones();

$filtros_activo = ["estado" => "activo"];
$res_activo = $usu->ListarTODOS(1, 10, $filtros_activo, 1, "admin");
echo "Activos: " . count($res_activo['promociones']) . " (Total: " . $res_activo['totalRegistros'] . ")\n";

$filtros_expirado = ["estado" => "expirado"];
$res_expirado = $usu->ListarTODOS(1, 10, $filtros_expirado, 1, "admin");
echo "Expirados: " . count($res_expirado['promociones']) . " (Total: " . $res_expirado['totalRegistros'] . ")\n";

$filtros_todos = ["estado" => "todos"];
$res_todos = $usu->ListarTODOS(1, 10, $filtros_todos, 1, "admin");
echo "Todos: " . count($res_todos['promociones']) . " (Total: " . $res_todos['totalRegistros'] . ")\n";
?>
