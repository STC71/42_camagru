<?php

/**
 * Modelo de imagenes
 * Gestiona las operaciones de base de datos de imagenes
 */
class Image {
    private $db;

    /**
     * Inicializa el modelo con el singleton de Database.
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Crea un nuevo registro de imagen para un usuario.
     * Devuelve el ID de imagen insertado en caso de exito.
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
     * Busca una imagen por ID junto con la informacion del usuario asociado.
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
     * Busca todas las imagenes de un usuario concreto.
     * Admite paginacion mediante limit y offset.
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
     * Obtiene todas las imagenes con conteos agregados de likes y comentarios.
     * Admite paginacion mediante limit y offset.
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
     * Cuenta el numero total de imagenes en la base de datos.
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM images";
        $stmt = $this->db->query($sql);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

    /**
     * Cuenta el numero total de imagenes subidas por un usuario concreto.
     */
    public function countByUser($userId) {
        $sql = "SELECT COUNT(*) as total FROM images WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        $result = $stmt ? $stmt->fetch() : false;
        return $result ? $result['total'] : 0;
    }

    /**
     * Elimina una imagen tras verificar la propiedad.
     * Tambien elimina el archivo asociado del sistema de ficheros.
     */
    public function delete($id, $userId) {
        // Verificar propiedad
        $image = $this->findById($id);
        if (!$image || $image['user_id'] != $userId) {
            return false;
        }

        // Eliminar archivo
        $filepath = '../public/uploads/images/' . $image['filename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Eliminar en base de datos
        $sql = "DELETE FROM images WHERE id = ? AND user_id = ?";
        return $this->db->query($sql, [$id, $userId]) !== false;
    }

    /**
     * Obtiene una imagen con detalle completo:
     * informacion del usuario, conteo de likes y conteo de comentarios.
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
