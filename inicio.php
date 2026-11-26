<?php

require_once __DIR__ . '/../clases/Conexion.php';
require_once __DIR__ . '/../clases/Sprint.php';
require_once __DIR__ . '/../clases/Item.php';

$estados = [
    'pendiente'   => 'Pendiente',
    'cumplida'    => 'Cumplida',
    'no_cumplida' => 'No cumplida',
];

function h($texto)
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}
