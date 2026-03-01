<?php

/**
 * Like Model
 * Handles like-related database operations
 */
class Like {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function toggle($imageId, $userId) {
        // Check if already liked
        if ($this->hasLiked($imageId, $userId)) {
            return $this->unlike($imageId, $userId);
        } else {
            return $this->like($imageId, $userId);
        }
    }

    public function like($imageId, $userId) {
        $sql = "INSERT INTO likes (image_id, user_id) VALUES (?, ?)";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    public function unlike($imageId, $userId) {
        $sql = "DELETE FROM likes WHERE image_id = ? AND user_id = ?";
        return $this->db->query($sql, [$imageId, $userId]) !== false;
    }

    public function hasLiked($imageId, $userId) {
        $sql = "SELECT id FROM likes WHERE image_id = ? AND user_id = ?";
        $stmt = $this->db->query($sql, [$imageId, $userId]);
        return $stmt && $stmt->fetch() !== false;
    }

    public function count($imageId) {
        $sql = "SELECT COUNT(*) as total FROM likes WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }
}
