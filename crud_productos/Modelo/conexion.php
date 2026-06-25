<?php
/**
 * Clase DB - Conexión segura a MySQL mediante PDO
 * Modelo/conexion.php
 */
class DB {
    // Configuración de la base de datos
    private $host     = "localhost";
    private $dbname   = "productosdb";
    private $user     = "root";
    private $password = "";          // Cambia si tu MySQL tiene contraseña
    private $charset  = "utf8";

    protected $pdo;

    /**
     * Constructor: establece la conexión PDO
     */
    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->pdo = new PDO($dsn, $this->user, $this->password, $opciones);
        } catch (PDOException $e) {
            // Retorna JSON de error sin exponer detalles internos
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "message" => "Error de conexión a la base de datos.",
                "accion"  => "Conexion"
            ]);
            exit;
        }
    }

    /**
     * Ejecuta un INSERT seguro con parámetros
     * @param string $sql    Consulta SQL con marcadores de posición
     * @param array  $params Arreglo de valores a enlazar
     * @return int   ID del registro insertado
     */
    public function insertSeguro(string $sql, array $params = []): int {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Ejecuta un UPDATE seguro con parámetros
     * @param string $sql    Consulta SQL con marcadores de posición
     * @param array  $params Arreglo de valores a enlazar
     * @return int   Número de filas afectadas
     */
    public function updateSeguro(string $sql, array $params = []): int {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Ejecuta una consulta SELECT y retorna los resultados
     * @param string $sql    Consulta SQL con marcadores de posición
     * @param array  $params Arreglo de valores a enlazar
     * @return array Arreglo de resultados
     */
    public function query(string $sql, array $params = []): array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>
