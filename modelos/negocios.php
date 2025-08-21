<?php
class Negocios
{


    public function ListarTODOS($pagina = 1, $registrosPorPagina = 10, $filtros = [])
    {
        $enlace = dbConectar();
        $offset = ($pagina - 1) * $registrosPorPagina;

        $sql = "SELECT `negocios`.*, `usuarios`.*, `categorias`.*
FROM `negocios` 
	LEFT JOIN `usuarios` ON `negocios`.`ID_Usuario` = `usuarios`.`ID_Usuario` 
	LEFT JOIN `categorias` ON `negocios`.`ID_Categoria` = `categorias`.`ID_Categoria` WHERE 1=1";
        $values = [];
        $tipos = "";

        // Filtros dinámicos
        if (!empty($filtros['Correo'])) {
        $sql .= " AND Nombre LIKE ?";
        $values[] = "%" . $filtros['Correo'] . "%";
        $tipos .= "s";
    }

        if (!empty($filtros['Nombre'])) {
            $sql .= " AND nombre_negocio LIKE ?";
            $values[] = "%" . $filtros['Nombre'] . "%";
            $tipos .= "s";
        }
        

       

        

        // Orden y paginación
        $sql .= " ORDER BY ID_Negocio DESC LIMIT ?, ?";
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
    public function Agregar($datos)
    {
        $enlace = dbConectar();

        $sql = "INSERT INTO negocios (ID_Usuario, nombre_negocio, ID_Categoria) VALUES (?, ?, ?)";
        $consulta = $enlace->prepare($sql);


        $consulta->bind_param(
            "isi",
            $datos["ID_Usuario"],
            $datos["nombre_negocio"],
            $datos["ID_Categoria"]
           
        );

        return $consulta->execute();
    }
    public function Editar($datos)
    {
        $enlace = dbConectar();

        
            $sql = "UPDATE negocios SET nombre_negocio=? WHERE ID_Negocio=?";
            $consulta = $enlace->prepare($sql);
            $consulta->bind_param(
                "si",
                $datos["nombre_negocio"],
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
        $sql = "SELECT ID_Negocio, nombre_negocio FROM negocios WHERE ID_Negocio=?";
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
    public function ObtenerNegocios()
{
    $enlace = dbConectar();
    $sql = "SELECT ID_Negocio, nombre_negocio FROM negocios";  // Tabla correcta
    $consulta = $enlace->prepare($sql);
    $consulta->execute();
    $result = $consulta->get_result();

    $negocios = [];
    while ($negocio = $result->fetch_assoc()) {
        $negocios[] = $negocio;
    }

    return $negocios;
}
}
