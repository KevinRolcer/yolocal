<?php
class NegocioLModelo {
    
    // TUS FUNCIONES EXISTENTES (SIN CAMBIOS)
 
    public static function obtenerTodos($conexion) {
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        try {
            $sql = "SELECT 
                        n.ID_Negocio,
                        n.nombre_negocio,
                        n.DescripcionN,
                        n.Direccion,
                        n.Rutaicono,
                        c.Descripcion AS nombre_categoria 
                    FROM 
                        negocios n
                    LEFT JOIN 
                        categorias c ON n.ID_Categoria = c.ID_Categoria
                    ORDER BY 
                    n.Relevancia DESC, n.nombre_negocio ASC";

            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $negocios = $resultado->fetch_all(MYSQLI_ASSOC);
            
            $stmt->close();
            
            return $negocios;

        } catch (Exception $e) {
            die("Error al consultar los negocios: " . $e->getMessage());
        }
    }

    public static function obtenerPorCategoria($conexion, $id_categoria) {
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        try {
            $sql = "SELECT 
                        n.ID_Negocio,
                        n.nombre_negocio,
                        n.DescripcionN,
                        n.Direccion,
                        n.Rutaicono,
                        c.Descripcion AS nombre_categoria 
                    FROM 
                        negocios n
                    LEFT JOIN 
                        categorias c ON n.ID_Categoria = c.ID_Categoria
                    WHERE 
                        n.ID_Categoria = ? 
                    ORDER BY 
                        n.nombre_negocio DESC";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_categoria); 
            $stmt->execute();
            $resultado = $stmt->get_result();
            $negocios = $resultado->fetch_all(MYSQLI_ASSOC);
            
            $stmt->close();
            
            return $negocios;

        } catch (Exception $e) {
            die("Error al consultar los negocios por categoría: " . $e->getMessage());
        }
    }

    public static function obtenerPorId($conexion, $id_negocio) {
        try {
            $sql = "SELECT 
                        n.*, c.Descripcion AS nombre_categoria
                    FROM 
                        negocios n
                    LEFT JOIN 
                        categorias c ON n.ID_Categoria = c.ID_Categoria
                    WHERE 
                        n.ID_Negocio = ?";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_negocio);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $negocio = $resultado->fetch_assoc(); 
            
            $stmt->close();
            return $negocio;

        } catch (Exception $e) {
            die("Error al consultar el negocio: " . $e->getMessage());
        }
    }

    public static function obtenerHorariosPorIdNegocio($conexion, $id_negocio) {
        try {
            $sql = "SELECT h.*
                    FROM horarios h
                    INNER JOIN (
                       SELECT dia_semana, MAX(ID_Horario) AS max_id
                       FROM horarios
                       WHERE ID_Negocio = ?
                       GROUP BY dia_semana
                    ) AS latest
                    ON h.ID_Horario = latest.max_id
                    ORDER BY FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')";

            $stmt = $conexion->prepare($sql);

            if ($stmt === false) {
                die("Error al preparar la consulta de horarios: " . $conexion->error);
            }
            
            $stmt->bind_param("i", $id_negocio);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $horarios = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $horarios;
            
        } catch (Exception $e) {
            die("Error al consultar horarios: " . $e->getMessage());
        }
    }

    public static function obtenerImagenesPorIdNegocio($conexion, $id_negocio) {
        try {
            $sql = "SELECT ruta_imagen FROM negocio_imagenes WHERE ID_Negocio = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_negocio);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $imagenes = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $imagenes;
        } catch (Exception $e) {
            die("Error al consultar las imágenes del negocio: " . $e->getMessage());
        }
    }

    public static function buscarPorNombre($conexion, $terminoBusqueda) {
        try {
            $terminoConComodines = "%" . $terminoBusqueda . "%";

            $sql = "SELECT 
                        n.ID_Negocio,
                        n.nombre_negocio,
                        n.DescripcionN,
                        n.Direccion,
                        n.Rutaicono,
                        c.Descripcion AS nombre_categoria 
                    FROM 
                        negocios n
                    LEFT JOIN 
                        categorias c ON n.ID_Categoria = c.ID_Categoria
                    WHERE 
                        n.nombre_negocio LIKE ?  
                    ORDER BY 
                        n.nombre_negocio ASC";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $terminoConComodines);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $negocios = $resultado->fetch_all(MYSQLI_ASSOC);
            
            $stmt->close();
            return $negocios;

        } catch (Exception $e) {
            die("Error al buscar los negocios: " . $e->getMessage());
        }
    }
    
    // ===============================================================
    // ========= FUNCIONES NUEVAS PARA LA PAGINACIÓN =========
    // ===============================================================

    /**
     * Obtiene una lista de negocios con paginación y filtros opcionales.
     */
    public static function obtenerTodosPaginados($conexion, $inicio, $cantidad, $id_categoria = null, $termino = null) {
        $sql = "SELECT 
                    n.ID_Negocio, n.nombre_negocio, n.DescripcionN, n.Direccion, n.Rutaicono,
                    c.Descripcion AS nombre_categoria 
                FROM negocios n
                LEFT JOIN categorias c ON n.ID_Categoria = c.ID_Categoria";
        
        $params = [];
        $types = '';
        $where = [];

        if ($id_categoria !== null) {
            $where[] = "n.ID_Categoria = ?";
            $types .= 'i';
            $params[] = $id_categoria;
        }

        if ($termino !== null) {
            $where[] = "n.nombre_negocio LIKE ?";
            $types .= 's';
            $params[] = '%' . $termino . '%';
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY n.Relevancia DESC, n.nombre_negocio ASC LIMIT ?, ?";
        $types .= 'ii';
        $params[] = $inicio;
        $params[] = $cantidad;
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $negocios = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $negocios;
    }

    /**
     * Cuenta el total de negocios, aplicando filtros opcionales.
     */
    public static function contarTodos($conexion, $id_categoria = null, $termino = null) {
        $sql = "SELECT COUNT(n.ID_Negocio) FROM negocios n";
        
        $params = [];
        $types = '';
        $where = [];

        if ($id_categoria !== null) {
            $where[] = "n.ID_Categoria = ?";
            $types .= 'i';
            $params[] = $id_categoria;
        }

        if ($termino !== null) {
            $where[] = "n.nombre_negocio LIKE ?";
            $types .= 's';
            $params[] = '%' . $termino . '%';
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $conexion->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        $total = $resultado->fetch_row()[0];
        $stmt->close();
        return (int)$total;
    }
}
?>