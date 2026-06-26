-- ============================================================
-- SMER — RNF06: Ampliación de columna EMAIL en ESTUDIANTES
-- ============================================================
-- El cast "encrypted" de Laravel genera texto cifrado en Base64
-- (AES-256-CBC) que es considerablemente más largo que el texto
-- original. Un correo de ~30 caracteres puede ocupar 300-500+
-- caracteres una vez cifrado. VARCHAR2(150) se queda corto.
--
-- Ejecutar este script en SQL Developer ANTES de correr el
-- comando de re-encriptación (php artisan app:encriptar-emails-estudiantes).
-- ============================================================

ALTER TABLE ESTUDIANTES MODIFY (email VARCHAR2(1000));

COMMIT;

-- Verificación:
-- SELECT column_name, data_type, data_length FROM user_tab_columns
-- WHERE table_name = 'ESTUDIANTES' AND column_name = 'EMAIL';
