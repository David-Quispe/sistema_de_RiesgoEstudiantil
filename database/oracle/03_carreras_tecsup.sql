-- ============================================================
-- Tabla CARRERAS — SMER
-- Ejecutar en SQL Developer como usuario SMER
-- ============================================================

CREATE SEQUENCE SEQ_CARRERAS START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

CREATE TABLE CARRERAS (
    id              NUMBER PRIMARY KEY,
    institucion_id  NUMBER        NOT NULL,
    nombre          VARCHAR2(150) NOT NULL,
    grupo           VARCHAR2(80),
    activo          NUMBER(1)     DEFAULT 1 NOT NULL CHECK (activo IN (0,1)),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_carrera_inst FOREIGN KEY (institucion_id) REFERENCES INSTITUCIONES(id)
);

CREATE INDEX idx_carreras_inst ON CARRERAS(institucion_id);

CREATE OR REPLACE TRIGGER trg_carreras_upd
BEFORE UPDATE ON CARRERAS
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

-- ============================================================
-- Carreras de TECSUP (institucion_id = 1)
-- Sin tildes ni enes para evitar problemas de encoding
-- ============================================================

-- Grupo: Ingenieria y Tecnologia
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Gestion y Mantenimiento de Maquinaria Pesada',              'Ingenieria y Tecnologia');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Mecatronica Industrial',                                     'Ingenieria y Tecnologia');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Tecnologia Mecanica Electrica',                              'Ingenieria y Tecnologia');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Electricidad Industrial con mencion en Sistemas Electricos de Potencia', 'Ingenieria y Tecnologia');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Electronica y Automatizacion Industrial',                    'Ingenieria y Tecnologia');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Procesos Quimicos y Metalurgicos',                           'Ingenieria y Tecnologia');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Tecnologia de la Produccion',                                'Ingenieria y Tecnologia');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Operaciones Mineras',                                        'Ingenieria y Tecnologia');

-- Grupo: Computacion, Informatica y Creatividad
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Diseno y Desarrollo de Software',                            'Computacion, Informatica y Creatividad');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Administracion de Redes y Comunicaciones',                   'Computacion, Informatica y Creatividad');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Big Data y Ciencia de Datos',                                'Computacion, Informatica y Creatividad');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Modelado y Animacion Digital',                               'Computacion, Informatica y Creatividad');
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Diseno y Desarrollo de Simuladores y Videojuegos',           'Computacion, Informatica y Creatividad');

-- Grupo: Gestion y Produccion
INSERT INTO CARRERAS (id, institucion_id, nombre, grupo) VALUES (SEQ_CARRERAS.NEXTVAL, 1, 'Produccion y Gestion Industrial',                            'Gestion y Produccion');

COMMIT;

-- Verificar
SELECT id, nombre, grupo FROM CARRERAS ORDER BY grupo, nombre;
