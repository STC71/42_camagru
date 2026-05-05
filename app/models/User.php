<?php

/**
 * User Model
 * Handles all user-related database operations
 */
class User {
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

     * @param mixed $username Descripción del parámetro $username.

     * @param mixed $email Descripción del parámetro $email.

     * @param mixed $password Descripción del parámetro $password.

     * @return mixed

     */

    /**

     * Método create.

     *

     * @param mixed $username Descripción del parámetro $username.

     * @param mixed $email Descripción del parámetro $email.

     * @param mixed $password Descripción del parámetro $password.

     * @return mixed

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

     * Método findByEmail.

     *

     * @param mixed $email Descripción del parámetro $email.

     * @return mixed

     */

    /**

     * Método findByEmail.

     *

     * @param mixed $email Descripción del parámetro $email.

     * @return mixed

     */

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**

     * Método findByUsername.

     *

     * @param mixed $username Descripción del parámetro $username.

     * @return mixed

     */

    /**

     * Método findByUsername.

     *

     * @param mixed $username Descripción del parámetro $username.

     * @return mixed

     */

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt ? $stmt->fetch() : false;
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
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**

     * Método findByVerificationToken.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @return mixed

     */

    /**

     * Método findByVerificationToken.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @return mixed

     */

    public function findByVerificationToken($token) {
        $sql = "SELECT * FROM users WHERE verification_token = ?";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**

     * Método findByResetToken.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @return mixed

     */

    /**

     * Método findByResetToken.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @return mixed

     */

    public function findByResetToken($token) {
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    /**

     * Método verify.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @return mixed

     */

    /**

     * Método verify.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @return mixed

     */

    public function verify($token) {
        $sql = "UPDATE users SET verified = 1, verification_token = NULL WHERE verification_token = ?";
        return $this->db->query($sql, [$token]) !== false;
    }

    /**

     * Método createResetToken.

     *

     * @param mixed $email Descripción del parámetro $email.

     * @return mixed

     */

    /**

     * Método createResetToken.

     *

     * @param mixed $email Descripción del parámetro $email.

     * @return mixed

     */

    public function createResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $result = $this->db->query($sql, [$token, $expiry, $email]);

        return $result ? $token : false;
    }

    /**

     * Método resetPassword.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @param mixed $newPassword Descripción del parámetro $newPassword.

     * @return mixed

     */

    /**

     * Método resetPassword.

     *

     * @param mixed $token Descripción del parámetro $token.

     * @param mixed $newPassword Descripción del parámetro $newPassword.

     * @return mixed

     */

    public function resetPassword($token, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL 
                WHERE reset_token = ? AND reset_token_expiry > NOW()";
        return $this->db->query($sql, [$hashedPassword, $token]) !== false;
    }

    /**

     * Método updateProfile.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $username Descripción del parámetro $username.

     * @param mixed $email Descripción del parámetro $email.

     * @return mixed

     */

    /**

     * Método updateProfile.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $username Descripción del parámetro $username.

     * @param mixed $email Descripción del parámetro $email.

     * @return mixed

     */

    public function updateProfile($userId, $username, $email) {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        return $this->db->query($sql, [$username, $email, $userId]) !== false;
    }

    /**

     * Método updatePassword.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $newPassword Descripción del parámetro $newPassword.

     * @return mixed

     */

    /**

     * Método updatePassword.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $newPassword Descripción del parámetro $newPassword.

     * @return mixed

     */

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->query($sql, [$hashedPassword, $userId]) !== false;
    }

    /**

     * Método updateNotificationSettings.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $notifyComments Descripción del parámetro $notifyComments.

     * @return mixed

     */

    /**

     * Método updateNotificationSettings.

     *

     * @param mixed $userId Descripción del parámetro $userId.

     * @param mixed $notifyComments Descripción del parámetro $notifyComments.

     * @return mixed

     */

    public function updateNotificationSettings($userId, $notifyComments) {
        $sql = "UPDATE users SET notify_comments = ? WHERE id = ?";
        return $this->db->query($sql, [$notifyComments ? 1 : 0, $userId]) !== false;
    }

    /**

     * Método verifyPassword.

     *

     * @param mixed $password Descripción del parámetro $password.

     * @param mixed $hash Descripción del parámetro $hash.

     * @return mixed

     */

    /**

     * Método verifyPassword.

     *

     * @param mixed $password Descripción del parámetro $password.

     * @param mixed $hash Descripción del parámetro $hash.

     * @return mixed

     */

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**

     * Método emailExists.

     *

     * @param mixed $email Descripción del parámetro $email.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

     */

    /**

     * Método emailExists.

     *

     * @param mixed $email Descripción del parámetro $email.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

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

     * Método usernameExists.

     *

     * @param mixed $username Descripción del parámetro $username.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

     */

    /**

     * Método usernameExists.

     *

     * @param mixed $username Descripción del parámetro $username.

     * @param mixed $param Descripción del parámetro $param.

     * @return mixed

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
