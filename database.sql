CREATE DATABASE IF NOT EXISTS registro_retro_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE registro_retro_db;

CREATE TABLE IF NOT EXISTS sprints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS retro_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sprint_id INT NOT NULL,
    categoria ENUM('logro','impedimento','accion') NOT NULL,
    descripcion TEXT NOT NULL,
    estado ENUM('pendiente','cumplida','no_cumplida') NOT NULL DEFAULT 'pendiente',
    FOREIGN KEY (sprint_id) REFERENCES sprints(id) ON DELETE CASCADE
);

INSERT INTO sprints (nombre, fecha_inicio, fecha_fin) VALUES
('Sprint 1', '2026-09-01', '2026-09-14'),
('Sprint 2', '2026-09-15', '2026-09-28');

INSERT INTO retro_items (sprint_id, categoria, descripcion) VALUES
(1, 'logro', 'Se completaron todas las historias de usuario.'),
(1, 'logro', 'Buena comunicación en las dailies.'),
(1, 'impedimento', 'Poca claridad en los criterios de aceptación.'),
(1, 'accion', 'Refinar el backlog antes de planificar el sprint.'),
(1, 'accion', 'Documentar el procedimiento de despliegue.');
