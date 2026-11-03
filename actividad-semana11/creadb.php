<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/funciones.php';
require_once __DIR__ . '/lib/instalacion.php';

$resultado     = ejecutar_paso('paso_crear_base');
$titulo_pagina = 'Paso 1 · Crear la base de datos';
$activo        = 'instalar';
include_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 fw-bold mb-3">Paso 1 · Crear la base de datos</h1>
<div class="alert alert-<?= $resultado['ok'] ? 'success' : 'danger' ?>"><?= e($resultado['msg']) ?></div>
<?php if (!$resultado['ok']): ?>
    <p class="text-secondary">Verifica que MySQL esté encendido y revisa <code>config/config.php</code>.</p>
<?php endif; ?>
<div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary" href="instalar.php">← Instalación completa</a>
    <a class="btn btn-slate" href="creatablas.php">Siguiente: crear tablas →</a>
</div>
<?php include_once __DIR__ . '/includes/footer.php'; ?>
