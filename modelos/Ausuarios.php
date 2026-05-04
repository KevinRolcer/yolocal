<?php
class Usuarios
{

    private function asegurarColumnaRutaPerfil($enlace)
    {
        $resultado = $enlace->query("SHOW COLUMNS FROM usuarios LIKE 'RutaPerfil'");
        if ($resultado && $resultado->num_rows === 0) {
            $enlace->query("ALTER TABLE usuarios ADD COLUMN RutaPerfil VARCHAR(255) NULL AFTER ApellidoM");
        }
    }

    private function asegurarColumnaEstatus($enlace)
    {
        $resultado = $enlace->query("SHOW COLUMNS FROM usuarios LIKE 'Estatus'");
        if ($resultado && $resultado->num_rows === 0) {
            $enlace->query("ALTER TABLE usuarios ADD COLUMN Estatus VARCHAR(20) NOT NULL DEFAULT 'activo' AFTER tipo_usuario");
        }
    }

    public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [], $ordenColumna = 'ID_Usuario', $ordenDireccion = 'DESC')
{
    $enlace = dbConectar();
    $this->asegurarColumnaEstatus($enlace);
    $offset = ($pagina - 1) * $registrosPorPagina;

    $sql = "SELECT * FROM usuarios WHERE 1=1";
    $countSql = "SELECT COUNT(*) as total FROM usuarios WHERE 1=1";
    $values = [];
    $countValues = [];
    $tipos = "";
    $countTipos = "";

    // Filtros dinámicos
    if (!empty($filtros['ID_Usuario'])) {
        $sql .= " AND ID_Usuario LIKE ?";
        $countSql .= " AND ID_Usuario LIKE ?";
        $values[] = "%" . $filtros['ID_Usuario'] . "%";
        $countValues[] = "%" . $filtros['ID_Usuario'] . "%";
        $tipos .= "s";
        $countTipos .= "s";
    }

    if (!empty($filtros['Nombre'])) {
        $sql .= " AND Nombre LIKE ?";
        $countSql .= " AND Nombre LIKE ?";
        $values[] = "%" . $filtros['Nombre'] . "%";
        $countValues[] = "%" . $filtros['Nombre'] . "%";
        $tipos .= "s";
        $countTipos .= "s";
    }

    if (!empty($filtros['Apellidos'])) {
        $sql .= " AND CONCAT(ApellidoP, ' ', ApellidoM) LIKE ?";
        $countSql .= " AND CONCAT(ApellidoP, ' ', ApellidoM) LIKE ?";
        $values[] = "%" . $filtros['Apellidos'] . "%";
        $countValues[] = "%" . $filtros['Apellidos'] . "%";
        $tipos .= "s";
        $countTipos .= "s";
    }

    if (!empty($filtros['Correo'])) {
        $sql .= " AND Correo LIKE ?";
        $countSql .= " AND Correo LIKE ?";
        $values[] = "%" . $filtros['Correo'] . "%";
        $countValues[] = "%" . $filtros['Correo'] . "%";
        $tipos .= "s";
        $countTipos .= "s";
    }

    // Filtro de estatus
    if (!empty($filtros['Estatus']) && $filtros['Estatus'] !== 'todos') {
        $sql .= " AND Estatus = ?";
        $countSql .= " AND Estatus = ?";
        $values[] = $filtros['Estatus'];
        $countValues[] = $filtros['Estatus'];
        $tipos .= "s";
        $countTipos .= "s";
    }

    // Validar orden dinámico (whitelist)
    $columnasValidas = ['ID_Usuario', 'Nombre', 'ApellidoP', 'Correo', 'Estatus'];
    $direccionValida = ['ASC', 'DESC'];
    
    if (!in_array($ordenColumna, $columnasValidas)) $ordenColumna = 'ID_Usuario';
    if (!in_array(strtoupper($ordenDireccion), $direccionValida)) $ordenDireccion = 'DESC';

    // Si el orden es por Nombre, usamos el nombre completo para que sea más natural
    $orderExpr = ($ordenColumna === 'Nombre') 
        ? "TRIM(CONCAT_WS(' ', Nombre, ApellidoP, ApellidoM))" 
        : "$ordenColumna";

    // Orden y paginación
    $sql .= " ORDER BY $orderExpr $ordenDireccion LIMIT ?, ?";
    $values[] = $offset;
    $values[] = $registrosPorPagina;
    $tipos .= "ii";

    // Preparar y ejecutar consulta principal
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

    // Total de registros con los mismos filtros
    $countConsulta = $enlace->prepare($countSql);
    if ($countTipos && count($countValues) > 0) {
        $countConsulta->bind_param($countTipos, ...$countValues);
    }
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
        "totalRegistros" => $totalRegistros,
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

        $sql = "INSERT INTO usuarios (Nombre, ApellidoP, ApellidoM, Correo, contra, cPrueba, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $consulta = $enlace->prepare($sql);


        $passwordHash = password_hash($datos["contra"], PASSWORD_DEFAULT);

        $consulta->bind_param(
            "sssssss",
            $datos["Nombre"],
            $datos["ApellidoP"],
            $datos["ApellidoM"],
            $datos["Correo"],
            $passwordHash,
            $datos["contra"],
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
    public function cambiarClave($idUsuario, $claveEncriptada, $claveNueva)
    {
        $enlace = dbConectar();
        $sql = "UPDATE usuarios SET cPrueba=?, Contra=? WHERE ID_Usuario=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("ssi", $claveNueva, $claveEncriptada, $idUsuario);
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
        $this->asegurarColumnaRutaPerfil($enlace);
        $sql = "SELECT ID_Usuario, Nombre, ApellidoP, ApellidoM, Correo, tipo_usuario, RutaPerfil FROM usuarios WHERE ID_Usuario=?";
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

    public function guardarFotoPerfil($idUsuario, $archivo)
    {
        $enlace = dbConectar();
        $this->asegurarColumnaRutaPerfil($enlace);

        if (!isset($archivo["error"]) || $archivo["error"] !== UPLOAD_ERR_OK) {
            $enlace->close();
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "No se pudo cargar la imagen en el servidor."
            ];
        }

        if (!isset($archivo["type"]) || strpos($archivo["type"], "image/") !== 0) {
            $enlace->close();
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "El archivo seleccionado no es una imagen valida."
            ];
        }

        $directorioBase = dirname(__DIR__) . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "perfiles";
        if (!is_dir($directorioBase) && !@mkdir($directorioBase, 0777, true)) {
            $enlace->close();
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "La carpeta de perfiles no esta disponible."
            ];
        }

        if (!is_writable($directorioBase)) {
            $enlace->close();
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "La carpeta de perfiles no tiene permisos de escritura."
            ];
        }

        $extension = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
        $extensionesPermitidas = ["jpg", "jpeg", "png", "webp", "gif"];
        if (!in_array($extension, $extensionesPermitidas, true)) {
            $enlace->close();
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "Formato de imagen no permitido."
            ];
        }

        $nombreArchivo = "perfil_" . intval($idUsuario) . "_" . uniqid() . "." . $extension;
        $rutaServidor = $directorioBase . DIRECTORY_SEPARATOR . $nombreArchivo;
        $rutaRelativa = "assets/uploads/perfiles/" . $nombreArchivo;

        if (!move_uploaded_file($archivo["tmp_name"], $rutaServidor)) {
            $enlace->close();
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "No fue posible mover la imagen al servidor."
            ];
        }

        $sql = "UPDATE usuarios SET RutaPerfil = ? WHERE ID_Usuario = ?";
        $consulta = $enlace->prepare($sql);
        if (!$consulta) {
            @unlink($rutaServidor);
            $enlace->close();
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "No se pudo actualizar la foto de perfil."
            ];
        }

        $consulta->bind_param("si", $rutaRelativa, $idUsuario);
        $status = $consulta->execute();
        $consulta->close();
        $enlace->close();

        if (!$status) {
            @unlink($rutaServidor);
            return [
                "success" => false,
                "storage" => "local",
                "msg" => "No se pudo guardar la ruta de la foto de perfil."
            ];
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION["foto_perfil"] = $rutaRelativa;
        }

        return [
            "success" => true,
            "storage" => "server",
            "ruta" => $rutaRelativa
        ];
    }
}
