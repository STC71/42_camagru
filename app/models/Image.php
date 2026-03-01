<?php

/**
 * Image Model
 * Handles all image-related database operations
 */
class Image {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($userId, $filename) {
        $sql = "INSERT INTO images (user_id, filename) VALUES (?, ?)";
        $result = $this->db->query($sql, [$userId, $filename]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function findById($id) {
        $sql = "SELECT i.*, u.username 
                FROM images i 
                JOIN users u ON i.user_id = u.id 
                WHERE i.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findByUserId($userId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM images WHERE user_id = ? ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt ? $stmt->fetchAll() : [];
    }

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

    public function count() {
        $sql = "SELECT COUNT(*) as total FROM images";
        $stmt = $this->db->query($sql);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

    public function countByUser($userId) {
        $sql = "SELECT COUNT(*) as total FROM images WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

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
