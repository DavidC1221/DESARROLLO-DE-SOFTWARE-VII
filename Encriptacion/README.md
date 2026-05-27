# 🔐 Seguridad en PHP con OpenSSL

> Laboratorio de implementación de funciones criptográficas usando la biblioteca OpenSSL en PHP.  
> **Desarrollo de Software VII** — Universidad Tecnológica de Panamá  
> Docente: Ing. Irina Fong | I Semestre 2026

---

## 📋 Descripción

Este proyecto implementa distintas funciones de seguridad criptográfica en PHP mediante la extensión OpenSSL. Se cubren tres grandes áreas:

- **Firma digital RSA** — generación de claves, firma y verificación de mensajes
- **Certificados X.509** — creación de certificados autofirmados
- **Cifrado simétrico AES-128-CBC** — encriptación y desencriptación con formulario web interactivo

---

## 📁 Estructura del proyecto

```
SeguridadOpenSSL/
│
├── keys/                        # Claves RSA generadas (private_key.pem, public_key.pem)
├── keysCert/                    # Certificado X.509 y clave asociada
│
├── firma7.php                   # Genera par de claves RSA y firma un mensaje
├── firmaOtra.php                # Genera claves RSA + certificado X.509 autofirmado
├── FirmarMesaje.php             # Firma un mensaje y verifica la firma con el certificado
├── Encriptacion.php             # Cifrado simétrico AES-128-CBC (versión lineal)
├── encriptacion.php             # Cifrado AES-128-CBC con formulario web interactivo ✨
├── encriptacionOpenssl_2.php    # Variante adicional de encriptación
├── Informacion.php              # Información general de la extensión OpenSSL
├── DiprenaGuardarAprobacion.php # Caso de uso: guardar aprobación firmada digitalmente
├── firma4.php                   # Variante de firma digital
├── firmaDigital.php             # Implementación base de firma digital
└── firmaDiprena.php             # Variante para caso DIPRENA
```

---

## ⚙️ Requisitos

| Requisito | Versión recomendada |
|-----------|-------------------|
| PHP | 7.4 o superior |
| XAMPP / WAMP | Cualquier versión moderna |
| Extensión OpenSSL | Habilitada en `php.ini` |

### Verificar que OpenSSL está activo

En tu `php.ini` debe existir (sin `;` al inicio):

```ini
extension=openssl
```

---

## 🚀 Instalación y uso

1. **Clona o descarga** este repositorio en la carpeta `htdocs` de XAMPP:
   ```
   C:\xampp\htdocs\SeguridadOpenSSL\
   ```

2. **Inicia Apache** desde el Panel de Control de XAMPP.

3. **Accede** desde el navegador:
   ```
   http://localhost/SeguridadOpenSSL/
   ```

4. Ejecuta cada script directamente por URL, por ejemplo:
   ```
   http://localhost/SeguridadOpenSSL/firma7.php
   http://localhost/SeguridadOpenSSL/encriptacion.php
   ```

---

## 🔑 Descripción de cada script

### `firma7.php` — Generación de claves y firma RSA
Genera un par de claves RSA de 2048 bits, firma un mensaje con la clave privada y guarda ambas claves en la carpeta `keys/`.

**Funciones OpenSSL usadas:**
- `openssl_pkey_new()` — genera el par de claves
- `openssl_pkey_export()` — exporta la clave privada
- `openssl_sign()` — firma el mensaje

---

### `firmaOtra.php` — Certificado X.509 autofirmado
Genera claves RSA y crea un certificado digital autofirmado X.509 válido por 365 días, guardando todo en `keysCert/`.

**Funciones OpenSSL usadas:**
- `openssl_csr_new()` — crea el Certificate Signing Request
- `openssl_csr_sign()` — firma el CSR generando el certificado
- `openssl_x509_export()` — exporta el certificado

---

### `FirmarMesaje.php` — Firma y verificación de mensaje
Firma un mensaje usando la clave privada y verifica la autenticidad de la firma usando la clave pública extraída del certificado X.509.

**Funciones OpenSSL usadas:**
- `openssl_sign()` — firma el mensaje
- `openssl_pkey_get_public()` — extrae clave pública del certificado
- `openssl_verify()` — verifica la firma (retorna `1` si válida)

---

### `encriptacion.php` ✨ — Cifrado AES-128-CBC interactivo
Formulario web que permite al usuario ingresar un mensaje y una clave secreta. La página cifra el mensaje con AES-128-CBC y muestra el resultado en tiempo real.

**Funciones OpenSSL usadas:**
- `openssl_random_pseudo_bytes()` — genera el IV aleatorio
- `openssl_encrypt()` — cifra el mensaje (retorna Base64)
- `openssl_decrypt()` — descifra y verifica el resultado

**Campos de resultado mostrados:**
| Campo | Descripción |
|-------|-------------|
| IV hexadecimal | Vector de Inicialización en formato `bin2hex` |
| Texto cifrado | Mensaje cifrado codificado en Base64 |
| Texto descifrado | Recuperación del mensaje original |

---

## 🛠️ Solución a errores comunes

### Error: no se puede localizar `openssl.cnf`

**Solución 1 — Dejar que XAMPP lo resuelva solo (recomendada):**

```php
$configArgs = array(
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA
    // Sin parámetro 'config' — XAMPP lo localiza automáticamente
);
```

**Solución 2 — Ruta dinámica (más robusta):**

```php
$fallbackCnf = 'C:\xampp\php\extras\ssl\openssl.cnf';
if (!file_exists($fallbackCnf) && isset($_SERVER['DOCUMENT_ROOT'])) {
    $fallbackCnf = dirname($_SERVER['DOCUMENT_ROOT']) . '\php\extras\ssl\openssl.cnf';
}

$configArgs = array(
    'config' => file_exists($fallbackCnf) ? $fallbackCnf : null,
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA
);
```

---

## 📊 Rúbrica de evaluación

| Criterio | Puntaje |
|----------|---------|
| Entrega puntual del informe | 10 pts |
| Evidencias (capturas de pantalla) | 35 pts |
| Documentación de funciones | 40 pts |
| Presentación y ortografía | 10 pts |
| Conclusión reflexiva | 5 pts |
| **Total** | **100 pts** |

---

## 👩‍💻 Créditos

**Docente:** Ing. Irina Fong  
**Facultad:** Ingeniería en Sistemas Computacionales  
**Materia:** Desarrollo de Software VII — PHP y MySQL  
**Universidad:** Universidad Tecnológica de Panamá (UTP)

---

> 💡 **Nota:** Los archivos dentro de `keys/` y `keysCert/` son generados automáticamente al ejecutar los scripts. No es necesario crearlos manualmente.
