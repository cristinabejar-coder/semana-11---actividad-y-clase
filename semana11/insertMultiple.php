<?php
include("conexion.php");

try {
    // Iniciar la transacción
    $conexion->beginTransaction();

    $stmt = $conexion->prepare("INSERT INTO t_usuario (nombre_usu, password_usu, estado) VALUES (:nombre, :pass, :estado)");

    // Registro 1
    $stmt->execute([':nombre' => 'John', ':pass' => 'Doe', ':estado' => 0]);
    // Registro 2
    $stmt->execute([':nombre' => 'Mary', ':pass' => 'Moe', ':estado' => 0]);
    // Registro 3
    $stmt->execute([':nombre' => 'Julie', ':pass' => 'Dooley', ':estado' => 0]);

    // Confirmar los cambios
    $conexion->commit();
    echo "Nuevos registros creados exitosamente mediante Transacción PDO";
} catch (PDOException $e) {
    // Revertir cambios en caso de fallo
    $conexion->rollBack();
    echo "Error en inserción múltiple: " . $e->getMessage();
}

$conexion = null;
?>
