<?php

class Sprint
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    public function listar()
    {
        $sql = "SELECT s.*,
                  (SELECT COUNT(*) FROM retro_items WHERE sprint_id = s.id AND categoria = 'logro') AS logros,
                  (SELECT COUNT(*) FROM retro_items WHERE sprint_id = s.id AND categoria = 'impedimento') AS impedimentos,
                  (SELECT COUNT(*) FROM retro_items WHERE sprint_id = s.id AND categoria = 'accion') AS acciones
                FROM sprints s
                ORDER BY s.fecha_inicio DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function buscar($id)
    {
        $st = $this->db->prepare("SELECT * FROM sprints WHERE id = ?");
        $st->execute([$id]);
        return $st->fetch();
    }

    public function anterior($id)
    {
        $sql = "SELECT * FROM sprints
                WHERE fecha_inicio < (SELECT fecha_inicio FROM sprints WHERE id = ?)
                ORDER BY fecha_inicio DESC
                LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute([$id]);
        return $st->fetch();
    }

    public function crear($nombre, $inicio, $fin)
    {
        $st = $this->db->prepare("INSERT INTO sprints (nombre, fecha_inicio, fecha_fin) VALUES (?, ?, ?)");
        $st->execute([$nombre, $inicio, $fin]);
        return $this->db->lastInsertId();
    }

    public function actualizar($id, $nombre, $inicio, $fin)
    {
        $st = $this->db->prepare("UPDATE sprints SET nombre = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ?");
        $st->execute([$nombre, $inicio, $fin, $id]);
    }

    public function eliminar($id)
    {
        $st = $this->db->prepare("DELETE FROM sprints WHERE id = ?");
        $st->execute([$id]);
    }
}
