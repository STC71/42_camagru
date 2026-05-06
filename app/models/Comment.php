<?php

/**
 * Comment Model
 * Handles comment-related database operations
 */
class Comment {
    private $db;

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
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**

     * Método create.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $comment Descripción del parámetro $comment.

     * @return mixed

     */
/**
 * Persiste datos en la base de datos relacionados con este recurso.
 *
 * Descripción detallada:
 * Este método `create` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $comment Descripción detallada de $comment.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->create($imageId, $userId, $comment);
 * ```
 */

/**
 * Persiste datos en la base de datos relacionados con este recurso.
 *
 * Descripción detallada:
 * Este método `create` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $comment Descripción detallada de $comment.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->create($imageId, $userId, $comment);
 * ```
 */
    public function create($imageId, $userId, $comment) {
        $sql = "INSERT INTO comments (image_id, user_id, comment) VALUES (?, ?, ?)";
        $result = $this->db->query($sql, [$imageId, $userId, $comment]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**

     * Método findByImageId.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $param Descripción del parámetro $param.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

     */
/**
 * Método `findByImageId` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByImageId` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed null Descripción detallada de null.
 * @param mixed 0 Descripción detallada de 0.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByImageId($imageId, null, 0);
 * ```
 */

/**
 * Método `findByImageId` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByImageId` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed null Descripción detallada de null.
 * @param mixed 0 Descripción detallada de 0.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByImageId($imageId, null, 0);
 * ```
 */
    public function findByImageId($imageId, $limit = null, $offset = 0) {
        $sql = "SELECT c.*, u.username 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.image_id = ? 
                ORDER BY c.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->query($sql, [$imageId]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**

     * Método count.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @return mixed

     */
/**
 * Método `count` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `count` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->count($imageId);
 * ```
 */

/**
 * Método `count` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `count` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->count($imageId);
 * ```
 */
    public function count($imageId) {
        $sql = "SELECT COUNT(*) as total FROM comments WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

    /**

     * Método delete.

     *

     * @param mixed $id Descripción del parámetro $id.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */
/**
 * Elimina el recurso indicado en la base de datos.
 *
 * Descripción detallada:
 * Este método `delete` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $id Descripción detallada de $id.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->delete($id, $userId);
 * ```
 */

/**
 * Elimina el recurso indicado en la base de datos.
 *
 * Descripción detallada:
 * Este método `delete` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $id Descripción detallada de $id.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->delete($id, $userId);
 * ```
 */
    public function delete($id, $userId) {
        // Verify ownership
        $sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
        return $this->db->query($sql, [$id, $userId]) !== false;
    }
}
