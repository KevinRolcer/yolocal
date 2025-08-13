<?php
class Usuarios
{


    public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [])
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;

    $sql = "SELECT * FROM usuarios WHERE 1=1";
    $values = [];
    $tipos = "";

    // Filtros dinámicos
    if (!empty($filtros['ID_Usuario'])) {
        $sql .= " AND ID_Usuario LIKE ?";
        $values[] = "%" . $filtros['ID_Usuario'] . "%";
        $tipos .= "s";
    }

    if (!empty($filtros['Nombre'])) {
        $sql .= " AND Nombre LIKE ?";
        $values[] = "%" . $filtros['Nombre'] . "%";
        $tipos .= "s";
    }

    if (!empty($filtros['Apellidos'])) {
        $sql .= " AND CONCAT(ApellidoP, ' ', ApellidoM) LIKE ?";
        $values[] = "%" . $filtros['Apellidos'] . "%";
        $tipos .= "s";
    }

    if (!empty($filtros['Correo'])) {
        $sql .= " AND Correo LIKE ?";
        $values[] = "%" . $filtros['Correo'] . "%";
        $tipos .= "s";
    }

    // Orden y paginación
    $sql .= " ORDER BY ID_Usuario DESC LIMIT ?, ?";
    $values[] = $offset;
    $values[] = $registrosPorPagina;
    $tipos .= "ii"; // offset y limit son enteros

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
    $countSql = "SELECT COUNT(*) as total FROM usuarios WHERE 1=1";

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

        $sql = "INSERT INTO usuarios (Nombre, ApellidoP, ApellidoM, Correo, contra, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?)";
        $consulta = $enlace->prepare($sql);


        $passwordHash = password_hash($datos["contra"], PASSWORD_DEFAULT);

        $consulta->bind_param(
            "ssssss",
            $datos["Nombre"],
            $datos["ApellidoP"],
            $datos["ApellidoM"],
            $datos["Correo"],
            $passwordHash,
            $datos["tipo_usuario"]
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
    public function ObtenerUsuario($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "SELECT ID_Usuario, Nombre, ApellidoP, ApellidoM, Correo, tipo_usuario FROM usuarios WHERE ID_Usuario=?";
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
}
