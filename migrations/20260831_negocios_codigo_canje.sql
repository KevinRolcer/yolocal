-- Código utilizado para validar el canje de cupones por negocio.
-- Ejecutar una sola vez en producción si SHOW COLUMNS no devuelve codigo_canje.

ALTER TABLE `negocios`
    ADD COLUMN `codigo_canje` VARCHAR(50) NULL DEFAULT NULL AFTER `fecha_ultimo_pago`;

-- Los códigos vacíos se generan automáticamente desde la aplicación cuando se usan.
