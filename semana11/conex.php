<?php
$servername = "localhost";
$username   = "root";
$password   = "";

try {
    // Definición del DSN (Data Source Name)
    $dsn = "mysql:host={$servername};charset=utf8mb4";

    // Creación de la instancia PDO
    $conexion = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones ante errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver arrays asociativos por defecto
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar sentencias preparadas reales
    ]);

    echo "Conectado exitosamente con PDO";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
