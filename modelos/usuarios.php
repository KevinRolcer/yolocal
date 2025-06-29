<?php
class Usuarios
{
    public function Login($correo, $clave)
{
    $enlace = dbConectar();
    session_start();

    $sql = "SELECT Nombre, ApellidoP, ApellidoM, usutip, Contra, ID_Usuario FROM usuarios WHERE NombreUsu=?";
    $consulta = $enlace->prepare($sql);
    $consulta->bind_param("s", $correo);
    $consulta->execute();
    $result = $consulta->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

  

        if (password_verify($clave, $usuario["Contra"])) {
            $_SESSION["sistema"] = "DragonGym";
            $_SESSION["correo"] = $correo;
            $_SESSION["nombre"] = "{$usuario['Nombre']} {$usuario['ApellidoP']} {$usuario['ApellidoM']}";
            $_SESSION["tipo"] = "{$usuario['usutip']}";
            $_SESSION["ID_Usuario"] = "{$usuario['ID_Usuario']}";
            $_SESSION["LAST_ACTIVITY"] = time();
            return array(true, $usuario['usutip']);
        } else {
            session_unset();
            session_destroy();
            return array(false, "Contraseña incorrecta");
        }
    } else {
        session_unset();
        session_destroy();
        return array(false, "Usuario no encontrado");
    }

    $enlace->close();
}
    
   
}

?>