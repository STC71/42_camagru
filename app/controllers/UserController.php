<?php

/**
 * UserController — perfil, ajustes y baja de cuenta.
 * La baja de cuenta es opcional (no exigida por el subject); no altera el mandatory.
 */
class UserController extends Controller {

    public function profile() {
        $this->requireAuth();

        $userModel = $this->model('User');
        $imageModel = $this->model('Image');
        $user = $userModel->findById($_SESSION['user_id']);
        $images = $imageModel->findByUserId($_SESSION['user_id']);

        $this->view('user/profile', [
            'user' => $user,
            'images' => $images,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function settings() {
        $this->requireAuth();

        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = function_exists('__')
                    ? __('flash.invalid_request')
                    : 'Invalid request. Please try again.';
                $this->redirect('settings');
            }

            $action = $_POST['action'] ?? '';
            $userModel = $this->model('User');
            $user = $userModel->findById($_SESSION['user_id']);

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
                case 'delete_account':
                    $this->deleteAccount($userModel, $user);
                    break;
                default:
                    $_SESSION['flash_error'] = function_exists('__')
                        ? __('flash.invalid_request')
                        : 'Invalid request. Please try again.';
                    $this->redirect('settings');
            }
            return;
        }

        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $this->view('user/settings', [
            'user' => $user,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    private function updateProfile($userModel, $user) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $errors = [];

        if (!Validator::username($username)) {
            $errors[] = function_exists('__') ? __('flash.invalid_username') : 'Invalid username format.';
        }
        if (!Validator::email($email)) {
            $errors[] = function_exists('__') ? __('flash.invalid_email') : 'Invalid email format.';
        }
        if ($userModel->usernameExists($username, $user['id'])) {
            $errors[] = function_exists('__') ? __('flash.username_taken') : 'Username already taken.';
        }
        if ($userModel->emailExists($email, $user['id'])) {
            $errors[] = function_exists('__') ? __('flash.email_taken') : 'Email already in use.';
        }

        if (empty($errors)) {
            if ($userModel->updateProfile($user['id'], $username, $email)) {
                $_SESSION['username'] = $username;
                $_SESSION['flash_success'] = function_exists('__')
                    ? __('flash.profile_updated')
                    : 'Profile updated successfully.';
            } else {
                $_SESSION['flash_error'] = function_exists('__')
                    ? __('flash.profile_failed')
                    : 'Failed to update profile.';
            }
        } else {
            $_SESSION['flash_error'] = implode('<br>', $errors);
        }
        $this->redirect('settings');
    }

    private function changePassword($userModel, $user) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $errors = [];

        if (!$userModel->verifyPassword($current, $user['password'])) {
            $errors[] = function_exists('__') ? __('flash.wrong_password') : 'Current password is incorrect.';
        }
        if (!Validator::password($new)) {
            $errors[] = function_exists('__') ? __('flash.weak_password') : 'New password is too weak.';
        }
        if ($new !== $confirm) {
            $errors[] = function_exists('__') ? __('flash.password_mismatch') : 'Passwords do not match.';
        }

        if (empty($errors)) {
            if ($userModel->updatePassword($user['id'], $new)) {
                $_SESSION['flash_success'] = function_exists('__')
                    ? __('flash.password_changed')
                    : 'Password changed successfully.';
            } else {
                $_SESSION['flash_error'] = function_exists('__')
                    ? __('flash.password_change_failed')
                    : 'Failed to change password.';
            }
        } else {
            $_SESSION['flash_error'] = implode('<br>', $errors);
        }
        $this->redirect('settings');
    }

    private function updateNotifications($userModel) {
        $notifyComments = isset($_POST['notify_comments']) ? 1 : 0;
        if ($userModel->updateNotificationSettings($_SESSION['user_id'], $notifyComments)) {
            $_SESSION['flash_success'] = function_exists('__')
                ? __('flash.notify_updated')
                : 'Notification settings updated.';
        } else {
            $_SESSION['flash_error'] = function_exists('__')
                ? __('flash.notify_failed')
                : 'Failed to update settings.';
        }
        $this->redirect('settings');
    }

    /**
     * Baja de cuenta (opcional).
     * - Exige contraseña actual + CSRF.
     * - Checkbox: borrar también los ficheros de fotos en disco.
     * - La BD ya tiene ON DELETE CASCADE en images/likes/comments.
     */
    private function deleteAccount($userModel, $user) {
        $password = $_POST['password'] ?? '';
        $deletePhotos = isset($_POST['delete_photos']);

        if (!$userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['flash_error'] = function_exists('__')
                ? __('flash.wrong_password')
                : 'Current password is incorrect.';
            $this->redirect('settings');
        }

        $userId = (int)$user['id'];

        if ($deletePhotos) {
            $imageModel = $this->model('Image');
            $imageModel->deleteAllByUser($userId);
        }

        if ($userModel->deleteById($userId)) {
            // Cerrar sesión
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();
            session_start();
            $_SESSION['flash_success'] = function_exists('__')
                ? __('flash.account_deleted')
                : 'Your account has been deleted.';
            $this->redirect('login');
        }

        $_SESSION['flash_error'] = function_exists('__')
            ? __('flash.account_delete_failed')
            : 'Could not delete the account. Please try again.';
        $this->redirect('settings');
    }
}
