<?php
include("conexion.php");

try {
    $idDelete = 3;

    $stmt = $conexion->prepare("DELETE FROM t_usuario WHERE id_usuario = :id");
    $stmt->execute([':id' => $idDelete]);

    echo "Filas eliminadas exitosamente: " . $stmt->rowCount();
} catch (PDOException $e) {
    echo "Error al eliminar: " . $e->getMessage();
}

$conexion = null;
?>
