<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/funciones.php';
require_once __DIR__ . '/lib/instalacion.php';

$resultado     = ejecutar_paso('paso_crear_tablas');
$titulo_pagina = 'Paso 2 · Crear las tablas';
$activo        = 'instalar';
include_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 fw-bold mb-3">Paso 2 · Crear las tablas</h1>
<div class="alert alert-<?= $resultado['ok'] ? 'success' : 'danger' ?>"><?= e($resultado['msg']) ?></div>
<?php if (!$resultado['ok']): ?>
    <p class="text-secondary">Si dice «Unknown database», ejecuta primero el <a href="creadb.php">Paso 1</a>.</p>
<?php endif; ?>
<div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary" href="creadb.php">← Paso 1</a>
    <a class="btn btn-slate" href="datos.php">Siguiente: insertar datos →</a>
</div>
<?php include_once __DIR__ . '/includes/footer.php'; ?>
