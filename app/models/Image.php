<?php

/**
 * Image Model
 * Handles all image-related database operations
 */
class Image {
    private $db;

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
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**

     * Método create.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $filename Descripción del parámetro $filename.

     * @return mixed

     */

    /**

     * Método create.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $filename Descripción del parámetro $filename.

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
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $filename Descripción detallada de $filename.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->create($userId, $filename);
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
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $filename Descripción detallada de $filename.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->create($userId, $filename);
 * ```
 */
    public function create($userId, $filename) {
        $sql = "INSERT INTO images (user_id, filename) VALUES (?, ?)";
        $result = $this->db->query($sql, [$userId, $filename]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**

     * Método findById.

     *

     * @param mixed $id Descripción del parámetro $id.

     * @return mixed

     */

    /**

     * Método findById.

     *

     * @param mixed $id Descripción del parámetro $id.

     * @return mixed

     */
/**
 * Método `findById` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findById` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $id Descripción detallada de $id.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findById($id);
 * ```
 */

/**
 * Método `findById` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findById` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $id Descripción detallada de $id.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findById($id);
 * ```
 */
    public function findById($id) {
        $sql = "SELECT i.*, u.username 
                FROM images i 
                JOIN users u ON i.user_id = u.id 
                WHERE i.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**

     * Método findByUserId.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $param Descripción del parámetro $param.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

     */

    /**

     * Método findByUserId.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $param Descripción del parámetro $param.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

     */
/**
 * Método `findByUserId` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByUserId` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed null Descripción detallada de null.
 * @param mixed 0 Descripción detallada de 0.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByUserId($userId, null, 0);
 * ```
 */

/**
 * Método `findByUserId` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByUserId` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed null Descripción detallada de null.
 * @param mixed 0 Descripción detallada de 0.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByUserId($userId, null, 0);
 * ```
 */
    public function findByUserId($userId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM images WHERE user_id = ? ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**

     * Método getAll.

     *

     * @param mixed $param Descripción del parámetro $param.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

     */

    /**

     * Método getAll.

     *

     * @param mixed $param Descripción del parámetro $param.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

     */
/**
 * Obtiene all y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getAll` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed 5 Descripción detallada de 5.
 * @param mixed 0 Descripción detallada de 0.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getAll(5, 0);
 * ```
 */

/**
 * Obtiene all y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getAll` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed 5 Descripción detallada de 5.
 * @param mixed 0 Descripción detallada de 0.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getAll(5, 0);
 * ```
 */
    public function getAll($limit = 5, $offset = 0) {
        $sql = "SELECT i.*, u.username, 
                (SELECT COUNT(*) FROM likes WHERE image_id = i.id) as like_count,
                (SELECT COUNT(*) FROM comments WHERE image_id = i.id) as comment_count
                FROM images i 
                JOIN users u ON i.user_id = u.id 
                ORDER BY i.created_at DESC 
                LIMIT $limit OFFSET $offset";
        
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**

     * Método count.

     * @return mixed

     */

    /**

     * Método count.

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
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->count();
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
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->count();
 * ```
 */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM images";
        $stmt = $this->db->query($sql);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

    /**

     * Método countByUser.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */

    /**

     * Método countByUser.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */
/**
 * Método `countByUser` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `countByUser` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->countByUser($userId);
 * ```
 */

/**
 * Método `countByUser` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `countByUser` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->countByUser($userId);
 * ```
 */
    public function countByUser($userId) {
        $sql = "SELECT COUNT(*) as total FROM images WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
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
        $image = $this->findById($id);
        if (!$image || $image['user_id'] != $userId) {
            return false;
        }

        // Delete file
        $filepath = '../public/uploads/images/' . $image['filename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete from database
        $sql = "DELETE FROM images WHERE id = ? AND user_id = ?";
        return $this->db->query($sql, [$id, $userId]) !== false;
    }

    /**

     * Método getWithDetails.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @return mixed

     */

    /**

     * Método getWithDetails.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @return mixed

     */
/**
 * Obtiene withdetails y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getWithDetails` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getWithDetails($imageId);
 * ```
 */

/**
 * Obtiene withdetails y lo devuelve.
 *
 * Descripción detallada:
 * Este método `getWithDetails` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->getWithDetails($imageId);
 * ```
 */
    public function getWithDetails($imageId) {
        $sql = "SELECT i.*, u.username, u.id as user_id,
                (SELECT COUNT(*) FROM likes WHERE image_id = i.id) as like_count,
                (SELECT COUNT(*) FROM comments WHERE image_id = i.id) as comment_count
                FROM images i 
                JOIN users u ON i.user_id = u.id 
                WHERE i.id = ?";
        
        $stmt = $this->db->query($sql, [$imageId]);
        return $stmt ? $stmt->fetch() : false;
    }
}
