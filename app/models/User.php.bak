<?php

/**
 * User Model
 * Handles all user-related database operations
 */
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

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

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findByVerificationToken($token) {
        $sql = "SELECT * FROM users WHERE verification_token = ?";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findByResetToken($token) {
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function verify($token) {
        $sql = "UPDATE users SET verified = 1, verification_token = NULL WHERE verification_token = ?";
        return $this->db->query($sql, [$token]) !== false;
    }

    public function createResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $result = $this->db->query($sql, [$token, $expiry, $email]);

        return $result ? $token : false;
    }

    public function resetPassword($token, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL 
                WHERE reset_token = ? AND reset_token_expiry > NOW()";
        return $this->db->query($sql, [$hashedPassword, $token]) !== false;
    }

    public function updateProfile($userId, $username, $email) {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        return $this->db->query($sql, [$username, $email, $userId]) !== false;
    }

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->query($sql, [$hashedPassword, $userId]) !== false;
    }

    public function updateNotificationSettings($userId, $notifyComments) {
        $sql = "UPDATE users SET notify_comments = ? WHERE id = ?";
        return $this->db->query($sql, [$notifyComments ? 1 : 0, $userId]) !== false;
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

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
