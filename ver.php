<?php

require 'includes/inicio.php';

$sprintObj = new Sprint();
$itemObj   = new Item();

$id = (int)($_GET['id'] ?? 0);
$sprint = $sprintObj->buscar($id);
if (!$sprint) {
    header('Location: index.php');
    exit;
}

$columnas = [
    'logro'       => '✅ Logros',
    'impedimento' => '⚠️ Impedimentos',
    'accion'      => '🎯 Acciones',
];

$anterior = $sprintObj->anterior($id);
$previas = [];
$porcentaje = 0;
if ($anterior) {
    $previas = $itemObj->listar($anterior['id'], 'accion');
    $cumplidas = 0;
    foreach ($previas as $p) {
        if ($p['estado'] == 'cumplida') {
            $cumplidas++;
        }
    }
    if (count($previas) > 0) {
        $porcentaje = round($cumplidas * 100 / count($previas));
    }
}

require 'includes/header.php';
?>

<h1><?= h($sprint['nombre']) ?></h1>
<p class="gris"><?= h($sprint['fecha_inicio']) ?> → <?= h($sprint['fecha_fin']) ?></p>
<div class="fila">
    <a class="btn gris-btn" href="sprint_form.php?id=<?= $id ?>">Editar sprint</a>
    <a class="btn gris-btn" href="index.php">← Historial</a>
</div>

<section class="revision">
    <h2>Acciones de la retrospectiva anterior</h2>

    <?php if (!$anterior): ?>
        <p class="gris">No hay un sprint anterior.</p>
    <?php elseif (count($previas) == 0): ?>
        <p class="gris"><?= h($anterior['nombre']) ?> no tuvo acciones.</p>
    <?php else: ?>
        <p>Acuerdos de <strong><?= h($anterior['nombre']) ?></strong> · Cumplimiento: <strong><?= $porcentaje ?>%</strong></p>
        <div class="progreso"><div style="width: <?= $porcentaje ?>%"></div></div>

        <?php foreach ($previas as $a): ?>
            <div class="item">
                <span class="estado <?= $a['estado'] ?>"><?= $estados[$a['estado']] ?></span>
                <?= h($a['descripcion']) ?>
                <div class="fila">
                    <?php foreach ($estados as $valor => $texto): ?>
                        <?php if ($valor != $a['estado']): ?>
                            <form method="post" action="item_estado.php">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                <input type="hidden" name="estado" value="<?= $valor ?>">
                                <input type="hidden" name="sprint_id" value="<?= $id ?>">
                                <button class="btn mini"><?= $texto ?></button>
                            </form>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<section class="tablero">
<?php foreach ($columnas as $categoria => $titulo): ?>
    <?php $items = $itemObj->listar($id, $categoria); ?>
    <div class="columna <?= $categoria ?>">
        <div class="fila entre">
            <h2><?= $titulo ?> (<?= count($items) ?>)</h2>
            <a class="btn mini" href="item_form.php?sprint_id=<?= $id ?>&categoria=<?= $categoria ?>">+ Agregar</a>
        </div>

        <?php if (count($items) == 0): ?><p class="gris">Sin registros.</p><?php endif; ?>

        <?php foreach ($items as $i): ?>
            <div class="item">
                <?php if ($categoria == 'accion'): ?>

                <?php endif; ?>
                <?= h($i['descripcion']) ?>
                <div class="fila">
                    <a class="btn mini gris-btn" href="item_form.php?id=<?= $i['id'] ?>">Editar</a>
                    <form method="post" action="item_eliminar.php" onsubmit="return confirm('¿Eliminar?')">
                        <input type="hidden" name="id" value="<?= $i['id'] ?>">
                        <input type="hidden" name="sprint_id" value="<?= $id ?>">
                        <button class="btn mini rojo">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
</section>

<?php require 'includes/footer.php'; ?>
