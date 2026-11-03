<?php
/**
 * Conexión inicial al SERVIDOR MySQL (sin seleccionar base de datos).
 * Se usa para crear la base de datos. Deja la variable $conexion disponible.
 * Si falla, lanza PDOException (la página que lo incluye la captura).
 */
require_once __DIR__ . '/config/config.php';

// Definición del DSN (Data Source Name)
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';

// Creación de la instancia PDO
$conexion = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones ante errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver arrays asociativos por defecto
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar sentencias preparadas reales
]);
