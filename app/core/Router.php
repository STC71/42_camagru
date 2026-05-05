<?php

/**
 * Clase `Router`
 * Responsable de resolver las URLs entrantes y despachar la petición
 * al controlador y método correspondientes. Mantiene un mapa de rutas
 * predefinidas y permite carga dinámica de controladores cuando la ruta
 * no está explícitamente listada.
 */
class Router {
    private $routes = [];
    private $controller = 'HomeController';
    private $method = 'index';
    private $params = [];

    /**

         * Método `__construct`.

         * Descripción: Implementación generada automáticamente: __construct.

         * @return mixed

         */

    
    /**

    
     * Método __construct.

    
     * @return mixed

    
     */

    
    /**

    
     * Método __construct.

    
     * @return mixed

    
     */

    
    public function __construct() {
        $this->loadRoutes();
    }

    /**
     * Carga el mapa de rutas estáticas de la aplicación.
     * La estructura es: 'ruta' => ['NombreController', 'metodo']
     * Esto facilita resoluciones rápidas para rutas conocidas y ayuda
     * a mantener una configuración centralizada de las mismas.
     */
    /**
     * Método loadRoutes.
     * @return mixed
     */
    /**
     * Método loadRoutes.
     * @return mixed
     */
    private function loadRoutes() {
        // Define rutas: 'url' => ['controlador', 'metodo']
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
     * Despacha la petición actual:
     * 1. Parsea la URL.
     * 2. Resuelve la ruta contra el mapa estático (`$this->routes`).
     * 3. Si la ruta no está en el mapa, intenta cargar dinámicamente
     *    un controlador con nombre basado en la primera parte de la URL.
     * 4. Incluye e instancia el controlador y resuelve el método a invocar
     *    (con conversión de kebab-case a camelCase si procede).
     * 5. Extrae los parámetros restantes y llama al método.
     *
     * Este proceso mantiene la separación clara entre resolución de rutas
     * y ejecución de controladores, y permite rutas dinámicas sencillas.
     */
    /**
         * Método `dispatch`.
         * Descripción: Implementación generada automáticamente: dispatch.
         * @return mixed
         */
        /**
         * Despacha la petición actual resolviendo controlador, método y parámetros.
         * @return mixed
         */
        /**
         * Despacha la petición actual resolviendo controlador, método y parámetros.
         * @return mixed
         */
        public function dispatch() {
        $url = $this->parseUrl();

        // Comprobar si la ruta existe
        if (isset($this->routes[$url[0]])) {
            $this->controller = $this->routes[$url[0]][0];
            $this->method = $this->routes[$url[0]][1];
            unset($url[0]);
        } else if (isset($url[0])) {
            // Intentar cargar el controlador dinámicamente
            $controller = ucfirst($url[0]) . 'Controller';
            if (file_exists('../app/controllers/' . $controller . '.php')) {
                $this->controller = $controller;
                unset($url[0]);
            }
        }

        // Incluir el fichero del controlador
        require_once '../app/controllers/' . $this->controller . '.php';

        // Instanciar el controlador
        $this->controller = new $this->controller;

        // Comprobar si existe el método
        if (isset($url[1])) {
            // Convertir kebab-case a camelCase para nombres de método
            $methodName = $this->toCamelCase($url[1]);
            
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } else if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Obtener parámetros
        $this->params = $url ? array_values($url) : [];

        // Llamar al método con los parámetros
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Convierte una cadena en `kebab-case` a `camelCase`.
     * Ejemplo: 'reset-password' -> 'resetPassword'
     * Esto permite definir URLs legibles en front-end y mapearlas
     * a nombres de métodos en los controladores.
     * @param string $string Cadena en kebab-case
     * @return string Cadena convertida a camelCase
     */
    /**
         * Método `toCamelCase`.
         * Descripción: Implementación generada automáticamente: toCamelCase.
         * @param mixed $string Descripción del parámetro $string
         * @return mixed
         */
        /**
         * Convierte una cadena en kebab-case a camelCase.
         *
         * @param mixed $string Descripción del parámetro $string.
         * @return mixed
         */
        /**
         * Convierte una cadena en kebab-case a camelCase.
         *
         * @param mixed $string Descripción del parámetro $string.
         * @return mixed
         */
        private function toCamelCase($string) {
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    /**
     * Parsea la URL proporcionada en `$_GET['url']` y devuelve sus partes.
     * - Elimina la barra final si existe
     * - Sanitiza la URL para evitar caracteres inválidos
     * - Devuelve un array con los segmentos de la ruta
     * Si no hay parámetro `url`, devuelve un array con cadena vacía.
     * @return array Segmentos de la URL
     */
    /**
         * Método `parseUrl`.
         * Descripción: Implementación generada automáticamente: parseUrl.
         * @return mixed
         */
        /**
         * Parsea la URL proporcionada y devuelve sus partes.
         * @return mixed
         */
        /**
         * Parsea la URL proporcionada y devuelve sus partes.
         * @return mixed
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
