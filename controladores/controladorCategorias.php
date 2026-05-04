<?php
include_once("../config.php");
error_reporting(0); // Prevent warnings from breaking JSON
ini_set('display_errors', 0);

if (isset($_POST["ope"])) {
    $ope = $_POST["ope"];
    include_once("../modelos/categorias.php");
    $usu2 = new Categoria();

    if ($ope == "LISTAUSUARIOS") {
        header('Content-Type: application/json');
        try {
            $pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
            $rpp = isset($_POST["registrosPorPagina"]) ? intval($_POST["registrosPorPagina"]) : 10;

            $filtros = [
                "Nombre" => $_POST["nombre"] ?? null,
                "SearchKey" => $_POST["searchKey"] ?? "Nombre",
                "Orden" => $_POST["orden"] ?? "ASC",
                "OrdenNum" => $_POST["ordenNum"] ?? "ASC"
            ];

            $lista = $usu2->ListarTODOS($pagina, $rpp, $filtros);

            echo json_encode([
                "success" => true,
                "lista" => $lista["miembros"],
                "totalPaginas" => $lista["totalPaginas"],
                "paginaActual" => $lista["paginaActual"]
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "msg" => "Error interno: " . $e->getMessage()]);
        }
    }
    elseif ($ope == "OBTENER") {
        if (isset($_POST["ID_Categoria"])) {
            $usuario = $usu2->ObtenerUsuario($_POST["ID_Categoria"]);
            if ($usuario) {
                echo json_encode(["success" => true, "usuario" => $usuario]);
            } else {
                echo json_encode(["success" => false, "msg" => "Categoría no encontrada."]);
            }
        } else {
            echo json_encode(["success" => false, "msg" => "ID no proporcionado."]);
        }
    }
    elseif ($ope == "AGREGAR" && isset($_POST["Nombre"])) {
        $nombre = $_POST["Nombre"];
        $color = $_POST["Color"] ?? "#7b68ee";
        $rutaImagen = null;

        if (isset($_FILES["Imagen"]) && $_FILES["Imagen"]["error"] == 0) {
            $directorio = "../assets/img/categorias/";
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }
            $extension = pathinfo($_FILES["Imagen"]["name"], PATHINFO_EXTENSION);
            $nombreArchivo = "cat_" . time() . "_" . uniqid() . "." . $extension;
            $rutaDestino = $directorio . $nombreArchivo;
            
            if (move_uploaded_file($_FILES["Imagen"]["tmp_name"], $rutaDestino)) {
                $rutaImagen = "assets/img/categorias/" . $nombreArchivo;
            }
        }

        $datos = [
            "Nombre" => $nombre,
            "Color"  => $color,
            "Imagen" => $rutaImagen
        ];

        $status = $usu2->Agregar($datos);
        echo json_encode(["success" => $status]);
    }
    elseif ($ope == "EDITAR" && isset($_POST["ID_Categoria"], $_POST["NombreEdit"])) {
        $id = $_POST['ID_Categoria'];
        $nombre = $_POST["NombreEdit"];
        $color = $_POST["ColorEdit"] ?? "#7b68ee";
        $rutaImagen = $_POST["RutaImagenActual"] ?? null;

        if (isset($_FILES["ImagenEdit"]) && $_FILES["ImagenEdit"]["error"] == 0) {
            $directorio = "../assets/img/categorias/";
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }
            $extension = pathinfo($_FILES["ImagenEdit"]["name"], PATHINFO_EXTENSION);
            $nombreArchivo = "cat_" . time() . "_" . uniqid() . "." . $extension;
            $rutaDestino = $directorio . $nombreArchivo;
            
            if (move_uploaded_file($_FILES["ImagenEdit"]["tmp_name"], $rutaDestino)) {
                $rutaImagen = "assets/img/categorias/" . $nombreArchivo;
            }
        }

        $datos = [
            "ID_Categoria" => $id,
            "Descripcion"  => $nombre,
            "Color"        => $color,
            "Imagen"       => $rutaImagen
        ];

        $status = $usu2->Editar($datos);
        echo json_encode(["success" => $status]);
    }
    elseif ($ope == "ELIMINAR" && isset($_POST["ID_Categoria"])) {
        $status = $usu2->Eliminar($_POST["ID_Categoria"]);
        echo json_encode(["success" => $status]);
    }
    elseif ($ope == "LISTARNEGOCIOS" && isset($_POST["ID_Categoria"])) {
        include_once("../modelos/negocios.php");
        $neg = new Negocios();
        $lista = $neg->ObtenerPorCategoria($_POST["ID_Categoria"]);
        echo json_encode(["success" => true, "lista" => $lista]);
    }
    elseif ($ope == "MOVERNEGOCIO" && isset($_POST["ID_Negocio"], $_POST["ID_Categoria"])) {
        include_once("../modelos/negocios.php");
        $neg = new Negocios();
        $status = $neg->MoverDeCategoria($_POST["ID_Negocio"], $_POST["ID_Categoria"]);
        echo json_encode(["success" => $status]);
    }
    else {
        echo json_encode(["success" => false, "msg" => "Operación no válida."]);
    }
} else {
    echo json_encode(["success" => false, "msg" => "Sin operación."]);
}
?>
