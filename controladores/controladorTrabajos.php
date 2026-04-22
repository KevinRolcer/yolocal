<?php
include_once("../config.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function trabajoPerteneceAlUsuario($idTrabajo, $idUsuario)
{
    $enlace = dbConectar();
    $sql = "SELECT 1
            FROM trabajos t
            INNER JOIN negocios n ON t.ID_Negocio = n.ID_Negocio
            WHERE t.ID_Trabajo = ? AND n.ID_Usuario = ?";
    $consulta = $enlace->prepare($sql);
    $consulta->bind_param("ii", $idTrabajo, $idUsuario);
    $consulta->execute();
    $result = $consulta->get_result();
    return $result->num_rows > 0;
}

function negocioPerteneceAlUsuario($idNegocio, $idUsuario)
{
    $enlace = dbConectar();
    $sql = "SELECT 1 FROM negocios WHERE ID_Negocio = ? AND ID_Usuario = ?";
    $consulta = $enlace->prepare($sql);
    $consulta->bind_param("ii", $idNegocio, $idUsuario);
    $consulta->execute();
    $result = $consulta->get_result();
    return $result->num_rows > 0;
}

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/trabajos.php");
    $usu = new Trabajos();
    $usuarioIdSesion = $_SESSION["ID_Usuario"] ?? null;
    $usuarioTipoSesion = $_SESSION["tipo"] ?? null;




    // listar 
    if ($ope == "LISTARPROMOCIONES") {
    header('Content-Type: application/json');

    // Validar sesión
    
        $usuarioId = $usuarioIdSesion ?? ($_POST['usuarioId'] ?? null);
        $usuarioTipo = $usuarioTipoSesion ?? ($_POST['usuarioTipo'] ?? null);


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
            if ($usuarioTipoSesion === "negocio" && !trabajoPerteneceAlUsuario(intval($_POST["ID_Promocion"]), intval($usuarioIdSesion))) {
                echo json_encode(["success" => false, "msg" => "No autorizado."]);
                exit();
            }
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
    elseif ($ope == "AGREGAR" && isset($_POST["Titulo"], $_POST["Descripcion"], $_POST["Horario"], $_POST["Salario"], $_POST["PerRequeridas"], $_POST["ID_Negocio"])) {
        if ($usuarioTipoSesion === "negocio" && !negocioPerteneceAlUsuario(intval($_POST["ID_Negocio"]), intval($usuarioIdSesion))) {
            echo json_encode(["success" => false, "msg" => "Solo puedes publicar vacantes de tu negocio."]);
            exit();
        }

        $datos = array(
            "Titulo" => $_POST["Titulo"],
            "Descripcion" => $_POST["Descripcion"],
            "Tipo_Horario" => $_POST["Horario"],
            "Salario" => $_POST["Salario"],
            "PerRequeridas" => $_POST["PerRequeridas"],
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
            $_POST["EditHorario"],
            $_POST["EditSalario"],
            $_POST["EditPerRequeridas"],
            $_POST["ID_NegocioEdit"]
        )
    ) {
        if ($usuarioTipoSesion === "negocio") {
            if (!trabajoPerteneceAlUsuario(intval($_POST["ID_Promocion"]), intval($usuarioIdSesion))) {
                echo json_encode(["success" => false, "msg" => "No autorizado."]);
                exit();
            }

            if (!negocioPerteneceAlUsuario(intval($_POST["ID_NegocioEdit"]), intval($usuarioIdSesion))) {
                echo json_encode(["success" => false, "msg" => "Solo puedes asignar vacantes a tu negocio."]);
                exit();
            }
        }

        $datos = array(
            "ID_Trabajo"   => $_POST["ID_Promocion"],
            "Titulo"         => $_POST["EditTitulo"],
            "Descripcion"    => $_POST["EditDescripcion"],
            "Tipo_Horario"   => $_POST["EditHorario"],
            "Salario"        => $_POST["EditSalario"],
            "PerRequeridas"  => $_POST["EditPerRequeridas"],
            "ID_Negocio"     => $_POST["ID_NegocioEdit"]
        );

        $status = $usu->Editar($datos); // Asumiendo que el objeto se llama $promo
        $info = array("success" => $status);
        echo json_encode($info);
    } 

    // eliminar 
 
    elseif ($ope == "CAMBIARESTATUS" && isset($_POST["ID_Promocion"], $_POST["estatus"])) {
    if ($usuarioTipoSesion === "negocio" && !trabajoPerteneceAlUsuario(intval($_POST["ID_Promocion"]), intval($usuarioIdSesion))) {
        echo json_encode(["success" => false, "msg" => "No autorizado."]);
        exit();
    }

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
