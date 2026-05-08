<?php

/**
 * Clase base con utilidades compartidas para todos los controladores.
 *
 * Agrupa acceso a modelos, vistas, redirecciones, respuestas JSON, CSRF,
 * saneamiento de entrada y comprobaciones de autenticación.
 */
class Controller {

    /**
     * Carga y devuelve una instancia de un modelo de `app/models`.
     */
    protected function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    /**
     * Carga una vista y expone los datos como variables locales.
     */
    protected function view($view, $data = []) {
        extract($data);
        require_once '../app/views/' . $view . '.php';
    }

    /**
     * Redirige a otra URL dentro de la aplicación.
     */
    protected function redirect($url) {
        header('Location: ' . $this->url($url));
        exit();
    }

    /**
     * Construye una URL absoluta a partir de `APP_URL`.
     */
    protected function url($path = '') {
        $baseUrl = rtrim(Config::get('APP_URL', 'http://localhost:8080'), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Envía una respuesta JSON y termina la ejecución.
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Devuelve `true` si la petición actual es `POST`.
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Devuelve `true` si la petición actual es `GET`.
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Comprueba que el token CSRF recibido coincida con el de sesión.
     */
    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    /**
     * Genera y guarda en sesión un token CSRF si todavía no existe.
     */
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Sanea recursivamente una entrada para reducir riesgos de XSS.
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
            return $data;
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Redirige al login si no existe una sesión autenticada.
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'You must be logged in to access this page.';
            $this->redirect('login');
        }
    }

    /**
     * Redirige a home si ya existe una sesión autenticada.
     */
    protected function requireGuest() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('home');
        }
    }
}
