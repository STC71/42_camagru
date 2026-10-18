<?php

/**
 * =============================================================================
 * Database.php - Gestión de la conexión a MySQL
 * =============================================================================
 * Esta clase se encarga de crear y mantener UNA SOLA conexión a la base de
 * datos durante toda la petición.
 *
 * Analogía cotidiana:
 * Imagina que en una oficina hay una sola máquina de café profesional.
 * En vez de que cada empleado se compre la suya, todos van a la misma máquina.
 * Esa máquina es el "Singleton" de esta clase.
 *
 * Ventajas del patrón Singleton:
 * - Solo se abre una conexión (más rápido y menos carga al servidor)
 * - Todos los modelos usan exactamente la misma conexión
 * =============================================================================
 */
class Database {

    /** Guarda la única instancia de esta clase */
    private static $instance = null;

    /** Guarda el objeto PDO real */
    private $connection;

    /**
     * Constructor privado.
     * Nadie puede hacer "new Database()" desde fuera.
     * Solo se puede obtener la instancia con Database::getInstance()
     */
    private function __construct() {
        try {
            // Cargamos las variables de configuración ($DB_DSN, $DB_USER, $DB_PASSWORD)
            require_once __DIR__ . '/../../config/database.php';

            $this->connection = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, [
                // Si hay un error de SQL, lanza una excepción (muy útil para depurar)
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Por defecto devolvemos arrays asociativos (más cómodo)
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Desactivamos la emulación de prepared statements (más seguro)
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Si no se puede conectar, paramos todo y mostramos el error
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Devuelve la única instancia de Database.
     * Si todavía no existe, la crea.
     *
     * Ejemplo de uso:
     *   $db = Database::getInstance();
     *   $pdo = $db->getConnection();
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Devuelve el objeto PDO para poder hacer consultas
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Método de conveniencia para ejecutar consultas preparadas
     *
     * Ejemplo:
     *   $stmt = $db->query(
     *       "SELECT * FROM users WHERE email = ?",
     *       [$email]
     *   );
     *   $user = $stmt->fetch();
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Devuelve el ID del último registro insertado
     * (muy útil después de un INSERT)
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}
