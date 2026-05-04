<?php
class Categoria
{


    public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [])
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;

    $sql = "SELECT ID_Categoria, Descripcion, Color, Imagen FROM categorias WHERE 1=1";
    $values = [];
    $tipos = "";

    // Filtros dinámicos
    $searchKey = $filtros['SearchKey'] ?? 'Nombre';
    if (!empty($filtros['Nombre'])) {
        if ($searchKey === 'ID') {
            $sql .= " AND ID_Categoria = ?";
            $values[] = intval($filtros['Nombre']);
            $tipos .= "i";
        } else {
            $sql .= " AND Descripcion LIKE ?";
            $values[] = "%" . $filtros['Nombre'] . "%";
            $tipos .= "s";
        }
    }

    // Orden y paginación
    $ordenAz = (isset($filtros["Orden"]) && strtoupper($filtros["Orden"]) === "DESC") ? "DESC" : "ASC";
    $ordenNum = (isset($filtros["OrdenNum"]) && !empty($filtros["OrdenNum"])) ? strtoupper($filtros["OrdenNum"]) : null;
    
    if ($ordenNum) {
        $sql .= " ORDER BY ID_Categoria $ordenNum";
    } else {
        $sql .= " ORDER BY Descripcion $ordenAz";
    }
    
    $sql .= " LIMIT ?, ?";
    $values[] = $offset;
    $values[] = $registrosPorPagina;
    $tipos .= "ii"; 

    // Preparar y ejecutar
    $consulta = $enlace->prepare($sql);
    if (!$consulta) {
        throw new Exception("Error en la preparación de la consulta: " . $enlace->error);
    }

    $consulta->bind_param($tipos, ...$values);
    $consulta->execute();
    $result = $consulta->get_result();

    $miembros = [];
    while ($row = $result->fetch_assoc()) {
        $miembros[] = $row;
    }

    // Total de registros para calcular total de páginas (sin filtros opcional)
    $countSql = "SELECT COUNT(*) as total FROM categorias WHERE 1=1";

    // Si quieres contar con los mismos filtros, repite los mismos pasos aquí
    $countConsulta = $enlace->prepare($countSql);
    $countConsulta->execute();
    $countResult = $countConsulta->get_result();
    $totalRegistros = $countResult->fetch_assoc()["total"];
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    // Cerrar conexiones
    $consulta->close();
    $countConsulta->close();

    return [
        "miembros" => $miembros,
        "totalPaginas" => $totalPaginas,
        "paginaActual" => $pagina,
    ];
}

  
    
    public function Agregar($datos)
    {
        $enlace = dbConectar();

        $sql = "INSERT INTO categorias (Descripcion, Color, Imagen) VALUES (?, ?, ?)";
        $consulta = $enlace->prepare($sql);

        $consulta->bind_param(
            "sss",
            $datos["Nombre"],
            $datos["Color"],
            $datos["Imagen"]
        );

        return $consulta->execute();
    }
    public function Editar($datos)
    {
        $enlace = dbConectar();

       
            $sql = "UPDATE categorias SET Descripcion=?, Color=?, Imagen=? WHERE ID_Categoria=?";
            $consulta = $enlace->prepare($sql);
            $consulta->bind_param(
                "sssi",
                $datos["Descripcion"],
                $datos["Color"],
                $datos["Imagen"],
                $datos["ID_Categoria"]
            );
        

        return $consulta->execute();
    }
    
    public function ObtenerUsuario($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "SELECT ID_Categoria, Descripcion, Color, Imagen FROM categorias WHERE ID_Categoria=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_usuario);
        $consulta->execute();
        $result = $consulta->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
    public function Eliminar($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "DELETE FROM categorias WHERE ID_Categoria=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_usuario);

        return $consulta->execute();
    }
    
}
