<?php
class Categoria
{


    public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [])
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;

    $sql = "SELECT * FROM categorias WHERE 1=1";
    $values = [];
    $tipos = "";

    // Filtros dinámicos
   

    if (!empty($filtros['Nombre'])) {
        $sql .= " AND Descripcion LIKE ?";
        $values[] = "%" . $filtros['Nombre'] . "%";
        $tipos .= "s";
    }

    

    // Orden y paginación
    $sql .= " ORDER BY Descripcion DESC LIMIT ?, ?";
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
    $enlace->close();

    return [
        "miembros" => $miembros,
        "totalPaginas" => $totalPaginas,
        "paginaActual" => $pagina,
    ];
}

  
    
    public function Agregar($datos)
    {
        $enlace = dbConectar();

        $sql = "INSERT INTO Categorias (Descripcion) VALUES (?)";
        $consulta = $enlace->prepare($sql);



        $consulta->bind_param(
            "s",
            $datos["Nombre"]
           
        );

        return $consulta->execute();
    }
    public function Editar($datos)
    {
        $enlace = dbConectar();

       
            $sql = "UPDATE categorias SET Descripcion=? WHERE ID_Categoria=?";
            $consulta = $enlace->prepare($sql);
            $consulta->bind_param(
                "si",
                $datos["Descripcion"],
              
                $datos["ID_Categoria"]
            );
        

        return $consulta->execute();
    }
    
    public function ObtenerUsuario($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "SELECT ID_Categoria, Descripcion FROM categorias WHERE ID_Categoria=?";
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
