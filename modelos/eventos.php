<?php
class ModeloEventos {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene todas las categorías de la base de datos.
     */
    public function listarCategorias() {
        $sql = "SELECT ID_Categoria, Descripcion FROM categorias ORDER BY Descripcion ASC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene todos los eventos, uniendo la información de la categoría.
     */
    public function listarEventos() {
        $sql = "SELECT e.*, c.Descripcion AS NombreCategoria 
                FROM eventos e
                JOIN categorias c ON e.ID_Categoria = c.ID_Categoria
                ORDER BY e.FechaE DESC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene un solo evento por su ID.
     */
    public function obtenerEventoPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM eventos WHERE ID_Evento = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
    
    /**
     * Agrega un nuevo evento a la base de datos.
     */
    public function agregarEvento($titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $nombreImagen, $idCategoria) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO eventos (TituloE, DescripcionE, PrecioE, FechaE, HoraE, UbicacionE, RutaImagenE, ID_Categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssssi", $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $nombreImagen, $idCategoria);
        return $stmt->execute();
    }

    /**
     * Edita un evento existente.
     */
    public function editarEvento($id, $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $nombreImagen, $idCategoria) {
        // Si no se subió una nueva imagen, no actualizamos ese campo
        if ($nombreImagen) {
            $sql = "UPDATE eventos SET TituloE = ?, DescripcionE = ?, PrecioE = ?, FechaE = ?, HoraE = ?, UbicacionE = ?, RutaImagenE = ?, ID_Categoria = ? WHERE ID_Evento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("sssssssii", $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $nombreImagen, $idCategoria, $id);
        } else {
            $sql = "UPDATE eventos SET TituloE = ?, DescripcionE = ?, PrecioE = ?, FechaE = ?, HoraE = ?, UbicacionE = ?, ID_Categoria = ? WHERE ID_Evento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ssssssii", $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $idCategoria, $id);
        }
        return $stmt->execute();
    }

    /**
     * Elimina un evento de la base de datos.
     */
    public function eliminarEvento($id) {
        $stmt = $this->conexion->prepare("DELETE FROM eventos WHERE ID_Evento = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>