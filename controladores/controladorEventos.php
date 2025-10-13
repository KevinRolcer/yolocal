<?php
include_once("../config.php");

if (isset($_POST["ope"])) {
    include_once("../modelos/eventos.php");
    $ev = new Eventos();
    $ope = $_POST["ope"];

    header('Content-Type: application/json');

    // ✅ LISTAR
    if ($ope == "LISTAR") {
        $pagina = $_POST["pagina"] ?? 1;
        $registrosPorPagina = $_POST["registrosPorPagina"] ?? 10;
        $filtros = [
            "titulo" => $_POST["titulo"] ?? null,
            "descripcion" => $_POST["descripcion"] ?? null
        ];

        $lista = $ev->ListarTODOS($pagina, $registrosPorPagina, $filtros);

        echo json_encode([
            "success" => true,
            "lista" => $lista["eventos"],
            "totalPaginas" => $lista["totalPaginas"],
            "paginaActual" => $lista["paginaActual"]
        ]);
    }

    // ✅ AGREGAR
    elseif ($ope == "AGREGAR") {
        $datos = [
            "TituloE" => $_POST["TituloE"],
            "DescripcionE" => $_POST["DescripcionE"],
            "PrecioE" => $_POST["PrecioE"],
            "FechaE" => $_POST["FechaE"],
            "HoraE" => $_POST["HoraE"],
            "UbicacionE" => $_POST["UbicacionE"],
            "RutaImagenE" => $_POST["RutaImagenE"],
            "ID_Categoria" => $_POST["ID_Categoria"]
        ];

        $status = $ev->Agregar($datos);
        echo json_encode(["success" => $status]);
    }

    // ✅ OBTENER
    elseif ($ope == "OBTENER") {
        $id = $_POST["ID_Evento"];
        $evento = $ev->Obtener($id);
        echo json_encode(["success" => (bool)$evento, "evento" => $evento]);
    }

    // ✅ EDITAR
    elseif ($ope == "EDITAR") {
        $datos = [
            "ID_Evento" => $_POST["ID_Evento"],
            "TituloE" => $_POST["TituloE"],
            "DescripcionE" => $_POST["DescripcionE"],
            "PrecioE" => $_POST["PrecioE"],
            "FechaE" => $_POST["FechaE"],
            "HoraE" => $_POST["HoraE"],
            "UbicacionE" => $_POST["UbicacionE"],
            "RutaImagenE" => $_POST["RutaImagenE"],
            "ID_Categoria" => $_POST["ID_Categoria"]
        ];

        $status = $ev->Editar($datos);
        echo json_encode(["success" => $status]);
    }

    // ✅ ELIMINAR
    elseif ($ope == "ELIMINAR") {
        $id = $_POST["ID_Evento"];
        $success = $ev->Eliminar($id);
        echo json_encode(["success" => $success]);
    }

    else {
        echo json_encode(["success" => false, "msg" => "Operación no válida"]);
    }
} else {
    echo json_encode(["success" => false, "msg" => "No se especificó operación"]);
}
?>
