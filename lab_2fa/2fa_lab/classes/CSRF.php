<?php
/**
 * classes/CSRF.php
 * Protección Anti-CSRF mediante tokens aleatorios por sesión.
 *
 * Flujo:
 *  1. El servidor genera un token y lo guarda en $_SESSION.
 *  2. El token se incluye como campo oculto en cada formulario.
 *  3. Al enviar el formulario, el servidor compara ambos tokens.
 *  4. Si no coinciden → solicitud rechazada.
 */

class CSRF
{
    private const SESSION_KEY = '_csrf_token';
    private const TOKEN_BYTES = 32; // 256 bits de entropía

    /**
     * Genera (o recupera) el token CSRF de la sesión actual.
     * Si no existe, crea uno nuevo y lo almacena.
     */
    public static function getToken(): string
    {
        self::iniciarSesionSiNecesario();
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(self::TOKEN_BYTES));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Devuelve el campo hidden HTML listo para insertar en el formulario.
     */
    public static function campoOculto(): string
    {
        $token = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Valida el token enviado en el POST contra el de la sesión.
     * Usa hash_equals() para prevenir ataques de timing.
     * Regenera el token después de validar (evita reutilización).
     */
    public static function validar(string $tokenEnviado): bool
    {
        self::iniciarSesionSiNecesario();
        $tokenEsperado = $_SESSION[self::SESSION_KEY] ?? '';
        $valido = !empty($tokenEnviado)
               && !empty($tokenEsperado)
               && hash_equals($tokenEsperado, $tokenEnviado);
        // Regenerar para que cada envío use un token fresco
        unset($_SESSION[self::SESSION_KEY]);
        return $valido;
    }

    /**
     * Lanza una excepción si el token no es válido.
     * Uso rápido al inicio de cualquier handler POST.
     */
    public static function verificarOFallar(string $tokenEnviado): void
    {
        if (!self::validar($tokenEnviado)) {
            http_response_code(403);
            die('<h1>403 - Token CSRF inválido. Solicitud rechazada.</h1>');
        }
    }

    private static function iniciarSesionSiNecesario(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
