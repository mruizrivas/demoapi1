-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS USUARIOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuenta VARCHAR(80) NOT NULL UNIQUE,
    clave_acceso VARCHAR(32) NOT NULL
);

-- Crear tabla de medidas
CREATE TABLE IF NOT EXISTS MEDIDAS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zona INT NOT NULL,
    fecha DATETIME NOT NULL,
    tension FLOAT,
    peso FLOAT,
    estructura VARCHAR(30),
    observaciones VARCHAR(80)
);

-- Insertar usuarios de prueba
INSERT INTO USUARIOS (cuenta, clave_acceso) VALUES
('usuario1@example.com', MD5('1234')),
('usuario2@example.com', MD5('abcd1234')),
('usuario3@example.com', MD5('testpass'));

-- Insertar medidas de ejemplo
INSERT INTO MEDIDAS (zona, fecha, tension, peso, estructura, observaciones) VALUES
(1, '2025-04-20 08:00:00', 220.5, 100.0, 'Estructura A', 'Todo OK'),
(2, '2025-04-21 09:30:00', 210.2, 95.5, 'Estructura B', 'Peso bajo'),
(1, '2025-04-22 10:15:00', 230.8, 105.0, 'Estructura A', 'Tensión alta'),
(3, '2025-04-23 11:00:00', 200.1, 90.0, 'Estructura C', 'Sin observaciones');
