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

    public function count($imageId) {
        $sql = "SELECT COUNT(*) as total FROM likes WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }
}
