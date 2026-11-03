-- ============================================================
-- ACTIVIDAD - SEMANA 11 · Script SQL (equivalente a la instalación en PHP)
-- Ejecútalo en VS Code (Run MySQL Query) o en phpMyAdmin.
-- ============================================================

CREATE DATABASE IF NOT EXISTS actividad_semana11
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;

USE actividad_semana11;

-- ------------------------------------------------------------
-- Tabla t_curso
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS t_curso (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombrecurso VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- ------------------------------------------------------------
-- Tabla t_alumno (con clave foránea hacia t_curso)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS t_alumno (
    id_alumno  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    ap_paterno VARCHAR(100) NOT NULL,
    ap_materno VARCHAR(100) NOT NULL,
    t_curso_id INT NULL,
    CONSTRAINT fk_alumno_curso FOREIGN KEY (t_curso_id)
        REFERENCES t_curso (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- ------------------------------------------------------------
-- Datos iniciales
-- ------------------------------------------------------------
INSERT INTO t_curso (id, nombrecurso) VALUES
    (1, 'Desarrollo Web con PHP 8'),
    (2, 'Base de Datos MySQL y PDO'),
    (3, 'JavaScript y Frontend'),
    (4, 'Programación Orientada a Objetos'),
    (5, 'Arquitectura y Servicios Web');

INSERT INTO t_alumno (id_alumno, nombre, ap_paterno, ap_materno, t_curso_id) VALUES
    (1, 'Carlos Alberto',  'Mendoza', 'Ríos',   1),
    (2, 'Ana Sofía',       'Gómez',   'Torres', 1),
    (3, 'Luis Fernando',   'Silva',   'Vargas', 2),
    (4, 'María Elena',     'Rojas',   'Castro', 3),
    (5, 'Kevin Alexander', 'Pérez',   'Suárez', 4);

-- ------------------------------------------------------------
-- Consultas requeridas
-- ------------------------------------------------------------
-- 1. Todos los cursos
SELECT * FROM t_curso;

-- 2. Todos los alumnos
SELECT * FROM t_alumno;

-- 3. Alumnos ordenados por apellido paterno (ascendente)
SELECT * FROM t_alumno ORDER BY ap_paterno ASC;

-- 4. Cantidad de alumnos matriculados por curso
SELECT C.nombrecurso, COUNT(A.id_alumno) AS cantidad_alumnos
FROM t_curso C
LEFT JOIN t_alumno A ON C.id = A.t_curso_id
GROUP BY C.id, C.nombrecurso;

-- 5. Alumnos junto con el nombre del curso en el que están inscritos
SELECT A.nombre, A.ap_paterno, A.ap_materno, C.nombrecurso
FROM t_alumno A
JOIN t_curso C ON A.t_curso_id = C.id;
