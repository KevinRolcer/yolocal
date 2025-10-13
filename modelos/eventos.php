<?php
class Eventos
{
    public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [])
    {
        $enlace = dbConectar();
        $offset = ($pagina - 1) * $registrosPorPagina;

        $sql = "SELECT 
                    e.ID_Evento,
                    e.TituloE,
                    e.DescripcionE,
                    e.PrecioE,
                    e.FechaE,
                    e.HoraE,
                    e.UbicacionE,
                    e.RutaImagenE,
                    c.Descripcion AS categoria
                FROM eventos e
                INNER JOIN categorias c ON e.ID_Categoria = c.ID_Categoria
                WHERE 1=1";

        $values = [];
        $tipos = "";

        // Filtros dinámicos
        if (!empty($filtros['titulo'])) {
            $sql .= " AND e.TituloE LIKE ?";
            $values[] = "%" . $filtros['titulo'] . "%";
            $tipos .= "s";
        }

        if (!empty($filtros['descripcion'])) {
            $sql .= " AND e.DescripcionE LIKE ?";
            $values[] = "%" . $filtros['descripcion'] . "%";
            $tipos .= "s";
        }

        // Orden y paginación
        $sql .= " ORDER BY e.ID_Evento DESC LIMIT ?, ?";
        $values[] = $offset;
        $values[] = $registrosPorPagina;
        $tipos .= "ii";

        $consulta = $enlace->prepare($sql);
        $consulta->bind_param($tipos, ...$values);
        $consulta->execute();

        $result = $consulta->get_result();
        $eventos = [];
        while ($row = $result->fetch_assoc()) {
            $eventos[] = $row;
        }

        // Total registros
        $countSql = "SELECT COUNT(*) as total FROM eventos";
        $countResult = $enlace->query($countSql);
        $totalRegistros = $countResult->fetch_assoc()["total"];
        $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

        $consulta->close();
        $enlace->close();

        return [
            "eventos" => $eventos,
            "totalPaginas" => $totalPaginas,
            "paginaActual" => $pagina,
        ];
    }

    public function Agregar($datos)
    {
        $enlace = dbConectar();
        $sql = "INSERT INTO eventos (TituloE, DescripcionE, PrecioE, FechaE, HoraE, UbicacionE, RutaImagenE, ID_Categoria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $consulta = $enlace->prepare($sql);

        $consulta->bind_param(
            "ssdssssi",
            $datos["TituloE"],
            $datos["DescripcionE"],
            $datos["PrecioE"],
            $datos["FechaE"],
            $datos["HoraE"],
            $datos["UbicacionE"],
            $datos["RutaImagenE"],
            $datos["ID_Categoria"]
        );

        return $consulta->execute();
    }

    public function Editar($datos)
    {
        $enlace = dbConectar();
        $sql = "UPDATE eventos 
                SET TituloE = ?, DescripcionE = ?, PrecioE = ?, FechaE = ?, HoraE = ?, 
                    UbicacionE = ?, RutaImagenE = ?, ID_Categoria = ?
                WHERE ID_Evento = ?";
        $consulta = $enlace->prepare($sql);

        $consulta->bind_param(
            "ssdssssii",
            $datos["TituloE"],
            $datos["DescripcionE"],
            $datos["PrecioE"],
            $datos["FechaE"],
            $datos["HoraE"],
            $datos["UbicacionE"],
            $datos["RutaImagenE"],
            $datos["ID_Categoria"],
            $datos["ID_Evento"]
        );

        return $consulta->execute();
    }

    public function Obtener($ID_Evento)
    {
        $enlace = dbConectar();
        $sql = "SELECT * FROM eventos WHERE ID_Evento=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_Evento);
        $consulta->execute();

        $result = $consulta->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function Eliminar($ID_Evento)
    {
        $enlace = dbConectar();
        $sql = "DELETE FROM eventos WHERE ID_Evento=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_Evento);
        return $consulta->execute();
    }
}
?>
