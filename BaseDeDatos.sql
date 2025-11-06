-- Crear tablas
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NULL,
    correo_institucional VARCHAR(255) UNIQUE NOT NULL CHECK (correo_institucional LIKE '%@etai.ac.cr'),
    contrasena VARCHAR(255) NOT NULL CHECK (LENGTH(contrasena) > 10),
    rol VARCHAR(50) NOT NULL DEFAULT 'estudiante' CHECK (rol IN ('administrador', 'profesor', 'estudiante', 'superAdmin')),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Se desactivo para probar lo de que llegue el correo
ALTER TABLE usuarios DROP CONSTRAINT IF EXISTS usuarios_correo_institucional_check;


CREATE TABLE IF NOT EXISTS laboratorios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capacidad INT NOT NULL
);

CREATE TABLE IF NOT EXISTS equipos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    tipo VARCHAR(50) NOT NULL CHECK (tipo IN ('computadora', 'mesa', 'silla')),
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS espacios (
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE,
    espacio_id INT NOT NULL CHECK (espacio_id BETWEEN 0 AND 22),
    activa BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (laboratorio_id, espacio_id)
);

CREATE TABLE IF NOT EXISTS reservas (
    id SERIAL PRIMARY KEY,
    laboratorio_id INT REFERENCES laboratorios(id) ON DELETE CASCADE,
    espacio_id INT,
    nombreEncargado VARCHAR(100) NULL,
    nombreAcompanante VARCHAR(100) NULL,
    horaInicio TIME NOT NULL,
    horaFinal TIME,  -- Sin valor por defecto
    diaR DATE NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_espacio_reserva FOREIGN KEY (laboratorio_id, espacio_id)
    REFERENCES espacios(laboratorio_id, espacio_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS cancelados (
    id SERIAL PRIMARY KEY,
    correo VARCHAR(255) NOT NULL
);
-- Crear tabla roles_asignados
CREATE TABLE IF NOT EXISTS roles_asignados (
    id SERIAL PRIMARY KEY,
    correo_institucional VARCHAR(255) UNIQUE NOT NULL CHECK (correo_institucional LIKE '%@etai.ac.cr'),
    rol VARCHAR(50) NOT NULL CHECK (rol IN ('administrador', 'profesor', 'estudiante', 'superAdmin'))
);

-- Crear tabla reportes_daños
CREATE TABLE IF NOT EXISTS reportes_daños (
    id SERIAL PRIMARY KEY,
    codigo_equipo VARCHAR(50) REFERENCES equipos(codigo) ON DELETE CASCADE,
    descripcion TEXT NOT NULL,
    fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reportado_por VARCHAR(255) NOT NULL
);



-- Crear tabla cuatrimestres
CREATE TABLE IF NOT EXISTS cuatrimestres (
    id SERIAL PRIMARY KEY,
	numero INT NOT NULL,
	anio INT NOT NULL
);

CREATE TABLE IF NOT EXISTS dias(
	id SERIAL PRIMARY KEY,
	idDia VARCHAR(10) NOT NULL
);




------
-- Agregar columna estado a la tabla equipos
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'equipos' AND column_name = 'estado') THEN
        ALTER TABLE equipos ADD COLUMN estado VARCHAR(50) DEFAULT 'disponible' CHECK (estado IN ('disponible', 'bloqueado'));
    END IF;
END $$;







--Funciones--

CREATE OR REPLACE FUNCTION insertar_equipo(tipo_equipo TEXT, codigo_equipo VARCHAR, laboratorio_id INT)
RETURNS VOID AS $$
BEGIN
    INSERT INTO equipos (codigo, tipo, laboratorio_id)
    VALUES (codigo_equipo, tipo_equipo, laboratorio_id);
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION borrar_equipo(tipo_equipo TEXT, codigo_equipo VARCHAR)
RETURNS VOID AS $$
BEGIN
    DELETE FROM equipos
    WHERE codigo = codigo_equipo AND tipo = tipo_equipo;
END;
$$ LANGUAGE plpgsql;




--RESETEAR ESPACIOS
CREATE OR REPLACE FUNCTION activar_espacios()
RETURNS VOID AS $$
BEGIN
    UPDATE espacios
    SET activa = TRUE;
END;
$$ LANGUAGE plpgsql;


--BORRAR DATOS REPETIDOS
CREATE OR REPLACE FUNCTION eliminar_duplicados_cancelados()
RETURNS VOID AS $$
BEGIN
    DELETE FROM cancelados
    WHERE id NOT IN (
        SELECT MIN(id)
        FROM cancelados
        GROUP BY correo
    );
END;
$$ LANGUAGE plpgsql;


--RESERVAS EN TIEMPO REAL
CREATE OR REPLACE FUNCTION desactivar_espacios_por_reservas(dia DATE, inicio TIME)
RETURNS VOID AS $$
BEGIN
    UPDATE espacios e
    SET activa = false
    FROM reservas r
    WHERE r.espacio_id = e.espacio_id
      AND r.laboratorio_id = e.laboratorio_id
      AND r.activa = true
      AND r.diaR = dia
      AND (r.horaInicio < (inicio + INTERVAL '3 hours') AND (r.horaFinal > inicio));
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION reserva_cuatrimestre(
    fecha_inicio DATE,
    laboratorio_id_se INT,
    nombre_encargado VARCHAR(100),
    hora_inicio TIME,
    hora_final TIME
)
RETURNS VOID AS $$
DECLARE
    fecha_actual DATE := fecha_inicio;
    fecha_final DATE := fecha_inicio + INTERVAL '4 months';
    reserva_existente RECORD;
    correo_encargado_existente VARCHAR(255);
BEGIN
    -- Loop para insertar reservas cada lunes
    WHILE fecha_actual <= fecha_final LOOP
        -- Verificar si ya existe una reserva activa para el mismo laboratorio, día y en la misma franja horaria
        SELECT * INTO reserva_existente
        FROM reservas
        WHERE laboratorio_id = laboratorio_id_se
          AND diaR = fecha_actual
          AND activa = true
          AND (
              (horaInicio, horaFinal) OVERLAPS (hora_inicio, hora_final)
          );

        IF FOUND THEN
            -- Marcar la reserva existente como inactiva
            UPDATE reservas
            SET activa = false
            WHERE id = reserva_existente.id;

            -- Obtener el correo del encargado de la reserva existente
            SELECT correo_institucional INTO correo_encargado_existente
            FROM usuarios
            WHERE nombre = reserva_existente.nombreEncargado;

            -- Insertar el correo del encargado existente en la tabla cancelados
            IF correo_encargado_existente IS NOT NULL THEN
                INSERT INTO cancelados (correo)
                VALUES (correo_encargado_existente);
            END IF;
        ELSE
            -- Insertar la nueva reserva si no hay conflicto
            INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, nombreAcompanante, horaInicio, horaFinal, diaR)
            VALUES (laboratorio_id_se, 0, nombre_encargado, NULL, hora_inicio, hora_final, fecha_actual);
        END IF;

        -- Avanzar al siguiente lunes
        fecha_actual := fecha_actual + INTERVAL '1 week';
    END LOOP;
END;
$$ LANGUAGE plpgsql;




--FUNCIÓN PARA REALIZAR LAS RESERVAS DE PROFESORES O ADMINISTRATIVOS


CREATE OR REPLACE FUNCTION realizar_reserva( 
    p_dia DATE,
    p_hora_inicio TIME,
    p_hora_final TIME,
    p_correo_profesor VARCHAR,
    p_laboratorio_id INT
) RETURNS INT AS $$
DECLARE
    v_nombre_encargado VARCHAR(100);
    v_rol_usuario VARCHAR(50);
    v_correo_encargado VARCHAR(255);
BEGIN
    -- Verificar si hay una reserva activa que coincide con los parámetros proporcionados
    FOR v_nombre_encargado IN
        SELECT nombreEncargado
        FROM reservas
        WHERE diaR = p_dia
          AND horaInicio < p_hora_final
          AND horaFinal > p_hora_inicio
          AND laboratorio_id = p_laboratorio_id
          AND activa = TRUE
    LOOP
        -- Verificar el rol del nombreEncargado en la tabla de usuarios
        SELECT rol INTO v_rol_usuario
        FROM usuarios
        WHERE nombre = v_nombre_encargado;
        
        -- Si el encargado no es un estudiante, devolver 1 y salir
        IF v_rol_usuario IS DISTINCT FROM 'estudiante' THEN
            RETURN 1;
        END IF;
    END LOOP;

    -- Insertar los correos de los encargados cuyas reservas se van a marcar como inactivas en la tabla cancelados
    FOR v_nombre_encargado IN
        SELECT nombreEncargado
        FROM reservas
        WHERE diaR = p_dia
          AND horaInicio < p_hora_final
          AND horaFinal > p_hora_inicio
          AND laboratorio_id = p_laboratorio_id
          AND activa = TRUE
    LOOP
        -- Obtener el correo institucional del encargado desde la tabla usuarios
        SELECT correo_institucional INTO v_correo_encargado
        FROM usuarios
        WHERE nombre = v_nombre_encargado;
        
        -- Insertar el correo en la tabla cancelados si se encontró un correo válido
        IF v_correo_encargado IS NOT NULL THEN
            INSERT INTO cancelados (correo) VALUES (v_correo_encargado);
        END IF;
    END LOOP;

    -- Si todas las reservas existentes son de estudiantes, ponerlas como inactivas
    UPDATE reservas
    SET activa = FALSE
    WHERE diaR = p_dia
      AND horaInicio < p_hora_final
      AND horaFinal > p_hora_inicio
      AND laboratorio_id = p_laboratorio_id
      AND activa = TRUE;

    -- Insertar la nueva reserva
    INSERT INTO reservas (diaR, espacio_id, horaInicio, horaFinal, nombreEncargado, laboratorio_id, activa)
    VALUES (p_dia, 0, p_hora_inicio, p_hora_final, p_correo_profesor, p_laboratorio_id, TRUE);

    -- Si todo fue exitoso, devolver 0 indicando que se realizó correctamente
    RETURN 0;
END;
$$ LANGUAGE plpgsql;



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
    SELECT COUNT(*)
    INTO conflicto
    FROM reservas
    WHERE laboratorio_id = _laboratorio_id
    AND espacio_id = _escritorio
    AND diaR = _dia
    AND activa = TRUE
    AND (
        (_hora_inicio BETWEEN horaInicio AND horaFinal)
        OR (horaInicio BETWEEN _hora_inicio AND _hora_inicio + INTERVAL '3 hours')
    );

    IF conflicto > 0 THEN
        RAISE EXCEPTION 'El escritorio ya está reservado en ese horario.';
    ELSE
        INSERT INTO reservas (nombreEncargado, nombreAcompanante, laboratorio_id, horaInicio, diaR, espacio_id, activa)
        VALUES (_nombre_encargado, _nombre_acompanante, _laboratorio_id, _hora_inicio, _dia, _escritorio, TRUE);
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION borrar_reserva(
    _laboratorio_id INT,
    _escritorio CHAR(1),
    _dia DATE
)
RETURNS VOID AS $$
BEGIN
    UPDATE reservas
    SET activa = FALSE
    WHERE laboratorio_id = _laboratorio_id
    AND espacio_id = _escritorio
    AND diaR = _dia
    AND activa = TRUE;
END;
$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION eliminar_laboratorio(
    p_id INT
)
RETURNS VOID AS $$
BEGIN
    DELETE FROM laboratorios
    WHERE id = p_id;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION agregar_persona(
    p_nombre VARCHAR(100),
    p_correo_institucional VARCHAR(255),
    p_contrasena VARCHAR(255),
    p_rol VARCHAR(50)
)
RETURNS TEXT AS $$
DECLARE
    v_correo_existe INT;
BEGIN
    SELECT COUNT(*)
    INTO v_correo_existe
    FROM usuarios
    WHERE correo_institucional = p_correo_institucional;

    IF v_correo_existe > 0 THEN
        RETURN 'El correo ya está registrado.';
    END IF;

    INSERT INTO usuarios (nombre, correo_institucional, contrasena, rol)
    VALUES (p_nombre, p_correo_institucional, p_contrasena, p_rol);
    
    RETURN 'Usuario agregado correctamente.';
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION insertar_equipo(tipo_equipo TEXT, codigo_equipo VARCHAR, laboratorio_id INT)
RETURNS VOID AS $$
BEGIN
    INSERT INTO equipos (codigo, tipo, laboratorio_id)
    VALUES (codigo_equipo, tipo_equipo, laboratorio_id);
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION borrar_equipo(tipo_equipo TEXT, codigo_equipo VARCHAR)
RETURNS VOID AS $$
BEGIN
    DELETE FROM equipos
    WHERE codigo = codigo_equipo AND tipo = tipo_equipo;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION modificar_equipo(
    tipo_equipo TEXT,
    codigo_equipo VARCHAR,
    nuevo_laboratorio_id INT DEFAULT NULL
)
RETURNS VOID AS $$
BEGIN
    UPDATE equipos
    SET laboratorio_id = nuevo_laboratorio_id
    WHERE codigo = codigo_equipo AND tipo = tipo_equipo;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION activar_espacios()
RETURNS VOID AS $$
BEGIN
    UPDATE espacios
    SET activa = TRUE;
END;
$$ LANGUAGE plpgsql;

-- Crear triggers
CREATE OR REPLACE FUNCTION verificar_capacidad_laboratorio()
RETURNS TRIGGER AS $$
DECLARE
    v_total_elementos INT;
    v_capacidad INT;
BEGIN
    SELECT capacidad INTO v_capacidad FROM laboratorios WHERE id = NEW.laboratorio_id;

    SELECT COUNT(*)
    INTO v_total_elementos
    FROM equipos
    WHERE laboratorio_id = NEW.laboratorio_id;

    IF v_total_elementos >= v_capacidad THEN
        RAISE EXCEPTION 'El laboratorio ha alcanzado su capacidad máxima.';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS verificar_capacidad_equipos ON equipos;
CREATE TRIGGER verificar_capacidad_equipos
BEFORE INSERT ON equipos
FOR EACH ROW
EXECUTE FUNCTION verificar_capacidad_laboratorio();

CREATE TABLE IF NOT EXISTS auditoria_laboratorios (
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

DROP TRIGGER IF EXISTS registrar_modificacion_laboratorio ON laboratorios;
CREATE TRIGGER registrar_modificacion_laboratorio
AFTER UPDATE ON laboratorios
FOR EACH ROW
EXECUTE FUNCTION registrar_cambios_laboratorio();

-- Limpiar datos existentes para permitir re-ejecución
TRUNCATE TABLE reservas CASCADE;
TRUNCATE TABLE espacios CASCADE;
TRUNCATE TABLE equipos CASCADE;
TRUNCATE TABLE reportes_daños CASCADE;
TRUNCATE TABLE cancelados CASCADE;
TRUNCATE TABLE roles_asignados CASCADE;
TRUNCATE TABLE cuatrimestres CASCADE;
TRUNCATE TABLE dias CASCADE;
TRUNCATE TABLE auditoria_laboratorios CASCADE;
TRUNCATE TABLE laboratorios CASCADE;

TRUNCATE TABLE usuarios CASCADE;

-- Resetear secuencia de laboratorios para que los IDs empiecen en 1
ALTER SEQUENCE laboratorios_id_seq RESTART WITH 1;

-- Insertar datos iniciales
INSERT INTO usuarios (nombre, correo_institucional, contrasena, rol) VALUES
('Carlos Gómez', 'cgomez@etai.ac.cr', 'claveSegura123', 'profesor'),
('Ana Pérez', 'aperez@etai.ac.cr', 'contrasenaFuerte2022', 'estudiante'),
('Luis Fernández', 'lfernandez@etai.ac.cr', 'miClaveSecreta123', 'estudiante'),
('Mario Rojas', 'mrojas@etai.ac.cr', 'claveDeAdmin123', 'administrador');


-- Insertar laboratorios antes de los espacios para cumplir la restricción de llave foránea
INSERT INTO laboratorios (nombre, capacidad) VALUES
('Laboratorio #1', 30),
('Laboratorio #2', 25);

-- Espacios para el laboratorio 1
DO $$
BEGIN
    FOR i IN 1..22 LOOP
        INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (1, i, TRUE);
    END LOOP;
END $$;

-- Espacios para el laboratorio 2
DO $$
BEGIN
    FOR i IN 1..20 LOOP
        INSERT INTO espacios (laboratorio_id, espacio_id, activa) VALUES (2, i, TRUE);
    END LOOP;
END $$;

INSERT INTO espacios (laboratorio_id, espacio_id) VALUES (1, 0); --El 0 significa todos los espacios
INSERT INTO espacios (laboratorio_id, espacio_id) VALUES (2, 0); 


-- Insertar reservas de prueba
INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, horaInicio, horaFinal, diaR)
VALUES (1, 11, 'Luis Fernando', '10:00:00', '13:00:00', '2024-10-15');

INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, horaInicio, HoraFinal, diaR)
VALUES (1, 12, 'Emma Lopez', '10:00:00', '13:00:00', '2024-10-15');

INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, horaInicio, horaFinal, diaR)
VALUES (1, 9, 'Ana Pérez', '10:00:00', '11:30:00', '2024-12-08');


INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, horaInicio, diaR)
VALUES (1, NULL, 'estudiante@institucion.com', '20:00:00', '2024-11-02');

INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, horaInicio, diaR)
VALUES (1, NULL, 'estudiante@institucion.com', '11:00:00', '2024-10-16');

INSERT INTO equipos (codigo, tipo, laboratorio_id) VALUES
('PC-INF-01', 'computadora', 1),
('PC-INF-02', 'computadora', 1),
('PC-ELC-01', 'computadora', 2),
('MESA-INF-01', 'mesa', 1),
('MESA-INF-02', 'mesa', 1),
('MESA-ELC-01', 'mesa', 2),
('SILLA-INF-01', 'silla', 1),
('SILLA-INF-02', 'silla', 1),
('SILLA-ELC-01', 'silla', 2);

-- Insertar un cuatrimestre
INSERT INTO cuatrimestres (id, numero, anio)
VALUES (1, 3, 2024);

-- Insertar días de la semana en la tabla dias
INSERT INTO dias (idDia)
VALUES ('Lunes'),
       ('Martes'),
       ('Miercoles'),
       ('Jueves'),
       ('Viernes'),
	   ('Sabado'),
	   ('Domingo');

-- Ensure cuatrimestres table is recreated
DROP TABLE IF EXISTS cuatrimestres CASCADE;

-- Crear tabla cuatrimestres
CREATE TABLE IF NOT EXISTS cuatrimestres (
    id SERIAL PRIMARY KEY,
	numero INT NOT NULL,
	anio INT NOT NULL
);
