<?php
include("conexion.php");

try {
    $stmt = $conexion->query("SELECT id_usuario, nombre_usu, password_usu, estado FROM t_usuario");

    // fetchAll() recupera todas las filas en un arreglo
    $filas = $stmt->fetchAll();

    if (count($filas) > 0) {
        foreach ($filas as $fila) {
            echo "ID: " . $fila["id_usuario"] . " - Nombre: " . $fila["nombre_usu"] . " | Clave: " . $fila["password_usu"] . " | Estado: " . $fila["estado"] . "<br>";
        }
    } else {
        echo "0 resultados";
    }
} catch (PDOException $e) {
    echo "Error al seleccionar: " . $e->getMessage();
}

$conexion = null;
?>
