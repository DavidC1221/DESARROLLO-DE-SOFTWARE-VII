<?php
/**
 * pages/dashboard.php
 * Solo accesible si la sesión está en FASE 2 (2FA completado).
 * Muestra confirmación de sesión y herramientas del laboratorio.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

// Proteger la ruta: requiere fase 2
if (empty($_SESSION['autenticado']) || $_SESSION['login_fase'] !== 2) {
    header('Location: login.php');
    exit;
}

// Obtener datos del usuario
$pdo  = getDB();
$stmt = $pdo->prepare('SELECT nombre, apellido, usuario, correo, fecha_sistema FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Lab 2FA</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="page-dashboard">

<header class="dash-header">
  <div class="dash-header-inner">
    <span class="dash-logo">🔐 Lab 2FA</span>
    <span class="dash-user">👤 <?= htmlspecialchars($user['usuario']) ?></span>
    <a href="logout.php" class="btn btn-outline btn-sm">Cerrar sesión</a>
  </div>
</header>

<main class="dash-main">

  <div class="session-banner">
    <div class="session-indicator phase-2">
      <span class="dot"></span>
      <strong>Sesión Fase 2 — 2FA Verificado</strong>
    </div>
    <p>Autenticación de dos factores completada exitosamente.</p>
  </div>

  <div class="dash-grid">

    <!-- Info del usuario -->
    <div class="dash-card">
      <h3>👤 Datos del Usuario</h3>
      <table class="info-table">
        <tr><th>Nombre</th>    <td><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></td></tr>
        <tr><th>Usuario</th>   <td><?= htmlspecialchars($user['usuario']) ?></td></tr>
        <tr><th>Correo</th>    <td><?= htmlspecialchars($user['correo']) ?></td></tr>
        <tr><th>Registro</th>  <td><?= htmlspecialchars($user['fecha_sistema']) ?></td></tr>
      </table>
    </div>

    <!-- Estado de la sesión -->
    <div class="dash-card">
      <h3>🔒 Estado de la Sesión</h3>
      <table class="info-table">
        <tr><th>Fase actual</th>  <td><span class="badge badge-success">Fase 2 — Completa</span></td></tr>
        <tr><th>ID sesión</th>   <td><code><?= substr(session_id(), 0, 16) ?>…</code></td></tr>
        <tr><th>Usuario ID</th>  <td><?= (int) $_SESSION['usuario_id'] ?></td></tr>
        <tr><th>Hora</th>        <td><?= date('Y-m-d H:i:s') ?></td></tr>
      </table>
    </div>

    <!-- Herramientas del lab -->
    <div class="dash-card">
      <h3>🛠 Herramientas del Laboratorio</h3>
      <div class="tool-links">
        <a href="hash_tool.php"     class="tool-btn">🔑 Generador / Validador de Hash</a>
        <a href="db_preview.php"    class="tool-btn">🗄 Ver tablas de la BD</a>
        <a href="privileges.php"    class="tool-btn">🛡 Privilegios del usuario DB</a>
      </div>
    </div>

  </div>
</main>

</body>
</html>
