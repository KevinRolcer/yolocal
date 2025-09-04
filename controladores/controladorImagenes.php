<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/imagenes.php");
    $img = new Imagenes();

    // Subir imágenes
    if ($ope == "SUBIR_IMAGENES" && isset($_POST["ID_Negocio"])) {
        $idNegocio = $_POST["ID_Negocio"];
        $status = $img->subirImagenes($idNegocio, $_FILES);

        echo json_encode($status);
    }

    // Listar imágenes de un negocio
    else if ($ope == "LISTAR_IMAGENES" && isset($_POST["ID_Negocio"])) {
        $idNegocio = $_POST["ID_Negocio"];
        $imagenes = $img->listarImagenes($idNegocio);

        echo json_encode(array("success" => true, "imagenes" => $imagenes));
    }

    // Operación no válida
    else {
        echo json_encode(array("success" => false, "msg" => "Operación no válida o parámetros insuficientes"));
    }
} else {
    echo json_encode(array("success" => false, "msg" => "Sin operación válida"));
}
