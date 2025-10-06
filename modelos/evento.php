<?php


class EventoModelo { 

    public static function obtenerTodos($conexion) {
        try {
   
            $sql = "SELECT 
                        e.ID_Evento,
                        e.TituloE,
                        e.DescripcionE,
                        e.PrecioE,
                        e.FechaE,
                        e.HoraE,
                        e.UbicacionE,
                        e.RutaImagenE,
                        c.Descripcion AS CategoriaNombre 
                    FROM 
                        eventos AS e
                    LEFT JOIN 
                        categorias AS c ON e.ID_Categoria = c.ID_Categoria
                    ORDER BY 
                        e.FechaE ASC, e.HoraE ASC"; 

            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $eventos = $resultado->fetch_all(MYSQLI_ASSOC); 
            
            $stmt->close();
            return $eventos;

        } catch (Exception $e) {
            die("Error al consultar los eventos: " . $e->getMessage());
        }
    }
}
?>