<?php
require_once __DIR__ . '/../config.php'; // Ajusta la ruta si es necesario

class Carrucel {
    private $conexion;

    public function __construct() {
        $this->conexion = dbConectar();
    }

    // --- ESTA ES LA FUNCIÓN QUE YA TENÍAS ---
    public function obtenerNegociosRelevantesPorCategoria($idCategoria) {
        $negocios = [];
        $sql = "SELECT ID_Negocio, nombre_negocio, DescripcionN, Rutaicono 
                FROM negocios 
                WHERE ID_Categoria = ? 
                AND Relevancia = '3' 
                AND estado = 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCategoria);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $negocios[] = $row;
        }
        $stmt->close();
        return $negocios;
    }

    // --- AQUÍ ESTÁ LA NUEVA FUNCIÓN IMPLEMENTADA ---
    /**
     * Obtiene los detalles de un negocio por su ID.
     *
     * @param int $idNegocio El ID del negocio a buscar.
     * @return array|null Los datos del negocio o null si no se encuentra.
     */
    public function obtenerNegocioPorId($idNegocio) {
        $sql = "SELECT n.*, c.Descripcion as nombre_categoria
                FROM negocios n
                LEFT JOIN categorias c ON n.ID_Categoria = c.ID_Categoria
                WHERE n.ID_Negocio = ? AND n.estado = 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idNegocio);
        $stmt->execute();
        $result = $stmt->get_result();
        $negocio = $result->fetch_assoc(); // Usamos fetch_assoc() porque solo es una fila
        $stmt->close();
        
        return $negocio;
    }
}
?>