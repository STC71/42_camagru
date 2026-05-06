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
/**
 * Método `__construct` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `__construct` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->__construct();
 * ```
 */

/**
 * Método `__construct` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `__construct` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->__construct();
 * ```
 */
    
/**
 * Método `__construct` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `__construct` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->__construct();
 * ```
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
/**
 * Obtiene connection y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getConnection` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getConnection();
 * ```
 */

/**
 * Obtiene connection y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getConnection` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getConnection();
 * ```
 */
    
/**
 * Obtiene connection y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getConnection` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getConnection();
 * ```
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
/**
 * Método `query` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `query` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $sql Descripción detallada de $sql.
 * @param mixed [] Descripción detallada de [].
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->query($sql, []);
 * ```
 */

/**
 * Método `query` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `query` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $sql Descripción detallada de $sql.
 * @param mixed [] Descripción detallada de [].
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->query($sql, []);
 * ```
 */
    
/**
 * Método `query` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `query` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $sql Descripción detallada de $sql.
 * @param mixed [] Descripción detallada de [].
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->query($sql, []);
 * ```
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
/**
 * Método `lastInsertId` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `lastInsertId` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->lastInsertId();
 * ```
 */

/**
 * Método `lastInsertId` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `lastInsertId` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->lastInsertId();
 * ```
 */
    
/**
 * Método `lastInsertId` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `lastInsertId` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->lastInsertId();
 * ```
 */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}
