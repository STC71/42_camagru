<?php

/**
 * =============================================================================
 * AuthController.php
 * =============================================================================
 * Este controlador se encarga de TODO lo relacionado con la identidad del usuario:
 * - Registro de nuevas cuentas
 * - Verificación del correo electrónico
 * - Inicio y cierre de sesión
 * - Recuperación de contraseña
 *
 * Analogía cotidiana:
 * Es como el departamento de recepción y seguridad de un edificio.
 * Decide quién puede entrar, comprueba el carnet, da llaves temporales
 * cuando alguien pierde las suyas, etc.
 * =============================================================================
 */
class AuthController extends Controller {

    /**
     * -------------------------------------------------------------------------
     * Página de inicio de sesión (Login)
     * -------------------------------------------------------------------------
     * Si el usuario envía el formulario (POST), intentamos autenticarlo.
     * Si solo visita la página (GET), le mostramos el formulario.
     */
    public function login() {
        // Solo pueden ver esta página los usuarios que NO están logueados
        $this->requireGuest();

        if ($this->isPost()) {
            // Protección CSRF: comprobamos que la petición viene de nuestro formulario
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('login');
            }

            $username = $this->sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $userModel = $this->model('User');

            // Permitimos login tanto con username como con email
            $user = $userModel->findByUsername($username);
            if (!$user) {
                $user = $userModel->findByEmail($username);
            }

            // Verificamos la contraseña
            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                // El usuario debe haber confirmado su email antes de poder entrar
                if (!$user['verified']) {
                    $_SESSION['flash_error'] = 'Please verify your email before logging in.';
                    $this->redirect('login');
                }

                // ¡Login correcto! Guardamos los datos en la sesión
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['flash_success'] = 'Welcome back, ' . $user['username'] . '!';
                $this->redirect('home');
            } else {
                $_SESSION['flash_error'] = 'Invalid username or password.';
                $this->redirect('login');
            }
        }

        // Si llegamos aquí es porque es un GET → mostramos el formulario
        $this->view('auth/login', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Página de registro de nuevos usuarios
     * -------------------------------------------------------------------------
     * Validamos todos los datos, creamos el usuario y le enviamos un email
     * de verificación.
     */
    public function register() {
        $this->requireGuest();

        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('register');
            }

            $username        = $this->sanitize($_POST['username'] ?? '');
            $email           = $this->sanitize($_POST['email'] ?? '');
            $password        = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];

            // ---------- Validaciones ----------
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
                // La contraseña debe tener mínimo 8 caracteres + letras + números
                $errors[] = 'Password must be at least 8 characters with letters and numbers.';
            }

            if (!Validator::match($password, $confirmPassword)) {
                $errors[] = 'Passwords do not match.';
            }

            $userModel = $this->model('User');

            // Comprobamos que el username y el email no existan ya
            if ($userModel->usernameExists($username)) {
                $errors[] = 'Username already exists.';
            }
            if ($userModel->emailExists($email)) {
                $errors[] = 'Email already registered.';
            }

            // Si no hay errores, creamos el usuario
            if (empty($errors)) {
                $token = $userModel->create($username, $email, $password);

                if ($token) {
                    // Enviamos el email de verificación
                    Mailer::sendVerificationEmail($email, $username, $token);
                    $_SESSION['flash_success'] = 'Registration successful! Please check your email to verify your account.';
                    $this->redirect('login');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }

            // Si llegamos aquí hubo errores → los guardamos y volvemos al formulario
            $_SESSION['flash_error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST; // Para no perder lo que el usuario ya escribió
            $this->redirect('register');
        }

        // Recuperamos los datos del formulario si los había
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);

        $this->view('auth/register', [
            'csrf_token' => $this->generateCSRF(),
            'form_data'  => $formData
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Verificación de la cuenta por email
     * -------------------------------------------------------------------------
     * El usuario hace clic en el enlace que recibió por correo.
     * El enlace contiene un token único. Si el token es válido,
     * marcamos la cuenta como verificada.
     */
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

    /**
     * -------------------------------------------------------------------------
     * Solicitud de recuperación de contraseña ("He olvidado mi contraseña")
     * -------------------------------------------------------------------------
     * El usuario introduce su email. Si existe, le enviamos un enlace
     * temporal para que pueda cambiar la contraseña.
     *
     * Nota de seguridad: siempre mostramos el mismo mensaje de éxito,
     * aunque el email no exista. Así no revelamos qué correos están registrados.
     */
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

            // Solo enviamos el correo si el usuario realmente existe
            if ($user) {
                $token = $userModel->createResetToken($email);
                if ($token) {
                    Mailer::sendPasswordResetEmail($email, $user['username'], $token);
                }
            }

            // Mensaje genérico (no revelamos si el email existe o no)
            $_SESSION['flash_success'] = 'If that email exists, a password reset link has been sent.';
            $this->redirect('login');
        }

        $this->view('auth/forgot-password', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Formulario para establecer la nueva contraseña
     * -------------------------------------------------------------------------
     * El usuario llega aquí desde el enlace del email.
     * Comprobamos que el token sea válido y no haya expirado.
     */
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

        // Si el usuario envía el formulario con la nueva contraseña
        if ($this->isPost()) {
            if (!$this->validateCSRF()) {
                $_SESSION['flash_error'] = 'Invalid request. Please try again.';
                $this->redirect('reset-password?token=' . $token);
            }

            $password        = $_POST['password'] ?? '';
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

        // Mostramos el formulario
        $this->view('auth/reset-password', [
            'csrf_token' => $this->generateCSRF(),
            'token'      => $token
        ]);
    }

    /**
     * -------------------------------------------------------------------------
     * Cierre de sesión (Logout)
     * -------------------------------------------------------------------------
     * Destruye la sesión actual y redirige al inicio.
     */
    public function logout() {
        session_destroy();
        header('Location: ' . $this->url('home'));
        exit();
    }
}
