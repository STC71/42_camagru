<?php

/**
 * Like Model
 * Handles like-related database operations
 */
class Like {
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

     * Método toggle.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */

    /**

     * Método toggle.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */
/**
 * Procesa/transforma la entrada según la lógica del método.
 *
 * Descripción detallada:
 * Este método `toggle` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->toggle($imageId, $userId);
 * ```
 */

/**
 * Procesa/transforma la entrada según la lógica del método.
 *
 * Descripción detallada:
 * Este método `toggle` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->toggle($imageId, $userId);
 * ```
 */
    public function toggle($imageId, $userId) {
        // Check if already liked
        if ($this->hasLiked($imageId, $userId)) {
            return $this->unlike($imageId, $userId);
        } else {
            return $this->like($imageId, $userId);
        }
    }

    /**

     * Método like.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */

    /**

     * Método like.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */
/**
 * Método `like` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `like` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->like($imageId, $userId);
 * ```
 */

/**
 * Método `like` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `like` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->like($imageId, $userId);
 * ```
 */
    public function like($imageId, $userId) {
        $sql = "INSERT INTO likes (image_id, user_id) VALUES (?, ?)";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    /**

     * Método unlike.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */

    /**

     * Método unlike.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */
/**
 * Método `unlike` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `unlike` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->unlike($imageId, $userId);
 * ```
 */

/**
 * Método `unlike` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `unlike` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->unlike($imageId, $userId);
 * ```
 */
    public function unlike($imageId, $userId) {
        $sql = "DELETE FROM likes WHERE image_id = ? AND user_id = ?";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    /**

     * Método hasLiked.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */

    /**

     * Método hasLiked.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @param mixed $userId Descripción del parámetro $userId.

     * @return mixed

     */
/**
 * Comprueba si sliked y devuelve booleano.
 *
 * Descripción detallada:
 * Este método `hasLiked` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->hasLiked($imageId, $userId);
 * ```
 */

/**
 * Comprueba si sliked y devuelve booleano.
 *
 * Descripción detallada:
 * Este método `hasLiked` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $imageId Descripción detallada de $imageId.
 * @param mixed $userId Descripción detallada de $userId.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->hasLiked($imageId, $userId);
 * ```
 */
    public function hasLiked($imageId, $userId) {
        $sql = "SELECT id FROM likes WHERE image_id = ? AND user_id = ?";
        $stmt = $this->db->query($sql, [$imageId, $userId]);
        return $stmt && $stmt->fetch() !== false;
    }

    /**

     * Método count.

     *

     * @param mixed $imageId Descripción del parámetro $imageId.

     * @return mixed

     */

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
        $sql = "SELECT COUNT(*) as total FROM likes WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }
}
