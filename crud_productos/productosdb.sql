-- ============================================================
-- Script SQL para la base de datos del proyecto CRUD Productos
-- Ejecutar en phpMyAdmin o desde la terminal MySQL
-- ============================================================

-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS productosdb
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

-- 2. Seleccionar la base de datos
USE productosdb;

-- 3. Crear la tabla productos
CREATE TABLE IF NOT EXISTS productos (
    id       INT          AUTO_INCREMENT PRIMARY KEY,
    codigo   VARCHAR(20)  NOT NULL,
    producto VARCHAR(100) NOT NULL,
    precio   DECIMAL(10,2) NOT NULL,
    cantidad INT          NOT NULL
);

-- 4. Datos de prueba (opcional)
INSERT INTO productos (codigo, producto, precio, cantidad) VALUES
    ('PROD-001', 'Laptop Lenovo IdeaPad',   599.99, 10),
    ('PROD-002', 'Monitor Samsung 24"',      189.50,  5),
    ('PROD-003', 'Teclado Mecánico Logitech', 79.99, 15);
