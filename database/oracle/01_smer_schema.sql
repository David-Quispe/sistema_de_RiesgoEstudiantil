
CREATE SEQUENCE SEQ_INSTITUCIONES START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_USUARIOS      START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_ESTUDIANTES   START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_PERIODOS      START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_ENTREVISTAS   START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_INDICADORES   START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_CONFIG_RIESGO START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_DERIVACIONES  START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_ALERTAS       START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_DOCUMENTOS    START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE SEQ_AUDITORIA     START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

CREATE TABLE INSTITUCIONES (
    id          NUMBER PRIMARY KEY,
    nombre      VARCHAR2(150) NOT NULL,
    codigo      VARCHAR2(20)  NOT NULL UNIQUE,
    activo      NUMBER(1)     DEFAULT 1 NOT NULL CHECK (activo IN (0,1)),
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE OR REPLACE TRIGGER trg_instituciones_upd
BEFORE UPDATE ON INSTITUCIONES
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE USUARIOS (
    id              NUMBER PRIMARY KEY,
    institucion_id  NUMBER        NOT NULL,
    nombre          VARCHAR2(100) NOT NULL,
    apellidos       VARCHAR2(100) NOT NULL,
    email           VARCHAR2(150) NOT NULL UNIQUE,
    password        VARCHAR2(255) NOT NULL,
    rol             VARCHAR2(20)  NOT NULL CHECK (rol IN ('consejero','coordinador','bienestar','admin')),
    activo          NUMBER(1)     DEFAULT 1 NOT NULL CHECK (activo IN (0,1)),
    ultimo_acceso   TIMESTAMP,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_inst FOREIGN KEY (institucion_id) REFERENCES INSTITUCIONES(id)
);

CREATE INDEX idx_usuarios_email ON USUARIOS(email);
CREATE INDEX idx_usuarios_rol   ON USUARIOS(rol);

CREATE OR REPLACE TRIGGER trg_usuarios_upd
BEFORE UPDATE ON USUARIOS
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE PERIODOS (
    id              NUMBER PRIMARY KEY,
    institucion_id  NUMBER       NOT NULL,
    nombre          VARCHAR2(50) NOT NULL,
    fecha_inicio    DATE         NOT NULL,
    fecha_fin       DATE         NOT NULL,
    activo          NUMBER(1)    DEFAULT 1 NOT NULL CHECK (activo IN (0,1)),
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_periodo_inst FOREIGN KEY (institucion_id) REFERENCES INSTITUCIONES(id)
);

CREATE OR REPLACE TRIGGER trg_periodos_upd
BEFORE UPDATE ON PERIODOS
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE ESTUDIANTES (
    id              NUMBER PRIMARY KEY,
    institucion_id  NUMBER        NOT NULL,
    codigo          VARCHAR2(20)  NOT NULL UNIQUE,
    nombre          VARCHAR2(100) NOT NULL,
    apellidos       VARCHAR2(100) NOT NULL,
    email           VARCHAR2(150),
    carrera         VARCHAR2(100) NOT NULL,
    ciclo           NUMBER(2)     NOT NULL,
    activo          NUMBER(1)     DEFAULT 1 NOT NULL CHECK (activo IN (0,1)),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_estudiante_inst FOREIGN KEY (institucion_id) REFERENCES INSTITUCIONES(id)
);

CREATE INDEX idx_estudiantes_carrera ON ESTUDIANTES(carrera);
CREATE INDEX idx_estudiantes_ciclo   ON ESTUDIANTES(ciclo);

CREATE OR REPLACE TRIGGER trg_estudiantes_upd
BEFORE UPDATE ON ESTUDIANTES
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE ENTREVISTAS (
    id                  NUMBER PRIMARY KEY,
    estudiante_id       NUMBER        NOT NULL,
    consejero_id        NUMBER        NOT NULL,
    periodo_id          NUMBER        NOT NULL,
    fecha_entrevista    DATE          NOT NULL,
    observaciones       CLOB,
    puntaje_total       NUMBER(5,2)   DEFAULT 0,
    nivel_riesgo        VARCHAR2(10)  DEFAULT 'BAJO' CHECK (nivel_riesgo IN ('BAJO','MEDIO','ALTO')),
    created_at          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_entrevista_est  FOREIGN KEY (estudiante_id) REFERENCES ESTUDIANTES(id),
    CONSTRAINT fk_entrevista_cons FOREIGN KEY (consejero_id)  REFERENCES USUARIOS(id),
    CONSTRAINT fk_entrevista_per  FOREIGN KEY (periodo_id)    REFERENCES PERIODOS(id)
);

CREATE INDEX idx_entrevistas_est    ON ENTREVISTAS(estudiante_id);
CREATE INDEX idx_entrevistas_riesgo ON ENTREVISTAS(nivel_riesgo);

CREATE OR REPLACE TRIGGER trg_entrevistas_upd
BEFORE UPDATE ON ENTREVISTAS
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE INDICADORES_ENTREVISTA (
    id              NUMBER PRIMARY KEY,
    entrevista_id   NUMBER        NOT NULL,
    nombre          VARCHAR2(80)  NOT NULL,
    puntaje         NUMBER(4,2)   NOT NULL CHECK (puntaje BETWEEN 0 AND 10),
    peso            NUMBER(4,2)   NOT NULL CHECK (peso BETWEEN 0 AND 1),
    observacion     VARCHAR2(500),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_indicador_entrev FOREIGN KEY (entrevista_id) REFERENCES ENTREVISTAS(id) ON DELETE CASCADE
);

CREATE INDEX idx_indicadores_entrev ON INDICADORES_ENTREVISTA(entrevista_id);

CREATE OR REPLACE TRIGGER trg_indicadores_upd
BEFORE UPDATE ON INDICADORES_ENTREVISTA
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE CONFIGURACION_RIESGO (
    id              NUMBER PRIMARY KEY,
    institucion_id  NUMBER        NOT NULL,
    indicador       VARCHAR2(80)  NOT NULL,
    peso            NUMBER(4,2)   NOT NULL CHECK (peso BETWEEN 0 AND 1),
    umbral_medio    NUMBER(4,2)   NOT NULL,
    umbral_alto     NUMBER(4,2)   NOT NULL,
    activo          NUMBER(1)     DEFAULT 1 NOT NULL CHECK (activo IN (0,1)),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_config_inst FOREIGN KEY (institucion_id) REFERENCES INSTITUCIONES(id)
);

CREATE OR REPLACE TRIGGER trg_config_upd
BEFORE UPDATE ON CONFIGURACION_RIESGO
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE DERIVACIONES (
    id              NUMBER PRIMARY KEY,
    entrevista_id   NUMBER        NOT NULL,
    consejero_id    NUMBER        NOT NULL,
    bienestar_id    NUMBER,
    motivo          CLOB          NOT NULL,
    prioridad       VARCHAR2(10)  DEFAULT 'NORMAL' CHECK (prioridad IN ('BAJA','NORMAL','ALTA','URGENTE')),
    estado          VARCHAR2(20)  DEFAULT 'PENDIENTE' CHECK (estado IN ('PENDIENTE','EN_ATENCION','RESUELTA','CERRADA')),
    resolucion      CLOB,
    fecha_cierre    DATE,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_deriv_entrev   FOREIGN KEY (entrevista_id) REFERENCES ENTREVISTAS(id),
    CONSTRAINT fk_deriv_consejero FOREIGN KEY (consejero_id) REFERENCES USUARIOS(id),
    CONSTRAINT fk_deriv_bienestar FOREIGN KEY (bienestar_id) REFERENCES USUARIOS(id)
);

CREATE INDEX idx_derivaciones_estado ON DERIVACIONES(estado);

CREATE OR REPLACE TRIGGER trg_derivaciones_upd
BEFORE UPDATE ON DERIVACIONES
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE ALERTAS (
    id              NUMBER PRIMARY KEY,
    estudiante_id   NUMBER        NOT NULL,
    usuario_id      NUMBER        NOT NULL,
    tipo            VARCHAR2(30)  NOT NULL CHECK (tipo IN ('RIESGO_ALTO','DETERIORO_PROGRESIVO','DERIVACION','SISTEMA')),
    canal           VARCHAR2(20)  DEFAULT 'SISTEMA' CHECK (canal IN ('EMAIL','SISTEMA','AMBOS')),
    mensaje         VARCHAR2(500) NOT NULL,
    leida           NUMBER(1)     DEFAULT 0 CHECK (leida IN (0,1)),
    fecha_lectura   TIMESTAMP,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alerta_est  FOREIGN KEY (estudiante_id) REFERENCES ESTUDIANTES(id),
    CONSTRAINT fk_alerta_user FOREIGN KEY (usuario_id)    REFERENCES USUARIOS(id)
);

CREATE INDEX idx_alertas_leida ON ALERTAS(leida);

CREATE OR REPLACE TRIGGER trg_alertas_upd
BEFORE UPDATE ON ALERTAS
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE DOCUMENTOS_ADJUNTOS (
    id              NUMBER PRIMARY KEY,
    entrevista_id   NUMBER        NOT NULL,
    nombre_archivo  VARCHAR2(255) NOT NULL,
    ruta            VARCHAR2(500) NOT NULL,
    tipo_mime       VARCHAR2(100),
    tamanio_kb      NUMBER(10),
    subido_por      NUMBER        NOT NULL,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_doc_entrev FOREIGN KEY (entrevista_id) REFERENCES ENTREVISTAS(id) ON DELETE CASCADE,
    CONSTRAINT fk_doc_user   FOREIGN KEY (subido_por)    REFERENCES USUARIOS(id)
);

CREATE OR REPLACE TRIGGER trg_documentos_upd
BEFORE UPDATE ON DOCUMENTOS_ADJUNTOS
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

CREATE TABLE AUDITORIA (
    id              NUMBER PRIMARY KEY,
    usuario_id      NUMBER        NOT NULL,
    accion          VARCHAR2(50)  NOT NULL,
    tabla_afectada  VARCHAR2(50)  NOT NULL,
    registro_id     NUMBER,
    detalle         CLOB,
    ip_address      VARCHAR2(45),
    user_agent      VARCHAR2(500),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_user FOREIGN KEY (usuario_id) REFERENCES USUARIOS(id)
);

CREATE INDEX idx_auditoria_usuario ON AUDITORIA(usuario_id);
CREATE INDEX idx_auditoria_tabla   ON AUDITORIA(tabla_afectada);


-- Vista 1: Resumen de riesgo por estudiante (última entrevista)
CREATE OR REPLACE VIEW V_RIESGO_ESTUDIANTES AS
SELECT
    e.id           AS estudiante_id,
    e.codigo,
    e.nombre || ' ' || e.apellidos AS nombre_completo,
    e.carrera,
    e.ciclo,
    ent.id         AS entrevista_id,
    ent.fecha_entrevista,
    ent.puntaje_total,
    ent.nivel_riesgo,
    p.nombre       AS periodo
FROM ESTUDIANTES e
JOIN ENTREVISTAS ent ON ent.id = (
    SELECT id FROM ENTREVISTAS
    WHERE estudiante_id = e.id
    ORDER BY fecha_entrevista DESC
    FETCH FIRST 1 ROWS ONLY
)
JOIN PERIODOS p ON p.id = ent.periodo_id;

-- Vista 2: Derivaciones pendientes con detalle
CREATE OR REPLACE VIEW V_DERIVACIONES_PENDIENTES AS
SELECT
    d.id              AS derivacion_id,
    d.prioridad,
    d.estado,
    d.created_at      AS fecha_derivacion,
    e.codigo          AS codigo_estudiante,
    e.nombre || ' ' || e.apellidos AS estudiante,
    e.carrera,
    u.nombre || ' ' || u.apellidos AS consejero,
    ent.nivel_riesgo
FROM DERIVACIONES d
JOIN ENTREVISTAS ent ON ent.id = d.entrevista_id
JOIN ESTUDIANTES e   ON e.id   = ent.estudiante_id
JOIN USUARIOS u      ON u.id   = d.consejero_id
WHERE d.estado IN ('PENDIENTE','EN_ATENCION')
ORDER BY
    CASE d.prioridad WHEN 'URGENTE' THEN 1 WHEN 'ALTA' THEN 2 WHEN 'NORMAL' THEN 3 ELSE 4 END,
    d.created_at ASC;


-- Institución TECSUP
INSERT INTO INSTITUCIONES (id, nombre, codigo)
VALUES (SEQ_INSTITUCIONES.NEXTVAL, 'TECSUP', 'TECSUP');

-- Periodos 2025
INSERT INTO PERIODOS (id, institucion_id, nombre, fecha_inicio, fecha_fin)
VALUES (SEQ_PERIODOS.NEXTVAL, 1, '2025-I', DATE '2025-03-01', DATE '2025-07-31');

INSERT INTO PERIODOS (id, institucion_id, nombre, fecha_inicio, fecha_fin)
VALUES (SEQ_PERIODOS.NEXTVAL, 1, '2025-II', DATE '2025-08-01', DATE '2025-12-31');

-- 6 Indicadores de riesgo con pesos por defecto (suma = 1.0)
INSERT INTO CONFIGURACION_RIESGO (id, institucion_id, indicador, peso, umbral_medio, umbral_alto)
VALUES (SEQ_CONFIG_RIESGO.NEXTVAL, 1, 'Rendimiento Académico',  0.25, 5.0, 3.0);

INSERT INTO CONFIGURACION_RIESGO (id, institucion_id, indicador, peso, umbral_medio, umbral_alto)
VALUES (SEQ_CONFIG_RIESGO.NEXTVAL, 1, 'Bienestar Socioemocional', 0.20, 5.0, 3.0);

INSERT INTO CONFIGURACION_RIESGO (id, institucion_id, indicador, peso, umbral_medio, umbral_alto)
VALUES (SEQ_CONFIG_RIESGO.NEXTVAL, 1, 'Asistencia',             0.20, 5.0, 3.0);

INSERT INTO CONFIGURACION_RIESGO (id, institucion_id, indicador, peso, umbral_medio, umbral_alto)
VALUES (SEQ_CONFIG_RIESGO.NEXTVAL, 1, 'Participación',          0.15, 5.0, 3.0);

INSERT INTO CONFIGURACION_RIESGO (id, institucion_id, indicador, peso, umbral_medio, umbral_alto)
VALUES (SEQ_CONFIG_RIESGO.NEXTVAL, 1, 'Situación Económica',   0.10, 5.0, 3.0);

INSERT INTO CONFIGURACION_RIESGO (id, institucion_id, indicador, peso, umbral_medio, umbral_alto)
VALUES (SEQ_CONFIG_RIESGO.NEXTVAL, 1, 'Red de Apoyo Familiar', 0.10, 5.0, 3.0);

COMMIT;

-- ============================================================
-- Verificación final
-- ============================================================
SELECT TABLE_NAME FROM USER_TABLES ORDER BY TABLE_NAME;
SELECT VIEW_NAME  FROM USER_VIEWS  ORDER BY VIEW_NAME;
