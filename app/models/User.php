<?php

/**
 * =============================================================================
 * User.php - Modelo de Usuarios
 * =============================================================================
 * Este modelo se encarga de todas las operaciones relacionadas con los usuarios
 * en la base de datos: crear cuentas, buscar usuarios, verificar emails,
 * restablecer contraseñas, etc.
 *
 * Analogía cotidiana:
 * Es como el archivo de personal de una empresa. Aquí se guardan y se consultan
 * todos los datos de los empleados (usuarios).
 * =============================================================================
 */
class User {

    /** Conexión a la base de datos (Singleton) */
    private $db;

    /**
     * Al crear el modelo, obtenemos la conexión única a la base de datos
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * -------------------------------------------------------------------------
     * Crea un nuevo usuario
     * -------------------------------------------------------------------------
     * - Hashea la contraseña (nunca se guarda en texto plano)
     * - Genera un token único de verificación de email
     * - Devuelve el token para poder enviarlo por correo
     *
     * @return string|false  Token de verificación o false si falla
     */
    public function create($username, $email, $password) {
        // password_hash usa el algoritmo más seguro disponible (actualmente bcrypt)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Token aleatorio de 64 caracteres hexadecimales
        $verificationToken = bin2hex(random_bytes(32));

        $sql = "INSERT INTO users (username, email, password, verification_token) 
                VALUES (?, ?, ?, ?)";

        $result = $this->db->query($sql, [
            $username,
            $email,
            $hashedPassword,
            $verificationToken
        ]);

        return $result ? $verificationToken : false;
    }

    /**
     * Busca un usuario por su dirección de correo electrónico
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por su nombre de usuario
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por su ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por su token de verificación de email
     */
    public function findByVerificationToken($token) {
        $sql = "SELECT * FROM users WHERE verification_token = ?";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Busca un usuario por su token de restablecimiento de contraseña
     * (solo si el token todavía no ha expirado)
     */
    public function findByResetToken($token) {
        $sql = "SELECT * FROM users 
                WHERE reset_token = ? 
                AND reset_token_expiry > NOW()";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Marca la cuenta como verificada y elimina el token de verificación
     */
    public function verify($token) {
        $sql = "UPDATE users 
                SET verified = 1, verification_token = NULL 
                WHERE verification_token = ?";
        return $this->db->query($sql, [$token]) !== false;
    }

    /**
     * Genera un token de restablecimiento de contraseña
     * El token tiene una validez de 1 hora
     *
     * @return string|false  Token generado o false si falla
     */
    public function createResetToken($email) {
        $token  = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "UPDATE users 
                SET reset_token = ?, reset_token_expiry = ? 
                WHERE email = ?";

        $result = $this->db->query($sql, [$token, $expiry, $email]);
        return $result ? $token : false;
    }

    /**
     * Cambia la contraseña usando el token de restablecimiento
     * Después de usarlo, el token se elimina
     */
    public function resetPassword($token, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET password = ?, 
                    reset_token = NULL, 
                    reset_token_expiry = NULL 
                WHERE reset_token = ? 
                AND reset_token_expiry > NOW()";

        return $this->db->query($sql, [$hashedPassword, $token]) !== false;
    }

    /**
     * Actualiza el nombre de usuario y el email de un usuario
     */
    public function updateProfile($userId, $username, $email) {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        return $this->db->query($sql, [$username, $email, $userId]) !== false;
    }

    /**
     * Cambia la contraseña de un usuario (desde el perfil)
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->query($sql, [$hashedPassword, $userId]) !== false;
    }

    /**
     * Activa o desactiva las notificaciones por email de comentarios
     */
    public function updateNotificationSettings($userId, $notifyComments) {
        $sql = "UPDATE users SET notify_comments = ? WHERE id = ?";
        return $this->db->query($sql, [$notifyComments ? 1 : 0, $userId]) !== false;
    }

    /**
     * Comprueba si una contraseña en texto plano coincide con el hash guardado
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Comprueba si un email ya está registrado
     * (opcionalmente excluye a un usuario concreto, útil al editar el perfil)
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
     * Comprueba si un nombre de usuario ya está en uso
     * (opcionalmente excluye a un usuario concreto)
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

    /**
     * Elimina la cuenta de usuario.
     * likes/comments/images tienen ON DELETE CASCADE en el esquema.
     * Los ficheros de imagen deben borrarse antes con Image::deleteAllByUser si se desea.
     */
    public function deleteById($userId) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->query($sql, [$userId]) !== false;
    }
}
