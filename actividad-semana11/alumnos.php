<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/funciones.php';

try {
    include __DIR__ . '/conexion.php';   // define $conexion
} catch (PDOException $e) {
    error_conexion_y_salir($e);
}

// ---------------- Filtros de la lista (se conservan al guardar/eliminar) ----------------
const ORDENES = ['id' => 'A.id_alumno', 'nombre' => 'A.nombre', 'ap_paterno' => 'A.ap_paterno',
                 'ap_materno' => 'A.ap_materno', 'curso' => 'C.nombrecurso'];

function filtros_alumnos(array $src): array
{
    $q = isset($src['q']) && is_string($src['q']) ? trim($src['q']) : '';
    $q = function_exists('mb_substr') ? mb_substr($q, 0, 50) : substr($q, 0, 50);

    $curso = $src['curso'] ?? '';
    $curso = (is_string($curso) && ($curso === 'sin' || ctype_digit($curso))) ? $curso : '';

    $orden = $src['orden'] ?? '';
    $orden = (is_string($orden) && isset(ORDENES[$orden])) ? $orden : 'ap_paterno';

    $dir = (($src['dir'] ?? '') === 'desc') ? 'desc' : 'asc';

    return ['q' => $q, 'curso' => $curso, 'orden' => $orden, 'dir' => $dir];
}

function url_alumnos(array $filtros, array $extra = []): string
{
    $param = array_filter($filtros, fn($v) => $v !== '');
    return 'alumnos.php?' . http_build_query(array_merge($param, $extra));
}

function campos_ocultos(array $filtros): string
{
    $html = '';
    foreach ($filtros as $nombre => $valor) {
        $html .= '<input type="hidden" name="' . e($nombre) . '" value="' . e($valor) . '">';
    }
    return $html;
}

$esPost  = $_SERVER['REQUEST_METHOD'] === 'POST';
$filtros = filtros_alumnos($esPost ? $_POST : $_GET);

$cursos   = $conexion->query('SELECT id, nombrecurso FROM t_curso ORDER BY nombrecurso')->fetchAll();
$idsCurso = array_map('intval', array_column($cursos, 'id'));

$form    = ['id_alumno' => '', 'nombre' => '', 'ap_paterno' => '', 'ap_materno' => '', 't_curso_id' => ''];
$errores = [];

// ---------------- Añadir / Actualizar / Eliminar ----------------
if ($esPost) {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear' || $accion === 'actualizar') {
            $nombre  = post_str('nombre');
            $paterno = post_str('ap_paterno');
            $materno = post_str('ap_materno');
            $cursoRaw = post_str('t_curso_id', 10);
            $cursoId  = $cursoRaw === '' ? null : filter_var($cursoRaw, FILTER_VALIDATE_INT);
            $id       = filter_var($_POST['id_alumno'] ?? '', FILTER_VALIDATE_INT);

            $form = ['id_alumno' => $accion === 'actualizar' ? (string) $id : '', 'nombre' => $nombre,
                     'ap_paterno' => $paterno, 'ap_materno' => $materno, 't_curso_id' => $cursoRaw];

            foreach (['nombre' => $nombre, 'apellido paterno' => $paterno, 'apellido materno' => $materno] as $campo => $valor) {
                if ($valor === '' || !preg_match('/^[\p{L}\s.\'-]+$/u', $valor)) {
                    $errores[] = "Ingresa un $campo válido (solo letras).";
                }
            }
            if ($cursoId === false || ($cursoId !== null && !in_array($cursoId, $idsCurso, true))) {
                $errores[] = 'Selecciona un curso válido.';
            }
            if ($accion === 'actualizar' && $id === false) {
                $errores[] = 'Alumno no válido.';
            }

            if (!$errores) {
                $tipoCurso = $cursoId === null ? PDO::PARAM_NULL : PDO::PARAM_INT;

                if ($accion === 'crear') {
                    $stmt = $conexion->prepare(
                        'INSERT INTO t_alumno (nombre, ap_paterno, ap_materno, t_curso_id)
                         VALUES (:nombre, :paterno, :materno, :curso)'
                    );
                    $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                    $stmt->bindValue(':paterno', $paterno, PDO::PARAM_STR);
                    $stmt->bindValue(':materno', $materno, PDO::PARAM_STR);
                    $stmt->bindValue(':curso', $cursoId, $tipoCurso);
                    $stmt->execute();
                    flash('success', 'Alumno añadido (ID ' . $conexion->lastInsertId() . ').');
                } else {
                    $stmt = $conexion->prepare(
                        'UPDATE t_alumno
                            SET nombre = :nombre, ap_paterno = :paterno, ap_materno = :materno, t_curso_id = :curso
                          WHERE id_alumno = :id'
                    );
                    $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                    $stmt->bindValue(':paterno', $paterno, PDO::PARAM_STR);
                    $stmt->bindValue(':materno', $materno, PDO::PARAM_STR);
                    $stmt->bindValue(':curso', $cursoId, $tipoCurso);
                    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    // rowCount() devuelve el número de filas afectadas por la operación
                    flash($stmt->rowCount() > 0 ? 'success' : 'info',
                          $stmt->rowCount() > 0 ? 'Alumno actualizado correctamente.' : 'No hubo cambios que guardar.');
                }
                redirigir(url_alumnos($filtros));
            }
        } elseif ($accion === 'eliminar') {
            $id = filter_var($_POST['id_alumno'] ?? '', FILTER_VALIDATE_INT);
            if ($id !== false) {
                $stmt = $conexion->prepare('DELETE FROM t_alumno WHERE id_alumno = :id');
                $stmt->execute([':id' => $id]);
                flash($stmt->rowCount() > 0 ? 'success' : 'warning',
                      $stmt->rowCount() > 0 ? 'Alumno eliminado.' : 'El alumno no existe.');
            }
            redirigir(url_alumnos($filtros));
        }
    } catch (PDOException $e) {
        $errores[] = 'Error en la base de datos: ' . $e->getMessage();
    }
}

// ---------------- Cargar alumno a editar ----------------
$editarId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
if ($editarId && !$esPost) {
    $stmt = $conexion->prepare(
        'SELECT id_alumno, nombre, ap_paterno, ap_materno, t_curso_id FROM t_alumno WHERE id_alumno = :id'
    );
    $stmt->execute([':id' => $editarId]);
    $fila = $stmt->fetch();
    if ($fila) {
        $form = array_map(fn($v) => (string) ($v ?? ''), $fila);
    } else {
        flash('warning', 'El alumno que quieres editar no existe.');
    }
}

// ---------------- Listado con filtro (WHERE / LIKE) y orden (ORDER BY) ----------------
$sql    = 'SELECT A.id_alumno, A.nombre, A.ap_paterno, A.ap_materno, A.t_curso_id, C.nombrecurso
             FROM t_alumno A
             LEFT JOIN t_curso C ON A.t_curso_id = C.id';
$where  = [];
$params = [];

if ($filtros['q'] !== '') {
    // Con sentencias preparadas reales no se puede repetir un mismo marcador: se usan tres distintos
    $where[] = '(A.nombre LIKE :q1 OR A.ap_paterno LIKE :q2 OR A.ap_materno LIKE :q3)';
    $like = '%' . addcslashes($filtros['q'], '%_\\') . '%';
    $params[':q1'] = $params[':q2'] = $params[':q3'] = $like;
}
if ($filtros['curso'] === 'sin') {
    $where[] = 'A.t_curso_id IS NULL';
} elseif ($filtros['curso'] !== '') {
    $where[] = 'A.t_curso_id = :curso';
    $params[':curso'] = (int) $filtros['curso'];
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY ' . ORDENES[$filtros['orden']] . ' ' . strtoupper($filtros['dir']) . ', A.id_alumno ASC';

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$alumnos = $stmt->fetchAll();

$editando      = $form['id_alumno'] !== '';
$hayFiltro     = $filtros['q'] !== '' || $filtros['curso'] !== '';
$titulo_pagina = 'Alumnos';
$activo        = 'alumnos';
include_once __DIR__ . '/includes/header.php';
?>
<h1 class="h3 fw-bold mb-3">Mantenimiento de alumnos <small class="text-secondary fs-6">t_alumno</small></h1>

<?php flash_mostrar(); ?>
<?php if ($errores): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $x): ?><li><?= e($x) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="row g-4">

    <div class="col-lg-4">
        <section class="panel">
            <div class="panel-head"><?= $editando ? 'Editar alumno #' . e($form['id_alumno']) : 'Añadir alumno' ?></div>
            <div class="panel-body">
                <form method="post" action="alumnos.php">
                    <?= campos_ocultos($filtros) ?>
                    <input type="hidden" name="accion" value="<?= $editando ? 'actualizar' : 'crear' ?>">
                    <input type="hidden" name="id_alumno" value="<?= e($form['id_alumno']) ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" maxlength="100" value="<?= e($form['nombre']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="ap_paterno" class="form-label fw-semibold">Apellido paterno</label>
                        <input type="text" id="ap_paterno" name="ap_paterno" class="form-control" maxlength="100" value="<?= e($form['ap_paterno']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="ap_materno" class="form-label fw-semibold">Apellido materno</label>
                        <input type="text" id="ap_materno" name="ap_materno" class="form-control" maxlength="100" value="<?= e($form['ap_materno']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="t_curso_id" class="form-label fw-semibold">Curso</label>
                        <select id="t_curso_id" name="t_curso_id" class="form-select">
                            <option value="">— Sin curso —</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= (int) $c['id'] ?>" <?= (string) $c['id'] === $form['t_curso_id'] ? 'selected' : '' ?>>
                                    <?= e($c['nombrecurso']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-slate"><?= $editando ? 'Guardar cambios' : 'Añadir' ?></button>
                        <?php if ($editando): ?><a class="btn btn-outline-secondary" href="<?= e(url_alumnos($filtros)) ?>">Cancelar</a><?php endif; ?>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-lg-8">
        <section class="panel mb-3">
            <div class="panel-head">Buscar, filtrar y ordenar</div>
            <div class="panel-body">
                <form method="get" action="alumnos.php" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="q" class="form-label small fw-semibold mb-1">Buscar (nombre o apellido)</label>
                        <input type="text" id="q" name="q" class="form-control form-control-sm" value="<?= e($filtros['q']) ?>" placeholder="Ej.: Car">
                    </div>
                    <div class="col-md-3">
                        <label for="curso" class="form-label small fw-semibold mb-1">Curso</label>
                        <select id="curso" name="curso" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="sin" <?= $filtros['curso'] === 'sin' ? 'selected' : '' ?>>Sin curso</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= (int) $c['id'] ?>" <?= $filtros['curso'] === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['nombrecurso']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="orden" class="form-label small fw-semibold mb-1">Ordenar por</label>
                        <select id="orden" name="orden" class="form-select form-select-sm">
                            <?php foreach (['id' => 'ID', 'nombre' => 'Nombre', 'ap_paterno' => 'Apellido paterno', 'ap_materno' => 'Apellido materno', 'curso' => 'Curso'] as $v => $t): ?>
                                <option value="<?= e($v) ?>" <?= $filtros['orden'] === $v ? 'selected' : '' ?>><?= e($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="dir" class="form-select form-select-sm" aria-label="Dirección">
                            <option value="asc" <?= $filtros['dir'] === 'asc' ? 'selected' : '' ?>>A → Z</option>
                            <option value="desc" <?= $filtros['dir'] === 'desc' ? 'selected' : '' ?>>Z → A</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-slate btn-sm" type="submit">Aplicar</button>
                        <?php if ($hayFiltro): ?><a class="btn btn-outline-secondary btn-sm" href="alumnos.php">Limpiar</a><?php endif; ?>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head">Lista de alumnos (<?= count($alumnos) ?>)</div>
            <div class="panel-body">
                <?php if (!$alumnos): ?>
                    <div class="alert alert-secondary mb-0">
                        <?= $hayFiltro ? 'No hay alumnos que coincidan con el filtro.' : 'No hay alumnos. Añade uno o ejecuta la <a href="instalar.php">instalación</a>.' ?>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle mb-0">
                        <thead><tr><th>ID</th><th>Nombre</th><th>Ap. paterno</th><th>Ap. materno</th><th>Curso</th><th class="text-end">Acciones</th></tr></thead>
                        <tbody>
                        <?php foreach ($alumnos as $a): ?>
                            <tr>
                                <td><?= e($a['id_alumno']) ?></td>
                                <td><?= e($a['nombre']) ?></td>
                                <td><?= e($a['ap_paterno']) ?></td>
                                <td><?= e($a['ap_materno']) ?></td>
                                <td><?= $a['nombrecurso'] === null ? '<span class="text-secondary">Sin curso</span>' : e($a['nombrecurso']) ?></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-outline-primary btn-sm" href="<?= e(url_alumnos($filtros, ['editar' => (int) $a['id_alumno']])) ?>">Editar</a>
                                    <form method="post" action="alumnos.php" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar a este alumno?');">
                                        <?= campos_ocultos($filtros) ?>
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_alumno" value="<?= (int) $a['id_alumno'] ?>">
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
