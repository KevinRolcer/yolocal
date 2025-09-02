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

    // Llamada al modelo
    $status = $hor->Agregar($datos);

    // Respuesta JSON
    $info = array("success" => $status);
    echo json_encode($info);
}


    // listar 
    else {
        echo json_encode(array("success" => false, "msg" => "Operación no válida o parámetros insuficientes"));
    }
} else {
    echo json_encode(array("success" => false, "msg" => "Sin operación válida"));
}
