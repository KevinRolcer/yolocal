<?php
require_once "config.php";
$enlace = dbConectar();
$sql = "ALTER TABLE negocios ADD COLUMN codigo_canje VARCHAR(50) DEFAULT '1234' AFTER fecha_ultimo_pago";
if ($enlace->query($sql)) {
    echo "Column codigo_canje added successfully.\n";
} else {
    echo "Error adding column: " . $enlace->error . "\n";
}
$enlace->close();
