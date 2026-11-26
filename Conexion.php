<?php

class Conexion
{

    public static function conectar()
    {
        $host    = 'localhost';
        $bd      = 'registro_retro_db';
        $usuario = 'root';
        $clave   = '';

        $pdo = new PDO("mysql:host=$host;dbname=$bd;charset=utf8mb4", $usuario, $clave);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }
}
