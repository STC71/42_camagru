<?php

/**
 * Controller Base Class
 * All controllers extend this class
 */
class Controller {
    
    protected function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    protected function view($view, $data = []) {
        extract($data);
        require_once '../app/views/' . $view . '.php';
    }

    protected function redirect($url) {
        header('Location: ' . $this->url($url));
        exit();
    }

    protected function url($path = '') {
        $baseUrl = rtrim(Config::get('APP_URL', 'http://localhost:8080'), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
            return $data;
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'You must be logged in to access this page.';
            $this->redirect('login');
        }
    }

    protected function requireGuest() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('home');
        }
    }
}
