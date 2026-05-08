<?php

/**
 * Modelo de usuarios
 * Gestiona las operaciones de base de datos de usuarios
 */
class User {
    private $db;

    /**
     * Inicializa el modelo con el singleton de Database.
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Crea una nueva cuenta de usuario con nombre, correo y contrasena hasheada.
     * Devuelve el token de verificacion para confirmar el correo.
     */
    public function create($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $verificationToken = bin2hex(random_bytes(32));

        $sql = "INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)";
        $result = $this->db->query($sql, [$username, $email, $hashedPassword, $verificationToken]);

        if ($result) {
            return $verificationToken;
        }
        return false;
    }

    /**
     * Busca un usuario por su direccion de correo.
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por nombre de usuario.
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por su ID.
     */
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por su token de verificacion de correo.
     */
    public function findByVerificationToken($token) {
        $sql = "SELECT * FROM users WHERE verification_token = ?";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por su token de restablecimiento de contrasena (no expirado).
     */
    public function findByResetToken($token) {
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Marca una cuenta como verificada y limpia su token de verificacion.
     */
    public function verify($token) {
        $sql = "UPDATE users SET verified = 1, verification_token = NULL WHERE verification_token = ?";
        return $this->db->query($sql, [$token]) !== false;
    }

    /**
     * Crea un token de restablecimiento y fija su expiracion a una hora desde ahora.
     * Devuelve el token en caso de exito o false si falla.
     */
    public function createResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $result = $this->db->query($sql, [$token, $expiry, $email]);

        return $result ? $token : false;
    }

    /**
     * Restablece la contrasena de un usuario usando su token de reseteo.
     * El token se limpia tras un reseteo correcto.
     */
    public function resetPassword($token, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL 
                WHERE reset_token = ? AND reset_token_expiry > NOW()";
        return $this->db->query($sql, [$hashedPassword, $token]) !== false;
    }

    /**
     * Actualiza el nombre de usuario y correo de un usuario.
     */
    public function updateProfile($userId, $username, $email) {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        return $this->db->query($sql, [$username, $email, $userId]) !== false;
    }

    /**
     * Cambia la contrasena de un usuario por ID.
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->query($sql, [$hashedPassword, $userId]) !== false;
    }

    /**
     * Actualiza las preferencias de notificacion de comentarios de un usuario.
     */
    public function updateNotificationSettings($userId, $notifyComments) {
        $sql = "UPDATE users SET notify_comments = ? WHERE id = ?";
        return $this->db->query($sql, [$notifyComments ? 1 : 0, $userId]) !== false;
    }

    /**
     * Verifica una contrasena en texto plano contra un hash almacenado.
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Comprueba si un correo ya existe en la base de datos.
     * Opcionalmente excluye un ID de usuario concreto de la comprobacion.
     */
    public function emailExists($email, $excludeUserId = null) {
        if ($excludeUserId) {
            $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
            $stmt = $this->db->query($sql, [$email, $excludeUserId]);
        } else {
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $this->db->query($sql, [$email]);
        }
        return $stmt && $stmt->fetch() !== false;
    }

    /**
     * Comprueba si un nombre de usuario ya existe en la base de datos.
     * Opcionalmente excluye un ID de usuario concreto de la comprobacion.
     */
    public function usernameExists($username, $excludeUserId = null) {
        if ($excludeUserId) {
            $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
            $stmt = $this->db->query($sql, [$username, $excludeUserId]);
        } else {
            $sql = "SELECT id FROM users WHERE username = ?";
            $stmt = $this->db->query($sql, [$username]);
        }
        return $stmt && $stmt->fetch() !== false;
    }
}
