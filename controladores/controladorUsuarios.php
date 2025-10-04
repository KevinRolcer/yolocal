<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/Ausuarios.php");
    $usu = new Usuarios();

    // Operación de Login
    if ($ope == "LOGIN" && isset($_POST["nombre"], $_POST["contra"])) {
        $correo = $_POST["nombre"];
        $pass = $_POST["contra"];

        $status = $usu->Login($correo, $pass);
        if ($status[0]) {
            $info = array(
                "success" => true,
                "ruta" => RUTA . "/?pag=" . $status[1]
            );
        } else {
            $info = array(
                "success" => false,
                "msg" => "El usuario o contraseña proporcionados son incorrectos."
            );
        }
        echo json_encode($info);
    }
    elseif ($ope == "VERIFICAR_NOMBREUSU") {
        $nombreUsu = trim($_POST['nombreUsu']);
        $existe = $usu->validarNombreUsuario($nombreUsu);
    
        $info = array(
            "success" => true,
            "existe" => $existe 
        );
    
        echo json_encode($info);
        exit();
    }
    elseif ($ope == "VERIFICAR_CORREOUSU") {
        $correoUsu = trim($_POST['correoUsu']);
        $existe = $usu->validarCorreoUsuario($correoUsu);
    
        $info = array(
            "success" => true,
            "existe" => $existe 
        );
    
        echo json_encode($info);
        exit();
    }
    // listar 
   elseif ($ope == "LISTAUSUARIOS") {
    header('Content-Type: application/json'); // <-- esta línea es clave

    $pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
    $registrosPorPagina = isset($_POST["registrosPorPagina"]) ? intval($_POST["registrosPorPagina"]) : 10;

    $filtros = [
        "ID_Usuario" => $_POST["id"] ?? null,
        "Nombre" => $_POST["nombre"] ?? null,
        "Apellidos" => $_POST["apellidos"] ?? null,
        "Correo" => $_POST["telefono"] ?? null
    ];

    $lista = $usu->ListarTODOS($pagina, $registrosPorPagina, $filtros);

    echo json_encode([
        "success" => true,
        "lista" => $lista["miembros"],
        "totalPaginas" => $lista["totalPaginas"],
        "paginaActual" => $lista["paginaActual"]
    ]);
}
    //  obtener 
    elseif ($ope == "OBTENER") {
        if (isset($_POST["ID_Usuario"])) {
            $usuario = $usu->ObtenerUsuario($_POST["ID_Usuario"]);
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
    elseif ($ope == "AGREGAR" && isset($_POST["Nombre"], $_POST["ApellidoP"], $_POST["ApellidoM"], $_POST["NombreUsu"], $_POST["Contra"],$_POST["usutip"])) {
        $datos = array(
            "Nombre" => $_POST["Nombre"],
            "ApellidoP" => $_POST["ApellidoP"],
            "ApellidoM" => $_POST["ApellidoM"],
            "Correo" => $_POST["NombreUsu"],
            "contra" => $_POST["Contra"],
            "cPrueba" => $_POST["Contra"],
            "tipo_usuario" => $_POST["usutip"]
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

    
        $status = $usu->cambiarClave($idUsuario, $claveEncriptada, $_POST["ClaveNueva"]);

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
