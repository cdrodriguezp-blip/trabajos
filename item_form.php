<?php

require 'includes/inicio.php';

$itemObj   = new Item();
$sprintObj = new Sprint();
$id    = $_GET['id'] ?? null;
$error = '';

if ($id) {

    $registro = $itemObj->buscar($id);
    if (!$registro) {
        header('Location: index.php');
        exit;
    }
    $sprint_id   = $registro['sprint_id'];
    $categoria   = $registro['categoria'];
    $descripcion = $registro['descripcion'];
    $estado      = $registro['estado'];
} else {

    $sprint_id   = (int)($_GET['sprint_id'] ?? 0);
    $categoria   = $_GET['categoria'] ?? '';
    $descripcion = '';
    $estado      = 'pendiente';

    $categoriasValidas = ['logro', 'impedimento', 'accion'];
    if (!in_array($categoria, $categoriasValidas) || !$sprintObj->buscar($sprint_id)) {
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = trim($_POST['descripcion']);
    $estado = $_POST['estado'] ?? $estado;

    if ($descripcion == '') {
        $error = 'La descripción no puede estar vacía.';
    } else {
        if ($id) {
            $itemObj->actualizar($id, $descripcion, $estado);
        } else {
            $itemObj->crear($sprint_id, $categoria, $descripcion);
        }
        header('Location: ver.php?id=' . $sprint_id);
        exit;
    }
}

$nombres = ['logro' => 'logro', 'impedimento' => 'impedimento', 'accion' => 'acción'];

require 'includes/header.php';
?>

<h1><?= $id ? 'Editar' : 'Agregar' ?> <?= $nombres[$categoria] ?></h1>

<?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>

<form method="post" class="formulario">
    <label>Descripción
        <textarea name="descripcion" rows="4"><?= h($descripcion) ?></textarea>
    </label>

    <?php if ($categoria == 'accion' && $id): ?>
        <label>Estado
            <select name="estado">
                <?php foreach ($estados as $valor => $texto): ?>
                    <option value="<?= $valor ?>" <?= $estado == $valor ? 'selected' : '' ?>><?= $texto ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    <?php endif; ?>

    <div class="fila">
        <button class="btn">Guardar</button>
        <a class="btn gris-btn" href="ver.php?id=<?= $sprint_id ?>">Cancelar</a>
    </div>
</form>

<?php require 'includes/footer.php'; ?>
