<?php

/**
 * Database Class
 * Handles database connections using PDO
 */
class Database {
    private static $instance = null;
    private $connection;

    /**

         * Método `__construct`.

         * Descripción: Implementación generada automáticamente: __construct.

         * @return mixed

         */

    
    /**

    
     * Método __construct.

    
     * @return mixed

    
     */

    
    /**

    
     * Método __construct.

    
     * @return mixed

    
     */

    
    private function __construct() {
        try {
            // Incluimos variables $DB_DSN, $DB_USER y $DB_PASSWORD desde el config oficial
            require_once __DIR__ . '/../../config/database.php';
            
            $this->connection = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**

         * Método `getConnection`.

         * Descripción: Implementación generada automáticamente: getConnection.

         * @return mixed

         */

    
    /**

    
     * Método getConnection.

    
     * @return mixed

    
     */

    
    /**

    
     * Método getConnection.

    
     * @return mixed

    
     */

    
    public function getConnection() {
        return $this->connection;
    }

    /**

         * Método `query`.

         * Descripción: Implementación generada automáticamente: query.

         * @param mixed $sql Descripción del parámetro $sql

         * @param mixed $params Descripción del parámetro $params

         * @return mixed

         */

    
    /**

    
     * Método query.

    
     *

    
     * @param mixed $sql Descripción del parámetro $sql.

    
     * @param mixed $param Descripción del parámetro $param.

    
     * @return mixed

    
     */

    
    /**

    
     * Método query.

    
     *

    
     * @param mixed $sql Descripción del parámetro $sql.

    
     * @param mixed $param Descripción del parámetro $param.

    
     * @return mixed

    
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

         * Método `lastInsertId`.

         * Descripción: Implementación generada automáticamente: lastInsertId.

         * @return mixed

         */

    
    /**

    
     * Método lastInsertId.

    
     * @return mixed

    
     */

    
    /**

    
     * Método lastInsertId.

    
     * @return mixed

    
     */

    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}
