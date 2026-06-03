<?php
/**
 * pages/db_preview.php
 * Vista rápida de las dos tablas de la BD.
 * Requiere sesión activa.
 */
session_start();
if (empty($_SESSION['autenticado'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';

$pdo     = getDB();
$users   = $pdo->query("SELECT id, nombre, apellido, sexo, usuario, correo, fecha_sistema FROM usuarios ORDER BY id DESC")->fetchAll();
$sesiones = $pdo->query("SELECT s.id, u.usuario, s.fase, s.ip, s.creado_en FROM sesiones_2fa s JOIN usuarios u ON u.id = s.usuario_id ORDER BY s.id DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Base de Datos — Lab 2FA</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="page-dashboard">
<header class="dash-header">
  <div class="dash-header-inner">
    <a href="dashboard.php" class="dash-logo">← Dashboard</a>
    <span>🗄 Tablas de la Base de Datos</span>
  </div>
</header>
<main class="dash-main">

  <div class="dash-card" style="margin-bottom:1.25rem">
    <h3>👥 Tabla <code>usuarios</code> — <?= count($users) ?> registro(s)</h3>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Sexo</th><th>Usuario</th><th>Correo</th><th>Fecha Sistema</th></tr></thead>
        <tbody>
          <?php foreach ($users as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['nombre']) ?></td>
            <td><?= htmlspecialchars($r['apellido']) ?></td>
            <td><?= htmlspecialchars($r['sexo']) ?></td>
            <td><?= htmlspecialchars($r['usuario']) ?></td>
            <td><?= htmlspecialchars($r['correo']) ?></td>
            <td><?= htmlspecialchars($r['fecha_sistema']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$users): ?><tr><td colspan="7" class="empty">Sin registros.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="dash-card">
    <h3>🔒 Tabla <code>sesiones_2fa</code> — últimas 20</h3>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>ID</th><th>Usuario</th><th>Fase</th><th>IP</th><th>Creada</th></tr></thead>
        <tbody>
          <?php foreach ($sesiones as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['usuario']) ?></td>
            <td><span class="badge <?= $r['fase']==2?'badge-success':'badge-warn' ?>">Fase <?= (int)$r['fase'] ?></span></td>
            <td><?= htmlspecialchars($r['ip']) ?></td>
            <td><?= htmlspecialchars($r['creado_en']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$sesiones): ?><tr><td colspan="5" class="empty">Sin sesiones.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>
</body>
</html>
