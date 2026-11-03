<?php
/**
 * Conexión reutilizable a la BASE DE DATOS de la actividad (usa el parámetro dbname).
 * Deja la variable $conexion disponible. Para cerrarla: $conexion = null;
 * Si falla, lanza PDOException (la página que lo incluye la captura).
 */
require_once __DIR__ . '/config/config.php';

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

$conexion = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
