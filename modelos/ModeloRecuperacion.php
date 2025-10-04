<?php
require_once "../config.php";

class Recuperacion {
    private $conexion;

    public function __construct() {
        $this->conexion = dbConectar();
    }

    public function obtenerUsuarioPorCorreo($correo) {
        $sql = "SELECT Nombre, ApellidoP, ApellidoM, contra FROM usuarios WHERE Correo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
    public function actualizarContraseña($correo, $nuevaContraseña, $cPrueba) {
        $sql = "UPDATE usuarios SET contra = ?, cPrueba = ? WHERE Correo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sss", $nuevaContraseña, $cPrueba, $correo);
        return $stmt->execute();
    }
}
?>