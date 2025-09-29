<?php
class Trabajos
{
    public function ListarTrabajos()
    {
        $enlace = dbConectar();

        $sql = "SELECT 
                    t.ID_Trabajo,
                    t.Titulo,
                    t.Descripcion,
                    n.nombre_negocio,
                    n.Direccion,
                    n.Telefono,
                    n.CorreoN,
                    n.Rutaicono
                FROM trabajos AS t
                INNER JOIN negocios AS n ON t.ID_Negocio = n.ID_Negocio
                WHERE t.Estatus = 1";

        $consulta = $enlace->prepare($sql);
        $consulta->execute();
        $result = $consulta->get_result();

        $trabajos = [];
        while ($row = $result->fetch_assoc()) {
            $trabajos[] = $row;
        }

        $consulta->close();
        $enlace->close();
        return $trabajos;
    }
}
?>