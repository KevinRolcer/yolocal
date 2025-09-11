<?php

// models/MiembroModel.php
class MiembroModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function obtenerNegocios()
    {
        $sql = "SELECT `negocios`.*, `usuarios`.*, `categorias`.*
FROM `negocios` 
	LEFT JOIN `usuarios` ON `negocios`.`ID_Usuario` = `usuarios`.`ID_Usuario` 
	LEFT JOIN `categorias` ON `negocios`.`ID_Categoria` = `categorias`.`ID_Categoria` WHERE 1=1";
        $resultado = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }
    public function obtenerCupones()
    {
        $sql = "SELECT p.ID_Promocion, p.titulo, p.descripcion, p.cantidad, p.fecha_fin, p.Estatus, p.Canjeados,p.Descargados, 
                   n.nombre_negocio AS nombre_negocio
            FROM promociones p
            INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
            WHERE 1=1;";
        $resultado = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }
    public function obtenerProductos()
    {
        $sql = "SELECT p.ID_Producto, p.img, p.Descripcion, p.Precio, p.Disponible, t.Descripcion AS TipoProducto 
                FROM productos p
                LEFT JOIN tipoi t ON p.ID_TipoProducto = t.ID_TipoProducto
                ORDER BY p.Disponible DESC";
        $resultado = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }
}