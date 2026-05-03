# Laboratorio: Autoload con Composer (PSR-4)

##  Descripción
Este proyecto implementa la carga automática de clases en PHP utilizando Composer bajo el estándar PSR-4.

##  Instalación
1. Clonar el repositorio
2. Ejecutar:
   composer dump-autoload

## Estructura del proyecto
- src/: Contiene las clases (Producto.php)
- index.php: Punto de entrada del sistema
- composer.json: Configuración de autoload

## Prueba de ejecución
Ejecutar:
php index.php

Salida esperada:
Autoload funcionando correctamente 🚀

## Conclusiones técnicas
- Mantenibilidad: Se pueden agregar clases sin modificar includes
- Eficiencia: Solo se cargan clases necesarias (lazy loading)
- Estandarización: PSR-4 mejora el trabajo en equipo