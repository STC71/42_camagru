<?php

/**
 * =============================================================================
 * UserController.php
 * =============================================================================
 * Este controlador gestiona el perfil del usuario y sus ajustes personales.
 *
 * Funcionalidades:
 * - Ver el perfil propio + lista de fotos
 * - Cambiar nombre de usuario y email
 * - Cambiar la contraseña
 * - Activar/desactivar notificaciones de comentarios
 *
 * Analogía cotidiana:
 * Es como la sección "Mi cuenta" de cualquier tienda online o red social.
 * =============================================================================
 */
class UserController extends Controller {

    /**
     * -------------------------------------------------------------------------
     * Página de perfil del usuario
     * -------------------------------------------------------------------------
     * Muestra los datos del usuario y todas las fotos que ha subido.
     */
    public function profile() {
        $this->requireAuth();

        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $imageModel = $this->model('Image');
        $userImages = $imageModel->findByUserId($_SESSION['user_id']);

        $this->view('user/profile', [
            'user'   => $user,
            'images' => $userImages
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Página de ajustes (Settings)
     * -------------------------------------------------------------------------
     * Aquí el usuario puede modificar sus datos personales y preferencias.
     * El mismo método atiende tanto la visualización como el envío de formularios.
     */
    public function settings() {
        $this->requireAuth();

        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        // Si se ha enviado un formulario
        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('settings');
            }

            $action = $_POST['action'] ?? '';

            // Según el botón que haya pulsado el usuario, ejecutamos una acción u otra
            switch ($action) {
                case 'update_profile':
                    $this->updateProfile($userModel, $user);
                    break;
                case 'change_password':
                    $this->changePassword($userModel, $user);
                    break;
                case 'update_notifications':
                    $this->updateNotifications($userModel);
                    break;
            }
        }

        // Volvemos a cargar los datos actualizados del usuario
        $user = $userModel->findById($_SESSION['user_id']);

        $this->view('user/settings', [
            'user'       => $user,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Actualiza el nombre de usuario y el email
     * -------------------------------------------------------------------------
     */
    private function updateProfile($userModel, $user) {
        $username = $this->sanitize($_POST['username'] ?? '');
        $email    = $this->sanitize($_POST['email'] ?? '');

        $errors = [];

        if (!Validator::username($username)) {
            $errors[] = 'Invalid username format.';
        }
        if (!Validator::email($email)) {
            $errors[] = 'Invalid email format.';
        }
        // Comprobamos que no estén ya en uso por otro usuario
        if ($userModel->usernameExists($username, $_SESSION['user_id'])) {
            $errors[] = 'Username already taken.';
        }
        if ($userModel->emailExists($email, $_SESSION['user_id'])) {
            $errors[] = 'Email already in use.';
        }

        if (empty($errors)) {
            if ($userModel->updateProfile($_SESSION['user_id'], $username, $email)) {
                // Actualizamos también la sesión para que el cambio se vea inmediatamente
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
     * -------------------------------------------------------------------------
     * Cambia la contraseña del usuario
     * -------------------------------------------------------------------------
     * Exige la contraseña actual por seguridad.
     */
    private function changePassword($userModel, $user) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        // Verificamos que la contraseña actual sea correcta
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
     * -------------------------------------------------------------------------
     * Actualiza la preferencia de notificaciones por email
     * -------------------------------------------------------------------------
     */
    private function updateNotifications($userModel) {
        // Si el checkbox está marcado llega el valor, si no llega vacío
        $notifyComments = isset($_POST['notify_comments']) ? 1 : 0;

        if ($userModel->updateNotificationSettings($_SESSION['user_id'], $notifyComments)) {
            $_SESSION['flash_success'] = 'Notification settings updated.';
        } else {
            $_SESSION['flash_error'] = 'Failed to update settings.';
        }

        $this->redirect('settings');
    }
}
