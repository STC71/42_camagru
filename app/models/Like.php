<?php

/**
 * Modelo de likes
 * Gestiona las operaciones de base de datos de likes
 */
class Like {
    private $db;

    /**
     * Inicializa el modelo con el singleton de Database.
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Alterna un like en una imagen para un usuario.
     * Si ya existia like, llama a unlike; en caso contrario, llama a like.
     */
    public function toggle($imageId, $userId) {
        // Comprobar si ya tiene like
        if ($this->hasLiked($imageId, $userId)) {
            return $this->unlike($imageId, $userId);
        } else {
            return $this->like($imageId, $userId);
        }
    }

    /**
     * Agrega un like a una imagen para un usuario.
     */
    public function like($imageId, $userId) {
        $sql = "INSERT INTO likes (image_id, user_id) VALUES (?, ?)";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    /**
     * Elimina un like de una imagen para un usuario.
     */
    public function unlike($imageId, $userId) {
        $sql = "DELETE FROM likes WHERE image_id = ? AND user_id = ?";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    /**
     * Comprueba si un usuario ha dado like a una imagen concreta.
     */
    public function hasLiked($imageId, $userId) {
        $sql = "SELECT id FROM likes WHERE image_id = ? AND user_id = ?";
        $stmt = $this->db->query($sql, [$imageId, $userId]);
        return $stmt && $stmt->fetch() !== false;
    }

    /**
     * Cuenta el numero total de likes de una imagen.
     */
    public function count($imageId) {
        $sql = "SELECT COUNT(*) as total FROM likes WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }
}
