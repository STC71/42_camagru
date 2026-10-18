<?php

/**
 * =============================================================================
 * Router.php - El "recepcionista" de la aplicación
 * =============================================================================
 * Cuando alguien visita una URL (por ejemplo /gallery o /login), este archivo
 * decide a qué controlador y a qué método hay que llamar.
 *
 * Analogía cotidiana:
 * Imagina un gran edificio de oficinas. El recepcionista mira el nombre
 * que dice el visitante y lo dirige al departamento correcto.
 * El Router hace exactamente eso con las URLs.
 *
 * Ejemplo:
 *   URL: /editor
 *   → Llama a EditorController::index()
 *
 *   URL: /gallery/like
 *   → Llama a GalleryController::like()
 * =============================================================================
 */
class Router {

    /** Mapa de rutas conocidas (ruta → [Controlador, método]) */
    private $routes = [];

    /** Controlador que se va a ejecutar (por defecto HomeController) */
    private $controller = 'HomeController';

    /** Método que se va a ejecutar (por defecto index) */
    private $method = 'index';

    /** Parámetros extra que se pasan al método */
    private $params = [];

    /**
     * Al crear el Router cargamos todas las rutas que conocemos
     */
    public function __construct() {
        $this->loadRoutes();
    }

    /**
     * Definimos aquí todas las rutas "bonitas" de la aplicación
     *
     * Formato:
     *   'ruta' => ['NombreDelControlador', 'nombreDelMetodo']
     */
    private function loadRoutes() {
        $this->routes = [
            ''                => ['HomeController',    'index'],
            'home'            => ['HomeController',    'index'],
            'gallery'         => ['GalleryController', 'index'],
            'editor'          => ['EditorController',  'index'],
            'login'           => ['AuthController',    'login'],
            'register'        => ['AuthController',    'register'],
            'logout'          => ['AuthController',    'logout'],
            'verify'          => ['AuthController',    'verify'],
            'forgot-password' => ['AuthController',    'forgotPassword'],
            'reset-password'  => ['AuthController',    'resetPassword'],
            'profile'         => ['UserController',    'profile'],
            'settings'        => ['UserController',    'settings'],
        ];
    }

    /**
     * Método principal: analiza la URL y ejecuta el controlador correcto
     */
    public function dispatch() {
        // Convertimos la URL en un array de segmentos
        // Ejemplo: /gallery/like/5  →  ['gallery', 'like', '5']
        $url = $this->parseUrl();

        // 1. ¿La primera parte de la URL está en nuestro mapa de rutas?
        if (isset($this->routes[$url[0]])) {
            $this->controller = $this->routes[$url[0]][0];
            $this->method     = $this->routes[$url[0]][1];
            unset($url[0]); // Ya hemos usado este segmento
        }
        // 2. Si no está en el mapa, intentamos cargar un controlador dinámicamente
        else if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Cargamos el archivo del controlador
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';

        // Creamos una instancia del controlador
        $this->controller = new $this->controller;

        // 3. ¿Hay un segundo segmento? Puede ser el nombre del método
        if (isset($url[1])) {
            // Convertimos kebab-case a camelCase (ej: forgot-password → forgotPassword)
            $methodName = $this->toCamelCase($url[1]);

            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } elseif (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Todo lo que quede en $url se considera parámetros
        $this->params = $url ? array_values($url) : [];

        // ¡Ejecutamos el método del controlador pasando los parámetros!
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Convierte una cadena kebab-case a camelCase
     *
     * Ejemplo:
     *   "forgot-password"  →  "forgotPassword"
     *   "reset-password"   →  "resetPassword"
     */
    private function toCamelCase($string) {
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    /**
     * Extrae y limpia la URL de la petición
     *
     * Ejemplo:
     *   URL real: /index.php?url=gallery/like
     *   Resultado: ['gallery', 'like']
     */
    private function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');                 // Quitamos la barra final
            $url = filter_var($url, FILTER_SANITIZE_URL);    // Limpiamos caracteres peligrosos
            $url = explode('/', $url);                       // Separamos por barras
            return $url;
        }
        return ['']; // Si no hay URL, vamos a la página de inicio
    }
}
