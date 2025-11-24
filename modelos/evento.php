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
    public static function obtenerPorId($db, $id) {

        $stmt = $db->prepare("SELECT TituloE, DescripcionE, RutaImagenE FROM eventos WHERE ID_Evento = ?");
        
        if ($stmt === false) {

            error_log('Error en la preparación de la consulta: ' . $db->error);
            return null;
        }

        $stmt->bind_param("i", $id);

        $stmt->execute();
        $resultado = $stmt->get_result();
        $evento = $resultado->fetch_assoc();
        $stmt->close();
        return $evento; 
    }

}
?>