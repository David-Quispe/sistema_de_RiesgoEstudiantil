-- ============================================================
-- SMER - Autoria y responsabilidad de Base de Datos / Oracle
-- ============================================================
-- Responsable: Ronal Dante Gonzales Quispe
-- Correo: ronal.gonzales@tecsup.edu.pe
-- GitHub: DanteDGRTX
-- Rol: Base de datos / Oracle (esquema, triggers, vistas, indices)
-- ============================================================

COMMENT ON TABLE INSTITUCIONES IS 'Tabla raiz multi-institucion. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE USUARIOS IS 'Usuarios del sistema con 4 roles. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE ESTUDIANTES IS 'Estudiantes con email cifrado AES-256. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE PERIODOS IS 'Periodos academicos. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE ENTREVISTAS IS 'Entrevistas de seguimiento con calculo de riesgo. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE INDICADORES_ENTREVISTA IS 'Indicadores de riesgo por entrevista. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE CONFIGURACION_RIESGO IS 'Pesos y umbrales configurables. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE DERIVACIONES IS 'Derivaciones a Bienestar Estudiantil. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE ALERTAS IS 'Alertas automaticas generadas por el sistema. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE DOCUMENTOS_ADJUNTOS IS 'Documentos adjuntos a entrevistas. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE AUDITORIA IS 'Auditoria transversal de acciones sensibles. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';
COMMENT ON TABLE CARRERAS IS 'Catalogo de las 14 carreras TECSUP. Mantenida por Ronal Dante Gonzales Quispe (Base de Datos/Oracle).';

COMMIT;
