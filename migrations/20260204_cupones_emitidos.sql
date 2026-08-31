-- Cupones individuales emitidos al descargar PDF; canje con codigo unico.
CREATE TABLE IF NOT EXISTS `cupones_emitidos` (
  `ID_Cupon` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ID_Promocion` int(11) NOT NULL,
  `codigo` varchar(48) NOT NULL,
  `canjeado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_emision` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_canje` datetime DEFAULT NULL,
  PRIMARY KEY (`ID_Cupon`),
  UNIQUE KEY `uk_cupones_emitidos_codigo` (`codigo`),
  KEY `idx_cupones_emitidos_promo` (`ID_Promocion`),
  KEY `idx_cupones_emitidos_promo_canjeado` (`ID_Promocion`, `canjeado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
