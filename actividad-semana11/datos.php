<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/funciones.php';
require_once __DIR__ . '/lib/instalacion.php';

$resultado     = ejecutar_paso('paso_insertar_datos');
$titulo_pagina = 'Paso 3 · Insertar los datos';
$activo        = 'instalar';
include_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 fw-bold mb-3">Paso 3 · Insertar los datos iniciales</h1>
<div class="alert alert-<?= $resultado['ok'] ? 'success' : 'danger' ?>"><?= e($resultado['msg']) ?></div>
<?php if (!$resultado['ok']): ?>
    <p class="text-secondary">Si falla, ejecuta antes el <a href="creadb.php">Paso 1</a> y el <a href="creatablas.php">Paso 2</a>.</p>
<?php endif; ?>
<div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary" href="creatablas.php">← Paso 2</a>
    <a class="btn btn-slate" href="alumnos.php">Ir a Alumnos →</a>
    <a class="btn btn-outline-dark" href="cursos.php">Ir a Cursos</a>
</div>
<?php include_once __DIR__ . '/includes/footer.php'; ?>
