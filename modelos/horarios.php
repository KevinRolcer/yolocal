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
   public function ListarHorarios($idNegocio) {
    $enlace = dbConectar();

    $sql = "SELECT h.*
            FROM horarios h
            INNER JOIN (
                SELECT dia_semana, MAX(ID_Horario) AS max_id
                FROM horarios
                WHERE ID_Negocio = ?
                GROUP BY dia_semana
            ) AS latest
            ON h.ID_Horario = latest.max_id
            ORDER BY FIELD(h.dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')";

    $consulta = $enlace->prepare($sql);
    if (!$consulta) {
        throw new Exception("Error en la preparación de la consulta: " . $enlace->error);
    }

    $consulta->bind_param("i", $idNegocio);
    $consulta->execute();
    $resultado = $consulta->get_result();

    $horarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $horarios[] = [
            "dia_semana"    => $fila['dia_semana'],
            "hora_apertura" => $fila['hora_apertura'],
            "hora_cierre"   => $fila['hora_cierre']
        ];
    }

    $consulta->close();
    $enlace->close();

    return $horarios;
}

}

?>