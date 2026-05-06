<?php

/**
 * User Model
 * Handles all user-related database operations
 */
class User {
    private $db;
/**
 * Método `__construct` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `__construct` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->__construct();
 * ```
 */

/**
 * Método `__construct` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `__construct` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->__construct();
 * ```
 */
    public function __construct() {
        $this->db = Database::getInstance();
    }
/**
 * Persiste datos en la base de datos relacionados con este recurso.
 *
 * Descripción detallada:
 * Este método `create` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $username Descripción detallada de $username.
 * @param mixed $email Descripción detallada de $email.
 * @param mixed $password Descripción detallada de $password.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->create($username, $email, $password);
 * ```
 */

/**
 * Persiste datos en la base de datos relacionados con este recurso.
 *
 * Descripción detallada:
 * Este método `create` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $username Descripción detallada de $username.
 * @param mixed $email Descripción detallada de $email.
 * @param mixed $password Descripción detallada de $password.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->create($username, $email, $password);
 * ```
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
 * Método `findByEmail` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByEmail` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $email Descripción detallada de $email.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByEmail($email);
 * ```
 */

/**
 * Método `findByEmail` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByEmail` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $email Descripción detallada de $email.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByEmail($email);
 * ```
 */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt ? $stmt->fetch() : false;
    }
/**
 * Método `findByUsername` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByUsername` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $username Descripción detallada de $username.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByUsername($username);
 * ```
 */

/**
 * Método `findByUsername` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByUsername` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $username Descripción detallada de $username.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByUsername($username);
 * ```
 */
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt ? $stmt->fetch() : false;
    }
/**
 * Método `findById` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findById` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $id Descripción detallada de $id.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findById($id);
 * ```
 */

/**
 * Método `findById` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findById` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $id Descripción detallada de $id.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findById($id);
 * ```
 */
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }
/**
 * Método `findByVerificationToken` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByVerificationToken` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByVerificationToken($token);
 * ```
 */

/**
 * Método `findByVerificationToken` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByVerificationToken` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByVerificationToken($token);
 * ```
 */
    public function findByVerificationToken($token) {
        $sql = "SELECT * FROM users WHERE verification_token = ?";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }
/**
 * Método `findByResetToken` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByResetToken` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByResetToken($token);
 * ```
 */

/**
 * Método `findByResetToken` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `findByResetToken` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->findByResetToken($token);
 * ```
 */
    public function findByResetToken($token) {
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
        $stmt = $this->db->query($sql, [$token]);
        return $stmt ? $stmt->fetch() : false;
    }
/**
 * Método `verify` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `verify` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->verify($token);
 * ```
 */

/**
 * Método `verify` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `verify` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->verify($token);
 * ```
 */
    public function verify($token) {
        $sql = "UPDATE users SET verified = 1, verification_token = NULL WHERE verification_token = ?";
        return $this->db->query($sql, [$token]) !== false;
    }
/**
 * Persiste datos en la base de datos relacionados con este recurso.
 *
 * Descripción detallada:
 * Este método `createResetToken` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $email Descripción detallada de $email.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->createResetToken($email);
 * ```
 */

/**
 * Persiste datos en la base de datos relacionados con este recurso.
 *
 * Descripción detallada:
 * Este método `createResetToken` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $email Descripción detallada de $email.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->createResetToken($email);
 * ```
 */
    public function createResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $result = $this->db->query($sql, [$token, $expiry, $email]);

        return $result ? $token : false;
    }
/**
 * Método `resetPassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `resetPassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 * @param mixed $newPassword Descripción detallada de $newPassword.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->resetPassword($token, $newPassword);
 * ```
 */

/**
 * Método `resetPassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `resetPassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $token Descripción detallada de $token.
 * @param mixed $newPassword Descripción detallada de $newPassword.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->resetPassword($token, $newPassword);
 * ```
 */
    public function resetPassword($token, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL 
                WHERE reset_token = ? AND reset_token_expiry > NOW()";
        return $this->db->query($sql, [$hashedPassword, $token]) !== false;
    }
/**
 * Método `updateProfile` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updateProfile` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $username Descripción detallada de $username.
 * @param mixed $email Descripción detallada de $email.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateProfile($userId, $username, $email);
 * ```
 */

/**
 * Método `updateProfile` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updateProfile` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $username Descripción detallada de $username.
 * @param mixed $email Descripción detallada de $email.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateProfile($userId, $username, $email);
 * ```
 */
    public function updateProfile($userId, $username, $email) {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        return $this->db->query($sql, [$username, $email, $userId]) !== false;
    }
/**
 * Método `updatePassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updatePassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $newPassword Descripción detallada de $newPassword.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updatePassword($userId, $newPassword);
 * ```
 */

/**
 * Método `updatePassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updatePassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $newPassword Descripción detallada de $newPassword.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updatePassword($userId, $newPassword);
 * ```
 */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->query($sql, [$hashedPassword, $userId]) !== false;
    }
/**
 * Método `updateNotificationSettings` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updateNotificationSettings` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $notifyComments Descripción detallada de $notifyComments.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateNotificationSettings($userId, $notifyComments);
 * ```
 */

/**
 * Método `updateNotificationSettings` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updateNotificationSettings` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userId Descripción detallada de $userId.
 * @param mixed $notifyComments Descripción detallada de $notifyComments.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateNotificationSettings($userId, $notifyComments);
 * ```
 */
    public function updateNotificationSettings($userId, $notifyComments) {
        $sql = "UPDATE users SET notify_comments = ? WHERE id = ?";
        return $this->db->query($sql, [$notifyComments ? 1 : 0, $userId]) !== false;
    }
/**
 * Método `verifyPassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `verifyPassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $password Descripción detallada de $password.
 * @param mixed $hash Descripción detallada de $hash.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->verifyPassword($password, $hash);
 * ```
 */

/**
 * Método `verifyPassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `verifyPassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $password Descripción detallada de $password.
 * @param mixed $hash Descripción detallada de $hash.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->verifyPassword($password, $hash);
 * ```
 */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
/**
 * Método `emailExists` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `emailExists` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $email Descripción detallada de $email.
 * @param mixed null Descripción detallada de null.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->emailExists($email, null);
 * ```
 */

/**
 * Método `emailExists` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `emailExists` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $email Descripción detallada de $email.
 * @param mixed null Descripción detallada de null.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->emailExists($email, null);
 * ```
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
 * Método `usernameExists` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `usernameExists` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $username Descripción detallada de $username.
 * @param mixed null Descripción detallada de null.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->usernameExists($username, null);
 * ```
 */

/**
 * Método `usernameExists` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `usernameExists` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $username Descripción detallada de $username.
 * @param mixed null Descripción detallada de null.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->usernameExists($username, null);
 * ```
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
