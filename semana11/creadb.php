<?php
include("conex.php");

try {
    // Consulta SQL para crear la base de datos
    $sql = "CREATE DATABASE IF NOT EXISTS data CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci";

    // Ejecución de la sentencia DDL
    $conexion->exec($sql);
    echo "Base de datos 'data' creada exitosamente";
} catch (PDOException $e) {
    echo "Error al crear la base de datos: " . $e->getMessage();
}

// En PDO, la conexión se cierra asignando null a la variable
$conexion = null;
?>
