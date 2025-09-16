<?php
class CategoriaModelo {
    
    
    public static function obtenerTodas($conexion) {
        try {
         
            $sql = "SELECT ID_Categoria, Descripcion FROM categorias ORDER BY Descripcion ASC";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $categorias = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            return $categorias;

        } catch (Exception $e) {
            die("Error al consultar las categorías: " . $e->getMessage());
        }
    }
}
?>