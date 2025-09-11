<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/cupones.php");
    $usu = new Cupones();




    // listar 
    if ($ope == "LISTARPROMOCIONES") {
    header('Content-Type: application/json');

    // Validar sesión
    
        $usuarioId = $_POST['usuarioId'] ?? null;
        $usuarioTipo = $_POST['usuarioTipo'] ?? null;

if (!$usuarioId || !$usuarioTipo) {
    echo json_encode(["success" => false, "msg" => "Usuario no autenticado."]);
    exit();
}
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
    elseif ($ope == "AGREGAR" && isset($_POST["Titulo"], $_POST["Descripcion"], $_POST["FechaFin"], $_POST["Cantidad"], $_POST["ID_Negocio"], $_POST["promoMiercoles"])) {
        $datos = array(
            "Titulo" => $_POST["Titulo"],
            "Descripcion" => $_POST["Descripcion"],
            "Cantidad" => $_POST["Cantidad"],
            "FechaFin" => $_POST["FechaFin"],
            "ID_Negocio" => $_POST["ID_Negocio"],
            "PromoMiercoles" => $_POST["promoMiercoles"] // Aquí se agrega el nuevo campo
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
            $_POST["EditFechaFin"],
            $_POST["EditCantidad"],
            $_POST["ID_NegocioEdit"]
        )
    ) {
        $datos = array(
            "ID_Promocion"   => $_POST["ID_Promocion"],
            "Titulo"         => $_POST["EditTitulo"],
            "Descripcion"    => $_POST["EditDescripcion"],
            "FechaFin"       => $_POST["EditFechaFin"],
            "Cantidad"       => $_POST["EditCantidad"],
            "ID_Negocio"     => $_POST["ID_NegocioEdit"]
        );

        $status = $usu->Editar($datos); // Asumiendo que el objeto se llama $promo
        $info = array("success" => $status);
        echo json_encode($info);
    } elseif ($_POST["ope"] == "CAMBIAR_CLAVE") {
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
    } elseif ($ope == "RESTARCUPON" && isset($_POST["ID_Promocion"])) {
        $nuevaCantidad = $usu->RestarCupon($_POST["ID_Promocion"]);
        // aquí haces que RestarCupon() devuelva la cantidad actualizada

        $info = array(
            "success" => $nuevaCantidad !== false,
            "nuevaCantidad" => $nuevaCantidad
        );
        echo json_encode($info);
    } elseif ($ope == "AGREGARCUPON" && isset($_POST["ID_PromocionC"]) && isset($_POST["cantidad"])) {
        $id = intval($_POST["ID_PromocionC"]);
        $cantidad = intval($_POST["cantidad"]);

        $success = $usu->SumarCupon($id, $cantidad);

        echo json_encode([
            "success" => $success
        ]);
    } 
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
