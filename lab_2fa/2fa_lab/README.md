# Laboratorio de Autenticación con 2FA
**Universidad Tecnológica de Panamá — Desarrollo Web VII — I Semestre 2026**

---

## Estructura del Proyecto

```
2fa_lab/
├── config/
│   └── database.php          # Conexión PDO (usuario con privilegios mínimos)
├── classes/
│   ├── Sanitizer.php         # Clase con 5 métodos estáticos de sanitización
│   ├── RegistroForm.php      # Clase del formulario (responsabilidad mínima por método)
│   ├── TOTP.php              # Implementación TOTP RFC 6238 (QR + validación)
│   └── CSRF.php              # Protección Anti-CSRF con tokens por sesión
├── pages/
│   ├── registro.php          # Formulario de registro + generación QR
│   ├── login.php             # Login en 2 fases (fase1=credenciales, fase2=TOTP)
│   ├── dashboard.php         # Panel principal (requiere sesión fase 2)
│   ├── hash_tool.php         # Interfaz para generar y validar hashes
│   ├── privileges.php        # Muestra privilegios DB + datos de tablas
│   ├── db_preview.php        # Vista completa de las 2 tablas
│   ├── check_duplicate.php   # Endpoint AJAX para validación frontend
│   └── logout.php
├── assets/
│   ├── css/style.css         # CSS profesional (dark-tech, Syne font)
│   └── js/registro.js        # Validaciones frontend + AJAX duplicados
└── sql/
    └── setup.sql             # Creación de BD, usuario con mínimos privilegios y tablas
```

---

## Instalación y Configuración

### 1. Requisitos
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Servidor web (Apache/Nginx) o `php -S localhost:8000`

### 2. Base de datos
Ejecuta el SQL **como superusuario** (root) **una sola vez**:
```sql
-- Desde la terminal:
mysql -u root -p < sql/setup.sql
```
Esto crea:
- La base de datos `lab_2fa`
- El usuario `lab2fa_user` con **solo** `SELECT, INSERT, UPDATE, DELETE`
- Las tablas `usuarios` y `sesiones_2fa`

Para **ver los privilegios** concedidos:
```sql
SHOW GRANTS FOR 'lab2fa_user'@'localhost';
```

### 3. Configurar conexión
Edita `config/database.php` si cambias host/puerto/contraseña.

### 4. Levantar el servidor
```bash
cd 2fa_lab
php -S localhost:8000
```
Luego abre: `http://localhost:8000/pages/registro.php`

---

## Flujo de Autenticación

```
[Registro] → Formulario → Validación (PHP + AJAX) → Insertar BD → Mostrar QR
    ↓
[Login Fase 1] → Usuario + Contraseña → password_verify() → Sesión Fase 1
    ↓
[Login Fase 2] → Código TOTP (6 dígitos) → TOTP::validarCodigo() → Sesión Fase 2
    ↓
[Dashboard] → Acceso completo (requiere sesión fase 2)
```

---

## Criterios de la Rúbrica Cubiertos

| Criterio | Archivo(s) | Pts |
|---|---|---|
| Usuario BD con privilegios mínimos | `sql/setup.sql`, `config/database.php`, `pages/privileges.php` | 15 |
| Formulario de registro con validaciones | `pages/registro.php` | 5 |
| Validación correo/usuario sin duplicados (frontend) | `pages/check_duplicate.php`, `assets/js/registro.js` | 5 |
| Clase para formulario (responsabilidad mínima) | `classes/RegistroForm.php` | 10 |
| Clase Sanitizer con métodos estáticos (≥3) | `classes/Sanitizer.php` | 10 |
| Genera código QR | `classes/TOTP.php`, `pages/registro.php` | 5 |
| Login solicita QR antes de entrar | `pages/login.php` (fase 2) | 5 |
| Segunda sesión de confirmación | `pages/login.php` (`$_SESSION['login_fase']`) | 5 |
| Interfaz para generar/validar hash | `pages/hash_tool.php` | 5 |
| Protección Anti-CSRF | `classes/CSRF.php` (todos los forms) | 10 |
| Tablas registran datos (2 tablas × 5 pts) | `pages/privileges.php`, `pages/db_preview.php` | 10 |
| Calidad CSS | `assets/css/style.css` | 10 |
| Puntualidad | — | 5 |

---

## Notas de Seguridad Implementadas
- **Prepared Statements** en todas las consultas (previene SQL Injection)
- **password_hash(BCRYPT cost=12)** para contraseñas
- **htmlspecialchars()** en todas las salidas (previene XSS)
- **Tokens Anti-CSRF** en todos los formularios POST
- **session_regenerate_id(true)** al completar el login (previene session fixation)
- **Privilegios mínimos** en el usuario de BD (principio de mínimo privilegio)
