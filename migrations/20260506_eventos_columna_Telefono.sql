-- Ejecutar UNA vez en la base marcoto1_yolocal (o la que uses).
-- Equivale al campo opcional que usa admin: modelos/eventos.php y controladores/controladorEventos.php
--
-- Si MySQL dice que la columna ya existe, no hace falta volver a correr este ALTER.

ALTER TABLE `eventos`
ADD COLUMN `Telefono` VARCHAR(15) NULL DEFAULT NULL AFTER `UbicacionE`;
