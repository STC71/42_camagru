<?php

/**
 * Modelo de comentarios
 * Gestiona las operaciones de base de datos de comentarios
 */
class Comment {
    private $db;

    /**
     * Inicializa el modelo con el singleton de Database.
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Crea un nuevo comentario en una imagen y devuelve el ID del comentario.
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
     * Busca todos los comentarios de una imagen con informacion de usuario.
     * Admite paginacion mediante limit y offset.
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
     * Cuenta el numero total de comentarios de una imagen.
     */
    public function count($imageId) {
        $sql = "SELECT COUNT(*) as total FROM comments WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

    /**
     * Elimina un comentario si el usuario solicitante es su propietario.
     */
    public function delete($id, $userId) {
        // Verificar propiedad
        $sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
        return $this->db->query($sql, [$id, $userId]) !== false;
    }
}
