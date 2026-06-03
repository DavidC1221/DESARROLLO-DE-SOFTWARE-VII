<?php
/**
 * pages/login.php
 * Login en 2 fases:
 *   Fase 1 → validar usuario + contraseña → crear sesión fase1
 *   Fase 2 → validar código TOTP        → crear sesión fase2 (confirmación)
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/TOTP.php';
require_once __DIR__ . '/../classes/CSRF.php';
require_once __DIR__ . '/../classes/Sanitizer.php';

$error = '';

// ── FASE 1: verificar credenciales ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fase']) && $_POST['fase'] === '1') {

    CSRF::verificarOFallar($_POST['csrf_token'] ?? '');

    $usuario  = Sanitizer::sanitizeUsername($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario && $password) {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id, hash, secreto_2fa, correo FROM usuarios WHERE usuario = :u LIMIT 1');
        $stmt->execute([':u' => $usuario]);
        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['hash'])) {
            // Credenciales correctas → guardar en sesión y pedir 2FA
            $_SESSION['login_usuario_id'] = $row['id'];
            $_SESSION['login_secreto']    = $row['secreto_2fa'];
            $_SESSION['login_correo']     = $row['correo'];
            $_SESSION['login_fase']       = 1;    // sesión FASE 1
            // Sin redirect: mostrar formulario 2FA en la misma página
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Completa todos los campos.';
    }
}

// ── FASE 2: verificar código TOTP ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fase']) && $_POST['fase'] === '2') {

    CSRF::verificarOFallar($_POST['csrf_token'] ?? '');

    if (empty($_SESSION['login_fase']) || $_SESSION['login_fase'] !== 1) {
        header('Location: login.php');
        exit;
    }

    $codigo  = trim($_POST['codigo_2fa'] ?? '');
    $secreto = $_SESSION['login_secreto'] ?? '';

    if (TOTP::validarCodigo($secreto, $codigo)) {
        // Código correcto → elevar a SESIÓN FASE 2
        session_regenerate_id(true);
        $_SESSION['login_fase']         = 2;      // sesión de confirmación 2FA
        $_SESSION['autenticado']        = true;
        $_SESSION['usuario_id']         = $_SESSION['login_usuario_id'];
        // Limpiar datos temporales de fase 1
        unset($_SESSION['login_secreto'], $_SESSION['login_usuario_id']);
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Código 2FA incorrecto o expirado. Inténtalo de nuevo.';
    }
}

$csrfCampo = CSRF::campoOculto();
$enFase2   = !empty($_SESSION['login_fase']) && $_SESSION['login_fase'] === 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Lab 2FA</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="page-login">

<div class="auth-wrapper">
  <div class="auth-card">

    <div class="auth-header">
      <span class="auth-logo"><?= $enFase2 ? '📱' : '🔑' ?></span>
      <h1><?= $enFase2 ? 'Verificación 2FA' : 'Iniciar Sesión' ?></h1>
      <p><?= $enFase2 ? 'Ingresa el código de tu app autenticadora' : 'Laboratorio de Autenticación 2FA' ?></p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$enFase2): ?>
    <!-- ── Formulario fase 1 ── -->
    <form method="POST" action="login.php">
      <?= $csrfCampo ?>
      <input type="hidden" name="fase" value="1">

      <div class="form-group">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="nombre_usuario" required autofocus>
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Tu contraseña" required>
      </div>

      <button type="submit" class="btn btn-primary btn-full">Entrar</button>
    </form>
    <p class="auth-switch">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>

    <?php else: ?>
    <!-- ── Formulario fase 2 (TOTP) ── -->
    <div class="fase-badge">✅ Credenciales verificadas — Sesión Fase 1 activa</div>
    <form method="POST" action="login.php">
      <?= $csrfCampo ?>
      <input type="hidden" name="fase" value="2">

      <div class="form-group totp-group">
        <label for="codigo_2fa">Código de 6 dígitos</label>
        <input type="text" id="codigo_2fa" name="codigo_2fa"
               placeholder="000000" maxlength="6" pattern="\d{6}"
               inputmode="numeric" autocomplete="one-time-code" required autofocus>
      </div>

      <button type="submit" class="btn btn-primary btn-full">Verificar Código</button>
    </form>
    <p class="auth-switch"><a href="login.php?reset=1">← Volver al login</a></p>
    <?php endif; ?>

  </div>
</div>

<?php
// Limpiar sesión si se pide reset
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
</body>
</html>
