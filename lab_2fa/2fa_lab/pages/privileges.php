<?php
/**
 * pages/privileges.php
 * Muestra los privilegios del usuario de BD y verifica las tablas.
 * Cumple: "Mostrar en pantalla los privilegios de los usuarios creados"
 *         "Mostrar el comando que permite ver los privilegios concedidos"
 *         "Verificar que las tablas registren los datos"
 */

session_start();
if (empty($_SESSION['autenticado'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';

$pdo = getDB();

// Ejecutar SHOW GRANTS para el usuario de la app
$grants     = $pdo->query("SHOW GRANTS FOR CURRENT_USER()")->fetchAll(PDO::FETCH_COLUMN);
$tablas     = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$countUsers = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$countSess  = $pdo->query("SELECT COUNT(*) FROM sesiones_2fa")->fetchColumn();

// Últimos 5 usuarios registrados
$ultimos = $pdo->query("SELECT id, nombre, apellido, usuario, correo, fecha_sistema FROM usuarios ORDER BY id DESC LIMIT 5")->fetchAll();

// Últimas 5 sesiones
$sesiones = $pdo->query("SELECT id, usuario_id, fase, ip, creado_en FROM sesiones_2fa ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privilegios DB — Lab 2FA</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="page-dashboard">

<header class="dash-header">
  <div class="dash-header-inner">
    <a href="dashboard.php" class="dash-logo">← Dashboard</a>
    <span>🛡 Privilegios y Base de Datos</span>
  </div>
</header>

<main class="dash-main">

  <!-- Privilegios del usuario -->
  <div class="dash-card">
    <h3>🛡 Privilegios del Usuario de Base de Datos</h3>
    <p class="hint">Comando utilizado: <code>SHOW GRANTS FOR CURRENT_USER()</code></p>
    <div class="grants-box">
      <?php foreach ($grants as $grant): ?>
        <code class="grant-line"><?= htmlspecialchars($grant) ?></code>
      <?php endforeach; ?>
    </div>
    <div class="info-note">
      ⚠️ El usuario <strong>lab2fa_user</strong> tiene solo
      <strong>SELECT, INSERT, UPDATE, DELETE</strong> sobre <em>lab_2fa</em>.
      <br>No tiene permisos de DROP, CREATE, ALTER, ni acceso a otras bases de datos.
    </div>
  </div>

  <!-- Tablas existentes -->
  <div class="dash-card">
    <h3>🗄 Tablas en la Base de Datos</h3>
    <div class="tables-summary">
      <?php foreach ($tablas as $tabla): ?>
        <span class="table-badge">📋 <?= htmlspecialchars($tabla) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="counters">
      <div class="counter-box">
        <span class="counter-num"><?= (int)$countUsers ?></span>
        <span class="counter-label">Usuarios registrados</span>
      </div>
      <div class="counter-box">
        <span class="counter-num"><?= (int)$countSess ?></span>
        <span class="counter-label">Sesiones 2FA</span>
      </div>
    </div>
  </div>

  <!-- Tabla usuarios -->
  <div class="dash-card">
    <h3>👥 Tabla <code>usuarios</code> (últimos 5)</h3>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Usuario</th><th>Correo</th><th>Registrado</th></tr>
        </thead>
        <tbody>
          <?php foreach ($ultimos as $row): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['apellido']) ?></td>
            <td><?= htmlspecialchars($row['usuario']) ?></td>
            <td><?= htmlspecialchars($row['correo']) ?></td>
            <td><?= htmlspecialchars($row['fecha_sistema']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($ultimos)): ?>
            <tr><td colspan="6" class="empty">Sin registros aún.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tabla sesiones_2fa -->
  <div class="dash-card">
    <h3>🔒 Tabla <code>sesiones_2fa</code> (últimas 5)</h3>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr><th>ID</th><th>Usuario ID</th><th>Fase</th><th>IP</th><th>Creada</th></tr>
        </thead>
        <tbody>
          <?php foreach ($sesiones as $row): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= (int)$row['usuario_id'] ?></td>
            <td>
              <span class="badge <?= $row['fase'] == 2 ? 'badge-success' : 'badge-warn' ?>">
                Fase <?= (int)$row['fase'] ?>
              </span>
            </td>
            <td><?= htmlspecialchars($row['ip']) ?></td>
            <td><?= htmlspecialchars($row['creado_en']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($sesiones)): ?>
            <tr><td colspan="5" class="empty">Sin sesiones registradas aún.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>
</body>
</html>
