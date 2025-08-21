<?php
class Cupones
{


public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [])
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;

    // Consulta con JOIN a negocios
    $sql = "SELECT p.ID_Promocion, p.titulo, p.descripcion, p.cantidad, p.fecha_fin, p.Estatus, 
                   n.nombre_negocio AS nombre_negocio
            FROM promociones p
            INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
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
        $sql .= " AND n.Nombre LIKE ?";
        $values[] = "%" . $filtros['NombreNegocio'] . "%";
        $tipos .= "s";
    }


    // Orden y paginación
    $sql .= " ORDER BY p.ID_Promocion DESC LIMIT ?, ?";
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

    // Total de registros (para paginación)
    $countSql = "SELECT COUNT(*) as total 
                 FROM promociones p
                 INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
                 WHERE 1=1";

    // Si quieres que el total también respete filtros, replica los mismos filtros
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
        "promociones" => $promociones,
        "totalPaginas" => $totalPaginas,
        "paginaActual" => $pagina,
    ];
}

    public function validarNombreUsuario($nombreUsu)
    {
        $enlace = dbConectar();
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE Correo = ?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("s", $nombreUsu);
        $consulta->execute();
        $resultado = $consulta->get_result()->fetch_assoc();
        $enlace->close();

        return $resultado['total'] > 0; // Retorna true si hay al menos un usuario con ese nombre
    }
    public function validarCorreoUsuario($correoUsu)
    {
        $enlace = dbConectar();
        $sql = "SELECT COUNT(*) AS total2 FROM usuarios WHERE Correo = ?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("s", $correoUsu);
        $consulta->execute();
        $resultado = $consulta->get_result()->fetch_assoc();
        $enlace->close();

        return $resultado['total2'] > 0; 
    }
    public function Agregar($datos)
    {
        $enlace = dbConectar();

        $sql = "INSERT INTO promociones (titulo, descripcion, cantidad, fecha_fin, ID_Negocio ) VALUES (?, ?, ?, ?, ?)";
        $consulta = $enlace->prepare($sql);



        $consulta->bind_param(
            "ssisi",
            $datos["Titulo"],
            $datos["Descripcion"],
            $datos["Cantidad"],
            $datos["FechaFin"],
            $datos["ID_Negocio"]
        );

        return $consulta->execute();
    }
    public function Editar($datos)
    {
        $enlace = dbConectar();

        if (isset($datos["Contra"])) {
            $sql = "UPDATE usuarios SET Nombre=?, ApellidoP=?, ApellidoM=?, Correo=?, tipo_usuario=? WHERE ID_Usuario=?";
            $consulta = $enlace->prepare($sql);

            $consulta->bind_param(
                "sssssi",
                $datos["Nombre"],
                $datos["ApellidoP"],
                $datos["ApellidoM"],
                $datos["Correo"],
                $datos["tipo_usuario"],
                $datos["ID_Usuario"]
            );
        } else {
            $sql = "UPDATE usuarios SET Nombre=?, ApellidoP=?, ApellidoM=?, Correo=?, tipo_usuario=? WHERE ID_Usuario=?";
            $consulta = $enlace->prepare($sql);
            $consulta->bind_param(
                "sssssi",
                $datos["Nombre"],
                $datos["ApellidoP"],
                $datos["ApellidoM"],
                $datos["Correo"],
                
                $datos["tipo_usuario"],
                $datos["ID_Usuario"]
            );
        }

        return $consulta->execute();
    }
    public function cambiarClave($idUsuario, $claveEncriptada)
    {
        $enlace = dbConectar();
        $sql = "UPDATE usuarios SET Contra=? WHERE ID_Usuario=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("si", $claveEncriptada, $idUsuario);
        $resultado = $consulta->execute();
        $enlace->close();
        return $resultado;
    }

    public function Eliminar($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "DELETE FROM usuarios WHERE ID_Usuario=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_usuario);

        return $consulta->execute();
    }
    public function ObtenerUsuario($ID_Promocion)
    {
        $enlace = dbConectar();
        $sql = "SELECT ID_Promocion, titulo, descripcion, fecha_fin, cantidad FROM promociones WHERE ID_Promocion=?";
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
}
