<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/funciones.php';

try {
    include __DIR__ . '/conexion.php';   // define $conexion
} catch (PDOException $e) {
    error_conexion_y_salir($e);
}

$form    = ['id' => '', 'nombrecurso' => ''];
$errores = [];

// ---------------- Añadir / Actualizar / Eliminar ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear' || $accion === 'actualizar') {
            $nombre = post_str('nombrecurso');
            $id     = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            $form   = ['id' => $accion === 'actualizar' ? (string) $id : '', 'nombrecurso' => $nombre];

            if ($nombre === '') {
                $errores[] = 'Ingresa el nombre del curso.';
            }
            if ($accion === 'actualizar' && $id === false) {
                $errores[] = 'Curso no válido.';
            }

            if (!$errores) {
                if ($accion === 'crear') {
                    $stmt = $conexion->prepare('INSERT INTO t_curso (nombrecurso) VALUES (:nombre)');
                    $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                    $stmt->execute();
                    flash('success', 'Curso añadido (ID ' . $conexion->lastInsertId() . ').');
                } else {
                    $stmt = $conexion->prepare('UPDATE t_curso SET nombrecurso = :nombre WHERE id = :id');
                    $stmt->execute([':nombre' => $nombre, ':id' => $id]);
                    flash($stmt->rowCount() > 0 ? 'success' : 'info',
                          $stmt->rowCount() > 0 ? 'Curso actualizado correctamente.' : 'No hubo cambios que guardar.');
                }
                redirigir('cursos.php');
            }
        } elseif ($accion === 'eliminar') {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            if ($id !== false) {
                $stmt = $conexion->prepare('DELETE FROM t_curso WHERE id = :id');
                $stmt->execute([':id' => $id]);
                flash($stmt->rowCount() > 0 ? 'success' : 'warning',
                      $stmt->rowCount() > 0
                        ? 'Curso eliminado. Los alumnos que lo tenían quedan sin curso.'
                        : 'El curso no existe.');
            }
            redirigir('cursos.php');
        }
    } catch (PDOException $e) {
        $errores[] = 'Error en la base de datos: ' . $e->getMessage();
    }
}

// ---------------- Cargar curso a editar ----------------
$editarId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
if ($editarId && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conexion->prepare('SELECT id, nombrecurso FROM t_curso WHERE id = :id');
    $stmt->execute([':id' => $editarId]);
    $fila = $stmt->fetch();
    if ($fila) {
        $form = ['id' => (string) $fila['id'], 'nombrecurso' => $fila['nombrecurso']];
    } else {
        flash('warning', 'El curso que quieres editar no existe.');
    }
}

// ---------------- Listado (con cantidad de alumnos) ----------------
$cursos = $conexion->query(
    'SELECT C.id, C.nombrecurso, COUNT(A.id_alumno) AS alumnos
       FROM t_curso C
       LEFT JOIN t_alumno A ON A.t_curso_id = C.id
      GROUP BY C.id, C.nombrecurso
      ORDER BY C.id'
)->fetchAll();

$editando      = $form['id'] !== '';
$titulo_pagina = 'Cursos';
$activo        = 'cursos';
include_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 fw-bold mb-3">Mantenimiento de cursos <small class="text-secondary fs-6">t_curso</small></h1>

<?php flash_mostrar(); ?>
<?php if ($errores): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $x): ?><li><?= e($x) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <section class="panel">
            <div class="panel-head"><?= $editando ? 'Editar curso #' . e($form['id']) : 'Añadir curso' ?></div>
            <div class="panel-body">
                <form method="post" action="cursos.php">
                    <input type="hidden" name="accion" value="<?= $editando ? 'actualizar' : 'crear' ?>">
                    <input type="hidden" name="id" value="<?= e($form['id']) ?>">
                    <label for="nombrecurso" class="form-label fw-semibold">Nombre del curso</label>
                    <input type="text" id="nombrecurso" name="nombrecurso" class="form-control" maxlength="100"
                           value="<?= e($form['nombrecurso']) ?>" required>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-slate"><?= $editando ? 'Guardar cambios' : 'Añadir' ?></button>
                        <?php if ($editando): ?><a class="btn btn-outline-secondary" href="cursos.php">Cancelar</a><?php endif; ?>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-lg-8">
        <section class="panel">
            <div class="panel-head">Lista de cursos (<?= count($cursos) ?>)</div>
            <div class="panel-body">
                <?php if (!$cursos): ?>
                    <div class="alert alert-secondary mb-0">No hay cursos. Añade uno o ejecuta la <a href="instalar.php">instalación</a>.</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle mb-0">
                        <thead><tr><th>ID</th><th>Curso</th><th class="text-center">Alumnos</th><th class="text-end">Acciones</th></tr></thead>
                        <tbody>
                        <?php foreach ($cursos as $c): ?>
                            <tr>
                                <td><?= e($c['id']) ?></td>
                                <td><?= e($c['nombrecurso']) ?></td>
                                <td class="text-center"><span class="badge text-bg-secondary"><?= e($c['alumnos']) ?></span></td>
                                <td class="text-end">
                                    <a class="btn btn-outline-primary btn-sm" href="cursos.php?editar=<?= (int) $c['id'] ?>">Editar</a>
                                    <form method="post" action="cursos.php" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este curso?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?php
$conexion = null;
include_once __DIR__ . '/includes/footer.php';
?>
