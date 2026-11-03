<?php
include("conexion.php");

try {
    $nombreBusqueda = "Jonás";

    // Consulta preparada para evitar Inyección SQL
    $stmt = $conexion->prepare("SELECT id_usuario, nombre_usu, password_usu, estado FROM t_usuario WHERE nombre_usu = :nombre");
    $stmt->execute([':nombre' => $nombreBusqueda]);

    $filas = $stmt->fetchAll();

    if (count($filas) > 0) {
        foreach ($filas as $fila) {
            echo "ID: " . $fila["id_usuario"] . " - Nombre: " . $fila["nombre_usu"] . " | Clave: " . $fila["password_usu"] . " | Estado: " . $fila["estado"] . "<br>";
        }
    } else {
        echo "0 resultados encontrados para '{$nombreBusqueda}'";
    }
} catch (PDOException $e) {
    echo "Error de filtrado: " . $e->getMessage();
}

$conexion = null;
?>
