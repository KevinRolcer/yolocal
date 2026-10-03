<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/horarios.php");
    $hor = new Horarios();

    // Operación de Login
if ($ope == "AGREGAR_HORARIO" && isset($_POST["ID_Negocio"], $_POST["dia_semana"], $_POST["hora_apertura"], $_POST["hora_cierre"])) {
    $datos = array(
        "ID_Negocio"    => $_POST["ID_Negocio"],
        "dia_semana"    => $_POST["dia_semana"],
        "hora_apertura" => $_POST["hora_apertura"],
        "hora_cierre"   => $_POST["hora_cierre"]
    );

    header('Content-Type: application/json; charset=utf-8');
    try {
        $status = $hor->Agregar($datos);
        echo json_encode([
            "success" => (bool) $status,
            "msg" => $status ? "Horario guardado correctamente." : "No se pudo guardar el horario."
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $error) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "msg" => "No se pudo guardar el horario. Verifica los datos e inténtalo de nuevo."
        ], JSON_UNESCAPED_UNICODE);
    }
}
else if ($ope == "LISTAR_HORARIOS" && isset($_POST["ID_Negocio"])) {
    $idNegocio = $_POST["ID_Negocio"];
    $horarios = $hor->listarHorarios($idNegocio); // 👈 necesitas este método en tu modelo

    echo json_encode(array("success" => true, "horarios" => $horarios));
}

    // listar 
    else {
        echo json_encode(array("success" => false, "msg" => "Operación no válida o parámetros insuficientes"));
    }
} else {
    echo json_encode(array("success" => false, "msg" => "Sin operación válida"));
}
