<?php
    include_once("../config.php");

    if(isset($_POST["ope"]))
    {
        $ope=$_POST["ope"];

        if($ope=="LOGIN" && isset($_POST["nombre"],$_POST["contra"]))
        {
            include_once("../modelos/usuarios.php");
            $usu = new Usuarios();
            $correo=$_POST["nombre"];
            $pass=$_POST["contra"];

            $status = $usu->Login($correo,$pass);
            if($status[0])
            {
                $tipoUsuario = $status[1];
                $info = array(
                    "success"=>true,
                    "tipo" => $tipoUsuario,
                    "ruta"=> RUTA."/?pag=".$status[1]
                );
            }
            else
            {
                $info = array(
                    "success"=>false,
                    "msg"=> "El usuario o contraseña proporcionados son incorrectos--!"
                );
            }
            echo json_encode($info);
        }
        elseif($ope="LISTAUSUARIOS")
        {
            include_once("../modelos/usuarios.php");
            $usu = new Usuarios();

            $lista = $usu->ListarTODOS();

            $info = array(
                "success"=>true,
                "lista"=>$lista
            );

            echo json_encode($info);
        }
        else
        {
            echo "Sin Parametros Validos";
        }
    }
    else
    {
        echo "Sin Operación valida";
    }

?>