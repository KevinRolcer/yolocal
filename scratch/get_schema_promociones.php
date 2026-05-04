<?php
require_once "config.php";
$enlace = dbConectar();
$res = $enlace->query("DESCRIBE promociones");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
$enlace->close();
