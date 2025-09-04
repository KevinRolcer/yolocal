<?php
class Imagenes {

    public function SubirImagenes($idNegocio, $files) {
        $enlace = dbConectar();

        try {
            $sql = "SELECT nombre_negocio FROM negocios WHERE ID_Negocio = ?";
            $consulta = $enlace->prepare($sql);
            if (!$consulta) {
                throw new Exception("Error en la preparación de la consulta: " . $enlace->error);
            }
            $consulta->bind_param("i", $idNegocio);
            $consulta->execute();
            $resultado = $consulta->get_result();
            $negocio = $resultado->fetch_assoc();
            $consulta->close();

            if (!$negocio) {
                throw new Exception("Negocio no encontrado");
            }

            // Crear carpeta del negocio
            $nombreCarpeta = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($negocio['nombre_negocio']));
            $carpeta = "../assets/uploads/" . $nombreCarpeta;

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $rutasGuardadas = [];

            for ($i = 1; $i <= 4; $i++) {
                $campo = "imagen$i";
                if (isset($files[$campo]) && $files[$campo]['error'] === 0) {
                    $ext = pathinfo($files[$campo]['name'], PATHINFO_EXTENSION);
                    $nombreArchivo = uniqid("img_", true) . "." . $ext;
                    $ruta = "$carpeta/$nombreArchivo";

                    if (move_uploaded_file($files[$campo]['tmp_name'], $ruta)) {
                        $sql = "INSERT INTO negocio_imagenes (ID_Negocio, ruta_imagen) VALUES (?, ?)";
                        $consulta = $enlace->prepare($sql);
                        if (!$consulta) {
                            throw new Exception("Error en la preparación de la consulta: " . $enlace->error);
                        }
                        $consulta->bind_param("is", $idNegocio, $ruta);

                        $status = $consulta->execute();
                        if (!$status) {
                            throw new Exception("Error en la ejecución de la consulta: " . $consulta->error);
                        }

                        $consulta->close();
                        $rutasGuardadas[] = $ruta;
                    }
                }
            }

            $enlace->close();
            return ["success" => true, "rutas" => $rutasGuardadas];

        } catch (Exception $e) {
            $enlace->close();
            return ["success" => false, "msg" => $e->getMessage()];
        }
    }

    public function ListarImagenes($idNegocio) {
        $enlace = dbConectar();

        $sql = "SELECT ruta_imagen FROM negocio_imagenes WHERE ID_Negocio = ?";
        $consulta = $enlace->prepare($sql);
        if (!$consulta) {
            throw new Exception("Error en la preparación de la consulta: " . $enlace->error);
        }

        $consulta->bind_param("i", $idNegocio);
        $consulta->execute();
        $resultado = $consulta->get_result();

        $imagenes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $imagenes[] = $fila['ruta_imagen'];
        }

        $consulta->close();
        $enlace->close();

        return $imagenes;
    }
}
?>
