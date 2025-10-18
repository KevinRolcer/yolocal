<?php
class ModeloEventos {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    
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
     */
    public function agregarEvento($titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $telefono, $nombreImagen, $idCategoria) { // Añadido $telefono
        $stmt = $this->conexion->prepare(
            // SQL incluye Telefono
            "INSERT INTO eventos (TituloE, DescripcionE, PrecioE, FechaE, HoraE, UbicacionE, Telefono, RutaImagenE, ID_Categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        // Bind Param incluye una 's' para $telefono
        $stmt->bind_param("ssssssssi", $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $telefono, $nombreImagen, $idCategoria);
        return $stmt->execute();
    }

    /**
     * Edita un evento existente (CON CAMPO TELEFONO).
     */
    public function editarEvento($id, $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $telefono, $nombreImagen, $idCategoria) { // Añadido $telefono
        // Si no se subió una nueva imagen, no actualizamos ese campo
        if ($nombreImagen) {
             // SQL incluye Telefono = ?
            $sql = "UPDATE eventos SET TituloE = ?, DescripcionE = ?, PrecioE = ?, FechaE = ?, HoraE = ?, UbicacionE = ?, Telefono = ?, RutaImagenE = ?, ID_Categoria = ? WHERE ID_Evento = ?";
            $stmt = $this->conexion->prepare($sql);
             // Bind Param incluye una 's' para $telefono
            $stmt->bind_param("ssssssssii", $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $telefono, $nombreImagen, $idCategoria, $id);
        } else {
             // SQL incluye Telefono = ?
            $sql = "UPDATE eventos SET TituloE = ?, DescripcionE = ?, PrecioE = ?, FechaE = ?, HoraE = ?, UbicacionE = ?, Telefono = ?, ID_Categoria = ? WHERE ID_Evento = ?";
            $stmt = $this->conexion->prepare($sql);
            // Bind Param incluye una 's' para $telefono
            $stmt->bind_param("sssssssii", $titulo, $descripcion, $precio, $fecha, $hora, $ubicacion, $telefono, $idCategoria, $id);
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