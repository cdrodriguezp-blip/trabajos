<?php

require 'includes/inicio.php';

$sprintObj = new Sprint();
$sprints = $sprintObj->listar();

require 'includes/header.php';
?>

<h1>Historial de retrospectivas</h1>

<?php if (count($sprints) == 0): ?>
    <p class="vacio">Aún no hay sprints. <a href="sprint_form.php">Crea el primero</a>.</p>
<?php endif; ?>

<div class="tarjetas">
<?php foreach ($sprints as $s): ?>
    <div class="tarjeta">
        <h2><a href="ver.php?id=<?= $s['id'] ?>"><?= h($s['nombre']) ?></a></h2>
        <p class="gris"><?= h($s['fecha_inicio']) ?> → <?= h($s['fecha_fin']) ?></p>
        <p>
            <span class="chip verde"><?= $s['logros'] ?> logros</span>
            <span class="chip naranja"><?= $s['impedimentos'] ?> impedimentos</span>
            <span class="chip azul"><?= $s['acciones'] ?> acciones</span>
        </p>
        <div class="fila">
            <a class="btn gris-btn" href="ver.php?id=<?= $s['id'] ?>">Abrir</a>
            <a class="btn gris-btn" href="sprint_form.php?id=<?= $s['id'] ?>">Editar</a>
            <form method="post" action="sprint_eliminar.php"
                  onsubmit="return confirm('¿Eliminar este sprint y todo su contenido?')">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button class="btn rojo">Eliminar</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php require 'includes/footer.php'; ?>
