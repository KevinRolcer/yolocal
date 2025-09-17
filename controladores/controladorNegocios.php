<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/negocios.php");
    $usu = new Negocios();

    // Operación de Login


    // listar 
    if ($ope == "LISTAUSUARIOS") {
        header('Content-Type: application/json'); // <-- esta línea es clave
        $usuarioId = $_POST['usuarioId'] ?? null;
        $usuarioTipo = $_POST['usuarioTipo'] ?? null;

        if (!$usuarioId || !$usuarioTipo) {
            echo json_encode(["success" => false, "msg" => "Usuario no autenticado."]);
            exit();
        }
        $pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
        $registrosPorPagina = isset($_POST["registrosPorPagina"]) ? intval($_POST["registrosPorPagina"]) : 10;

        $filtros = [
            "ID_Usuario" => $_POST["id"] ?? null,
            "Nombre" => $_POST["nombre"] ?? null,

            "Correo" => $_POST["telefono"] ?? null
        ];

        $lista = $usu->ListarTODOS($pagina, $registrosPorPagina, $filtros, $usuarioId, $usuarioTipo);

        echo json_encode([
            "success" => true,
            "lista" => $lista["miembros"],
            "totalPaginas" => $lista["totalPaginas"],
            "paginaActual" => $lista["paginaActual"]
        ]);
    }elseif ($ope == "LISTAICONOS") {
        header('Content-Type: application/json'); // <-- esta línea es clave
      


        

        $lista = $usu->ListarIconos();

        echo json_encode([
            "success" => true,
            "lista" => $lista["miembros"]
           
        ]);
    }
    //  obtener 
    elseif ($ope == "OBTENER") {
        if (isset($_POST["ID_Negocio"])) {
            $usuario = $usu->ObtenerUsuario($_POST["ID_Negocio"]);
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
    elseif ($ope == "AGREGAR" && isset($_POST["ID_Usuario"], $_POST["Nombre"], $_POST["ID_Categoria"])) {
        $datos = array(
            "ID_Usuario" => $_POST["ID_Usuario"],
            "nombre_negocio" => $_POST["Nombre"],
            "ID_Categoria" => $_POST["ID_Categoria"]

        );

        $status = $usu->Agregar($datos);
        $info = array("success" => $status);
        echo json_encode($info);
    } elseif ($ope == "OBTENERCLASESDIA") {
        $membresias = $usu->ObtenerClasesDia();  // Llamar a la función en el modelo
        $info = array(
            "success" => true,
            "membresias" => $membresias
        );
        echo json_encode($info);
    }
    // editar  usuario 
   elseif ($ope == "EDITAR" && isset($_POST["ID_Negocio"])) {

    $datos = array(
        "ID_Negocio" => $_POST["ID_Negocio"],
        "nombre_negocio" => $_POST["nombre_negocioEdit"] ?? '',
        "DescripcionN"   => $_POST["DescripcionNEdit"] ?? '',
        "Direccion"      => $_POST["DireccionEdit"] ?? '',
        "Telefono"       => $_POST["TelefonoEdit"] ?? '',
        "CorreoN"        => $_POST["CorreoNEdit"] ?? '',
        "SitioWeb"       => $_POST["SitioWebEdit"] ?? '',
        "Facebook"       => $_POST["FacebookEdit"] ?? '',
        "Instagram"      => $_POST["InstagramEdit"] ?? '',
        "TikTok"         => $_POST["TikTokEdit"] ?? '',
        "Relevancia"     => $_POST["RelevanciaEdit"] ?? '',
        "Icono"          => $_POST["IconoActual"] ?? '' // para mantener el anterior si no se sube uno nuevo
    );

    // Aquí pasamos el archivo completo al modelo si existe
    $archivoIcono = $_FILES["IconoNegocioEdit"] ?? null;

    $status = $usu->Editar($datos, $archivoIcono);

    $info = array(
        "success" => $status,
        "usuario" => $datos  // opcional: enviar los datos de vuelta al JS
    );

    echo json_encode($info);
}
 elseif ($ope === "BUSCAR_MIEMBRO") {
        if (isset($_POST["ID_Miembro"])) {
            $miembro = $usu->buscarMiembroPorID($_POST["ID_Miembro"]);
            if ($miembro) {
                echo json_encode(["success" => true, "miembro" => $miembro]);
            } else {
                echo json_encode(["success" => false, "msg" => "Miembro no encontrado."]);
            }
        } else {
            echo json_encode(["success" => false, "msg" => "ID de miembro no proporcionado."]);
        }
    } elseif ($ope == "OBTENERMEMBRESIAS") {
        $negocios = $usu->ObtenerNegocios();  // Llamar a la función en el modelo
        $info = array(
            "success" => true,
            "negocios" => $negocios
        );
        echo json_encode($info);
    }
    // eliminar 
    elseif ($ope == "ELIMINAR" && isset($_POST["ID_Negocio"])) {
        $status = $usu->Eliminar($_POST["ID_Negocio"]);
        $info = array("success" => $status);
        echo json_encode($info);
    }
    elseif ($ope == "CAMBIARESTATUS" && isset($_POST["ID_Negocio"], $_POST["estado"])) {
    $id = intval($_POST["ID_Negocio"]);
    $estado = intval($_POST["estado"]);

    $success = $usu->CambiarEstatus($id, $estado);

    echo json_encode([
        "success" => $success
    ]);
} else {
        echo json_encode(array("success" => false, "msg" => "Operación no válida o parámetros insuficientes"));
    }
} else {
    echo json_encode(array("success" => false, "msg" => "Sin operación válida"));
}
