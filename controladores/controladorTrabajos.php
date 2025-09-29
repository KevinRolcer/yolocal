<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/trabajos.php");
    $usu = new Trabajos();




    // listar 
    if ($ope == "LISTARPROMOCIONES") {
    header('Content-Type: application/json');

    // Validar sesión
    
        $usuarioId = $_POST['usuarioId'] ?? null;
        $usuarioTipo = $_POST['usuarioTipo'] ?? null;


    $pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
    $registrosPorPagina = isset($_POST["registrosPorPagina"]) ? intval($_POST["registrosPorPagina"]) : 10;

    // Filtros disponibles
    $filtros = [
        "titulo"        => $_POST["titulo"] ?? null,
        "descripcion"   => $_POST["descripcion"] ?? null,
        "NombreNegocio" => $_POST["negocio"] ?? null
    ];

   

    $lista = $usu->ListarTODOS($pagina, $registrosPorPagina, $filtros,  $usuarioId, $usuarioTipo);

    echo json_encode([
        "success"      => true,
        "lista"        => $lista["promociones"],
        "totalPaginas" => $lista["totalPaginas"],
        "paginaActual" => $lista["paginaActual"]
    ]);
}



    //  obtener 
    elseif ($ope == "OBTENER") {
        if (isset($_POST["ID_Promocion"])) {
            $usuario = $usu->ObtenerUsuario($_POST["ID_Promocion"]);
            if ($usuario) {
                echo json_encode(["success" => true, "usuario" => $usuario]);
            } else {
                echo json_encode(["success" => false, "msg" => "Usuario no encontrado."]);
            }
        } else {
            echo json_encode(["success" => false, "msg" => "ID de usuario no proporcionado."]);
        }
    }
    // para agregar  
    elseif ($ope == "AGREGAR" && isset($_POST["Titulo"], $_POST["Descripcion"],$_POST["ID_Negocio"])) {
        $datos = array(
            "Titulo" => $_POST["Titulo"],
            "Descripcion" => $_POST["Descripcion"],

            "ID_Negocio" => $_POST["ID_Negocio"],
           
        );

        $status = $usu->Agregar($datos);
        $info = array("success" => $status);
        echo json_encode($info);
    }
    // editar  usuario 
    elseif (
        $ope == "EDITAR" &&
        isset(
            $_POST["ID_Promocion"],
            $_POST["EditTitulo"],
            $_POST["EditDescripcion"],
            $_POST["ID_NegocioEdit"]
        )
    ) {
        $datos = array(
            "ID_Trabajo"   => $_POST["ID_Promocion"],
            "Titulo"         => $_POST["EditTitulo"],
            "Descripcion"    => $_POST["EditDescripcion"],
            "ID_Negocio"     => $_POST["ID_NegocioEdit"]
        );

        $status = $usu->Editar($datos); // Asumiendo que el objeto se llama $promo
        $info = array("success" => $status);
        echo json_encode($info);
    } 

    // eliminar 
 
    elseif ($ope == "CAMBIARESTATUS" && isset($_POST["ID_Promocion"], $_POST["estatus"])) {
    $id = intval($_POST["ID_Promocion"]);
    $estatus = intval($_POST["estatus"]);

    $success = $usu->CambiarEstatus($id, $estatus);

    echo json_encode([
        "success" => $success
    ]);
}
} else {
    echo json_encode(array("success" => false, "msg" => "Sin operación válida"));
}
