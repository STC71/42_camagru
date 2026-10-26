<?php

/**
 * =============================================================================
 * Controller.php - Clase base de todos los controladores
 * =============================================================================
 * Todos los controladores (AuthController, EditorController, etc.) heredan
 * de esta clase. Aquí ponemos las herramientas comunes que todos necesitan.
 *
 * Analogía cotidiana:
 * Imagina que tienes varios departamentos en una empresa (Ventas, RRHH,
 * Producción...). Todos necesitan usar la fotocopiadora, el teléfono y el
 * sistema de fichajes. Esta clase es como el "departamento de servicios
 * generales" que proporciona esas herramientas a todo el mundo.
 * =============================================================================
 */
class Controller {

    /**
     * Carga un modelo (clase que habla con la base de datos)
     *
     * Ejemplo de uso:
     *   $userModel = $this->model('User');
     *   $user = $userModel->findById(5);
     */
    protected function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    /**
     * Carga una vista (archivo HTML/PHP) y le pasa datos
     *
     * Ejemplo de uso:
     *   $this->view('gallery/index', [
     *       'images' => $listaDeImagenes,
     *       'page'   => 1
     *   ]);
     */
    protected function view($view, $data = []) {
        // Convertimos el array $data en variables normales
        // para que en la vista podamos escribir $images en vez de $data['images']
        extract($data);
        require_once __DIR__ . '/../views/' . $view . '.php';
    }

    /**
     * Redirige al usuario a otra página de la aplicación
     *
     * Ejemplo:
     *   $this->redirect('login');
     *   $this->redirect('gallery');
     */
    protected function redirect($url) {
        header('Location: ' . $this->url($url));
        exit();
    }

    /**
     * Construye una URL completa a partir de una ruta relativa
     *
     * Ejemplo:
     *   $this->url('uploads/images/foto.png')
     *   → http://localhost:8080/uploads/images/foto.png
     */
    protected function url($path = '') {
        $baseUrl = rtrim(Config::get('APP_URL', 'http://localhost:8080'), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Envía una respuesta JSON al navegador y termina la ejecución
     * (muy usado en las peticiones AJAX)
     *
     * Ejemplo:
     *   $this->json(['success' => true, 'message' => 'Like añadido']);
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Devuelve true si la petición actual es de tipo POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Devuelve true si la petición actual es de tipo GET
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Comprueba que el token CSRF recibido sea válido
     *
     * ¿Qué es CSRF?
     * Imagina que alguien te engaña para que hagas clic en un enlace malicioso
     * mientras estás logueado en Camagru. Sin protección CSRF, ese enlace
     * podría borrar todas tus fotos. El token CSRF es como un "sello secreto"
     * que solo tu navegador conoce.
     */
    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        // hash_equals evita ataques de temporización
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    /**
     * Genera (o recupera) el token CSRF de la sesión actual
     */
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Limpia un texto para evitar ataques XSS
     *
     * Ejemplo:
     *   Entrada:  <script>alert('hack')</script>
     *   Salida:   &lt;script&gt;alert('hack')&lt;/script&gt;
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
     * Obliga a que el usuario esté logueado.
     * Si no lo está, le manda al login.
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            // Peticiones AJAX/fetch: devolver JSON 401 en lugar de redirect HTML
            $isAjax = (
                (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                || (isset($_SERVER['HTTP_ACCEPT'])
                    && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                || (isset($_SERVER['CONTENT_TYPE'])
                    && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
                || (isset($_SERVER['HTTP_CONTENT_TYPE'])
                    && strpos($_SERVER['HTTP_CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false
                    && !empty($_SERVER['HTTP_SEC_FETCH_MODE'])
                    && $_SERVER['HTTP_SEC_FETCH_MODE'] === 'cors')
            );
            // Heurística simple: rutas de editor/gallery por POST suelen ser AJAX
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            if ($isAjax || (
                $_SERVER['REQUEST_METHOD'] === 'POST'
                && (strpos($uri, '/editor/') !== false || strpos($uri, '/gallery/') !== false)
            )) {
                $this->json(['success' => false, 'message' => 'Unauthorized. Please log in again.'], 401);
            }
            $_SESSION['flash_error'] = 'You must be logged in to access this page.';
            $this->redirect('login');
        }
    }

    /**
     * Obliga a que el usuario NO esté logueado.
     * (Útil en las páginas de login y registro)
     */
    protected function requireGuest() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('home');
        }
    }
}
