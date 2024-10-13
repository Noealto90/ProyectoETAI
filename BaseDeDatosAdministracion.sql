
--Tabla para crear los usuarios
CREATE TABLE usuarios ( 
    id SERIAL PRIMARY KEY,
	nombre VARCHAR(100) NULL,
    correo_institucional VARCHAR(255) UNIQUE NOT NULL CHECK (correo_institucional LIKE '%@etai.ac.cr'),
    contrasena VARCHAR(255) NOT NULL CHECK (LENGTH(contrasena) > 10),
 	rol VARCHAR(50) NOT NULL DEFAULT 'estudiante' CHECK (rol IN ('administrador', 'profesor', 'estudiante', 'superAdmin')), -- Rol por defecto 'estudiante'
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación del usuario
);

-- Tabla de laboratorios con su capacidad
CREATE TABLE laboratorios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capacidad INT NOT NULL
);

-- Tabla de computadoras
CREATE TABLE computadoras (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- Código único para cada computadora
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE
);

-- Tabla de mesas
CREATE TABLE mesas (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- Código único para cada mesa
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE
);

-- Tabla de sillas
CREATE TABLE sillas (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- Código único para cada silla
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE
);

CREATE TABLE espacios (
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE,  -- Relación con la tabla laboratorios
    espacio_id INT NOT NULL CHECK (espacio_id BETWEEN 1 AND 22),  -- Los espacios están limitados del 1 al 22
    activa BOOLEAN DEFAULT TRUE,  -- Campo que indica si el espacio está activo
    PRIMARY KEY (laboratorio_id, espacio_id)  -- Clave primaria compuesta por laboratorio y espacio
);


CREATE TABLE reservas (
    id SERIAL PRIMARY KEY,  -- Identificador único para cada reserva
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE,  -- Relación con la tabla de laboratorios
    espacio_id INT,  -- Relación con la tabla de espacios (id del espacio)
    nombreEncargado VARCHAR(100) NULL,  -- Nombre del encargado, puede ser NULL
    nombreAcompanante VARCHAR(100) NULL,  -- Nombre del acompañante, puede ser NULL
    horaInicio TIME NOT NULL,  -- Hora de inicio de la reserva
    horaFinal TIME GENERATED ALWAYS AS (horaInicio + INTERVAL '3 hours') STORED,  -- Hora final calculada automáticamente
    dia DATE NOT NULL,  -- Fecha de la reserva
    activa BOOLEAN DEFAULT TRUE,  -- Campo que indica si la reserva está activa o no
    UNIQUE (laboratorio_id, espacio_id, dia, horaInicio),  -- Restricción para evitar reservas duplicadas en el mismo espacio y hora
    CONSTRAINT fk_espacio_reserva FOREIGN KEY (laboratorio_id, espacio_id)
    REFERENCES espacios(laboratorio_id, espacio_id) ON DELETE RESTRICT  -- Relación con la tabla espacios
);










--Datos de prueba: 

INSERT INTO usuarios (nombre, correo_institucional, contrasena) VALUES
('Carlos Gómez', 'cgomez@etai.ac.cr', 'claveSegura123'),
('Ana Pérez', 'aperez@etai.ac.cr', 'contrasenaFuerte2022'),
('Luis Fernández', 'lfernandez@etai.ac.cr', 'miClaveSecreta123');


INSERT INTO usuarios (nombre, correo_institucional, contrasena, rol) VALUES
('Mario Rojas', 'mrojas@etai.ac.cr', 'claveDeAdmin123', 'administrador');

INSERT INTO laboratorios (nombre, capacidad) VALUES
('Laboratorio #1', 30),
('Laboratorio #2', 25);

INSERT INTO computadoras (codigo, laboratorio_id) VALUES
('PC-INF-01', 1),
('PC-INF-02', 1),
('PC-ELC-01', 2);

INSERT INTO mesas (codigo, laboratorio_id) VALUES
('MESA-INF-01', 1),
('MESA-INF-02', 1),
('MESA-ELC-01', 2);
INSERT INTO sillas (codigo, laboratorio_id) VALUES
('SILLA-INF-01', 1),
('SILLA-INF-02', 1),
('SILLA-ELC-01', 2);



-- Espacios para el laboratorio 1

INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 1, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 2, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 3, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 4, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 5, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 6, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 7, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 8, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 9, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 10, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 11, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 12, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 13, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 14, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 15, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 16, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 17, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 18, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 19, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 20, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 21, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, 22, TRUE);

-- Espacios para el laboratorio 2
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 1, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 2, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 3, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 4, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 5, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 6, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 7, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 8, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 9, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 10, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 11, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 12, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 13, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 14, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 15, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 16, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 17, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 18, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 19, TRUE);
INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, 20, TRUE);





select * from usuarios;
select * from espacios;
select * from reservas;


select * from laboratorios;
select * from computadoras;
select * from mesas;
select * from sillas;
select * from usuarios;

select * from reservas
select actualizar_reservas_pasadas()

select agregar_reserva(null, null, lab, hora ini, dia, escritorio)
--Prueba de conexion
CREATE USER proyecto2 WITH PASSWORD 'Proyecto123';
GRANT ALL PRIVILEGES ON DATABASE administracion TO proyecto2;





-- FUNCIONES

--FUNCIONES DE RESERVAS

CREATE OR REPLACE FUNCTION agregar_reserva(
    _nombre_encargado VARCHAR,
    _nombre_acompanante VARCHAR,
    _laboratorio_id INT,
    _hora_inicio TIME,
    _dia DATE,
    _escritorio CHAR(1)
)
RETURNS VOID AS $$
DECLARE
    conflicto INT;
BEGIN
    -- Verifica si hay conflicto con otra reserva (estado = 1: prestado)
    SELECT COUNT(*)
    INTO conflicto
    FROM reservas
    WHERE laboratorio_id = _laboratorio_id
    AND escritorio = _escritorio
    AND dia = _dia
    AND estado = 1
    AND (
        (_hora_inicio BETWEEN hora_inicio AND hora_final)
        OR (hora_inicio BETWEEN _hora_inicio AND _hora_inicio + INTERVAL '3 hours')
    );

    IF conflicto > 0 THEN
        RAISE EXCEPTION 'El escritorio ya está reservado en ese horario.';
    ELSE
        -- Inserta la nueva reserva
        INSERT INTO reservas (nombre_encargado, nombre_acompanante, laboratorio_id, hora_inicio, dia, escritorio, estado)
        VALUES (_nombre_encargado, _nombre_acompanante, _laboratorio_id, _hora_inicio, _dia, _escritorio, 1);
    END IF;
END;
$$ LANGUAGE plpgsql;



--A esta en buena teoria deberia recibir el nombre del encargado
--y obtener el nombre desde el login para asegurarnos que la persona encargada lo devuelva
CREATE OR REPLACE FUNCTION borrar_reserva(
    _laboratorio_id INT,
    _escritorio CHAR(1),
    _dia DATE
)
RETURNS VOID AS $$
BEGIN
    UPDATE reservas
    SET estado = 0 -- Estado 0 = no prestado
    WHERE laboratorio_id = _laboratorio_id
    AND escritorio = _escritorio
    AND dia = _dia
    AND estado = 1; -- Solo si estaba prestado
END;
$$ LANGUAGE plpgsql;


-- Agregar un Laboratorio
CREATE OR REPLACE FUNCTION agregar_laboratorio(
    p_nombre VARCHAR(100),
    p_capacidad INT
)
RETURNS VOID AS $$
BEGIN
    INSERT INTO laboratorios (nombre, capacidad)
    VALUES (p_nombre, p_capacidad);
END;
$$ LANGUAGE plpgsql;










-- Modificar un laboratorio
CREATE OR REPLACE FUNCTION modificar_laboratorio(
    p_id INT,
    p_nombre VARCHAR(100),
    p_capacidad INT
)
RETURNS VOID AS $$
BEGIN
    UPDATE laboratorios
    SET nombre = p_nombre,
        capacidad = p_capacidad
    WHERE id = p_id;
END;
$$ LANGUAGE plpgsql;

-- Eliminar un laboratorio
CREATE OR REPLACE FUNCTION eliminar_laboratorio(
    p_id INT
)
RETURNS VOID AS $$
BEGIN
    DELETE FROM laboratorios
    WHERE id = p_id;
END;
$$ LANGUAGE plpgsql;

--  Agregar persona (verificando correo y duplicados)
CREATE OR REPLACE FUNCTION agregar_persona(
    p_correo_institucional VARCHAR(255),
    p_contrasena VARCHAR(255)
)
RETURNS TEXT AS $$
DECLARE
    v_correo_existe INT;
BEGIN
    -- Verificar si el correo ya existe
    SELECT COUNT(*)
    INTO v_correo_existe
    FROM usuarios
    WHERE correo_institucional = p_correo_institucional;

    IF v_correo_existe > 0 THEN
        RETURN 'El correo ya está registrado.';
    END IF;

    -- Insertar nueva persona
    INSERT INTO usuarios (correo_institucional, contrasena)
    VALUES (p_correo_institucional, p_contrasena);
    
    RETURN 'Usuario agregado correctamente.';
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION insertar_equipo(tipo_equipo TEXT, codigo_equipo VARCHAR, laboratorio_id INT)
RETURNS VOID AS $$
BEGIN
    -- Verificar el tipo de equipo y realizar la inserción en la tabla correspondiente
    IF tipo_equipo = 'computadora' THEN
        INSERT INTO computadoras (codigo, laboratorio_id)
        VALUES (codigo_equipo, laboratorio_id);
        
    ELSIF tipo_equipo = 'mesa' THEN
        INSERT INTO mesas (codigo, laboratorio_id)
        VALUES (codigo_equipo, laboratorio_id);
        
    ELSIF tipo_equipo = 'silla' THEN
        INSERT INTO sillas (codigo, laboratorio_id)
        VALUES (codigo_equipo, laboratorio_id);
        
    ELSE
        RAISE EXCEPTION 'Tipo de equipo no válido: %', tipo_equipo;
    END IF;
END;
$$ LANGUAGE plpgsql;


SELECT insertar_equipo('computadora', 'COMP001', 1);
select * from computadoras


--Borrar equipos--
CREATE OR REPLACE FUNCTION borrar_equipo(tipo_equipo TEXT, codigo_equipo VARCHAR)
RETURNS VOID AS $$
BEGIN
    -- Verificar el tipo de equipo y realizar la eliminación en la tabla correspondiente
    IF tipo_equipo = 'computadora' THEN
        DELETE FROM computadoras
        WHERE codigo = codigo_equipo;

    ELSIF tipo_equipo = 'mesa' THEN
        DELETE FROM mesas
        WHERE codigo = codigo_equipo;

    ELSIF tipo_equipo = 'silla' THEN
        DELETE FROM sillas
        WHERE codigo = codigo_equipo;

    ELSE
        RAISE EXCEPTION 'Tipo de equipo no válido: %', tipo_equipo;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Función para modificar el laboratorio donde esta el equipo
CREATE OR REPLACE FUNCTION modificar_equipo1(
    tipo_equipo TEXT,
    codigo_equipo VARCHAR,
    nuevo_laboratorio_id INT DEFAULT NULL
)
RETURNS VOID AS $$
BEGIN
    -- Verificar el tipo de equipo y realizar la actualización en la tabla correspondiente
    IF tipo_equipo = 'computadora' THEN
        UPDATE computadoras
        SET laboratorio_id = nuevo_laboratorio_id
        WHERE codigo = codigo_equipo;

    ELSIF tipo_equipo = 'mesa' THEN
        UPDATE mesas
        SET laboratorio_id = nuevo_laboratorio_id
        WHERE codigo = codigo_equipo;

    ELSIF tipo_equipo = 'silla' THEN
        UPDATE sillas
        SET laboratorio_id = nuevo_laboratorio_id
        WHERE codigo = codigo_equipo;

    ELSE
        RAISE EXCEPTION 'Tipo de equipo no válido: %', tipo_equipo;
    END IF;
END;
$$ LANGUAGE plpgsql;






-- TRIGGERS

-- Trigger para verificar la capacidad del laboratorio al insertar una computadora, mesa o silla : se asegura de que no se puedan agregar más elementos (computadoras, mesas o sillas) a un laboratorio que ya haya alcanzado su capacidad.

CREATE OR REPLACE FUNCTION verificar_capacidad_laboratorio()
RETURNS TRIGGER AS $$
DECLARE
    v_total_elementos INT;
    v_capacidad INT;
BEGIN
    -- Obtener la capacidad del laboratorio
    SELECT capacidad INTO v_capacidad FROM laboratorios WHERE id = NEW.laboratorio_id;

    -- Contar computadoras, mesas y sillas
    SELECT COUNT(*) INTO v_total_elementos
    FROM computadoras WHERE laboratorio_id = NEW.laboratorio_id
    UNION ALL
    SELECT COUNT(*) FROM mesas WHERE laboratorio_id = NEW.laboratorio_id
    UNION ALL
    SELECT COUNT(*) FROM sillas WHERE laboratorio_id = NEW.laboratorio_id;

    IF v_total_elementos >= v_capacidad THEN
        RAISE EXCEPTION 'El laboratorio ha alcanzado su capacidad máxima.';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;



-- Trigger para computadoras
CREATE TRIGGER verificar_capacidad_computadoras
BEFORE INSERT ON computadoras
FOR EACH ROW
EXECUTE FUNCTION verificar_capacidad_laboratorio();



-- Trigger para mesas
CREATE TRIGGER verificar_capacidad_mesas
BEFORE INSERT ON mesas
FOR EACH ROW
EXECUTE FUNCTION verificar_capacidad_laboratorio();



-- Trigger para sillas
CREATE TRIGGER verificar_capacidad_sillas
BEFORE INSERT ON sillas
FOR EACH ROW
EXECUTE FUNCTION verificar_capacidad_laboratorio();



-- Trigger para registrar cambios en los laboratorios : Este trigger registra en una tabla de auditoría cada vez que se modifica un laboratorio, para mantener un registro de los cambios realizados.

CREATE TABLE auditoria_laboratorios (
    id SERIAL PRIMARY KEY,
    laboratorio_id INT,
    accion VARCHAR(50),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario VARCHAR(100)
);


CREATE OR REPLACE FUNCTION registrar_cambios_laboratorio()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO auditoria_laboratorios (laboratorio_id, accion, usuario)
    VALUES (NEW.id, TG_OP, current_user);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER registrar_modificacion_laboratorio
AFTER UPDATE ON laboratorios
FOR EACH ROW
EXECUTE FUNCTION registrar_cambios_laboratorio();