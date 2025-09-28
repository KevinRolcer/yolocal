<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/trabajosPag.php");
    $trab = new Trabajos();

    if ($ope == "LISTAR_TRABAJOS") {
        header('Content-Type: application/json');

        $lista = $trab->ListarTrabajos();

        echo json_encode([
            "success" => true,
            "trabajos" => $lista
        ]);
        exit();
    }
    else {
        echo json_encode([
            "success" => false,
            "msg" => "Operación no válida o parámetros insuficientes"
        ]);
    }
} 
else {
    echo json_encode([
        "success" => false,
        "msg" => "Sin operación válida"
    ]);
}
?>
