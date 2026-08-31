<?php
class Negocios
{


    public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [], $usuarioId, $usuarioTipo, $ordenColumna = 'ID_Negocio', $ordenDireccion = 'DESC')
{
    $enlace = dbConectar();
    $offset = ($pagina - 1) * $registrosPorPagina;

    $sql = "SELECT negocios.*, usuarios.*, categorias.Descripcion AS CategoriaNombre
            FROM negocios 
            LEFT JOIN usuarios ON negocios.ID_Usuario = usuarios.ID_Usuario 
            LEFT JOIN categorias ON negocios.ID_Categoria = categorias.ID_Categoria 
            WHERE 1=1";

    $values = [];
    $tipos = "";

    // Filtros dinámicos
    if (!empty($filtros['Correo'])) {
        $sql .= " AND (usuarios.Correo LIKE ? OR usuarios.nombre LIKE ?)";
        $values[] = "%" . $filtros['Correo'] . "%";
        $values[] = "%" . $filtros['Correo'] . "%";
        $tipos .= "ss";
    }

    if (!empty($filtros['Nombre'])) {
        $sql .= " AND negocios.nombre_negocio LIKE ?";
        $values[] = "%" . $filtros['Nombre'] . "%";
        $tipos .= "s";
    }

    if (isset($filtros['Estatus']) && $filtros['Estatus'] !== 'todos') {
        $sql .= " AND negocios.estado = ?";
        $values[] = intval($filtros['Estatus']);
        $tipos .= "i";
    }

    if (isset($filtros['Categorias']) && !empty($filtros['Categorias'])) {
        $cats = $filtros['Categorias'];
        if (!is_array($cats)) $cats = explode(',', $cats);
        
        $placeholders = [];
        $hasZero = false;
        $validCats = [];

        foreach ($cats as $c) {
            if ($c == "0") {
                $hasZero = true;
            } else {
                $validCats[] = intval($c);
            }
        }

        $catConditions = [];
        if (!empty($validCats)) {
            $placeholders = array_fill(0, count($validCats), '?');
            $catConditions[] = "negocios.ID_Categoria IN (" . implode(',', $placeholders) . ")";
            foreach ($validCats as $vc) {
                $values[] = $vc;
                $tipos .= "i";
            }
        }
        if ($hasZero) {
            $catConditions[] = "(negocios.ID_Categoria IS NULL OR negocios.ID_Categoria = 0)";
        }

        if (!empty($catConditions)) {
            $sql .= " AND (" . implode(' OR ', $catConditions) . ")";
        }
    }

    // Si el usuario es tipo negocio, filtrar por su propio ID
    if ($usuarioTipo === "negocio") {
        $sql .= " AND usuarios.ID_Usuario = ?";
        $values[] = $usuarioId;
        $tipos .= "i";
    }

    // Validar columna de orden y asignar prefijo de tabla correcto
    $columnasPermitidas = ['ID_Negocio', 'nombre_negocio', 'Correo'];
    if (!in_array($ordenColumna, $columnasPermitidas)) {
        $ordenColumna = 'ID_Negocio';
    }

    $prefix = ($ordenColumna === 'Correo') ? 'usuarios' : 'negocios';
    $ordenDireccion = (strtoupper($ordenDireccion) === 'ASC') ? 'ASC' : 'DESC';

    // Para orden alfabético, usamos LOWER para evitar problemas de capitalización
    $orderSql = ($ordenColumna === 'nombre_negocio') 
        ? "LOWER($prefix.$ordenColumna)" 
        : "$prefix.$ordenColumna";

    // Orden y paginación
    $sql .= " ORDER BY $orderSql $ordenDireccion LIMIT ?, ?";
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

    $miembros = [];
    while ($row = $result->fetch_assoc()) {
        $miembros[] = $row;
    }

    // Total de registros
    $countSql = "SELECT COUNT(*) as total 
                 FROM negocios 
                 LEFT JOIN usuarios ON negocios.ID_Usuario = usuarios.ID_Usuario 
                 WHERE 1=1";

    $countValues = [];
    $countTipos = "";

    if (!empty($filtros['Correo'])) {
        $countSql .= " AND (usuarios.Correo LIKE ? OR usuarios.nombre LIKE ?)";
        $countValues[] = "%" . $filtros['Correo'] . "%";
        $countValues[] = "%" . $filtros['Correo'] . "%";
        $countTipos .= "ss";
    }

    if (!empty($filtros['Nombre'])) {
        $countSql .= " AND negocios.nombre_negocio LIKE ?";
        $countValues[] = "%" . $filtros['Nombre'] . "%";
        $countTipos .= "s";
    }

    if (isset($filtros['Estatus']) && $filtros['Estatus'] !== 'todos') {
        $countSql .= " AND negocios.estado = ?";
        $countValues[] = intval($filtros['Estatus']);
        $countTipos .= "i";
    }

    if (isset($filtros['Categorias']) && !empty($filtros['Categorias'])) {
        $cats = $filtros['Categorias'];
        if (!is_array($cats)) $cats = explode(',', $cats);
        $hasZero = false;
        $validCats = [];
        foreach ($cats as $c) {
            if ($c == "0") $hasZero = true;
            else $validCats[] = intval($c);
        }
        $catConditions = [];
        if (!empty($validCats)) {
            $placeholders = array_fill(0, count($validCats), '?');
            $catConditions[] = "negocios.ID_Categoria IN (" . implode(',', $placeholders) . ")";
            foreach ($validCats as $vc) {
                $countValues[] = $vc;
                $countTipos .= "i";
            }
        }
        if ($hasZero) {
            $catConditions[] = "(negocios.ID_Categoria IS NULL OR negocios.ID_Categoria = 0)";
        }
        if (!empty($catConditions)) {
            $countSql .= " AND (" . implode(' OR ', $catConditions) . ")";
        }
    }

    if ($usuarioTipo === "negocio") {
        $countSql .= " AND usuarios.ID_Usuario = ?";
        $countValues[] = $usuarioId;
        $countTipos .= "i";
    }

    $countConsulta = $enlace->prepare($countSql);
    if (!$countConsulta) {
        throw new Exception("Error en la preparación de la consulta COUNT: " . $enlace->error);
    }

    if (!empty($countValues)) {
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
        "miembros" => $miembros,
        "totalPaginas" => $totalPaginas,
        "paginaActual" => $pagina,
        "totalRegistros" => $totalRegistros
    ];
}

    public function ListarIconos()
    {
        $enlace = dbConectar();
        if (!($enlace instanceof mysqli)) {
            throw new Exception("No se pudo conectar a la base de datos.");
        }
       

        $sql = "SELECT ID_Negocio, nombre_negocio, Rutaicono, Direccion FROM negocios WHERE 1=1 ORDER BY RAND()";
        

        // Filtros dinámicos
        
        $consulta = $enlace->prepare($sql);
        if (!$consulta) {
            throw new Exception("Error al preparar LISTAICONOS: " . $enlace->error);
        }
        

        $consulta->execute();
        $result = $consulta->get_result();

        $miembros = [];
        while ($row = $result->fetch_assoc()) {
            $miembros[] = $row;
        }

        // Cerrar conexiones
        $consulta->close();
        
        $enlace->close();

        return [
            "miembros" => $miembros
            
        ];
    }
    public function ListarIconosBanner()
    {
        $enlace = dbConectar();
        if (!($enlace instanceof mysqli)) {
            throw new Exception("No se pudo conectar a la base de datos.");
        }
       $sql = "SELECT ID_Negocio, nombre_negocio, Rutaicono, DescripcionN, c.Descripcion AS nombre_categoria 
            FROM negocios n 
            INNER JOIN categorias c ON n.ID_Categoria = c.ID_Categoria 
            WHERE n.Relevancia = 4 
            ORDER BY RAND()"; // Solo negocios con Relevancia = 3

        // Filtros dinámicos
        
        $consulta = $enlace->prepare($sql);
        if (!$consulta) {
            throw new Exception("Error al preparar LISTAICONOSBanner: " . $enlace->error);
        }
        

        $consulta->execute();
        $result = $consulta->get_result();

        $miembros = [];
        while ($row = $result->fetch_assoc()) {
            $miembros[] = $row;
        }

        // Cerrar conexiones
        $consulta->close();
        
        $enlace->close();

        return [
            "miembros" => $miembros
            
        ];
    }
    public function ListarIconos2()
    {
        $enlace = dbConectar();
        if (!($enlace instanceof mysqli)) {
            throw new Exception("No se pudo conectar a la base de datos.");
        }
       

        $sql = "SELECT ID_Negocio, nombre_negocio, Rutaicono, Direccion FROM negocios WHERE 1=1 AND Relevancia IN (3, 2) ORDER BY RAND()";
        

        // Filtros dinámicos
        
        $consulta = $enlace->prepare($sql);
        if (!$consulta) {
            throw new Exception("Error al preparar LISTAICONOS2: " . $enlace->error);
        }
        

        $consulta->execute();
        $result = $consulta->get_result();

        $miembros = [];
        while ($row = $result->fetch_assoc()) {
            $miembros[] = $row;
        }

        // Cerrar conexiones
        $consulta->close();
        
        $enlace->close();

        return [
            "miembros" => $miembros
            
        ];
    }
    public function buscarMiembroPorID($ID_Miembro)
    {
        $enlace = dbConectar();
        $sql = "SELECT * FROM usuarios WHERE ID_Usuario = ? ";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_Miembro);
        $consulta->execute();
        $result = $consulta->get_result();

        return $result->fetch_assoc();
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
    public function ObtenerClasesDia()
    {
        $enlace = dbConectar();
        $sql = "SELECT * FROM categorias";  // Suponiendo que la tabla se llama 'membresias'
        $consulta = $enlace->prepare($sql);
        $consulta->execute();
        $result = $consulta->get_result();

        $membresias = [];
        while ($membresia = $result->fetch_assoc()) {
            $membresias[] = $membresia;
        }

        return $membresias;
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

    private static $codigosCanjeAseguradosEnPeticion = false;

    /** Código alfanumérico único para canje de cupones (sin 0/O ni 1/I). */
    private function generarCodigoCanjeUnico($enlace)
    {
        $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        for ($attempt = 0; $attempt < 60; $attempt++) {
            $code = "";
            for ($i = 0; $i < 8; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $st = $enlace->prepare("SELECT 1 FROM negocios WHERE codigo_canje = ? LIMIT 1");
            $st->bind_param("s", $code);
            $st->execute();
            if ($st->get_result()->num_rows === 0) {
                return $code;
            }
        }
        return strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
    }

    /** Asigna código de canje a negocios que no tienen uno (NULL o vacío). */
    public function asegurarCodigosCanjeFaltantes()
    {
        if (self::$codigosCanjeAseguradosEnPeticion) {
            return;
        }
        self::$codigosCanjeAseguradosEnPeticion = true;

        $enlace = dbConectar();
        $chk = $enlace->query("SELECT COUNT(*) AS n FROM negocios WHERE codigo_canje IS NULL OR TRIM(codigo_canje) = ''");
        if (!$chk) {
            return;
        }
        $n = (int) ($chk->fetch_assoc()["n"] ?? 0);
        if ($n === 0) {
            return;
        }

        $r = $enlace->query("SELECT ID_Negocio FROM negocios WHERE codigo_canje IS NULL OR TRIM(codigo_canje) = ''");
        if (!$r) {
            return;
        }
        while ($row = $r->fetch_assoc()) {
            $id = (int) $row["ID_Negocio"];
            $nuevo = $this->generarCodigoCanjeUnico($enlace);
            $u = $enlace->prepare("UPDATE negocios SET codigo_canje = ? WHERE ID_Negocio = ?");
            $u->bind_param("si", $nuevo, $id);
            $u->execute();
        }
    }

    /** Un solo negocio (p. ej. vista pública canje.php sin pasar por el admin). */
    public function asegurarCodigoCanjeSiVacioPorNegocio($idNegocio)
    {
        $idNegocio = (int) $idNegocio;
        if ($idNegocio <= 0) {
            return;
        }
        $enlace = dbConectar();
        $st = $enlace->prepare("SELECT codigo_canje FROM negocios WHERE ID_Negocio = ?");
        $st->bind_param("i", $idNegocio);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        if (!$row) {
            return;
        }
        if (trim((string) ($row["codigo_canje"] ?? "")) !== "") {
            return;
        }
        $nuevo = $this->generarCodigoCanjeUnico($enlace);
        $u = $enlace->prepare("UPDATE negocios SET codigo_canje = ? WHERE ID_Negocio = ?");
        $u->bind_param("si", $nuevo, $idNegocio);
        $u->execute();
    }

    public function Agregar($datos)
    {
        $enlace = dbConectar();

        $nombre = $datos["nombre_negocio"] ?? $datos["Nombre"] ?? "";
        $cod = isset($datos["CodigoCanje"]) ? trim((string) $datos["CodigoCanje"]) : "";
        if ($cod === "") {
            $cod = $this->generarCodigoCanjeUnico($enlace);
        }

        $sql = "INSERT INTO negocios (ID_Usuario, nombre_negocio, ID_Categoria, codigo_canje, estado, fecha_registro, Relevancia) VALUES (?, ?, ?, ?, 1, NOW(), 1)";
        $consulta = $enlace->prepare($sql);

        $consulta->bind_param(
            "isis",
            $datos["ID_Usuario"],
            $nombre,
            $datos["ID_Categoria"],
            $cod
        );

        return $consulta->execute();
    }
    public function Editar($datos, $archivoIcono = null)
{
    $enlace = dbConectar();
    $rutaIconoFinal = $datos["Icono"]; // valor por defecto, mantiene el anterior

    if ($archivoIcono && $archivoIcono["error"] === UPLOAD_ERR_OK) {
        $tiposPermitidos = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp",
            "image/gif" => "gif"
        ];
        $detectorMime = new finfo(FILEINFO_MIME_TYPE);
        $tipoMime = $detectorMime->file($archivoIcono["tmp_name"]);

        if (!isset($tiposPermitidos[$tipoMime])) {
            return false;
        }

        $directorio = __DIR__ . "/../assets/uploads/iconos/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreBase = pathinfo($archivoIcono["name"], PATHINFO_FILENAME);
        $nombreBase = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $nombreBase);
        $nombreArchivo = uniqid("icono_") . "_" . trim($nombreBase, "_") . "." . $tiposPermitidos[$tipoMime];
        $rutaServidor  = $directorio . $nombreArchivo;

        if (move_uploaded_file($archivoIcono["tmp_name"], $rutaServidor)) {
            $rutaIconoFinal = "../assets/uploads/iconos/" . $nombreArchivo;
        }
    }

    $sql = "UPDATE negocios SET 
                nombre_negocio = ?, 
                DescripcionN   = ?, 
                Direccion      = ?, 
                Telefono       = ?, 
                CorreoN        = ?, 
                SitioWeb       = ?, 
                Facebook       = ?, 
                Instagram      = ?,
                TikTok         = ?,
                GoogleMaps     = ?,
                Latitud        = ?,
                Longitud       = ?,
                Relevancia     = ?,
                Rutaicono      = ?
            WHERE ID_Negocio = ?";

    $consulta = $enlace->prepare($sql);

    $consulta->bind_param(
        "sssssssssssssssi",
        $datos["nombre_negocioEdit"],
        $datos["DescripcionNEdit"],
        $datos["DireccionEdit"],
        $datos["TelefonoEdit"],
        $datos["CorreoNEdit"],
        $datos["SitioWebEdit"],
        $datos["FacebookEdit"],
        $datos["InstagramEdit"],
        $datos["TikTokEdit"],
        $datos["GoogleMapsEdit"],
        $datos["LatitudEdit"],
        $datos["LongitudEdit"],
        $datos["RelevanciaEdit"],
        $rutaIconoFinal,
        $datos["ID_Negocio"]
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
        $sql = "DELETE FROM Negocios WHERE ID_Negocio=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_usuario);

        return $consulta->execute();
    }
    public function ObtenerUsuario($ID_usuario)
    {
        $enlace = dbConectar();
        $sql = "SELECT * FROM negocios WHERE ID_Negocio=?";
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
    public function ObtenerNegocios($idUsuario = null)
{
    $this->asegurarCodigosCanjeFaltantes();
    $enlace = dbConectar();
    $sql = "SELECT * FROM negocios";
    if ($idUsuario !== null) {
        $sql .= " WHERE ID_Usuario = ?";
    }
    $consulta = $enlace->prepare($sql);
    if ($idUsuario !== null) {
        $consulta->bind_param("i", $idUsuario);
    }
    $consulta->execute();
    $result = $consulta->get_result();

    $negocios = [];
    while ($negocio = $result->fetch_assoc()) {
        $negocios[] = $negocio;
    }

    return $negocios;
}

public function ObtenerCoordenadas()
{
    $enlace = dbConectar();
    $sql = "SELECT ID_Negocio, nombre_negocio, Latitud, Longitud FROM negocios";  // Tabla correcta
    $consulta = $enlace->prepare($sql);
    $consulta->execute();
    $result = $consulta->get_result();

    $coordenadas = [];
    while ($negocio = $result->fetch_assoc()) {
        $coordenadas[] = $negocio;
    }

    return $coordenadas;
}
public function CambiarEstatus($ID_Negocio, $estatus)
{
    $enlace = dbConectar();
    $sql = "UPDATE negocios SET estado = ? WHERE ID_Negocio = ?";
    $consulta = $enlace->prepare($sql);
    $consulta->bind_param("ii", $estatus, $ID_Negocio);

    return $consulta->execute();
}
    public function PagarCuota($ID_Negocio)
    {
        $enlace = dbConectar();
        $sql = "UPDATE negocios SET fecha_ultimo_pago = CURDATE() WHERE ID_Negocio = ?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $ID_Negocio);


        return $consulta->execute();
    }

    public function ObtenerPorCategoria($idCategoria)
    {
        $enlace = dbConectar();
        $sql = "SELECT ID_Negocio, nombre_negocio FROM negocios WHERE ID_Categoria = ? ORDER BY nombre_negocio ASC";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("i", $idCategoria);
        $consulta->execute();
        $result = $consulta->get_result();

        $negocios = [];
        while ($row = $result->fetch_assoc()) {
            $negocios[] = $row;
        }
        $enlace->close();
        return $negocios;
    }

    public function MoverDeCategoria($idNegocio, $nuevaCategoriaId)
    {
        $enlace = dbConectar();
        $sql = "UPDATE negocios SET ID_Categoria = ? WHERE ID_Negocio = ?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("ii", $nuevaCategoriaId, $idNegocio);
        $resultado = $consulta->execute();
        $enlace->close();
        return $resultado;
    }
}
