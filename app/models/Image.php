<?php

/**
 * =============================================================================
 * Image.php - Modelo de Imágenes
 * =============================================================================
 * Este modelo gestiona todas las operaciones relacionadas con las fotos
 * (y GIFs) que los usuarios suben o capturan.
 *
 * Analogía cotidiana:
 * Es como el álbum de fotos de la aplicación. Aquí se registran todas las
 * fotos, se consultan, se cuentan likes y comentarios, y se eliminan.
 * =============================================================================
 */
class Image {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * -------------------------------------------------------------------------
     * Guarda una nueva imagen en la base de datos
     * -------------------------------------------------------------------------
     * @param int    $userId    ID del usuario propietario
     * @param string $filename  Nombre del archivo (ej: img_5_1729....png)
     * @return int|false        ID de la imagen creada o false si falla
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
     * Busca una imagen por su ID e incluye el nombre de usuario del autor
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
     * Devuelve las imágenes de un usuario concreto
     * (útil para mostrar las miniaturas en el editor)
     *
     * @param int $userId
     * @param int|null $limit   Número máximo de resultados
     * @param int $offset       Desde qué posición empezar
     */
    public function findByUserId($userId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM images 
                WHERE user_id = ? 
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }

        $stmt = $this->db->query($sql, [$userId]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * -------------------------------------------------------------------------
     * Obtiene todas las imágenes de la galería pública
     * -------------------------------------------------------------------------
     * Incluye:
     * - Nombre de usuario del autor
     * - Número de likes
     * - Número de comentarios
     *
     * Se usa para la paginación de la galería.
     */
    public function getAll($limit = 5, $offset = 0) {
        $sql = "SELECT i.*, u.username,
                       (SELECT COUNT(*) FROM likes WHERE image_id = i.id) AS like_count,
                       (SELECT COUNT(*) FROM comments WHERE image_id = i.id) AS comment_count
                FROM images i
                JOIN users u ON i.user_id = u.id
                ORDER BY i.created_at DESC
                LIMIT " . intval($limit) . " OFFSET " . intval($offset);

        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Cuenta el número total de imágenes (necesario para la paginación)
     */
    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM images";
        $stmt = $this->db->query($sql);
        $row = $stmt ? $stmt->fetch() : null;
        return $row ? (int)$row['total'] : 0;
    }

    /**
     * -------------------------------------------------------------------------
     * Elimina una imagen
     * -------------------------------------------------------------------------
     * Solo se permite si el usuario es el propietario.
     * También borra el archivo físico del disco.
     *
     * @return bool  true si se eliminó correctamente
     */
    public function delete($imageId, $userId) {
        // Primero comprobamos que la imagen pertenece al usuario
        $image = $this->findById($imageId);
        if (!$image || $image['user_id'] != $userId) {
            return false;
        }

        // Borramos el registro de la base de datos
        $sql = "DELETE FROM images WHERE id = ? AND user_id = ?";
        $result = $this->db->query($sql, [$imageId, $userId]);

        if ($result) {
            // También borramos el archivo físico
            $filepath = realpath(__DIR__ . '/../../public/uploads/images/' . $image['filename']);
            if ($filepath && file_exists($filepath)) {
                @unlink($filepath);
            }
            return true;
        }

        return false;
    }

    /**
     * Devuelve todas las imágenes de un usuario (para borrado de cuenta).
     */
    public function getAllByUser($userId) {
        $sql = "SELECT * FROM images WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Borra del disco y de la BD todas las fotos de un usuario.
     * Las FKs en likes/comments ya hacen CASCADE sobre images.
     */
    public function deleteAllByUser($userId) {
        $images = $this->getAllByUser($userId);
        $dir = realpath(__DIR__ . '/../../public/uploads/images');
        foreach ($images as $image) {
            if ($dir && !empty($image['filename'])) {
                $filepath = $dir . DIRECTORY_SEPARATOR . $image['filename'];
                if (is_file($filepath)) {
                    @unlink($filepath);
                }
            }
        }
        $sql = "DELETE FROM images WHERE user_id = ?";
        return $this->db->query($sql, [$userId]) !== false;
    }
}
