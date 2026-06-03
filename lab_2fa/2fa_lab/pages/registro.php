<?php
/**
 * pages/registro.php
 * Formulario de registro con validación frontend + backend,
 * sanitización, generación de secreto 2FA y protección CSRF.
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Sanitizer.php';
require_once __DIR__ . '/../classes/RegistroForm.php';
require_once __DIR__ . '/../classes/TOTP.php';
require_once __DIR__ . '/../classes/CSRF.php';

$errores  = [];
$exito    = false;
$qrUrl    = '';
$secreto  = '';

// ── Procesar POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Validar CSRF
    CSRF::verificarOFallar($_POST['csrf_token'] ?? '');

    // 2. Cargar y validar el formulario
    $form = new RegistroForm();
    $form->cargarDesdePost($_POST);

    if ($form->validar()) {
        $datos   = $form->getDatos();
        $hash    = $form->hashearContrasena();
        $secreto = TOTP::generarSecreto();
        $secreto = Sanitizer::sanitize2FASecret($secreto);

        // 3. Insertar en la BD con prepared statement
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (nombre, apellido, sexo, usuario, correo, hash, secreto_2fa)
                 VALUES (:nombre, :apellido, :sexo, :usuario, :correo, :hash, :secreto)'
            );
            $stmt->execute([
                ':nombre'   => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':sexo'     => $datos['sexo'],
                ':usuario'  => $datos['usuario'],
                ':correo'   => $datos['correo'],
                ':hash'     => $hash,
                ':secreto'  => $secreto,
            ]);

            // 4. Generar URL del QR
            $uri   = TOTP::generarURI($secreto, $datos['correo']);
            $qrUrl = TOTP::urlQR($uri, 220);

            // 5. Guardar en sesión que el registro fue exitoso (fase 0)
            $_SESSION['reg_usuario'] = $datos['usuario'];
            $_SESSION['reg_correo']  = $datos['correo'];
            $_SESSION['reg_fase']    = 'qr_generado';

            $exito = true;
        } catch (PDOException $e) {
            $errores['db'] = 'Error al guardar el registro. Intenta de nuevo.';
        }
    } else {
        $errores = $form->getErrores();
    }
}

$csrfCampo = CSRF::campoOculto();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro — Lab 2FA</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="page-registro">

<div class="auth-wrapper">
  <div class="auth-card">

    <div class="auth-header">
      <span class="auth-logo">🔐</span>
      <h1>Crear Cuenta</h1>
      <p>Laboratorio de Autenticación 2FA</p>
    </div>

    <?php if ($exito): ?>
    <!-- ── QR generado ── -->
    <div class="qr-section">
      <h2>¡Registro exitoso!</h2>
      <p>Escanea el código QR con <strong>Google Authenticator</strong> o <strong>Authy</strong>.</p>
      <div class="qr-box">
        <img src="<?= htmlspecialchars($qrUrl) ?>" alt="Código QR 2FA" width="220" height="220">
      </div>
      <p class="secreto-label">Secreto manual: <code><?= htmlspecialchars($secreto) ?></code></p>
      <a href="login.php" class="btn btn-primary">Ir al Login →</a>
    </div>

    <?php else: ?>
    <!-- ── Formulario ── -->
    <?php if (!empty($errores['db'])): ?>
      <div class="alert alert-error"><?= htmlspecialchars($errores['db']) ?></div>
    <?php endif; ?>

    <form id="formRegistro" method="POST" action="registro.php" novalidate>
      <?= $csrfCampo ?>

      <div class="form-row">
        <div class="form-group <?= isset($errores['nombre']) ? 'has-error' : '' ?>">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre"
                 value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                 placeholder="Tu nombre" required minlength="2" maxlength="80">
          <?php if (isset($errores['nombre'])): ?>
            <span class="error-msg"><?= htmlspecialchars($errores['nombre']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errores['apellido']) ? 'has-error' : '' ?>">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="apellido"
                 value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>"
                 placeholder="Tu apellido" required minlength="2" maxlength="80">
          <?php if (isset($errores['apellido'])): ?>
            <span class="error-msg"><?= htmlspecialchars($errores['apellido']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group <?= isset($errores['sexo']) ? 'has-error' : '' ?>">
          <label for="sexo">Sexo</label>
          <select id="sexo" name="sexo" required>
            <option value="">-- Seleccionar --</option>
            <option value="M"    <?= (($_POST['sexo'] ?? '') === 'M')    ? 'selected' : '' ?>>Masculino</option>
            <option value="F"    <?= (($_POST['sexo'] ?? '') === 'F')    ? 'selected' : '' ?>>Femenino</option>
            <option value="Otro" <?= (($_POST['sexo'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
          </select>
          <?php if (isset($errores['sexo'])): ?>
            <span class="error-msg"><?= htmlspecialchars($errores['sexo']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errores['usuario']) ? 'has-error' : '' ?>">
          <label for="usuario">Usuario</label>
          <input type="text" id="usuario" name="usuario"
                 value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                 placeholder="nombre_usuario" required minlength="3" maxlength="50"
                 pattern="[\w]+" title="Solo letras, números y guion bajo">
          <span class="field-hint" id="usuario-hint"></span>
          <?php if (isset($errores['usuario'])): ?>
            <span class="error-msg"><?= htmlspecialchars($errores['usuario']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group <?= isset($errores['correo']) ? 'has-error' : '' ?>">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo"
               value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
               placeholder="usuario@ejemplo.com" required maxlength="150">
        <span class="field-hint" id="correo-hint"></span>
        <?php if (isset($errores['correo'])): ?>
          <span class="error-msg"><?= htmlspecialchars($errores['correo']) ?></span>
        <?php endif; ?>
      </div>

      <div class="form-row">
        <div class="form-group <?= isset($errores['password']) ? 'has-error' : '' ?>">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password"
                 placeholder="Mínimo 8 caracteres" required minlength="8">
          <?php if (isset($errores['password'])): ?>
            <span class="error-msg"><?= htmlspecialchars($errores['password']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errores['password2']) ? 'has-error' : '' ?>">
          <label for="password2">Confirmar contraseña</label>
          <input type="password" id="password2" name="password2"
                 placeholder="Repetir contraseña" required minlength="8">
          <?php if (isset($errores['password2'])): ?>
            <span class="error-msg"><?= htmlspecialchars($errores['password2']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full">Crear Cuenta</button>
    </form>

    <p class="auth-switch">¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
    <?php endif; ?>

  </div>
</div>

<script src="../assets/js/registro.js"></script>
</body>
</html>
