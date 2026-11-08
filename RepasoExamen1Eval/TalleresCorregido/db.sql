CREATE DATABASE IF NOT EXISTS sim02_talleres
DEFAULT CHARACTER SET utf8mb4
DEFAULT COLLATE utf8mb4_general_ci;
USE sim02_talleres;
CREATE TABLE inscripciones (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
email VARCHAR(120) NOT NULL UNIQUE,
taller VARCHAR(20) NOT NULL,
turno VARCHAR(10) NOT NULL,
plazas INT NOT NULL,
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO inscripciones
(nombre, email, taller, turno, plazas) VALUES
('Ane Ruiz', 'ane@example.com', 'php', 'manana', 2),
('Unai Gil', 'unai@example.com', 'css', 'tarde', 1),
('Leire Soto', 'leire@example.com', 'php', 'tarde', 1);