-- Base de datos para el proyecto iTECH
CREATE DATABASE IF NOT EXISTS itech CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE itech;

CREATE TABLE IF NOT EXISTS inscriptor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identidad VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    sexo VARCHAR(15) NOT NULL,
    nacionalidad VARCHAR(50) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
