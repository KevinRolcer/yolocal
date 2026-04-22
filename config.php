<?php
    define("RUTA","");
    date_default_timezone_set('America/Mexico_City');
//s
    function dbConectar()
    {
        static $conexion;

        if(!isset($connection)) 
        {
            $config = parse_ini_file('config.ini'); 
            $conexion = mysqli_connect($config['servidor'],$config['usuario'],$config['pass'],$config['bbdd']);
            $query="set CHARSET 'utf8'";
			$conexion->query($query);
        }
        if($conexion === false) 
        {
            return mysqli_connect_error(); 
        }
        return $conexion;

    }

?>
