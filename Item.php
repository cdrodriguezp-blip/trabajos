<?php

class Item
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function listar($sprint_id, $categoria)
    {
        $st = $this->db->prepare("SELECT * FROM retro_items WHERE sprint_id = ? AND categoria = ? ORDER BY id");
        $st->execute([$sprint_id, $categoria]);
        return $st->fetchAll();
    }

    public function buscar($id)
    {
        $st = $this->db->prepare("SELECT * FROM retro_items WHERE id = ?");
        $st->execute([$id]);
        return $st->fetch();
    }

    public function crear($sprint_id, $categoria, $descripcion)
    {
        $st = $this->db->prepare("INSERT INTO retro_items (sprint_id, categoria, descripcion) VALUES (?, ?, ?)");
        $st->execute([$sprint_id, $categoria, $descripcion]);
    }

    public function actualizar($id, $descripcion, $estado)
    {
        $st = $this->db->prepare("UPDATE retro_items SET descripcion = ?, estado = ? WHERE id = ?");
        $st->execute([$descripcion, $estado, $id]);
    }

    public function cambiarEstado($id, $estado)
    {
        $st = $this->db->prepare("UPDATE retro_items SET estado = ? WHERE id = ?");
        $st->execute([$estado, $id]);
    }

    public function eliminar($id)
    {
        $st = $this->db->prepare("DELETE FROM retro_items WHERE id = ?");
        $st->execute([$id]);
    }
}
