<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "data";

try {
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    $conexion = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
