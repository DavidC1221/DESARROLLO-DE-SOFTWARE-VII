<?php
/**
 * registrar.php - Controlador principal de operaciones CRUD
 * Recibe $_POST con el campo "Accion" y ejecuta la lógica correspondiente.
 */

// Asegura que la respuesta siempre sea JSON limpio
header("Content-Type: application/json");

// Incluye el modelo de Productos (que a su vez incluye la conexión)
require_once "Modelo/Productos.php";

// ── Leer la acción enviada por el formulario ────────────────────────────────
$accion = isset($_POST["Accion"]) ? trim($_POST["Accion"]) : "";

// ── Switch principal de acciones ────────────────────────────────────────────
switch ($accion) {

    // ── GUARDAR ─────────────────────────────────────────────────────────────
    case "Guardar":
        $producto           = new Producto();
        $producto->codigo   = $_POST["codigo"]   ?? "";
        $producto->producto = $_POST["producto"] ?? "";
        $producto->precio   = $_POST["precio"]   ?? 0;
        $producto->cantidad = $_POST["cantidad"] ?? 0;

        $respuesta = $producto->guardar();
        echo json_encode($respuesta);
        break;

    // ── MODIFICAR ────────────────────────────────────────────────────────────
    case "Modificar":
        $producto           = new Producto();
        $producto->id       = (int)($_POST["id"]       ?? 0);
        $producto->codigo   = $_POST["codigo"]          ?? "";
        $producto->producto = $_POST["producto"]        ?? "";
        $producto->precio   = $_POST["precio"]          ?? 0;
        $producto->cantidad = $_POST["cantidad"]        ?? 0;

        $respuesta = $producto->editar();
        echo json_encode($respuesta);
        break;

    // ── BUSCAR ───────────────────────────────────────────────────────────────
    case "Buscar":
        $producto         = new Producto();
        $producto->codigo = $_POST["codigo"] ?? "";

        $respuesta = $producto->buscar();
        echo json_encode($respuesta);
        break;

    // ── LISTAR (para recargar la tabla) ─────────────────────────────────────
    case "Listar":
        $producto  = new Producto();
        $lista     = $producto->listar();
        echo json_encode([
            "success" => true,
            "message" => "Lista obtenida.",
            "accion"  => "Listar",
            "data"    => $lista
        ]);
        break;

    // ── Acción desconocida ───────────────────────────────────────────────────
    default:
        echo json_encode([
            "success" => false,
            "message" => "Acción no reconocida: '{$accion}'.",
            "accion"  => $accion,
            "errors"  => ["La acción enviada no es válida."]
        ]);
        break;
}

exit;
?>
