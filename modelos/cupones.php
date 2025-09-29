<?php
class Cupones
{


public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [], $usuarioId, $usuarioTipo)
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;
 
    // Consulta con JOIN a negocios
    $sql = "SELECT 
    p.ID_Promocion,
    p.titulo,
    p.descripcion,
    p.cantidad,
    p.fecha_fin,
    p.Estatus,
    p.Canjeados,
    p.Descargados,
    n.nombre_negocio AS nombre_negocio,
    n.Direccion AS direccion_negocio,
    c.Descripcion AS categoria
FROM promociones p
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

    // Si el tipo de usuario es "negocio", filtrar por ID_Usuario
    if ($usuarioTipo === "negocio") {
        $sql .= " AND n.ID_Usuario = ?";
        $values[] = $usuarioId;  // Filtrar por negocio específico
        $tipos .= "i";
    }

    // Orden y paginación
    $sql .= " ORDER BY p.ID_Promocion DESC LIMIT ?, ?";
    $values[] = $offset;
    $values[] = $registrosPorPagina;
    $tipos .= "ii";

    // Preparar y ejecutar la consulta
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
                 FROM promociones p
                 INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
                 WHERE 1=1";

    // Si el tipo de usuario es "negocio", aplicar el filtro
    if ($usuarioTipo === "negocio") {
        $countSql .= " AND n.ID_Usuario = ?";
    }

    $countConsulta = $enlace->prepare($countSql);
    if ($usuarioTipo === "negocio") {
        $countConsulta->bind_param("i", $usuarioId);
    }
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

public function ListarTODOSP($pagina = 1, $registrosPorPagina = 10, $filtros = [], $usuarioId, $usuarioTipo)
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;
 
    // Consulta con JOIN a negocios
    $sql = "SELECT 
    p.ID_Promocion,
    p.titulo,
    p.descripcion,
    p.cantidad,
    p.fecha_fin,
    p.Estatus,
    p.Canjeados,
    p.Descargados,
    n.nombre_negocio AS nombre_negocio,
    n.Direccion AS direccion_negocio,
    c.Descripcion AS categoria
FROM promociones p
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

    if ($usuarioTipo === "negocio") {
        $sql .= " AND ID_Usuario = ?";
        $values[] = $usuarioId;
        $tipos .= "i";
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

    // Total de registros 
    $countSql = "SELECT COUNT(*) as total 
                 FROM promociones p
                 INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
                 WHERE p.fecha_fin >= CURDATE()";

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

        $sql = "INSERT INTO promociones (titulo, descripcion, cantidad, fecha_fin, ID_Negocio, PromoMiercoles, Estatus ) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $consulta = $enlace->prepare($sql);



        $consulta->bind_param(
            "ssisii",
            $datos["Titulo"],
            $datos["Descripcion"],
            $datos["Cantidad"],
            $datos["FechaFin"],
            $datos["ID_Negocio"],
            $datos["PromoMiercoles"]
        );

        return $consulta->execute();
    }
    public function Editar($datos)
{
    $enlace = dbConectar();

    $sql = "UPDATE promociones 
            SET Titulo = ?, 
                Descripcion = ?, 
                Fecha_Fin = ?, 
                Cantidad = ?, 
                ID_Negocio = ? 
            WHERE ID_Promocion = ?";

    $consulta = $enlace->prepare($sql);

    $consulta->bind_param(
        "sssiii", // Tipos: string, string, string, int, int, int
        $datos["Titulo"],
        $datos["Descripcion"],
        $datos["FechaFin"],
        $datos["Cantidad"],
        $datos["ID_Negocio"],
        $datos["ID_Promocion"]
    );

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
     public function RestarCupon($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "UPDATE promociones SET cantidad = cantidad - 1 WHERE ID_Promocion=?";
        $sql2 = "UPDATE promociones SET Canjeados = Canjeados + 1 WHERE ID_Promocion=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_usuario);
        $consulta2 = $enlace->prepare($sql2);
        $consulta2->bind_param("i", $ID_usuario);

        return $consulta->execute() && $consulta2->execute();
    }
     public function DESCARGARCUPON($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "UPDATE promociones SET Descargados = Descargados + 1 WHERE ID_Promocion=?";
      
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_usuario);
        

        return $consulta->execute();
    }
    public function SumarCupon($ID_Promocion, $cantidad)
{
    $enlace = dbConectar();

    $sql = "UPDATE promociones SET cantidad = cantidad + ? WHERE ID_Promocion = ?";
    $consulta = $enlace->prepare($sql);
    $consulta->bind_param("ii", $cantidad, $ID_Promocion);

    return $consulta->execute(); // devuelve true si se ejecutó correctamente
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
     public function CambiarEstatus($ID_Promocion, $estatus)
{
    $enlace = dbConectar();
    $sql = "UPDATE promociones SET estatus = ? WHERE ID_Promocion = ?";
    $consulta = $enlace->prepare($sql);
    $consulta->bind_param("ii", $estatus, $ID_Promocion);

    return $consulta->execute();
}

}
