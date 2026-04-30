#  CRUD de Productos con Laravel

##  Descripción

Este proyecto consiste en la implementación de un sistema CRUD (Create, Read, Update, Delete) utilizando el framework Laravel. Permite gestionar productos mediante una interfaz web sencilla, incluyendo la creación, visualización, edición y eliminación de registros almacenados en una base de datos.

---

##  Tecnologías utilizadas

* PHP 8.x
* Laravel 13
* MySQL (WAMP)
* Blade (motor de plantillas)
* Composer

---

##  Instalación y configuración

### 1. Crear el proyecto Laravel

```bash
composer create-project laravel/laravel crud_rapido
```

---

### 2. Configurar la base de datos

Se creó la base de datos manualmente mediante consola debido a problemas con phpMyAdmin:

```sql
CREATE DATABASE crud_db;
```

Configuración en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_db
DB_USERNAME=root
DB_PASSWORD=
```

---

### 3. Ejecutar migraciones iniciales

```bash
php artisan migrate
```

---

### 4. Crear modelo y migración

```bash
php artisan make:model Product -m
```

Se modificó la migración para incluir los campos:

```php
$table->id();
$table->string('description');
$table->double('price', 8, 2);
$table->integer('stock');
$table->timestamps();
```

Ejecutar:

```bash
php artisan migrate:fresh
```

---

### 5. Generar CRUD automático

```bash
composer require ibex/crud-generator --dev
php artisan vendor:publish --tag=crud
php artisan make:crud products
```

---

##  Problemas encontrados y soluciones

###  Error de conexión con phpMyAdmin

* Se utilizó MySQL por consola para crear la base de datos.

###  Puerto incorrecto en WAMP

* Se accedió mediante `http://127.0.0.1:8080`.

###  Rutas no registradas

* Se agregaron manualmente en `routes/web.php`:

```php
Route::resource('products', ProductController::class);
```

### 🔸 Vistas no generadas automáticamente

* Se crearon manualmente las siguientes vistas:

  * `index.blade.php`
  * `create.blade.php`
  * `edit.blade.php`

---

##  Ejecución del proyecto

```bash
php artisan serve
```

Acceder en el navegador:

```
http://127.0.0.1:8000/products
```

---

##  Funcionalidades

*  Crear productos
*  Listar productos
*  Editar productos
*  Eliminar productos

---

##  Estructura del proyecto (relevante)

```
app/
 └── Models/Product.php

database/
 └── migrations/

resources/
 └── views/product/
      ├── index.blade.php
      ├── create.blade.php
      └── edit.blade.php

routes/
 └── web.php
```

---

##  Conclusión

Se logró implementar un sistema CRUD funcional en Laravel, resolviendo problemas reales durante el proceso como errores de configuración del servidor, fallos en generación automática de vistas y conexión a la base de datos. Esto permitió comprender mejor el funcionamiento interno del framework y el flujo completo de desarrollo.

---

##  Autor

David Córdoba/ 8-1009-1486
