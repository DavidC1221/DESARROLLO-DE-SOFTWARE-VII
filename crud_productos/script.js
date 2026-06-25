/**
 * script.js - Lógica del cliente para el CRUD de Productos
 * Usa Fetch API + FormData + SweetAlert2
 * Estructura: switch (accion) para manejar respuestas del servidor
 */

// ── URL del controlador backend ──────────────────────────────────────────────
const URL_BACKEND = "registrar.php";

// ── Estado: controla si el formulario está en modo Guardar o Modificar ───────
let modoEdicion = false;

// ── Referencias a los campos del formulario ──────────────────────────────────
const campoId       = () => document.getElementById("idProducto");
const campoCodigo   = () => document.getElementById("Codigo");
const campoProducto = () => document.getElementById("Producto");
const campoPrecio   = () => document.getElementById("Precio");
const campoCantidad = () => document.getElementById("Cantidad");
const btnGuardar    = () => document.getElementById("btnGuardar");

// ── Validación en el cliente (JavaScript) ────────────────────────────────────
/**
 * Valida el formulario antes de enviarlo.
 * Retorna true si es válido, false si hay errores.
 */
function validarFormulario(accion) {
    const errores = [];

    if (!campoCodigo().value.trim()) {
        errores.push("El campo Código es obligatorio.");
    }

    if (!campoProducto().value.trim()) {
        errores.push("El campo Producto es obligatorio.");
    }

    const precio = parseFloat(campoPrecio().value);
    if (isNaN(precio) || precio <= 0) {
        errores.push("El Precio debe ser un número mayor a 0.");
    }

    const cantidad = parseInt(campoCantidad().value);
    const minCant  = accion === "Guardar" ? 1 : 0;

    if (isNaN(cantidad) || cantidad < minCant) {
        const msg = accion === "Guardar"
            ? "La Cantidad mínima para un nuevo producto es 1."
            : "La Cantidad no puede ser negativa.";
        errores.push(msg);
    }

    if (errores.length > 0) {
        Swal.fire({
            icon: "warning",
            title: "Campos requeridos",
            html: errores.map(e => `• ${e}`).join("<br>"),
            confirmButtonColor: "#4e73df"
        });
        return false;
    }

    return true;
}

// ── SWITCH de acciones en el cliente ─────────────────────────────────────────
/**
 * Decide qué hacer según la respuesta JSON del servidor.
 * @param {object} data  Respuesta JSON del servidor
 */
function procesarRespuesta(data) {
    switch (data.accion) {

        case "Guardar":
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "¡Guardado!",
                    text: data.message,
                    confirmButtonColor: "#4e73df",
                    timer: 2000,
                    showConfirmButton: false
                });
                limpiarFormulario();
                ListarProductos();
            } else {
                mostrarError(data);
            }
            break;

        case "Modificar":
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "¡Actualizado!",
                    text: data.message,
                    confirmButtonColor: "#4e73df",
                    timer: 2000,
                    showConfirmButton: false
                });
                limpiarFormulario();
                ListarProductos();
            } else {
                mostrarError(data);
            }
            break;

        case "Buscar":
            if (data.success) {
                // Rellenar el formulario con los datos encontrados
                cargarEnFormulario(data.data, true);
                Swal.fire({
                    icon: "info",
                    title: "Producto encontrado",
                    text: `Se cargó: ${data.data.producto}`,
                    confirmButtonColor: "#4e73df",
                    timer: 1800,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Sin resultados",
                    text: data.message,
                    confirmButtonColor: "#4e73df"
                });
            }
            break;

        case "Listar":
            if (data.success) {
                renderizarTabla(data.data);
            } else {
                mostrarError(data);
            }
            break;

        default:
            Swal.fire({
                icon: "error",
                title: "Acción desconocida",
                text: `Acción '${data.accion}' no reconocida.`,
                confirmButtonColor: "#4e73df"
            });
            break;
    }
}

// ── Mostrar errores de validación / servidor ──────────────────────────────────
function mostrarError(data) {
    const errores = Array.isArray(data.errors) && data.errors.length > 0
        ? data.errors.map(e => `• ${e}`).join("<br>")
        : data.message;

    Swal.fire({
        icon: "error",
        title: "Error",
        html: errores,
        confirmButtonColor: "#e74a3b"
    });
}

// ── GUARDAR o MODIFICAR (botón principal del formulario) ──────────────────────
async function manejarAccion() {
    const accion = modoEdicion ? "Modificar" : "Guardar";

    // Validación en el cliente
    if (!validarFormulario(accion)) return;

    // Construir FormData
    const formData = new FormData();
    formData.append("Accion",   accion);
    formData.append("id",       campoId().value);
    formData.append("codigo",   campoCodigo().value.trim());
    formData.append("producto", campoProducto().value.trim());
    formData.append("precio",   campoPrecio().value);
    formData.append("cantidad", campoCantidad().value);

    try {
        const response = await fetch(URL_BACKEND, {
            method: "POST",
            body:   formData
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        procesarRespuesta(data);

    } catch (error) {
        Swal.fire({
            icon:  "error",
            title: "Error de conexión",
            text:  `No se pudo comunicar con el servidor. ${error.message}`,
            confirmButtonColor: "#e74a3b"
        });
    }
}

// ── BUSCAR por código ─────────────────────────────────────────────────────────
async function buscarProducto() {
    const codigo = campoCodigo().value.trim();

    if (!codigo) {
        Swal.fire({
            icon:  "warning",
            title: "Campo requerido",
            text:  "Ingresa un código para buscar.",
            confirmButtonColor: "#4e73df"
        });
        return;
    }

    const formData = new FormData();
    formData.append("Accion", "Buscar");
    formData.append("codigo", codigo);

    try {
        const response = await fetch(URL_BACKEND, {
            method: "POST",
            body:   formData
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const data = await response.json();
        procesarRespuesta(data);

    } catch (error) {
        Swal.fire({
            icon:  "error",
            title: "Error de conexión",
            text:  error.message,
            confirmButtonColor: "#e74a3b"
        });
    }
}

// ── LISTAR todos los productos (recarga la tabla) ─────────────────────────────
async function ListarProductos() {
    const formData = new FormData();
    formData.append("Accion", "Listar");

    try {
        const response = await fetch(URL_BACKEND, {
            method: "POST",
            body:   formData
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const data = await response.json();
        procesarRespuesta(data);

    } catch (error) {
        document.getElementById("tablaProductos").innerHTML = `
            <tr>
                <td colspan="6" class="text-danger py-4">
                    <i class="bi bi-exclamation-triangle"></i>
                    Error al cargar los productos: ${error.message}
                </td>
            </tr>`;
    }
}

// ── Renderizar la tabla de productos ──────────────────────────────────────────
function renderizarTabla(productos) {
    const tbody = document.getElementById("tablaProductos");

    if (!productos || productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-muted py-4">
                    <i class="bi bi-inbox"></i> No hay productos registrados.
                </td>
            </tr>`;
        return;
    }

    // Construir filas de la tabla
    tbody.innerHTML = productos.map((p, i) => `
        <tr>
            <td>${i + 1}</td>
            <td><span class="badge bg-secondary">${p.codigo}</span></td>
            <td class="text-start fw-semibold">${p.producto}</td>
            <td>$${parseFloat(p.precio).toFixed(2)}</td>
            <td>
                <span class="badge ${parseInt(p.cantidad) > 0 ? 'bg-success' : 'bg-danger'}">
                    ${p.cantidad}
                </span>
            </td>
            <td>
                <button
                    class="btn btn-warning btn-sm me-1"
                    onclick="prepararEdicion(${JSON.stringify(p).replace(/"/g, '&quot;')})"
                    title="Editar"
                >
                    <i class="bi bi-pencil"></i>
                </button>
            </td>
        </tr>
    `).join("");
}

// ── Cargar datos en el formulario para editar ─────────────────────────────────
/**
 * @param {object}  producto  Objeto con los datos del producto
 * @param {boolean} desdeBusqueda  true = solo carga sin activar modo edición automáticamente
 */
function cargarEnFormulario(producto, desdeBusqueda = false) {
    campoId().value       = producto.id;
    campoCodigo().value   = producto.codigo;
    campoProducto().value = producto.producto;
    campoPrecio().value   = producto.precio;
    campoCantidad().value = producto.cantidad;

    if (!desdeBusqueda) {
        activarModoEdicion();
    }
}

// ── Preparar el formulario para editar desde la tabla ─────────────────────────
function prepararEdicion(producto) {
    cargarEnFormulario(producto, false);
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// ── Activar modo edición ──────────────────────────────────────────────────────
function activarModoEdicion() {
    modoEdicion = true;
    btnGuardar().innerHTML = '<i class="bi bi-pencil-square me-1"></i> Actualizar';
    btnGuardar().classList.replace("btn-primary", "btn-warning");

    // Permitir cantidad = 0 en modo edición
    campoCantidad().min = "0";
}

// ── Limpiar el formulario y regresar a modo Guardar ───────────────────────────
function limpiarFormulario() {
    modoEdicion = false;

    campoId().value       = "0";
    campoCodigo().value   = "";
    campoProducto().value = "";
    campoPrecio().value   = "";
    campoCantidad().value = "";

    // Restaurar botón y restricción de cantidad
    btnGuardar().innerHTML = '<i class="bi bi-save me-1"></i> Registrar';
    btnGuardar().classList.replace("btn-warning", "btn-primary");
    campoCantidad().min = "1";

    campoCodigo().focus();
}
