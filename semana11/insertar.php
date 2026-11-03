<?php
include("conexion.php");

try {
    $sql = "INSERT INTO t_usuario (nombre_usu, password_usu, estado)
            VALUES ('José', 'data2024', 1)";

    $conexion->exec($sql);
    $last_id = $conexion->lastInsertId(); // Obtiene el último ID generado por AUTO_INCREMENT

    echo "Nuevo registro creado exitosamente. ID generado: " . $last_id;
} catch (PDOException $e) {
    echo "Error en la inserción: " . $e->getMessage();
}

$conexion = null;
?>
