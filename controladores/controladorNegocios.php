<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . "/../config.php";

if (function_exists("iniciarSesionYoLocal")) {
    iniciarSesionYoLocal();
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    require_once __DIR__ . "/../modelos/negocios.php";
    $usu = new Negocios();

    // Operación de Login


    // listar 
    if ($ope == "LISTAUSUARIOS") {
        $usuarioId = $_POST['usuarioId'] ?? null;
        $usuarioTipo = $_POST['usuarioTipo'] ?? null;

        if (!$usuarioId || !$usuarioTipo) {
            echo json_encode(["success" => false, "msg" => "Usuario no autenticado."]);
            exit();
        }
        $pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
        $registrosPorPagina = isset($_POST["registrosPorPagina"]) ? intval($_POST["registrosPorPagina"]) : 10;

        $filtros = [
            "Nombre"  => $_POST["nombre"] ?? null,
            "Correo"  => $_POST["telefono"] ?? null,
            "Estatus" => $_POST["estatus"] ?? null,
            "Categorias" => $_POST["categorias"] ?? null
        ];

        $ordenColumna   = $_POST["ordenColumna"] ?? 'ID_Negocio';
        $ordenDireccion = $_POST["ordenDireccion"] ?? 'DESC';

        // Map frontend column names to DB column names for businesses
        if ($ordenColumna === 'Nombre') $ordenColumna = 'nombre_negocio';
        if ($ordenColumna === 'id') $ordenColumna = 'ID_Negocio';

        $lista = $usu->ListarTODOS($pagina, $registrosPorPagina, $filtros, $usuarioId, $usuarioTipo, $ordenColumna, $ordenDireccion);

        echo json_encode([
            "success" => true,
            "lista" => $lista["miembros"],
            "totalPaginas" => $lista["totalPaginas"],
            "paginaActual" => $lista["paginaActual"],
            "totalRegistros" => $lista["totalRegistros"]
        ]);
    }elseif ($ope == "LISTAICONOS") {
        // JSON header global definido al inicio del controlador
      


        

        $lista = $usu->ListarIconos();

        echo json_encode([
            "success" => true,
            "lista" => $lista["miembros"]
           
        ]);
    }elseif ($ope == "LISTAICONOSBanner") {
        // JSON header global definido al inicio del controlador
        $lista = $usu->ListarIconosBanner();

        echo json_encode([
            "success" => true,
            "lista" => $lista["miembros"]
           
        ]);
    }
    elseif ($ope == "LISTAICONOS2") {
        // JSON header global definido al inicio del controlador
      

        $lista = $usu->ListarIconos2();

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
            "ID_Categoria" => $_POST["ID_Categoria"],
            "CodigoCanje" => trim($_POST["CodigoCanje"] ?? ""),
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
        "nombre_negocioEdit" => $_POST["nombre_negocioEdit"] ?? '',
        "DescripcionN"   => $_POST["DescripcionNEdit"] ?? '',
        "Direccion"      => $_POST["DireccionEdit"] ?? '',
        "Telefono"       => $_POST["TelefonoEdit"] ?? '',
        "CorreoN"        => $_POST["CorreoNEdit"] ?? '',
        "SitioWeb"       => $_POST["SitioWebEdit"] ?? '',
        "Facebook"       => $_POST["FacebookEdit"] ?? '',
        "Instagram"      => $_POST["InstagramEdit"] ?? '',
        "GoogleMaps"     => $_POST["GoogleMapsEdit"] ?? '',
        "Latitud"        => $_POST["LatitudEdit"] ?? '',
        "Longitud"       => $_POST["LongitudEdit"] ?? '',
        "TikTok"         => $_POST["TikTokEdit"] ?? '',
        "Relevancia"     => $_POST["RelevanciaEdit"] ?? '',
        "codigo_canjeEdit" => trim($_POST["codigo_canjeEdit"] ?? ""),
        "Icono"          => $_POST["RutaiconoEdit"] ?? '' // para mantener el anterior si no se sube uno nuevo
    );

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
        $tipoSesion = $_SESSION["tipo"] ?? null;
        $idSesion = $_SESSION["ID_Usuario"] ?? null;

        if ($tipoSesion === "negocio" && $idSesion) {
            $negocios = $usu->ObtenerNegocios($idSesion);
        } else {
            $negocios = $usu->ObtenerNegocios();
        }

        $info = array(
            "success" => true,
            "negocios" => $negocios
        );
        echo json_encode($info);
    }
    elseif ($ope == "OBTENERCOORDENADAS") {
    $coordenadasOriginales = $usu->ObtenerCoordenadas();  
    $coordenadasLimpias = [];

    foreach ($coordenadasOriginales as $fila) {
        $latitud = floatval(trim(str_replace(['°', ','], ['', '.'], $fila['Latitud'])));
        $longitud = floatval(trim(str_replace(['°', ','], ['', '.'], $fila['Longitud'])));

        // Asegurar signo negativo en Puebla
        if ($longitud > 0) {
            $longitud = -$longitud;
        }

        if ($latitud >= 18.9 && $latitud <= 19.6 && $longitud <= -97.8 && $longitud >= -98.6) {
            $coordenadasLimpias[] = [
                "ID_Negocio" => $fila['ID_Negocio'] ?? '',
                "Latitud" => $latitud,
                "Longitud" => $longitud,
                "nombre_negocio" => $fila['nombre_negocio'] ?? ''
            ];
        }
    }

    echo json_encode([
        "success" => true,
        "coordenadas" => $coordenadasLimpias
    ]);
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
} elseif ($ope == "PAGAR" && isset($_POST["ID_Negocio"])) {
    $id = intval($_POST["ID_Negocio"]);

    $success = $usu->PagarCuota($id);

    echo json_encode([
        "success" => $success
    ]);
}else {
        echo json_encode(array("success" => false, "msg" => "Operación no válida o parámetros insuficientes"));
    }
} else {
    echo json_encode(array("success" => false, "msg" => "Sin operación válida"));
}
} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "msg" => "No se pudo conectar a la base de datos o procesar la solicitud.",
        "error" => $e->getMessage()
    ]);
}
