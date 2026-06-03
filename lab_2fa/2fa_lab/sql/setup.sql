-- ============================================================
--  LABORATORIO 2FA - UNIVERSIDAD TECNOLÓGICA DE PANAMÁ
--  Ejecutar como superusuario SOLO para crear la BD y usuarios
-- ============================================================

-- 1) Crear la base de datos
CREATE DATABASE IF NOT EXISTS lab_2fa
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
    define('DB_PASS', '');

-- 2) Crear usuario con PRIVILEGIOS MÍNIMOS (NO superusuario)
--    Solo puede operar sobre lab_2fa
CREATE USER IF NOT EXISTS 'lab2fa_user'@'localhost' IDENTIFIED BY 'Lab2FA_P@ss2026';

-- Otorgar solo lo estrictamente necesario
GRANT SELECT, INSERT, UPDATE, DELETE ON lab_2fa.* TO 'lab2fa_user'@'localhost';

FLUSH PRIVILEGES;

-- 3) Verificar privilegios concedidos (mostrar en pantalla)
SHOW GRANTS FOR 'lab2fa_user'@'localhost';

-- ============================================================
--  TABLAS (ejecutar conectado como lab2fa_user o superusuario)
-- ============================================================

USE lab_2fa;

-- Tabla 1: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(80)     NOT NULL,
    apellido      VARCHAR(80)     NOT NULL,
    sexo          ENUM('M','F','Otro') NOT NULL,
    usuario       VARCHAR(50)     NOT NULL UNIQUE,
    correo        VARCHAR(150)    NOT NULL UNIQUE,
    hash          VARCHAR(255)    NOT NULL,          -- password_hash()
    secreto_2fa   VARCHAR(64)     NOT NULL,          -- TOTP secret
    fecha_sistema DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_correo  (correo),
    INDEX idx_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla 2: sesiones_2fa
CREATE TABLE IF NOT EXISTS sesiones_2fa (
    id            INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    usuario_id    INT UNSIGNED    NOT NULL,
    token_fase1   VARCHAR(128)    NOT NULL,   -- sesión tras login exitoso
    token_fase2   VARCHAR(128)    NULL,        -- sesión tras 2FA exitoso
    ip            VARCHAR(45)     NOT NULL,
    creado_en     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fase          TINYINT(1)      NOT NULL DEFAULT 1, -- 1=esperando 2FA, 2=completo
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token1 (token_fase1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
