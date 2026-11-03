<?php
declare(strict_types=1);

/**
 * Funciones reutilizables de la actividad.
 */

require_once dirname(__DIR__) . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Escapa un valor para imprimirlo de forma segura en HTML. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Lee un campo de texto enviado por POST (recortado y con largo máximo). */
function post_str(string $campo, int $max = 100): string
{
    $valor = $_POST[$campo] ?? '';
    $valor = is_string($valor) ? trim($valor) : '';
    return function_exists('mb_substr') ? mb_substr($valor, 0, $max) : substr($valor, 0, $max);
}

function redirigir(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// ---------------------------------------------------------------
// Mensajes (flash): se guardan en sesión y se muestran una sola vez
// ---------------------------------------------------------------

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function flash_mostrar(): void
{
    foreach ($_SESSION['flash'] ?? [] as $f) {
        echo '<div class="alert alert-' . e($f['tipo']) . ' alert-dismissible fade show" role="alert">'
           . e($f['mensaje'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>';
    }
    unset($_SESSION['flash']);
}

// ---------------------------------------------------------------
// Pasos de instalación y errores de conexión
// ---------------------------------------------------------------

/** Ejecuta un paso de instalación y devuelve ['ok' => bool, 'msg' => string]. */
function ejecutar_paso(callable $paso): array
{
    try {
        return ['ok' => true, 'msg' => (string) $paso()];
    } catch (PDOException $e) {
        return ['ok' => false, 'msg' => 'Error: ' . $e->getMessage()];
    }
}

/** Muestra una página de error de conexión dentro de la plantilla y termina. */
function error_conexion_y_salir(PDOException $e): void
{
    http_response_code(500);
    $titulo_pagina = 'Error de conexión';
    $activo        = '';
    include BASE_PATH . '/includes/header.php';
    echo '<div class="alert alert-danger"><h2 class="h5">No se pudo conectar a la base de datos</h2>'
       . '<p class="mb-2">' . e($e->getMessage()) . '</p>'
       . '<ul class="mb-0">'
       . '<li>Verifica que MySQL esté encendido (en XAMPP: botón <strong>Start</strong> de MySQL).</li>'
       . '<li>Revisa usuario, contraseña y puerto en <code>config/config.php</code>.</li>'
       . '<li>Si es la primera vez, ejecuta antes la <a href="instalar.php">instalación</a>.</li>'
       . '</ul></div>';
    include BASE_PATH . '/includes/footer.php';
    exit;
}

// ---------------------------------------------------------------
// Presentación
// ---------------------------------------------------------------

/** Convierte un conjunto de filas (arrays asociativos) en una tabla Bootstrap. */
function tabla_resultados(array $filas): string
{
    if (!$filas) {
        return '<div class="alert alert-secondary mb-0">La consulta no devolvió resultados.</div>';
    }
    $html = '<div class="table-responsive"><table class="table table-striped table-sm align-middle mb-0"><thead><tr>';
    foreach (array_keys($filas[0]) as $columna) {
        $html .= '<th>' . e($columna) . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    foreach ($filas as $fila) {
        $html .= '<tr>';
        foreach ($fila as $valor) {
            $html .= '<td>' . ($valor === null ? '<span class="text-secondary">—</span>' : e($valor)) . '</td>';
        }
        $html .= '</tr>';
    }
    return $html . '</tbody></table></div>';
}
