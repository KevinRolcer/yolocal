<?php
require_once __DIR__ . "/../config.php";

$db = dbConectar();
$resultado = $db->query("SELECT ID_Promocion FROM promociones ORDER BY ID_Promocion DESC LIMIT 1");
$promocion = $resultado ? $resultado->fetch_assoc() : null;

if (!$promocion) {
    fwrite(STDERR, "SKIP: no existe una promoción para probar la vista de canje.\n");
    exit(77);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION["canje_ok"] = "¡Cupón canjeado con éxito!";
$_GET["id"] = (int) $promocion["ID_Promocion"];
$_SERVER["REQUEST_METHOD"] = "GET";

ob_start();
require __DIR__ . "/../canje.php";
$html = ob_get_clean();

if (strpos($html, 'id="modalCanjeExitoso"') === false) {
    fwrite(STDERR, "FAIL: el canje exitoso no renderizó modalCanjeExitoso.\n");
    exit(1);
}

if (strpos($html, "Cupón canjeado correctamente") === false) {
    fwrite(STDERR, "FAIL: el modal no contiene el título de éxito esperado.\n");
    exit(1);
}

echo "PASS: el canje exitoso renderiza su modal de confirmación.\n";
