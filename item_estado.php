<?php

require 'includes/inicio.php';

$sprint_id = (int)($_POST['sprint_id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists($_POST['estado'], $estados)) {
    $itemObj = new Item();
    $itemObj->cambiarEstado($_POST['id'], $_POST['estado']);
}
header('Location: ver.php?id=' . $sprint_id);
exit;
