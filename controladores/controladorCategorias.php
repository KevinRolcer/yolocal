<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/categorias.php");
    $usu2 = new Categoria();

    
    
    // listar 
   if ($ope == "LISTAUSUARIOS") {
    header('Content-Type: application/json'); // <-- esta línea es clave

    $pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
    $registrosPorPagina = isset($_POST["registrosPorPagina"]) ? intval($_POST["registrosPorPagina"]) : 10;

    $filtros = [
        "ID_Usuario" => $_POST["id"] ?? null,
        "Nombre" => $_POST["nombre"] ?? null,
        "Apellidos" => $_POST["apellidos"] ?? null,
        "Correo" => $_POST["telefono"] ?? null
    ];

    $lista = $usu2->ListarTODOS($pagina, $registrosPorPagina, $filtros);

    echo json_encode([
        "success" => true,
        "lista" => $lista["miembros"],
        "totalPaginas" => $lista["totalPaginas"],
        "paginaActual" => $lista["paginaActual"]
    ]);
}
    elseif ($ope == "OBTENER") {
        if (isset($_POST["ID_Usuario"])) {
            $usuario = $usu2->ObtenerUsuario($_POST["ID_Usuario"]);
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
    elseif ($ope == "AGREGAR" && isset($_POST["Nombre"])) {
        $datos = array(
            "Nombre" => $_POST["Nombre"],
            
        );

        $status = $usu2->Agregar($datos);
        $info = array("success" => $status);
        echo json_encode($info);
    }
    // editar  usuario 
    elseif ($ope == "EDITAR" && isset($_POST["ID_Usuario"], $_POST["NombreEdit"])) {
        $datos = array(
            "ID_Categoria" => $_POST["ID_Usuario"],
            "Descripcion" => $_POST["NombreEdit"],
            
        );

      
        

        $status = $usu2->Editar($datos);
        $info = array("success" => $status);
        echo json_encode($info);
    }
    
    
    // eliminar 
    elseif ($ope == "ELIMINAR" && isset($_POST["ID_Usuario"])) {
        $status = $usu2->Eliminar($_POST["ID_Usuario"]);
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
