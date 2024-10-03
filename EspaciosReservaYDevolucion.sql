


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



select * from usuarios;
select * from espacios;
select * from reservas;
