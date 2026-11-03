<?php
include("conexion.php");

try {
    // Preparar la plantilla SQL con marcadores nombrados
    $stmt = $conexion->prepare("INSERT INTO t_usuario (nombre_usu, password_usu, estado) VALUES (:nombre, :pass, :estado)");

    // Vincular parámetros mediante bindParam
    $stmt->bindParam(':nombre', $nombre_usu, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $password_usu, PDO::PARAM_STR);
    $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);

    // Ejecución 1
    $nombre_usu   = "Jonás";
    $password_usu = "JonasTorres";
    $estado       = 0;
    $stmt->execute();

    // Ejecución 2
    $nombre_usu   = "Maria";
    $password_usu = "MariaMorales";
    $estado       = 1;
    $stmt->execute();

    // Ejecución 3
    $nombre_usu   = "Julia";
    $password_usu = "JDominguez";
    $estado       = 1;
    $stmt->execute();

    echo "Nuevos registros creados exitosamente con sentencias preparadas";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conexion = null;
?>
