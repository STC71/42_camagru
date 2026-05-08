<?php

/**
 * Resuelve la URL entrante y despacha la petición al controlador correcto.
 *
 * Mantiene un mapa de rutas conocidas y, si no encuentra una coincidencia,
 * intenta cargar un controlador dinámico a partir del primer segmento de la URL.
 */
class Router {
    private $routes = [];
    private $controller = 'HomeController';
    private $method = 'index';
    private $params = [];

    /**
     * Inicializa el mapa de rutas estáticas.
     */
    public function __construct() {
        $this->loadRoutes();
    }

    /**
     * Carga el mapa de rutas estáticas de la aplicación.
     *
     * Estructura: 'ruta' => ['NombreController', 'metodo'].
     */
    private function loadRoutes() {
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

    /**
     * Despacha la petición actual al controlador y método resueltos.
     */
    public function dispatch() {
        $url = $this->parseUrl();

        if (isset($this->routes[$url[0]])) {
            $this->controller = $this->routes[$url[0]][0];
            $this->method = $this->routes[$url[0]][1];
            unset($url[0]);
        } else if (isset($url[0])) {
            $controller = ucfirst($url[0]) . 'Controller';
            if (file_exists('../app/controllers/' . $controller . '.php')) {
                $this->controller = $controller;
                unset($url[0]);
            }
        }

        require_once '../app/controllers/' . $this->controller . '.php';

        $this->controller = new $this->controller;

        if (isset($url[1])) {
            $methodName = $this->toCamelCase($url[1]);
            
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } else if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Convierte una ruta en kebab-case a camelCase.
     */
    private function toCamelCase($string) {
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    /**
     * Parsea `$_GET['url']` y devuelve sus segmentos.
     */
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
