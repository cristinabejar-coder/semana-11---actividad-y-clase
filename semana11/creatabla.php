<?php
include("conexion.php");

try {
    $sql = "CREATE TABLE IF NOT EXISTS `t_usuario` (
        `id_usuario` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `nombre_usu` VARCHAR(100) NOT NULL,
        `password_usu` VARCHAR(100) NOT NULL,
        `estado` TINYINT(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci";

    $conexion->exec($sql);
    echo "La tabla 't_usuario' se creó correctamente";
} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage();
}

$conexion = null;
?>
