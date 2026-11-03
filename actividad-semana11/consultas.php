<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/funciones.php';

try {
    include __DIR__ . '/conexion.php';   // define $conexion
} catch (PDOException $e) {
    error_conexion_y_salir($e);
}

// Las 5 consultas requeridas por la actividad
$consultas = [
    [
        'titulo' => 'Obtener todos los cursos disponibles en la tabla t_curso',
        'sql'    => 'SELECT * FROM t_curso;',
    ],
    [
        'titulo' => 'Obtener todos los alumnos de la tabla t_alumno',
        'sql'    => 'SELECT * FROM t_alumno;',
    ],
    [
        'titulo' => 'Obtener todos los alumnos ordenados por su apellido paterno (orden ascendente)',
        'sql'    => 'SELECT * FROM t_alumno ORDER BY ap_paterno ASC;',
    ],
    [
        'titulo' => 'Obtener la cantidad de alumnos matriculados por curso',
        'sql'    => "SELECT C.nombrecurso, COUNT(A.id_alumno) AS cantidad_alumnos\nFROM t_curso C\nLEFT JOIN t_alumno A ON C.id = A.t_curso_id\nGROUP BY C.id, C.nombrecurso;",
    ],
    [
        'titulo' => 'Obtener la lista de alumnos junto con el nombre del curso en el que están inscritos',
        'sql'    => "SELECT A.nombre, A.ap_paterno, A.ap_materno, C.nombrecurso\nFROM t_alumno A\nJOIN t_curso C ON A.t_curso_id = C.id;",
    ],
];

$resultados = [];
foreach ($consultas as $i => $c) {
    try {
        // Consultas que devuelven resultados: query() + fetchAll()
        $resultados[$i] = ['ok' => true, 'filas' => $conexion->query($c['sql'])->fetchAll()];
    } catch (PDOException $e) {
        $resultados[$i] = ['ok' => false, 'error' => $e->getMessage()];
    }
}
$conexion = null;

$titulo_pagina = 'Consultas SQL';
$activo        = 'consultas';
include_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 fw-bold mb-1">Consultas SQL con PHP PDO</h1>
<p class="text-secondary">Cada consulta se ejecuta en vivo contra la base <code><?= e(DB_NAME) ?></code>.</p>

<?php foreach ($consultas as $i => $c): ?>
    <section class="panel mb-4">
        <div class="panel-head">Consulta <?= $i + 1 ?></div>
        <div class="panel-body">
            <h2 class="h6 fw-bold"><?= e($c['titulo']) ?></h2>
            <pre class="sql-code mb-3"><?= e($c['sql']) ?></pre>
            <?php if ($resultados[$i]['ok']): ?>
                <div class="small text-secondary mb-2"><?= count($resultados[$i]['filas']) ?> fila(s)</div>
                <?= tabla_resultados($resultados[$i]['filas']) ?>
            <?php else: ?>
                <div class="alert alert-danger mb-0"><?= e($resultados[$i]['error']) ?></div>
            <?php endif; ?>
        </div>
    </section>
<?php endforeach; ?>
<?php include_once __DIR__ . '/includes/footer.php'; ?>
