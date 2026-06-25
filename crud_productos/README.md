# CRUD - API Fetch y MySQL (Guardar, Editar, Buscar)

**Curso:** Desarrollo de Software VII  
**Instructor:** Ing. Irina Fong  
**Grupos:** IGS131 / 1GS132 / 1GS133  
**Universidad:** Universidad Tecnológica de Panamá – FISC  

---

## Descripción

Aplicación web dinámica bajo arquitectura cliente-servidor que implementa un CRUD de productos (Guardar, Editar, Buscar) usando **Fetch API** en el frontend y **PHP OOP + PDO + MySQL** en el backend. Las respuestas del servidor se manejan en formato **JSON** y las alertas al usuario se muestran con **SweetAlert2**.

---

## Estructura del Proyecto

```
proyecto/
├── index.html              # Formulario principal (Código, Producto, Precio, Cantidad)
├── script.js               # Lógica JS: captura eventos, FormData, fetch, switch, SweetAlert2
├── registrar.php           # Controlador: recibe $_POST, ejecuta switch($accion) → JSON
└── Modelo/
    ├── conexion.php        # Clase DB: conexión PDO (insertSeguro, updateSeguro, query)
    └── Productos.php       # Clase Producto: guardar(), editar(), buscar(), validaciones
```

---

## Base de Datos

**Base de datos:** `productosdb`

```sql
CREATE TABLE productos (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    codigo   VARCHAR(20)     NOT NULL,
    producto VARCHAR(100)    NOT NULL,
    precio   DECIMAL(10,2)   NOT NULL,
    cantidad INT             NOT NULL
);
```

---

## Tecnologías Utilizadas

| Tecnología | Uso |
|---|---|
| HTML5 + Bootstrap | Formulario y diseño responsivo |
| JavaScript (Fetch API) | Peticiones asíncronas al backend |
| FormData | Serialización de datos del formulario |
| PHP 8 (OOP) | Lógica de negocio y controlador |
| PDO (MySQL) | Conexión segura, prevención de SQL Injection |
| SweetAlert2 | Alertas de éxito y error al usuario |
| WampServer / XAMPP | Servidor local de desarrollo |

---

## Funcionalidades

- **Guardar:** Registra un nuevo producto. Cantidad mínima: 1 (regla de negocio).
- **Editar / Modificar:** Actualiza los datos de un producto existente por su `id`. Permite cantidad 0 (producto agotado).
- **Buscar:** Consulta productos por código o nombre y rellena el formulario para edición.
- **Listar:** Recarga automáticamente la tabla con `ListarProductos()` tras cada operación.

---

## Flujo de Datos

```
Usuario llena formulario
    → script.js captura clic
    → crea FormData con campo "Accion" (Guardar / Modificar)
    → fetch(POST) → registrar.php
        → switch($_POST['Accion'])
            → Clase Producto → método guardar() o editar()
                → Clase DB → PDO → MySQL
        → retorna JSON { success, message, accion, errors }
    → script.js switch(data.accion) → SweetAlert2
    → ListarProductos() recarga tabla
```

---

## Formato de Respuesta JSON

```json
{
  "success": true,
  "message": "Producto guardado correctamente.",
  "accion": "Guardar",
  "errors": []
}
```

En caso de error:

```json
{
  "success": false,
  "message": "Producto no creado",
  "accion": "Guardar",
  "errors": { "codigo": "El código es obligatorio." }
}
```

---

## Requisitos Previos

- WampServer o XAMPP activo (Apache + MySQL)
- PHP 8.x
- Navegador moderno (Chrome, Firefox, Edge)
- VS Code (recomendado)

---

## Instalación y Ejecución

1. Clonar o copiar la carpeta del proyecto dentro de `C:/wamp64/www/` (Wamp) o `C:/xampp/htdocs/` (XAMPP).
2. Importar la base de datos:
   - Abrir `http://localhost/phpmyadmin`
   - Crear la base de datos `productosdb`
   - Ejecutar el script SQL de la sección **Base de Datos**
3. Configurar credenciales en `Modelo/conexion.php` si es necesario (host, usuario, contraseña).
4. Abrir el proyecto en el navegador: `http://localhost/[nombre-carpeta]/index.html`

---

## Seguridad

- Consultas preparadas con **PDO** para prevenir inyección SQL.
- Validaciones de campos obligatorios tanto en el cliente (JS) como en el servidor (PHP).
- El archivo PHP retorna **solo** `json_encode(...)` — sin `echo`, `print` ni `var_dump` adicionales.
- El header `Content-Type: application/json` se declara al inicio de `registrar.php`.

---

## Rúbrica (100 pts)

| Criterio | Pts |
|---|---|
| Formulario realiza guardar, editar y buscar correctamente | 10 |
| Uso de Bootstrap para diseño y botones | 10 |
| Lógica Guardar/Modificar centralizada con switch (PHP) | 15 |
| Uso de switch también en JavaScript | 10 |
| Clase DB implementada | 10 |
| Clase Producto implementada | 10 |
| Validaciones + control de errores (servidor y cliente) | 10 |
| Respuestas JSON con success, message, errors | 10 |
| Uso de SweetAlert2 para éxito y errores | 10 |
| Código limpio, comentarios y estructura de carpetas | 5 |
| **Total** | **100** |

---

## CDN SweetAlert2

```html
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

---

## Video de Referencia

[https://www.youtube.com/watch?v=AXZGTOd8ASk](https://www.youtube.com/watch?v=AXZGTOd8ASk)
