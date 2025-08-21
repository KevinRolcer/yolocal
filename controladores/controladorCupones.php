<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/cupones.php");
    $usu = new Cupones();

    
   
  
    // listar 
   if ($ope == "LISTARPROMOCIONES") {
    header('Content-Type: application/json'); // <-- importante para la respuesta JSON

    $pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
    $registrosPorPagina = isset($_POST["registrosPorPagina"]) ? intval($_POST["registrosPorPagina"]) : 10;

    // Filtros disponibles
    $filtros = [
        "titulo"        => $_POST["titulo"] ?? null,
        "descripcion"   => $_POST["descripcion"] ?? null,
        "NombreNegocio" => $_POST["negocio"] ?? null
       
    ];

    $lista = $usu->ListarTODOS($pagina, $registrosPorPagina, $filtros);

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
    elseif ($ope == "AGREGAR" && isset($_POST["Titulo"], $_POST["Descripcion"], $_POST["FechaFin"], $_POST["Cantidad"], $_POST["ID_Negocio"])) {
        $datos = array(
            "Titulo" => $_POST["Titulo"],
            "Descripcion" => $_POST["Descripcion"],
            "Cantidad" => $_POST["Cantidad"],
            "FechaFin" => $_POST["FechaFin"],
            "ID_Negocio" => $_POST["ID_Negocio"]
            
        );

        $status = $usu->Agregar($datos);
        $info = array("success" => $status);
        echo json_encode($info);
    }
    // editar  usuario 
    elseif ($ope == "EDITAR" && isset($_POST["ID_Usuario"], $_POST["NombreEdit"], $_POST["ApellidoPEdit"], $_POST["ApellidoMEdit"], $_POST["NombreUsuEdit"],  $_POST["usutipEdit"])) {
        $datos = array(
            "ID_Usuario" => $_POST["ID_Usuario"],
            "Nombre" => $_POST["NombreEdit"],
            "ApellidoP" => $_POST["ApellidoPEdit"],
            "ApellidoM" => $_POST["ApellidoMEdit"],
           
            "Correo" => $_POST["NombreUsuEdit"],
            
           
            "tipo_usuario" => $_POST["usutipEdit"]
        );

      
        

        $status = $usu->Editar($datos);
        $info = array("success" => $status);
        echo json_encode($info);
    }
    elseif ($_POST["ope"] == "CAMBIAR_CLAVE") {
        $idUsuario = $_POST["ID_Usuario"];
        $claveNueva = $_POST["ClaveNueva"];

        
        $claveEncriptada = password_hash($claveNueva, PASSWORD_DEFAULT);

    
        $status = $usu->cambiarClave($idUsuario, $claveEncriptada);

        echo json_encode(["success" => $status]);
        exit();
    }
    
    // eliminar 
    elseif ($ope == "ELIMINAR" && isset($_POST["ID_Usuario"])) {
        $status = $usu->Eliminar($_POST["ID_Usuario"]);
        $info = array("success" => $status);
        echo json_encode($info);
    }
    
    else {
        echo json_encode(array("success" => false, "msg" => "Operación no válida o parámetros insuficientes"));
    }
} 

else {
    echo json_encode(array("success" => false, "msg" => "Sin operación válida"));
}
?>
