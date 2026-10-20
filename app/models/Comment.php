<?php

/**
 * =============================================================================
 * Comment.php - Modelo de Comentarios
 * =============================================================================
 * Gestiona los comentarios que los usuarios escriben en las fotos.
 *
 * IMPORTANTE: la columna en la base de datos se llama `comment`
 * (ver database/init.sql). Todo el modelo debe usar ese nombre.
 *
 * Características:
 * - Cualquier usuario logueado puede comentar
 * - Se puede obtener la lista de comentarios de una foto
 * - Se notifica por email al autor de la foto (si tiene notificaciones activadas)
 *
 * Analogía cotidiana:
 * Es como el libro de visitas de una exposición de fotografías.
 * =============================================================================
 */
class Comment {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * -------------------------------------------------------------------------
     * Añade un nuevo comentario a una imagen
     * -------------------------------------------------------------------------
     * @param int    $imageId  ID de la foto
     * @param int    $userId   ID del usuario que comenta
     * @param string $text     Texto del comentario
     * @return int|false       ID del comentario creado o false si falla
     */
    public function create($imageId, $userId, $text) {
        // Limitamos la longitud por seguridad
        $text = mb_substr(trim($text), 0, 500);

        if (empty($text)) {
            return false;
        }

        // Columna real en BD: `comment` (NO "content")
        $sql = "INSERT INTO comments (image_id, user_id, comment) VALUES (?, ?, ?)";
        $result = $this->db->query($sql, [$imageId, $userId, $text]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Devuelve todos los comentarios de una imagen
     * (ordenados del más reciente al más antiguo)
     * Incluye el nombre de usuario del autor del comentario
     */
    public function findByImageId($imageId) {
        $sql = "SELECT c.*, u.username
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.image_id = ?
                ORDER BY c.created_at DESC";

        $stmt = $this->db->query($sql, [$imageId]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Cuenta cuántos comentarios tiene una imagen
     */
    public function countByImage($imageId) {
        $sql = "SELECT COUNT(*) AS total FROM comments WHERE image_id = ?";
        $stmt = $this->db->query($sql, [$imageId]);
        $row = $stmt ? $stmt->fetch() : null;
        return $row ? (int)$row['total'] : 0;
    }

    /**
     * Elimina un comentario (solo el autor del comentario)
     */
    public function delete($commentId, $userId) {
        $sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
        return $this->db->query($sql, [$commentId, $userId]) !== false;
    }
}
