<?php

/**
 * Router Class
 * Handles URL routing and dispatches requests to appropriate controllers
 */
class Router {
    private $routes = [];
    private $controller = 'HomeController';
    private $method = 'index';
    private $params = [];

    public function __construct() {
        $this->loadRoutes();
    }

    private function loadRoutes() {
        // Define routes: 'url' => ['controller', 'method']
        $this->routes = [
            '' => ['HomeController', 'index'],
            'home' => ['HomeController', 'index'],
            'gallery' => ['GalleryController', 'index'],
            'editor' => ['EditorController', 'index'],
            'login' => ['AuthController', 'login'],
            'register' => ['AuthController', 'register'],
            'logout' => ['AuthController', 'logout'],
            'verify' => ['AuthController', 'verify'],
            'forgot-password' => ['AuthController', 'forgotPassword'],
            'reset-password' => ['AuthController', 'resetPassword'],
            'profile' => ['UserController', 'profile'],
            'settings' => ['UserController', 'settings'],
        ];
    }

    public function dispatch() {
        $url = $this->parseUrl();

        // Check if route exists
        if (isset($this->routes[$url[0]])) {
            $this->controller = $this->routes[$url[0]][0];
            $this->method = $this->routes[$url[0]][1];
            unset($url[0]);
        } else if (isset($url[0])) {
            // Try to load controller dynamically
            $controller = ucfirst($url[0]) . 'Controller';
            if (file_exists('../app/controllers/' . $controller . '.php')) {
                $this->controller = $controller;
                unset($url[0]);
            }
        }

        // Require controller
        require_once '../app/controllers/' . $this->controller . '.php';

        // Instantiate controller
        $this->controller = new $this->controller;

        // Check if method exists
        if (isset($url[1])) {
            // Convert kebab-case to camelCase for method names
            $methodName = $this->toCamelCase($url[1]);
            
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } else if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call method with params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Convert kebab-case to camelCase
     */
    private function toCamelCase($string) {
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [''];
    }
}
