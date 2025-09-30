<?php
class Trabajos
{


public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [], $usuarioId, $usuarioTipo)
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;
 
    // Consulta con JOIN a negocios
    $sql = "SELECT 
    p.ID_Trabajo,
    p.titulo,
    p.descripcion,
    p.Tipo_Horario,
    p.Salario,
    p.PerRequeridas,
   p.Estatus,
    n.nombre_negocio AS nombre_negocio,
    n.Direccion AS direccion_negocio,
    c.Descripcion AS categoria
FROM trabajos p
INNER JOIN negocios n 
    ON p.ID_Negocio = n.ID_Negocio
INNER JOIN categorias c 
    ON n.ID_Categoria = c.ID_Categoria
WHERE 1=1";
    
    $values = [];
    $tipos = "";

    // Filtros dinámicos
    if (!empty($filtros['titulo'])) {
        $sql .= " AND p.titulo LIKE ?";
        $values[] = "%" . $filtros['titulo'] . "%";
        $tipos .= "s";
    }

    if (!empty($filtros['descripcion'])) {
        $sql .= " AND p.descripcion LIKE ?";
        $values[] = "%" . $filtros['descripcion'] . "%";
        $tipos .= "s";
    }

    if (!empty($filtros['NombreNegocio'])) {
        $sql .= " AND n.nombre_negocio LIKE ?";
        $values[] = "%" . $filtros['NombreNegocio'] . "%";
        $tipos .= "s";
    }

   
    // Orden y paginación
    $sql .= " ORDER BY p.ID_Trabajo DESC LIMIT ?, ?";
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

    $promociones = [];
    while ($row = $result->fetch_assoc()) {
        $promociones[] = $row;
    }

    // Total de registros 
    $countSql = "SELECT COUNT(*) as total 
                 FROM trabajos p
                 INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
                 WHERE 1=1";

    $countConsulta = $enlace->prepare($countSql);
    $countConsulta->execute();
    $countResult = $countConsulta->get_result();
    $totalRegistros = $countResult->fetch_assoc()["total"];
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    $consulta->close();
    $countConsulta->close();
    $enlace->close();

    return [
        "promociones" => $promociones,
        "totalPaginas" => $totalPaginas,
        "paginaActual" => $pagina,
    ];
}

   
    public function Agregar($datos)
    {
        $enlace = dbConectar();

        $sql = "INSERT INTO trabajos (titulo, descripcion, Tipo_Horario, Salario, PerRequeridas, ID_Negocio, Estatus ) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $consulta = $enlace->prepare($sql);



        $consulta->bind_param(
            "ssssii",
            $datos["Titulo"],
            $datos["Descripcion"],
            $datos["Tipo_Horario"],
            $datos["Salario"],
            $datos["PerRequeridas"],
            $datos["ID_Negocio"]
        
        );

        return $consulta->execute();
    }
    public function Editar($datos)
{
    $enlace = dbConectar();

    $sql = "UPDATE trabajos 
            SET Titulo = ?, 
                Descripcion = ?, 
                Tipo_Horario = ?,
                Salario = ?,
                PerRequeridas = ?,
                ID_Negocio = ? 
            WHERE ID_Trabajo = ?";

    $consulta = $enlace->prepare($sql);

    $consulta->bind_param(
        "ssssiii", // Tipos: string, string, int, int
        $datos["Titulo"],
        $datos["Descripcion"],
        $datos["Tipo_Horario"],
        $datos["Salario"],
        $datos["PerRequeridas"],
        $datos["ID_Negocio"],
        $datos["ID_Trabajo"]
    );

    return $consulta->execute();
}



    public function ObtenerUsuario($ID_Promocion)
    {
        $enlace = dbConectar();
        $sql = "SELECT * FROM trabajos WHERE ID_Trabajo=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_Promocion);
        $consulta->execute();
        $result = $consulta->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
     public function CambiarEstatus($ID_Promocion, $estatus)
{
    $enlace = dbConectar();
    $sql = "UPDATE trabajos SET Estatus = ? WHERE ID_Trabajo = ?";
    $consulta = $enlace->prepare($sql);
    $consulta->bind_param("ii", $estatus, $ID_Promocion);

    return $consulta->execute();
}

}
