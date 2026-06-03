<?php
/**
 * classes/Sanitizer.php
 * Clase con métodos ESTÁTICOS para sanitizar datos del formulario.
 * Cumple: mínimo 3 métodos, todos estáticos.
 */

class Sanitizer
{
    /**
     * Método 1: sanitizeText
     * Elimina etiquetas HTML, entidades peligrosas y espacios excesivos.
     * Uso: nombres, apellidos, campos de texto general.
     */
    public static function sanitizeText(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);                         // quita <script>, <b>, etc.
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // convierte & " ' < >
        $value = preg_replace('/\s+/', ' ', $value);         // espacios múltiples → uno
        return $value;
    }

    /**
     * Método 2: sanitizeEmail
     * Elimina caracteres no permitidos en correos y valida formato.
     * Retorna el email limpio o cadena vacía si es inválido.
     */
    public static function sanitizeEmail(string $value): string
    {
        $value  = trim($value);
        $clean  = filter_var($value, FILTER_SANITIZE_EMAIL);  // quita espacios, comas, etc.
        $valid  = filter_var($clean, FILTER_VALIDATE_EMAIL);  // valida estructura RFC
        return $valid !== false ? strtolower($clean) : '';
    }

    /**
     * Método 3: sanitizeUsername
     * Permite solo letras (incluyendo tildes/ñ), números y guiones bajos.
     * Longitud entre 3 y 50 caracteres.
     */
    public static function sanitizeUsername(string $value): string
    {
        $value = trim($value);
        // Permitir letras unicode (tildes, ñ), números y guion bajo
        $value = preg_replace('/[^\p{L}0-9_]/u', '', $value);
        $value = substr($value, 0, 50);
        return $value;
    }

    /**
     * Método 4: sanitizeEnum
     * Valida que el valor pertenezca a una lista blanca de opciones.
     * Uso: campo Sexo (M, F, Otro).
     */
    public static function sanitizeEnum(string $value, array $allowed): string
    {
        return in_array($value, $allowed, true) ? $value : '';
    }

    /**
     * Método 5: sanitize2FASecret
     * Valida que el secreto TOTP sea una cadena Base32 válida.
     */
    public static function sanitize2FASecret(string $secret): string
    {
        $secret = strtoupper(trim($secret));
        return preg_match('/^[A-Z2-7=]+$/', $secret) ? $secret : '';
    }
}
