<?php

require 'includes/inicio.php';

$sprintObj = new Sprint();
$id = $_GET['id'] ?? null;
$error = '';
$datos = ['nombre' => '', 'fecha_inicio' => '', 'fecha_fin' => ''];

if ($id) {
    $datos = $sprintObj->buscar($id);
    if (!$datos) {
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $inicio = $_POST['fecha_inicio'];
    $fin    = $_POST['fecha_fin'];
    $datos  = ['nombre' => $nombre, 'fecha_inicio' => $inicio, 'fecha_fin' => $fin];

    if ($nombre == '' || $inicio == '' || $fin == '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($fin < $inicio) {
        $error = 'La fecha de fin no puede ser anterior a la de inicio.';
    } else {
        if ($id) {
            $sprintObj->actualizar($id, $nombre, $inicio, $fin);
        } else {
            $id = $sprintObj->crear($nombre, $inicio, $fin);
        }
        header('Location: ver.php?id=' . $id);
        exit;
    }
}

require 'includes/header.php';
?>

<h1><?= $id ? 'Editar sprint' : 'Nuevo sprint' ?></h1>

<?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>

<form method="post" class="formulario">
    <label>Nombre
        <input type="text" name="nombre" maxlength="100" value="<?= h($datos['nombre']) ?>" placeholder="Ej. Sprint 3">
    </label>
    <label>Fecha de inicio
        <input type="date" name="fecha_inicio" value="<?= h($datos['fecha_inicio']) ?>">
    </label>
    <label>Fecha de fin
        <input type="date" name="fecha_fin" value="<?= h($datos['fecha_fin']) ?>">
    </label>
    <div class="fila">
        <button class="btn">Guardar</button>
        <a class="btn gris-btn" href="index.php">Cancelar</a>
    </div>
</form>

<?php require 'includes/footer.php'; ?>
