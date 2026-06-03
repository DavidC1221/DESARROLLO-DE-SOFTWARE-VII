<?php
/**
 * classes/TOTP.php
 * Implementación TOTP (RFC 6238) sin dependencias externas.
 * Genera secretos Base32, URIs para QR y valida códigos de 6 dígitos.
 */

class TOTP
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const DIGITS       = 6;
    private const PERIOD       = 30;  // segundos
    private const WINDOW       = 1;   // pasos de tolerancia (±30 s)

    // ── Generación de secreto ─────────────────────────────────

    /**
     * Genera un secreto aleatorio en Base32 de 16 caracteres (80 bits).
     */
    public static function generarSecreto(): string
    {
        $secreto = '';
        for ($i = 0; $i < 16; $i++) {
            $secreto .= self::BASE32_CHARS[random_int(0, 31)];
        }
        return $secreto;
    }

    // ── Generación del código ─────────────────────────────────

    /**
     * Genera el código TOTP para un instante dado.
     */
    public static function generarCodigo(string $secreto, int $timestamp = 0): string
    {
        if ($timestamp === 0) $timestamp = time();
        $counter = (int) floor($timestamp / self::PERIOD);
        return self::hotp($secreto, $counter);
    }

    // ── Validación ────────────────────────────────────────────

    /**
     * Valida el código del usuario con ventana de tolerancia.
     */
    public static function validarCodigo(string $secreto, string $codigoUsuario): bool
    {
        $codigoUsuario = trim($codigoUsuario);
        $ahora = time();
        for ($delta = -self::WINDOW; $delta <= self::WINDOW; $delta++) {
            $ts = $ahora + ($delta * self::PERIOD);
            if (hash_equals(self::generarCodigo($secreto, $ts), $codigoUsuario)) {
                return true;
            }
        }
        return false;
    }

    // ── URI para QR ───────────────────────────────────────────

    /**
     * Genera la URI otpauth:// para mostrar en el código QR.
     */
    public static function generarURI(string $secreto, string $correo, string $issuer = 'Lab2FA UTP'): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($correo),
            $secreto,
            rawurlencode($issuer),
            self::DIGITS,
            self::PERIOD
        );
    }

    /**
     * Retorna la URL de la API de Google Charts para generar el QR como imagen PNG.
     * No requiere librería externa: usa la API pública de Google.
     */
    public static function urlQR(string $uri, int $size = 200): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size='
            . $size . 'x' . $size
            . '&data=' . rawurlencode($uri)
            . '&ecc=M';
    }

    // ── Internos ──────────────────────────────────────────────

    private static function hotp(string $secreto, int $counter): string
    {
        $clave   = self::base32Decode($secreto);
        $msg     = pack('N*', 0) . pack('N*', $counter);
        $hash    = hash_hmac('sha1', $msg, $clave, true);
        $offset  = ord($hash[19]) & 0x0F;
        $code    = (
            ((ord($hash[$offset])     & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) <<  8) |
            ( ord($hash[$offset + 3]) & 0xFF)
        ) % (10 ** self::DIGITS);
        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $input): string
    {
        $input    = strtoupper($input);
        $output   = '';
        $buffer   = 0;
        $bitsLeft = 0;
        for ($i = 0, $len = strlen($input); $i < $len; $i++) {
            $c = $input[$i];
            if ($c === '=') break;
            $pos = strpos(self::BASE32_CHARS, $c);
            if ($pos === false) continue;
            $buffer    = ($buffer << 5) | $pos;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output   .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $output;
    }
}
