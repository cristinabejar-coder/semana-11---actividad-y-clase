<?php
include("conexion.php");

try {
    $idUpdate = 2;
    $nuevoNombre = "Marciano";

    $stmt = $conexion->prepare("UPDATE t_usuario SET nombre_usu = :nombre WHERE id_usuario = :id");
    $stmt->execute([
        ':nombre' => $nuevoNombre,
        ':id'     => $idUpdate
    ]);

    // rowCount() devuelve el número de filas afectadas por la operación
    echo "Filas actualizadas correctamente: " . $stmt->rowCount();
} catch (PDOException $e) {
    echo "Error al actualizar: " . $e->getMessage();
}

$conexion = null;
?>
