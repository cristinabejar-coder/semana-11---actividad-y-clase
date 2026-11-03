<?php
// Variables esperadas: $titulo_pagina, $activo (inicio|instalar|cursos|alumnos|consultas)
$titulo_pagina = $titulo_pagina ?? APP_NAME;
$activo        = $activo ?? '';
$enlaces = [
    'instalar'  => ['instalar.php',  'Instalación'],
    'cursos'    => ['cursos.php',    'Cursos'],
    'alumnos'   => ['alumnos.php',   'Alumnos'],
    'consultas' => ['consultas.php', 'Consultas SQL'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo_pagina) ?> · <?= e(APP_NAME) ?></title>
    <link href="<?= CDN_BOOTSTRAP_CSS ?>" rel="stylesheet">
    <link href="assets/estilo.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark app-navbar">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="index.html"><?= e(APP_NAME) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                <?php foreach ($enlaces as $clave => [$url, $texto]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $activo === $clave ? 'active' : '' ?>" href="<?= e($url) ?>"><?= e($texto) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
