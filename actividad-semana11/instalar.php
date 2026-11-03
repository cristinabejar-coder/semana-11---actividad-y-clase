<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/funciones.php';
require_once __DIR__ . '/lib/instalacion.php';

$pasos = [
    'Paso 1 · Crear la base de datos'      => 'paso_crear_base',
    'Paso 2 · Crear las tablas'            => 'paso_crear_tablas',
    'Paso 3 · Insertar los datos iniciales' => 'paso_insertar_datos',
];

$resultados = [];
foreach ($pasos as $titulo => $funcion) {
    $resultados[$titulo] = ejecutar_paso($funcion);
    if (!$resultados[$titulo]['ok']) {
        break;   // si un paso falla, no se continúa con los siguientes
    }
}
$todoOk = count($resultados) === count($pasos) && !in_array(false, array_column($resultados, 'ok'), true);

$titulo_pagina = 'Instalación completa';
$activo        = 'instalar';
include_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 fw-bold mb-3">Instalación completa</h1>

<?php foreach ($resultados as $titulo => $r): ?>
    <div class="panel mb-3">
        <div class="panel-head"><?= e($titulo) ?></div>
        <div class="panel-body">
            <div class="alert alert-<?= $r['ok'] ? 'success' : 'danger' ?> mb-0"><?= e($r['msg']) ?></div>
        </div>
    </div>
<?php endforeach; ?>

<?php if ($todoOk): ?>
    <p>La base <code><?= e(DB_NAME) ?></code> está lista. Puedes repetir esta página sin problema: no duplica datos.</p>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-slate" href="alumnos.php">Alumnos</a>
        <a class="btn btn-slate" href="cursos.php">Cursos</a>
        <a class="btn btn-slate" href="consultas.php">Consultas SQL</a>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        Revisa que MySQL esté encendido (XAMPP → Start) y los datos de <code>config/config.php</code>; luego recarga esta página.
    </div>
<?php endif; ?>

<hr>
<p class="small text-secondary mb-2">También puedes ejecutar los pasos por separado:</p>
<div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-dark btn-sm" href="creadb.php">1. Crear base</a>
    <a class="btn btn-outline-dark btn-sm" href="creatablas.php">2. Crear tablas</a>
    <a class="btn btn-outline-dark btn-sm" href="datos.php">3. Insertar datos</a>
</div>
<?php include_once __DIR__ . '/includes/footer.php'; ?>
