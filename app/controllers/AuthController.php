<?php

/**
 * AuthController
 * Handles authentication: login, register, logout, verification, password reset
 */
class AuthController extends Controller {

    public function login() {
        $this->requireGuest();

        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('login');
            }

            $username = $this->sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $userModel = $this->model('User');
            $user = $userModel->findByUsername($username);

            if (!$user) {
                $user = $userModel->findByEmail($username);
            }

            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                if (!$user['verified']) {
                    $_SESSION['flash_error'] = 'Please verify your email before logging in.';
                    $this->redirect('login');
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['flash_success'] = 'Welcome back, ' . $user['username'] . '!';
                $this->redirect('home');
            } else {
                $_SESSION['flash_error'] = 'Invalid username or password.';
                $this->redirect('login');
            }
        }

        $this->view('auth/login', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function register() {
        $this->requireGuest();

        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('register');
            }

            $username = $this->sanitize($_POST['username'] ?? '');
            $email = $this->sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];

            // Validation
            if (!Validator::required($username)) {
                $errors[] = 'Username is required.';
            } elseif (!Validator::username($username)) {
                $errors[] = 'Username must be 3-20 characters, alphanumeric and underscore only.';
            }

            if (!Validator::required($email)) {
                $errors[] = 'Email is required.';
            } elseif (!Validator::email($email)) {
                $errors[] = 'Invalid email format.';
            }

            if (!Validator::required($password)) {
                $errors[] = 'Password is required.';
            } elseif (!Validator::password($password)) {
                $errors[] = 'Password must be at least 8 characters with letters and numbers.';
            }

            if (!Validator::match($password, $confirmPassword)) {
                $errors[] = 'Passwords do not match.';
            }

            $userModel = $this->model('User');

            if ($userModel->usernameExists($username)) {
                $errors[] = 'Username already exists.';
            }

            if ($userModel->emailExists($email)) {
                $errors[] = 'Email already registered.';
            }

            if (empty($errors)) {
                $token = $userModel->create($username, $email, $password);
                if ($token) {
                    Mailer::sendVerificationEmail($email, $username, $token);
                    $_SESSION['flash_success'] = 'Registration successful! Please check your email to verify your account.';
                    $this->redirect('login');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }

            $_SESSION['flash_error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->redirect('register');
        }

        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);

        $this->view('auth/register', [
            'csrf_token' => $this->generateCSRF(),
            'form_data' => $formData
        ]);
    }

    public function verify() {
        $token = $_GET['token'] ?? '';

        if (!$token) {
            $_SESSION['flash_error'] = 'Invalid verification link.';
            $this->redirect('login');
        }

        $userModel = $this->model('User');
        $user = $userModel->findByVerificationToken($token);

        if (!$user) {
            $_SESSION['flash_error'] = 'Invalid or expired verification link.';
            $this->redirect('login');
        }

        if ($userModel->verify($token)) {
            $_SESSION['flash_success'] = 'Email verified successfully! You can now log in.';
        } else {
            $_SESSION['flash_error'] = 'Verification failed. Please try again.';
        }

        $this->redirect('login');
    }

    public function forgotPassword() {
        $this->requireGuest();

        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('forgot-password');
            }

            $email = $this->sanitize($_POST['email'] ?? '');

            if (!Validator::email($email)) {
                $_SESSION['flash_error'] = 'Invalid email format.';
                $this->redirect('forgot-password');
            }

            $userModel = $this->model('User');
            $user = $userModel->findByEmail($email);

            if ($user) {
                $token = $userModel->createResetToken($email);
                if ($token) {
                    Mailer::sendPasswordResetEmail($email, $user['username'], $token);
                }
            }

            // Always show success to prevent email enumeration
            $_SESSION['flash_success'] = 'If that email exists, a password reset link has been sent.';
            $this->redirect('login');
        }

        $this->view('auth/forgot-password', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function resetPassword() {
        $token = $_GET['token'] ?? '';

        if (!$token) {
            $_SESSION['flash_error'] = 'Invalid reset link.';
            $this->redirect('login');
        }

        $userModel = $this->model('User');
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $_SESSION['flash_error'] = 'Invalid or expired reset link.';
            $this->redirect('login');
        }

        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('reset-password?token=' . $token);
            }

            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];

            if (!Validator::password($password)) {
                $errors[] = 'Password must be at least 8 characters with letters and numbers.';
            }

            if (!Validator::match($password, $confirmPassword)) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                if ($userModel->resetPassword($token, $password)) {
                    $_SESSION['flash_success'] = 'Password reset successful! You can now log in.';
                    $this->redirect('login');
                } else {
                    $_SESSION['flash_error'] = 'Password reset failed. Please try again.';
                }
            } else {
                $_SESSION['flash_error'] = implode('<br>', $errors);
            }

            $this->redirect('reset-password?token=' . $token);
        }

        $this->view('auth/reset-password', [
            'csrf_token' => $this->generateCSRF(),
            'token' => $token
        ]);
    }

    public function logout() {
        session_destroy();
        header('Location: ' . $this->url('home'));
        exit();
    }
}
