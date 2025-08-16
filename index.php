<?php 
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Si la sesión NO está iniciada, ir al login
    if (!isset($_SESSION["sistema"]) || $_SESSION["sistema"] !== "DragonGym") {
        include_once("vistas/login.php");
        exit(); 
    }

    
    $tipoUsuario = $_SESSION["tipo"];
    

    if(isset($_GET["pag"])) {
        $pag = $_GET["pag"];

      
        if($pag == "admin" && $tipoUsuario == "admin") {
            include_once("vistas/home.php");
        }       
        elseif($pag == "usuarios" && ($tipoUsuario == "admin"  )) {
            include_once("vistas/usuarios.php");
        }
        elseif($pag == "home" && ($tipoUsuario == "admin"  )) {
            include_once("vistas/home.php");
        }
        elseif($pag == "negocios" && ($tipoUsuario == "admin"  )) {
            include_once("vistas/negocios.php");
        }
        elseif($pag == "reportes" && ($tipoUsuario == "admin"  )) {
            include_once("vistas/reportes.php");
        }
        elseif($pag == "ventas" && ($tipoUsuario == "admin" || $tipoUsuario == "coach")) {
            include_once("vistas/ventas.php");
        }
        elseif($pag == "categorias" && ($tipoUsuario == "admin" )) {
            include_once("vistas/categorias.php");
        }
        
        else {
            include_once("vistas/acceso_denegado.php");
            exit();
        }
    } else {
        
        if ($tipoUsuario == "admin") {
            session_unset();
            session_destroy();
            include_once("vistas/login.php");
            exit();
        } elseif ($tipoUsuario == "gestor") {
            header("Location: index.php?pag=gestor");
        } elseif ($tipoUsuario == "general") {
            header("Location: index.php?pag=user");
        }
        elseif ($tipoUsuario == "coach") {
            header("Location: index.php?pag=coach");
        } else {
            session_unset();
            session_destroy();
            include_once("vistas/login.php");
            exit();
        }
    }
?>