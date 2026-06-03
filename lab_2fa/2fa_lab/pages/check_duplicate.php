<?php
/**
 * pages/check_duplicate.php
 * Endpoint AJAX para validación en tiempo real (frontend).
 * Verifica si usuario o correo ya existen en la BD.
 * Cumple: "Validaciones del lado del frontend" (via AJAX).
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Sanitizer.php';

$tipo  = $_GET['tipo']  ?? '';
$valor = $_GET['valor'] ?? '';

if (!in_array($tipo, ['usuario', 'correo'], true) || empty($valor)) {
    echo json_encode(['existe' => false, 'mensaje' => '']);
    exit;
}

if ($tipo === 'usuario') {
    $valor = Sanitizer::sanitizeUsername($valor);
    $campo = 'usuario';
} else {
    $valor = Sanitizer::sanitizeEmail($valor);
    $campo = 'correo';
}

if (empty($valor)) {
    echo json_encode(['existe' => false, 'mensaje' => 'Valor no válido.']);
    exit;
}

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE {$campo} = :v");
$stmt->execute([':v' => $valor]);
$count = (int) $stmt->fetchColumn();

echo json_encode([
    'existe'  => $count > 0,
    'mensaje' => $count > 0 ? "Este {$campo} ya está en uso." : '',
]);
