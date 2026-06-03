<?php
/**
 * config/database.php
 * Conexión a la base de datos usando el usuario con privilegios mínimos.
 * NUNCA usar el superusuario en la aplicación.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'lab_2fa');
define('DB_USER', 'lab2fa_user');       // usuario con privilegios mínimos
define('DB_PASS', 'Lab2FA_P@ss2026');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En producción nunca mostrar detalles del error
            die(json_encode(['error' => 'No se pudo conectar a la base de datos.']));
        }
    }
    return $pdo;
}
