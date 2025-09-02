<?php
class Horarios{
    public function Agregar($datos)
    {
        $enlace = dbConectar();

        $sql = "INSERT INTO Horarios (ID_Negocio, dia_semana, hora_apertura, hora_cierre) VALUES (?, ?, ?, ?)";
        $consulta = $enlace->prepare($sql);
        if (!$consulta) {
            throw new Exception("Error en la preparación de la consulta: " . $enlace->error);
        }

        $consulta->bind_param(
            "isss",
            $datos["ID_Negocio"],
            $datos["dia_semana"],
            $datos["hora_apertura"],
            $datos["hora_cierre"]
        );

        $status = $consulta->execute();
        if (!$status) {
            throw new Exception("Error en la ejecución de la consulta: " . $consulta->error);
        }

        // Cerrar conexiones
        $consulta->close();
        $enlace->close();

        return $status;
    }
}

?>