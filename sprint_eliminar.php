<?php

require 'includes/inicio.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sprintObj = new Sprint();
    $sprintObj->eliminar($_POST['id']);
}
header('Location: index.php');
exit;
