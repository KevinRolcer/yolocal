<?php
class Cupones
{

    /** Evita error SQL si aún no existe la tabla (migración pendiente). */
    private function tieneTablaCuponesEmitidos($enlace): bool
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $r = @$enlace->query("SHOW TABLES LIKE 'cupones_emitidos'");
        $cached = $r && $r->num_rows > 0;
        return $cached;
    }

public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [], $usuarioId, $usuarioTipo)
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;

    $colsEmitidos = $this->tieneTablaCuponesEmitidos($enlace)
        ? ", (SELECT COUNT(*) FROM cupones_emitidos ce WHERE ce.ID_Promocion = p.ID_Promocion AND ce.canjeado = 1) AS cupones_canjeados_emitidos,
    (SELECT COUNT(*) FROM cupones_emitidos ce WHERE ce.ID_Promocion = p.ID_Promocion AND ce.canjeado = 0) AS cupones_pendientes_canje"
        : ", 0 AS cupones_canjeados_emitidos, 0 AS cupones_pendientes_canje";
 
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
    {$colsEmitidos}
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

    if (!empty($filtros['estado'])) {
        if ($filtros['estado'] === 'activo') {
            $sql .= " AND p.Estatus = 1 AND p.fecha_fin >= CURDATE()";
        } elseif ($filtros['estado'] === 'expirado') {
            $sql .= " AND p.fecha_fin < CURDATE()";
        }
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

    // Total de registros (con filtros)
    $countSql = "SELECT COUNT(*) as total 
                 FROM promociones p
                 INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
                 WHERE 1=1";
    
    $countValues = [];
    $countTipos = "";

    if (!empty($filtros['titulo'])) {
        $countSql .= " AND p.titulo LIKE ?";
        $countValues[] = "%" . $filtros['titulo'] . "%";
        $countTipos .= "s";
    }
    if (!empty($filtros['descripcion'])) {
        $countSql .= " AND p.descripcion LIKE ?";
        $countValues[] = "%" . $filtros['descripcion'] . "%";
        $countTipos .= "s";
    }
    if (!empty($filtros['NombreNegocio'])) {
        $countSql .= " AND n.nombre_negocio LIKE ?";
        $countValues[] = "%" . $filtros['NombreNegocio'] . "%";
        $countTipos .= "s";
    }

    if (!empty($filtros['estado'])) {
        if ($filtros['estado'] === 'activo') {
            $countSql .= " AND p.Estatus = 1 AND p.fecha_fin >= CURDATE()";
        } elseif ($filtros['estado'] === 'expirado') {
            $countSql .= " AND p.fecha_fin < CURDATE()";
        }
    }

    if ($usuarioTipo === "negocio") {
        $countSql .= " AND n.ID_Usuario = ?";
        $countValues[] = $usuarioId;
        $countTipos .= "i";
    }

    $countConsulta = $enlace->prepare($countSql);
    if (!empty($countTipos)) {
        $countConsulta->bind_param($countTipos, ...$countValues);
    }
    $countConsulta->execute();
    $countResult = $countConsulta->get_result();
    $totalRegistros = $countResult->fetch_assoc()["total"];
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    $consulta->close();
    $countConsulta->close();

    return [
        "promociones" => $promociones,
        "totalPaginas" => $totalPaginas,
        "paginaActual" => $pagina,
        "totalRegistros" => $totalRegistros
    ];
}

public function ListarTODOSP($pagina = 1, $registrosPorPagina = 10, $filtros = [], $usuarioId, $usuarioTipo)
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;

    $select = "SELECT 
        p.ID_Promocion,
        p.titulo,
        p.descripcion,
        p.cantidad,
        p.fecha_fin,
        p.Estatus,
        p.Canjeados,
        p.Descargados,
        p.PromoMiercoles,
        n.nombre_negocio AS nombre_negocio,
        n.Direccion AS direccion_negocio,
        n.ID_Categoria,
        c.Descripcion AS categoria
    FROM promociones p
    INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
    INNER JOIN categorias c ON n.ID_Categoria = c.ID_Categoria
    WHERE 1=1";

    $values = [];
    $tipos = "";
    $this->aplicarFiltrosPublicos($select, $values, $tipos, $filtros, $usuarioId, $usuarioTipo);

    $ordenes = [
        "recientes" => "p.ID_Promocion DESC",
        "vencen_pronto" => "p.fecha_fin ASC, p.ID_Promocion DESC",
        "mas_disponibles" => "p.cantidad DESC, p.ID_Promocion DESC",
        "negocio" => "n.nombre_negocio ASC, p.ID_Promocion DESC"
    ];
    $orden = $ordenes[$filtros['orden'] ?? "recientes"] ?? $ordenes["recientes"];
    $sql = $select . " ORDER BY $orden LIMIT ?, ?";
    $values[] = $offset;
    $values[] = $registrosPorPagina;
    $tipos .= "ii";

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

    $countSql = "SELECT COUNT(*) as total
        FROM promociones p
        INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
        INNER JOIN categorias c ON n.ID_Categoria = c.ID_Categoria
        WHERE 1=1";
    $countValues = [];
    $countTipos = "";
    $this->aplicarFiltrosPublicos($countSql, $countValues, $countTipos, $filtros, $usuarioId, $usuarioTipo);

    $countConsulta = $enlace->prepare($countSql);
    if (!empty($countTipos)) {
        $countConsulta->bind_param($countTipos, ...$countValues);
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
        "totalRegistros" => $totalRegistros
    ];
}

private function aplicarFiltrosPublicos(&$sql, &$values, &$tipos, $filtros, $usuarioId, $usuarioTipo)
{
    if (!empty($filtros['busqueda'])) {
        $sql .= " AND (p.titulo LIKE ? OR p.descripcion LIKE ? OR n.nombre_negocio LIKE ? OR c.Descripcion LIKE ?)";
        $termino = "%" . $filtros['busqueda'] . "%";
        array_push($values, $termino, $termino, $termino, $termino);
        $tipos .= "ssss";
    }
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
    if (!empty($filtros['categoria']) && is_numeric($filtros['categoria'])) {
        $sql .= " AND n.ID_Categoria = ?";
        $values[] = intval($filtros['categoria']);
        $tipos .= "i";
    }

    $estado = $filtros['estado'] ?? "activos";
    if ($estado === "miercoles") {
        $sql .= " AND p.Estatus = 1 AND p.cantidad > 0 AND p.fecha_fin >= CURDATE() AND p.PromoMiercoles = 1";
    } elseif ($estado === "por_agotarse") {
        $sql .= " AND p.Estatus = 1 AND p.cantidad BETWEEN 1 AND 5 AND p.fecha_fin >= CURDATE()";
    } else {
        $sql .= " AND p.Estatus = 1 AND p.cantidad > 0 AND p.fecha_fin >= CURDATE()";
    }

    if ($usuarioTipo === "negocio") {
        $sql .= " AND n.ID_Usuario = ?";
        $values[] = $usuarioId;
        $tipos .= "i";
    }
}

public function ListarCategoriasConCupones()
{
    $enlace = dbConectar();
    $sql = "SELECT DISTINCT c.ID_Categoria, c.Descripcion
            FROM categorias c
            INNER JOIN negocios n ON n.ID_Categoria = c.ID_Categoria
            INNER JOIN promociones p ON p.ID_Negocio = n.ID_Negocio
            WHERE p.Estatus = 1 AND p.cantidad > 0 AND p.fecha_fin >= CURDATE()
            ORDER BY c.Descripcion ASC";
    $resultado = $enlace->query($sql);
    $categorias = [];
    while ($row = $resultado->fetch_assoc()) {
        $categorias[] = $row;
    }
    $enlace->close();
    return $categorias;
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
        $sql = "SELECT p.ID_Promocion, p.titulo, p.descripcion, p.fecha_fin, p.cantidad, p.ID_Negocio
                FROM promociones p
                INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
                WHERE p.ID_Promocion = ?";
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

    private function generarCodigoCuponUnico($enlace, int $idPromocion): string
    {
        for ($i = 0; $i < 18; $i++) {
            $part = str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
            $codigo = $part . "-" . $idPromocion . "-" . str_pad((string) random_int(0, 9999), 4, "0", STR_PAD_LEFT);
            $chk = $enlace->prepare("SELECT 1 FROM cupones_emitidos WHERE codigo = ? LIMIT 1");
            $chk->bind_param("s", $codigo);
            $chk->execute();
            if ($chk->get_result()->num_rows === 0) {
                return $codigo;
            }
        }
        throw new RuntimeException("unique");
    }

    /**
     * Emite un cupón con código único (descarga PDF). No decrementa cantidad hasta el canje.
     */
    public function emitirCuponDescargaPdf(int $idPromocion): array
    {
        $enlace = dbConectar();
        $enlace->begin_transaction();
        try {
            $stmt = $enlace->prepare("SELECT cantidad, Estatus, fecha_fin FROM promociones WHERE ID_Promocion = ? FOR UPDATE");
            $stmt->bind_param("i", $idPromocion);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            if (!$res) {
                throw new RuntimeException("nf");
            }
            if ((int) $res["Estatus"] !== 1) {
                throw new RuntimeException("inactivo");
            }
            if (!empty($res["fecha_fin"]) && strtotime($res["fecha_fin"] . " 23:59:59") < time()) {
                throw new RuntimeException("exp");
            }
            $cant = (int) $res["cantidad"];
            if ($cant <= 0) {
                throw new RuntimeException("stock");
            }
            $qPend = $enlace->prepare("SELECT COUNT(*) AS c FROM cupones_emitidos WHERE ID_Promocion = ? AND canjeado = 0");
            $qPend->bind_param("i", $idPromocion);
            $qPend->execute();
            $pend = (int) $qPend->get_result()->fetch_assoc()["c"];
            if ($pend >= $cant) {
                throw new RuntimeException("cap");
            }
            $codigo = $this->generarCodigoCuponUnico($enlace, $idPromocion);
            $ins = $enlace->prepare("INSERT INTO cupones_emitidos (ID_Promocion, codigo, canjeado) VALUES (?, ?, 0)");
            $ins->bind_param("is", $idPromocion, $codigo);
            if (!$ins->execute()) {
                throw new RuntimeException("ins");
            }
            $up = $enlace->prepare("UPDATE promociones SET Descargados = Descargados + 1 WHERE ID_Promocion = ?");
            $up->bind_param("i", $idPromocion);
            $up->execute();
            $enlace->commit();
            return ["success" => true, "codigo" => $codigo];
        } catch (Throwable $e) {
            $enlace->rollback();
            $code = $e instanceof RuntimeException ? $e->getMessage() : "x";
            $map = [
                "nf" => "Promoción no encontrada.",
                "inactivo" => "La promoción no está activa.",
                "exp" => "La promoción ya venció.",
                "stock" => "No hay cupones disponibles.",
                "cap" => "Se alcanzó el límite de cupones emitidos pendientes de canje.",
                "ins" => "No se pudo registrar el cupón.",
                "unique" => "No se pudo generar un código único. Intente de nuevo.",
            ];
            $msg = $map[$code] ?? "No se pudo generar el cupón. Si acaba de actualizar, ejecute la migración SQL (tabla cupones_emitidos).";
            return ["success" => false, "msg" => $msg];
        }
    }

    public function canjearConCodigoCupon(int $idPromocion, string $codigoRaw): array
    {
        $codigo = preg_replace("/\s+/", "", trim($codigoRaw));
        if ($codigo === "") {
            return ["ok" => false, "msg" => "Ingrese el código del cupón."];
        }
        $enlace = dbConectar();
        $enlace->begin_transaction();
        try {
            $st = $enlace->prepare("SELECT ID_Cupon, canjeado FROM cupones_emitidos WHERE ID_Promocion = ? AND codigo = ? FOR UPDATE");
            $st->bind_param("is", $idPromocion, $codigo);
            $st->execute();
            $row = $st->get_result()->fetch_assoc();
            if (!$row) {
                $enlace->rollback();
                return ["ok" => false, "msg" => "Código de cupón incorrecto o no corresponde a esta promoción."];
            }
            if ((int) $row["canjeado"] === 1) {
                $enlace->rollback();
                return [
                    "ok" => false,
                    "reason" => "already_redeemed",
                    "msg" => "Este cupón ya fue canjeado anteriormente."
                ];
            }
            $idCupon = (int) $row["ID_Cupon"];
            $up1 = $enlace->prepare("UPDATE promociones SET cantidad = cantidad - 1, Canjeados = Canjeados + 1 WHERE ID_Promocion = ? AND cantidad > 0");
            $up1->bind_param("i", $idPromocion);
            $up1->execute();
            if ($up1->affected_rows !== 1) {
                $enlace->rollback();
                return ["ok" => false, "msg" => "No quedan unidades disponibles para canjear."];
            }
            $up2 = $enlace->prepare("UPDATE cupones_emitidos SET canjeado = 1, fecha_canje = NOW() WHERE ID_Cupon = ? AND canjeado = 0");
            $up2->bind_param("i", $idCupon);
            $up2->execute();
            if ($up2->affected_rows !== 1) {
                $enlace->rollback();
                return ["ok" => false, "msg" => "No se pudo completar el canje. Intente de nuevo."];
            }
            $enlace->commit();
            return ["ok" => true, "msg" => "¡Cupón canjeado con éxito!"];
        } catch (Throwable $e) {
            $enlace->rollback();
            return ["ok" => false, "msg" => "Error al procesar el canje."];
        }
    }

}
