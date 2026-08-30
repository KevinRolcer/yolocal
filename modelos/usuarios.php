<?php
include_once(__DIR__ . "/../config.php");

class Usuarios
{
    public function Login($correo, $clave)
    {
        $enlace = dbConectar();
        iniciarSesionYoLocal();

        $sql = "SELECT * FROM usuarios WHERE Correo=?";
        $consulta = $enlace->prepare($sql);
        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $result = $consulta->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();

            if (password_verify($clave, $usuario["contra"])) {
                session_regenerate_id(true);
                $_SESSION["sistema"] = "YoLocal";
                $_SESSION["correo"] = $correo;
                $_SESSION["nombre"] = "{$usuario['Nombre']} {$usuario['ApellidoP']} {$usuario['ApellidoM']}";
                $_SESSION["tipo"] = "{$usuario['tipo_usuario']}";
                $_SESSION["ID_Usuario"] = "{$usuario['ID_Usuario']}";
                $_SESSION["LAST_ACTIVITY"] = time();
                session_write_close();

                return array(true, $usuario["tipo_usuario"]);
            }

            $_SESSION = [];
            session_destroy();

            return array(false, "ContraseÃ±a incorrecta");
        }

        $_SESSION = [];
        session_destroy();

        return array(false, "Usuario no encontrado");
    }
}
?>
