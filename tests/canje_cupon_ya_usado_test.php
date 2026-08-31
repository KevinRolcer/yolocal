<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../modelos/cupones.php";

$db = dbConectar();
$resultado = $db->query(
    "SELECT ID_Promocion, codigo
     FROM cupones_emitidos
     WHERE canjeado = 1
     ORDER BY ID_Cupon DESC
     LIMIT 1"
);
$fixture = $resultado ? $resultado->fetch_assoc() : null;

if (!$fixture) {
    fwrite(STDERR, "SKIP: no existe un cupón canjeado para la prueba de integración.\n");
    exit(77);
}

$modelo = new Cupones();
$respuesta = $modelo->canjearConCodigoCupon(
    (int) $fixture["ID_Promocion"],
    (string) $fixture["codigo"]
);

if (($respuesta["reason"] ?? null) !== "already_redeemed") {
    fwrite(
        STDERR,
        "FAIL: se esperaba reason=already_redeemed; respuesta=" .
        json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n"
    );
    exit(1);
}

echo "PASS: un cupón utilizado devuelve el estado already_redeemed.\n";
