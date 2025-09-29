<?php
include_once("../config.php");

header('Content-Type: application/json');

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/trabajosPag.php");
    $trab = new Trabajos();

    if ($ope == "LISTAR_TRABAJOS") {
        $lista = $trab->ListarTrabajos();

        echo json_encode([
            "success" => true,
            "trabajos" => $lista
        ]);
    }
    else {
        echo json_encode([
            "success" => false,
            "msg" => "Operación '$ope' no válida"
        ]);
    }
} 
else {
    echo json_encode([
        "success" => false,
        "msg" => "Sin operación válida",
        "debug_post" => $_POST,
        "debug_request" => $_REQUEST
    ]);
}
?>