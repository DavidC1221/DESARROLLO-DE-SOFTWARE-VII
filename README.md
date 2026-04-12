[README.md](https://github.com/user-attachments/files/26661294/README.md)
# Laboratorio #2 – Implementación del Login en Laravel

## Introducción

En este laboratorio se desarrolló un sistema de autenticación de usuarios utilizando Laravel, aplicando la arquitectura Modelo-Vista-Controlador (MVC). El propósito fue comprender cómo Laravel organiza la lógica del sistema, las vistas y el acceso a la base de datos para permitir funciones como registro, inicio de sesión y acceso a un panel protegido.

Laravel facilita este proceso mediante herramientas de autenticación como Breeze, las cuales permiten generar rápidamente la estructura base para el manejo de usuarios. Durante el laboratorio se configuró el entorno de desarrollo, la base de datos y las migraciones necesarias para lograr el funcionamiento correcto del login.

Además, este laboratorio permitió reforzar el uso de comandos en consola, la configuración del archivo `.env`, la creación de tablas con migraciones y la validación del funcionamiento del sistema a través del registro exitoso de un usuario y su acceso al dashboard.

---

## Objetivo del laboratorio

Implementar un sistema de autenticación en Laravel que permita registrar usuarios, iniciar sesión y acceder a una vista protegida, comprendiendo el uso de la arquitectura MVC, la configuración del entorno de desarrollo y la conexión con la base de datos.

---

## Requisitos previos

Para ejecutar correctamente este laboratorio se necesitó el siguiente entorno de desarrollo:

* PHP 8.0 o superior
* Composer
* Laravel
* WampServer
* Apache
* MySQL o MariaDB
* Visual Studio Code
* Node.js
* NPM
* Navegador web
* Sistema operativo Windows

---

## Tecnologías utilizadas

* **Laravel**: Framework PHP utilizado para desarrollar la aplicación.
* **PHP**: Lenguaje de programación del lado del servidor.
* **Composer**: Administrador de dependencias de PHP.
* **Laravel Breeze**: Paquete utilizado para generar la autenticación.
* **Blade**: Motor de plantillas de Laravel.
* **MySQL**: Sistema de gestión de base de datos.
* **WampServer**: Entorno de desarrollo local.
* **Node.js y NPM**: Utilizados para instalar y compilar recursos del frontend.

---

## Estructura MVC en Laravel

Laravel utiliza la arquitectura MVC, la cual divide el proyecto en tres partes principales:

### Modelo

El modelo representa la estructura de los datos y la interacción con la base de datos. En este laboratorio, Laravel utilizó el modelo `User` para gestionar la información de los usuarios registrados.

### Vista

La vista es la parte visual del sistema, es decir, lo que el usuario observa en pantalla. Laravel utiliza Blade para construir interfaces como el formulario de login, registro y dashboard.

### Controlador

El controlador se encarga de recibir las solicitudes del usuario, procesarlas y devolver la respuesta correspondiente. En la autenticación, Laravel maneja esta lógica mediante los controladores generados por Breeze.

### Rutas

Las rutas definen las URLs del sistema y permiten dirigir cada solicitud al controlador o vista correspondiente. Estas rutas se encuentran principalmente en el archivo `routes/web.php`.

---

## Principales carpetas del proyecto

### `app/`

Contiene la lógica principal de la aplicación, incluyendo modelos, controladores y otras clases del sistema.

### `resources/views/`

Almacena las vistas del proyecto, es decir, los archivos que conforman la interfaz del usuario.

### `routes/`

Contiene la definición de las rutas web de la aplicación.

### `database/migrations/`

Incluye las migraciones utilizadas para crear y modificar las tablas en la base de datos.

### `public/`

Contiene los archivos públicos del proyecto, como imágenes, hojas de estilo y el punto de entrada principal.

---

## Flujo de comandos utilizados

Para realizar el laboratorio se siguió una secuencia de comandos en la terminal.

### 1. Creación del proyecto Laravel

```bash
composer create-project laravel/laravel laboratorioLaravel
```

### 2. Ingreso al proyecto

```bash
cd \wamp64\www\laboratorioLaravel
```

### 3. Ejecución del servidor local

```bash
php artisan serve
```

### 4. Instalación del paquete de autenticación Breeze

```bash
composer require laravel/breeze --dev
php artisan breeze:install
```

### 5. Instalación de dependencias de frontend

```bash
npm install
npm run dev
```

### 6. Ejecución de migraciones

```bash
php artisan migrate
```

---

## Configuración de la base de datos

Para que Laravel pudiera conectarse a MySQL, fue necesario configurar el archivo `.env` con los datos de conexión.

Ejemplo de configuración:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laboratoriolaravel
DB_USERNAME=root
DB_PASSWORD=
```

Luego de configurar estos valores, se ejecutaron las migraciones para crear las tablas necesarias en la base de datos. Entre ellas se encuentra la tabla `users`, que almacena la información de los usuarios registrados en el sistema.

Comando utilizado:

```bash
php artisan migrate
```

---

## Migraciones realizadas

Las migraciones son archivos que permiten crear la estructura de las tablas de la base de datos desde Laravel.

En este laboratorio se utilizaron las migraciones por defecto del sistema de autenticación, que permitieron crear tablas como:

* `users`
* `password_reset_tokens`
* `sessions`

Esto permitió que el sistema manejara el registro de usuarios, recuperación de contraseñas y sesiones activas.

---

## Resultado obtenido

El laboratorio se ejecutó correctamente. Se logró registrar un usuario e iniciar sesión de manera satisfactoria. Luego del inicio de sesión, el sistema redirigió al usuario al dashboard, mostrando el mensaje de confirmación correspondiente.

La aplicación se ejecutó en la siguiente dirección:

```text
http://127.0.0.1:8000
```

También fue posible acceder desde el entorno WAMP mediante:

```text
http://localhost/laboratorioLaravel/public
```

### Evidencia del resultado


A continuación se debe colocar la captura del dashboard o del login funcionando.

![Resultado del laboratorio](images/dashboard.png)



## Dificultades encontradas y soluciones aplicadas

Durante el laboratorio se presentaron algunos inconvenientes técnicos que fueron resueltos de la siguiente manera:

### 1. Error al ejecutar `php artisan serve`

Al principio apareció el mensaje:

```text
Could not open input file: artisan
```

**Causa:** El comando se estaba ejecutando desde una carpeta incorrecta.

**Solución:** Se ubicó la ruta correcta del proyecto Laravel en:

```text
C:\wamp64\www\laboratorioLaravel
```

Luego, al ejecutar nuevamente el comando en esa carpeta, el servidor inició correctamente.

### 2. Dificultad para ubicar el proyecto

Inicialmente se pensó que el proyecto estaba en otra carpeta, pero no contenía los archivos propios de Laravel, como `artisan` y `composer.json`.

**Solución:** Se revisó la carpeta `www` de WampServer hasta encontrar el proyecto correcto.

### 3. Configuración de base de datos

Fue necesario asegurar que el archivo `.env` tuviera los datos correctos de conexión a MySQL.

**Solución:** Se revisaron el nombre de la base de datos, el usuario y el puerto antes de ejecutar las migraciones.

### 4. Instalación de dependencias del frontend

Para que Breeze funcionara correctamente, fue necesario instalar y compilar las dependencias con Node.js.

**Solución:** Se ejecutaron los comandos:

```bash
npm install
npm run dev
```

---

## Backup de base de datos

Como parte de la documentación del laboratorio, se recomienda generar un respaldo de la base de datos utilizada y mantenerlo dentro del repositorio.

El respaldo puede exportarse desde phpMyAdmin en formato `.sql` y guardarse en una carpeta como:

```text
/database/backup/
```

Ejemplo:

```text
/database/backup/laboratoriolaravel.sql
```

---

## Referencias

1. Documentación oficial de Laravel.
2. Documentación oficial de Laravel Breeze.
3. Stack Overflow.

Ejemplo en formato simple:

* Laravel Documentation
* Laravel Breeze Documentation
* Stack Overflow

---

## Fecha de ejecución del laboratorio

Fecha de ejecución: __12_ / __4_ / 2026

---

## Conclusión

Este laboratorio permitió comprender de manera práctica el funcionamiento de la autenticación en Laravel y la aplicación del patrón MVC. Se configuró correctamente el entorno de desarrollo, la base de datos y las migraciones, logrando como resultado un sistema funcional de registro e inicio de sesión.

Además, se reforzó el uso de herramientas importantes del ecosistema Laravel, como Composer, Artisan, Breeze, NPM y MySQL, así como la importancia de documentar correctamente cada paso realizado durante el desarrollo.

---

## Información del estudiante

Este laboratorio ha sido desarrollado por el estudiante de la Universidad Tecnológica de Panamá:

**Nombre:** David Córdoba
**Correo:** david.cordoba@utp.ac.pa
**Curso:** Desarrollo Web VII
**Instructor del Laboratorio:** Irina Fong

---

## Fecha de entrega

Desde el 06 hasta el 22 de abril de 2026.

---

## Forma de entrega

A través del repositorio en GitHub o GitLab enlazado en la plataforma académica, con hoja de presentación.
