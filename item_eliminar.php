<?php

require 'includes/inicio.php';

$sprint_id = (int)($_POST['sprint_id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemObj = new Item();
    $itemObj->eliminar($_POST['id']);
}
header('Location: ver.php?id=' . $sprint_id);
exit;
