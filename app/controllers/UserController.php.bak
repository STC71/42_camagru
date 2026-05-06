<?php

/**
 * UserController
 * Handles user profile and settings
 */
class UserController extends Controller {

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
