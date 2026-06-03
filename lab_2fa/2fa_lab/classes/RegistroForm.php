<?php
/**
 * classes/RegistroForm.php
 * Clase para el Formulario de Registro.
 * Cada método tiene responsabilidad mínima (Single Responsibility).
 */

require_once __DIR__ . '/Sanitizer.php';
require_once __DIR__ . '/../config/database.php';

class RegistroForm
{
    private array $datos  = [];
    private array $errores = [];

    // ── Getters ──────────────────────────────────────────────

    public function getDatos(): array   { return $this->datos;   }
    public function getErrores(): array { return $this->errores; }
    public function esValido(): bool    { return empty($this->errores); }

    // ── Carga y sanitización ─────────────────────────────────

    /**
     * Carga los datos del POST y los sanitiza.
     */
    public function cargarDesdePost(array $post): void
    {
        $this->datos = [
            'nombre'    => Sanitizer::sanitizeText($post['nombre']   ?? ''),
            'apellido'  => Sanitizer::sanitizeText($post['apellido'] ?? ''),
            'sexo'      => Sanitizer::sanitizeEnum($post['sexo'] ?? '', ['M', 'F', 'Otro']),
            'usuario'   => Sanitizer::sanitizeUsername($post['usuario'] ?? ''),
            'correo'    => Sanitizer::sanitizeEmail($post['correo']  ?? ''),
            'password'  => $post['password']  ?? '',   // se valida sin sanitizar
            'password2' => $post['password2'] ?? '',
        ];
    }

    // ── Validaciones individuales (cada una con su responsabilidad) ──

    /**
     * Valida que los campos requeridos no estén vacíos.
     */
    public function validarCamposRequeridos(): void
    {
        $requeridos = ['nombre', 'apellido', 'sexo', 'usuario', 'correo', 'password'];
        foreach ($requeridos as $campo) {
            if (empty($this->datos[$campo])) {
                $this->errores[$campo] = "El campo '{$campo}' es obligatorio.";
            }
        }
    }

    /**
     * Valida que el correo tenga formato válido.
     */
    public function validarFormatoCorreo(): void
    {
        if (!empty($this->datos['correo']) &&
            !filter_var($this->datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $this->errores['correo'] = 'El correo electrónico no es válido.';
        }
    }

    /**
     * Valida que ambas contraseñas coincidan.
     */
    public function validarContrasenas(): void
    {
        if ($this->datos['password'] !== $this->datos['password2']) {
            $this->errores['password2'] = 'Las contraseñas no coinciden.';
        }
    }

    /**
     * Valida la longitud mínima de la contraseña.
     */
    public function validarLongitudContrasena(int $minimo = 8): void
    {
        if (strlen($this->datos['password']) < $minimo) {
            $this->errores['password'] = "La contraseña debe tener al menos {$minimo} caracteres.";
        }
    }

    /**
     * Valida que el usuario y correo no estén duplicados en la BD.
     */
    public function validarDuplicados(): void
    {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT usuario, correo FROM usuarios WHERE usuario = :u OR correo = :c LIMIT 1'
        );
        $stmt->execute([
            ':u' => $this->datos['usuario'],
            ':c' => $this->datos['correo'],
        ]);
        $row = $stmt->fetch();
        if ($row) {
            if ($row['usuario'] === $this->datos['usuario']) {
                $this->errores['usuario'] = 'El nombre de usuario ya está en uso.';
            }
            if ($row['correo'] === $this->datos['correo']) {
                $this->errores['correo'] = 'El correo electrónico ya está registrado.';
            }
        }
    }

    /**
     * Ejecuta todas las validaciones en orden.
     */
    public function validar(): bool
    {
        $this->errores = [];
        $this->validarCamposRequeridos();
        $this->validarFormatoCorreo();
        $this->validarContrasenas();
        $this->validarLongitudContrasena();
        if ($this->esValido()) {
            $this->validarDuplicados();   // consulta BD solo si lo anterior pasa
        }
        return $this->esValido();
    }

    /**
     * Hashea la contraseña con BCRYPT.
     */
    public function hashearContrasena(): string
    {
        return password_hash($this->datos['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
