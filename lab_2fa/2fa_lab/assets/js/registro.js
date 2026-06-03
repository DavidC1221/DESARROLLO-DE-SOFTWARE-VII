/**
 * assets/js/registro.js
 * Validaciones del lado del FRONTEND:
 *  - Verificación en tiempo real de usuario y correo duplicados (AJAX)
 *  - Validación de contraseñas coincidentes
 *  - Bloqueo del submit si hay errores
 */

document.addEventListener('DOMContentLoaded', () => {

  const form      = document.getElementById('formRegistro');
  if (!form) return;

  const usuario   = document.getElementById('usuario');
  const correo    = document.getElementById('correo');
  const password  = document.getElementById('password');
  const password2 = document.getElementById('password2');

  const usuarioHint = document.getElementById('usuario-hint');
  const correoHint  = document.getElementById('correo-hint');

  let usuarioOk = true;
  let correoOk  = true;

  // ── Utilidades ──────────────────────────────────────────────

  function debounce(fn, delay = 500) {
    let timer;
    return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
  }

  function setHint(el, msg, isError) {
    el.textContent = msg;
    el.className   = 'field-hint ' + (isError ? 'taken' : 'ok');
  }

  async function checkDuplicate(tipo, valor, hintEl) {
    if (!valor || valor.length < 3) { hintEl.textContent = ''; return true; }
    try {
      const res  = await fetch(`check_duplicate.php?tipo=${tipo}&valor=${encodeURIComponent(valor)}`);
      const data = await res.json();
      if (data.existe) {
        setHint(hintEl, '✗ ' + data.mensaje, true);
        return false;
      } else {
        setHint(hintEl, '✓ Disponible', false);
        return true;
      }
    } catch {
      hintEl.textContent = '';
      return true;
    }
  }

  // ── Listeners ───────────────────────────────────────────────

  const checkUsuario = debounce(async () => {
    usuarioOk = await checkDuplicate('usuario', usuario.value.trim(), usuarioHint);
  });

  const checkCorreo = debounce(async () => {
    correoOk = await checkDuplicate('correo', correo.value.trim(), correoHint);
  });

  usuario.addEventListener('input', checkUsuario);
  correo.addEventListener('input', checkCorreo);

  // Validación visual de contraseñas coincidentes
  password2.addEventListener('input', () => {
    if (password2.value && password.value !== password2.value) {
      password2.style.borderColor = 'var(--error)';
    } else {
      password2.style.borderColor = '';
    }
  });

  // ── Bloquear submit si hay errores frontend ──────────────────
  form.addEventListener('submit', (e) => {
    let valid = true;

    // Contraseñas
    if (password.value !== password2.value) {
      password2.style.borderColor = 'var(--error)';
      valid = false;
    }

    // Duplicados
    if (!usuarioOk) {
      setHint(usuarioHint, '✗ Este usuario ya está en uso.', true);
      valid = false;
    }
    if (!correoOk) {
      setHint(correoHint, '✗ Este correo ya está registrado.', true);
      valid = false;
    }

    if (!valid) {
      e.preventDefault();
      // Scroll al primer error
      const firstError = form.querySelector('.taken, [style*="error"]');
      if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

});
