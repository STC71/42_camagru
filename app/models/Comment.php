<?php

/**
 * Comment Model
 * Handles comment-related database operations
 */
class Comment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($imageId, $userId, $comment) {
        $sql = "INSERT INTO comments (image_id, user_id, comment) VALUES (?, ?, ?)";
        $result = $this->db->query($sql, [$imageId, $userId, $comment]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

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

    public function count($imageId) {
        $sql = "SELECT COUNT(*) as total FROM comments WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

    public function delete($id, $userId) {
        // Verify ownership
        $sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
        return $this->db->query($sql, [$id, $userId]) !== false;
    }
}
