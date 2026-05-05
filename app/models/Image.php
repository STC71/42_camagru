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
