<?php
/**
 * pages/hash_tool.php
 * Interfaz para generar y validar hashes con password_hash() / password_verify().
 * Requiere sesión fase 2.
 */

session_start();
if (empty($_SESSION['autenticado'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../classes/CSRF.php';

$hash      = '';
$resultado = '';
$accion    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::verificarOFallar($_POST['csrf_token'] ?? '');
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'generar') {
        $plain = $_POST['plain'] ?? '';
        if ($plain) {
            $hash = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
        }
    } elseif ($accion === 'validar') {
        $plain    = $_POST['plain'] ?? '';
        $hashTest = $_POST['hash_test'] ?? '';
        if ($plain && $hashTest) {
            $resultado = password_verify($plain, $hashTest)
                ? '✅ La contraseña COINCIDE con el hash.'
                : '❌ La contraseña NO coincide con el hash.';
        }
    }
}

$csrf = CSRF::campoOculto();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generador de Hash — Lab 2FA</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="page-dashboard">

<header class="dash-header">
  <div class="dash-header-inner">
    <a href="dashboard.php" class="dash-logo">← Dashboard</a>
    <span>🔑 Herramienta de Hash</span>
  </div>
</header>

<main class="dash-main">
<div class="hash-grid">

  <!-- Generar hash -->
  <div class="dash-card">
    <h3>🔑 Generar Hash (BCRYPT)</h3>
    <form method="POST" action="hash_tool.php">
      <?= $csrf ?>
      <input type="hidden" name="accion" value="generar">
      <div class="form-group">
        <label for="plain_gen">Contraseña en texto plano</label>
        <input type="text" id="plain_gen" name="plain" placeholder="Escribe una contraseña..." required>
      </div>
      <button type="submit" class="btn btn-primary">Generar Hash</button>
    </form>

    <?php if ($accion === 'generar' && $hash): ?>
    <div class="hash-result">
      <label>Hash generado:</label>
      <code id="hashOutput"><?= htmlspecialchars($hash) ?></code>
      <button onclick="navigator.clipboard.writeText(document.getElementById('hashOutput').textContent)"
              class="btn btn-outline btn-sm">📋 Copiar</button>
    </div>
    <?php endif; ?>
  </div>

  <!-- Validar hash -->
  <div class="dash-card">
    <h3>✅ Validar Hash</h3>
    <form method="POST" action="hash_tool.php">
      <?= $csrf ?>
      <input type="hidden" name="accion" value="validar">
      <div class="form-group">
        <label for="plain_val">Contraseña en texto plano</label>
        <input type="text" id="plain_val" name="plain" placeholder="Contraseña original..." required>
      </div>
      <div class="form-group">
        <label for="hash_test">Hash a verificar</label>
        <textarea id="hash_test" name="hash_test" rows="3"
                  placeholder="$2y$12$..." required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Validar</button>
    </form>

    <?php if ($accion === 'validar' && $resultado): ?>
    <div class="hash-result <?= str_contains($resultado, 'COINCIDE') ? 'success' : 'error' ?>">
      <?= htmlspecialchars($resultado) ?>
    </div>
    <?php endif; ?>
  </div>

</div>
</main>
</body>
</html>
