<?php
require 'config.php';
$db = dbConectar();
$res = $db->query('SHOW TABLES');
while ($row = $res->fetch_row()) {
    echo $row[0] . "\n";
}
