<?php

/**
 * UserController
 * Handles user profile and settings
 */
class UserController extends Controller {
/**
 * Método `profile` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `profile` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->profile();
 * ```
 */

/**
 * Método `profile` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `profile` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->profile();
 * ```
 */
    public function profile() {
        $this->requireAuth();

        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $imageModel = $this->model('Image');
        $userImages = $imageModel->findByUserId($_SESSION['user_id']);
        $imageCount = $imageModel->countByUser($_SESSION['user_id']);

        $this->view('user/profile', [
            'user' => $user,
            'images' => $userImages,
            'image_count' => $imageCount
        ]);
    }
/**
 * Establece el valor de tings proporcionado.
 *
 * Descripción detallada:
 * Este método `settings` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->settings();
 * ```
 */

/**
 * Establece el valor de tings proporcionado.
 *
 * Descripción detallada:
 * Este método `settings` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->settings();
 * ```
 */
    public function settings() {
        $this->requireAuth();

        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('settings');
            }

            $action = $_POST['action'] ?? '';

            if ($action === 'update_profile') {
                $this->updateProfile($userModel, $user);
            } elseif ($action === 'change_password') {
                $this->changePassword($userModel, $user);
            } elseif ($action === 'update_notifications') {
                $this->updateNotifications($userModel);
            }
        }

        $this->view('user/settings', [
            'user' => $user,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
/**
 * Método `updateProfile` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updateProfile` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userModel Descripción detallada de $userModel.
 * @param mixed $user Descripción detallada de $user.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateProfile($userModel, $user);
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
 * @param mixed $userModel Descripción detallada de $userModel.
 * @param mixed $user Descripción detallada de $user.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateProfile($userModel, $user);
 * ```
 */
    private function updateProfile($userModel, $user) {
        $username = $this->sanitize($_POST['username'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');

        $errors = [];

        if (!Validator::username($username)) {
            $errors[] = 'Invalid username format.';
        }

        if (!Validator::email($email)) {
            $errors[] = 'Invalid email format.';
        }

        if ($userModel->usernameExists($username, $_SESSION['user_id'])) {
            $errors[] = 'Username already taken.';
        }

        if ($userModel->emailExists($email, $_SESSION['user_id'])) {
            $errors[] = 'Email already in use.';
        }

        if (empty($errors)) {
            if ($userModel->updateProfile($_SESSION['user_id'], $username, $email)) {
                $_SESSION['username'] = $username;
                $_SESSION['flash_success'] = 'Profile updated successfully.';
            } else {
                $_SESSION['flash_error'] = 'Failed to update profile.';
            }
        } else {
            $_SESSION['flash_error'] = implode('<br>', $errors);
        }

        $this->redirect('settings');
    }
/**
 * Método `changePassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `changePassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userModel Descripción detallada de $userModel.
 * @param mixed $user Descripción detallada de $user.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->changePassword($userModel, $user);
 * ```
 */

/**
 * Método `changePassword` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `changePassword` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userModel Descripción detallada de $userModel.
 * @param mixed $user Descripción detallada de $user.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->changePassword($userModel, $user);
 * ```
 */
    private function changePassword($userModel, $user) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (!$userModel->verifyPassword($currentPassword, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        }

        if (!Validator::password($newPassword)) {
            $errors[] = 'New password must be at least 8 characters with letters and numbers.';
        }

        if (!Validator::match($newPassword, $confirmPassword)) {
            $errors[] = 'New passwords do not match.';
        }

        if (empty($errors)) {
            if ($userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                $_SESSION['flash_success'] = 'Password changed successfully.';
            } else {
                $_SESSION['flash_error'] = 'Failed to change password.';
            }
        } else {
            $_SESSION['flash_error'] = implode('<br>', $errors);
        }

        $this->redirect('settings');
    }
/**
 * Método `updateNotifications` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updateNotifications` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userModel Descripción detallada de $userModel.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateNotifications($userModel);
 * ```
 */

/**
 * Método `updateNotifications` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `updateNotifications` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $userModel Descripción detallada de $userModel.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->updateNotifications($userModel);
 * ```
 */
    private function updateNotifications($userModel) {
        $notifyComments = isset($_POST['notify_comments']) ? 1 : 0;

        if ($userModel->updateNotificationSettings($_SESSION['user_id'], $notifyComments)) {
            $_SESSION['flash_success'] = 'Notification settings updated.';
        } else {
            $_SESSION['flash_error'] = 'Failed to update settings.';
        }

        $this->redirect('settings');
    }
}
