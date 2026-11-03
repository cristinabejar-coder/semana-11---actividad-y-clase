<?php
declare(strict_types=1);

/**
 * Pasos de instalación con PDO. Cada función devuelve un mensaje
 * o lanza PDOException si algo falla.
 */

/** Paso 1: crear la base de datos (usa conex.php, sin dbname). */
function paso_crear_base(): string
{
    include BASE_PATH . '/conex.php';   // define $conexion

    // Las sentencias sin resultados (DDL) se ejecutan con exec()
    $sql = 'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` '
         . 'CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci';
    $conexion->exec($sql);

    $conexion = null;   // en PDO la conexión se cierra asignando null
    return "Base de datos '" . DB_NAME . "' creada correctamente (o ya existía).";
}

/** Paso 2: crear las tablas t_curso y t_alumno con su clave foránea. */
function paso_crear_tablas(): string
{
    include BASE_PATH . '/conexion.php';   // define $conexion (con dbname)

    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS `t_curso` (
            `id`          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `nombrecurso` VARCHAR(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci"
    );

    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS `t_alumno` (
            `id_alumno`  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `nombre`     VARCHAR(100) NOT NULL,
            `ap_paterno` VARCHAR(100) NOT NULL,
            `ap_materno` VARCHAR(100) NOT NULL,
            `t_curso_id` INT NULL,
            CONSTRAINT `fk_alumno_curso` FOREIGN KEY (`t_curso_id`)
                REFERENCES `t_curso` (`id`)
                ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci"
    );

    $conexion = null;
    return "Tablas 't_curso' y 't_alumno' creadas correctamente (con FOREIGN KEY t_alumno.t_curso_id → t_curso.id).";
}

/** Paso 3: insertar los registros iniciales con sentencias preparadas y una transacción. */
function paso_insertar_datos(): string
{
    include BASE_PATH . '/conexion.php';

    $existentes = (int) $conexion->query('SELECT COUNT(*) FROM t_curso')->fetchColumn()
                + (int) $conexion->query('SELECT COUNT(*) FROM t_alumno')->fetchColumn();
    if ($existentes > 0) {
        $conexion = null;
        return "Las tablas ya contienen datos ($existentes registros). No se insertó nada para evitar duplicados.";
    }

    $cursos = [
        [1, 'Desarrollo Web con PHP 8'],
        [2, 'Base de Datos MySQL y PDO'],
        [3, 'JavaScript y Frontend'],
        [4, 'Programación Orientada a Objetos'],
        [5, 'Arquitectura y Servicios Web'],
    ];
    $alumnos = [
        [1, 'Carlos Alberto',  'Mendoza', 'Ríos',   1],
        [2, 'Ana Sofía',       'Gómez',   'Torres', 1],
        [3, 'Luis Fernando',   'Silva',   'Vargas', 2],
        [4, 'María Elena',     'Rojas',   'Castro', 3],
        [5, 'Kevin Alexander', 'Pérez',   'Suárez', 4],
    ];

    try {
        $conexion->beginTransaction();   // todo o nada

        $stmt = $conexion->prepare('INSERT INTO t_curso (id, nombrecurso) VALUES (:id, :nombre)');
        foreach ($cursos as [$id, $nombre]) {
            $stmt->execute([':id' => $id, ':nombre' => $nombre]);
        }

        $stmt = $conexion->prepare(
            'INSERT INTO t_alumno (id_alumno, nombre, ap_paterno, ap_materno, t_curso_id)
             VALUES (:id, :nombre, :paterno, :materno, :curso)'
        );
        foreach ($alumnos as [$id, $nombre, $paterno, $materno, $curso]) {
            $stmt->execute([':id' => $id, ':nombre' => $nombre, ':paterno' => $paterno,
                            ':materno' => $materno, ':curso' => $curso]);
        }

        $conexion->commit();
    } catch (PDOException $e) {
        $conexion->rollBack();   // revertir cambios en caso de fallo
        throw $e;
    }

    $conexion = null;
    return 'Datos iniciales insertados: ' . count($cursos) . ' cursos y ' . count($alumnos) . ' alumnos.';
}
