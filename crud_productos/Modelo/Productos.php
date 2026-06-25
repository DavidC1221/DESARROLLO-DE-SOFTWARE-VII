<?php
/**
 * Clase Producto - Gestión de productos en la BD
 * Modelo/Productos.php
 */
require_once "conexion.php";

class Producto extends DB {

    // ── Propiedades del modelo ──────────────────────────────────────────
    public int    $id       = 0;
    public string $codigo   = "";
    public string $producto = "";
    public float  $precio   = 0.0;
    public int    $cantidad = 0;

    // Almacena errores de validación
    public array $errores = [];

    // ── Validación ──────────────────────────────────────────────────────
    /**
     * Valida los campos obligatorios antes de guardar o editar.
     * Retorna true si todo está correcto, false si hay errores.
     * @param bool $esNuevo true = Guardar (cantidad mín. 1), false = Modificar (cantidad mín. 0)
     */
    public function validar(bool $esNuevo = true): bool {
        $this->errores = [];

        if (empty(trim($this->codigo))) {
            $this->errores[] = "El código del producto es obligatorio.";
        }

        if (empty(trim($this->producto))) {
            $this->errores[] = "El nombre del producto es obligatorio.";
        }

        if (!is_numeric($this->precio) || $this->precio <= 0) {
            $this->errores[] = "El precio debe ser un número mayor a 0.";
        }

        $minCantidad = $esNuevo ? 1 : 0;
        if (!is_numeric($this->cantidad) || $this->cantidad < $minCantidad) {
            $mensaje = $esNuevo
                ? "La cantidad mínima para un producto nuevo es 1."
                : "La cantidad no puede ser negativa.";
            $this->errores[] = $mensaje;
        }

        return empty($this->errores);
    }

    // ── CRUD ────────────────────────────────────────────────────────────
    /**
     * Guarda un nuevo producto en la base de datos.
     * @return array Respuesta con success, message y accion
     */
    public function guardar(): array {
        if (!$this->validar(true)) {
            return [
                "success" => false,
                "message" => "Error de validación.",
                "accion"  => "Guardar",
                "errors"  => $this->errores
            ];
        }

        $sql = "INSERT INTO productos (codigo, producto, precio, cantidad)
                VALUES (:codigo, :producto, :precio, :cantidad)";

        $id = $this->insertSeguro($sql, [
            ":codigo"   => trim($this->codigo),
            ":producto" => trim($this->producto),
            ":precio"   => $this->precio,
            ":cantidad" => $this->cantidad
        ]);

        if ($id > 0) {
            return [
                "success" => true,
                "message" => "Producto guardado correctamente.",
                "accion"  => "Guardar",
                "id"      => $id
            ];
        }

        return [
            "success" => false,
            "message" => "No se pudo guardar el producto.",
            "accion"  => "Guardar",
            "errors"  => []
        ];
    }

    /**
     * Edita un producto existente en la base de datos.
     * @return array Respuesta con success, message y accion
     */
    public function editar(): array {
        if ($this->id <= 0) {
            return [
                "success" => false,
                "message" => "ID de producto inválido.",
                "accion"  => "Modificar",
                "errors"  => ["El ID del producto es requerido para editar."]
            ];
        }

        if (!$this->validar(false)) {
            return [
                "success" => false,
                "message" => "Error de validación.",
                "accion"  => "Modificar",
                "errors"  => $this->errores
            ];
        }

        $sql = "UPDATE productos
                SET codigo = :codigo, producto = :producto,
                    precio = :precio, cantidad = :cantidad
                WHERE id = :id";

        $filas = $this->updateSeguro($sql, [
            ":codigo"   => trim($this->codigo),
            ":producto" => trim($this->producto),
            ":precio"   => $this->precio,
            ":cantidad" => $this->cantidad,
            ":id"       => $this->id
        ]);

        if ($filas > 0) {
            return [
                "success" => true,
                "message" => "Producto actualizado correctamente.",
                "accion"  => "Modificar"
            ];
        }

        return [
            "success" => false,
            "message" => "No se realizaron cambios o el producto no existe.",
            "accion"  => "Modificar",
            "errors"  => []
        ];
    }

    /**
     * Busca un producto por su código.
     * @return array Respuesta con success, data y accion
     */
    public function buscar(): array {
        if (empty(trim($this->codigo))) {
            return [
                "success" => false,
                "message" => "Debe ingresar un código para buscar.",
                "accion"  => "Buscar",
                "errors"  => ["El campo código es obligatorio para la búsqueda."]
            ];
        }

        $sql       = "SELECT * FROM productos WHERE codigo = :codigo LIMIT 1";
        $resultado = $this->query($sql, [":codigo" => trim($this->codigo)]);

        if (!empty($resultado)) {
            return [
                "success" => true,
                "message" => "Producto encontrado.",
                "accion"  => "Buscar",
                "data"    => $resultado[0]
            ];
        }

        return [
            "success" => false,
            "message" => "No se encontró ningún producto con ese código.",
            "accion"  => "Buscar",
            "errors"  => []
        ];
    }

    /**
     * Lista todos los productos de la base de datos.
     * @return array Lista de productos
     */
    public function listar(): array {
        return $this->query("SELECT * FROM productos ORDER BY id DESC");
    }
}
?>
