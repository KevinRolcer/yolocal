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
}
?>