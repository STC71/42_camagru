<?php

/**
 * =============================================================================
 * Like.php - Modelo de Likes (Me gusta)
 * =============================================================================
 * Gestiona los "me gusta" que los usuarios dan a las fotos.
 *
 * Reglas importantes:
 * - Un usuario solo puede dar un like a cada foto (constraint UNIQUE en la BD)
 * - Se puede quitar el like (toggle)
 *
 * Analogía cotidiana:
 * Es como el contador de "me gusta" de Instagram, pero controlado desde
 * el servidor para evitar trampas.
 * =============================================================================
 */
class Like {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * -------------------------------------------------------------------------
     * Añade o quita un like (comportamiento tipo interruptor / toggle)
     * -------------------------------------------------------------------------
     * Si el usuario ya había dado like → se lo quitamos
     * Si no había dado like → se lo añadimos
     *
     * @return array  ['liked' => bool, 'count' => int]
     */
    public function toggle($imageId, $userId) {
        // ¿Ya existe el like?
        if ($this->exists($imageId, $userId)) {
            // Sí → lo quitamos
            $this->remove($imageId, $userId);
            $liked = false;
        } else {
            // No → lo añadimos
            $this->add($imageId, $userId);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'count' => $this->countByImage($imageId)
        ];
    }

    /**
     * Comprueba si un usuario ya dio like a una imagen
     */
    public function exists($imageId, $userId) {
        $sql = "SELECT id FROM likes WHERE image_id = ? AND user_id = ?";
        $stmt = $this->db->query($sql, [$imageId, $userId]);
        return $stmt && $stmt->fetch() !== false;
    }

    /**
     * Añade un like
     */
    public function add($imageId, $userId) {
        $sql = "INSERT INTO likes (image_id, user_id) VALUES (?, ?)";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    /**
     * Quita un like
     */
    public function remove($imageId, $userId) {
        $sql = "DELETE FROM likes WHERE image_id = ? AND user_id = ?";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    /**
     * Cuenta cuántos likes tiene una imagen
     */
    public function countByImage($imageId) {
        $sql = "SELECT COUNT(*) AS total FROM likes WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $row = $stmt ? $stmt->fetch() : null;
        return $row ? (int)$row['total'] : 0;
    }
}
