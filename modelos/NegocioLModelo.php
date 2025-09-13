<?php
class NegocioLModelo {
    
  
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
                        n.nombre_negocio ASC";

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
                        n.ID_Categoria = ?  -- El '?' filtra de forma segura
                    ORDER BY 
                        n.nombre_negocio ASC";

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
       
        $sql = "SELECT dia_semana, hora_apertura, hora_cierre 
                FROM horarios
                WHERE ID_Negocio = ?";

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
}


?>