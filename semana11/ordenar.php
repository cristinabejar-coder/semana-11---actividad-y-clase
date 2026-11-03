<?php
include("conexion.php");

try {
    $stmt = $conexion->query("SELECT id_usuario, nombre_usu, password_usu, estado FROM t_usuario ORDER BY nombre_usu ASC");

    while ($fila = $stmt->fetch()) {
        echo "ID: " . $fila["id_usuario"] . " - Nombre: " . $fila["nombre_usu"] . " | Clave: " . $fila["password_usu"] . " | Estado: " . $fila["estado"] . "<br>";
    }
} catch (PDOException $e) {
    echo "Error al ordenar: " . $e->getMessage();
}

$conexion = null;
?>
