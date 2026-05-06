<?php

/**
 * Clase base `Controller`
 * Todos los controladores de la aplicación extienden de esta clase.
 * Aquí van utilidades comunes para los controladores: carga de modelos,
 * renderizado de vistas, redirecciones, envío de JSON, validación CSRF,
 * saneamiento de entradas y comprobaciones de autenticación.
 */
/**
     * Clase `Controller`
     * Descripción: Clase relacionada con Controller (documentación generada automáticamente en español).
     */
class Controller {
    
    /**
     * Carga y devuelve una instancia del modelo solicitado.
     * @param string $model Nombre del archivo/modelo en `app/models` (sin extensión)
     * @return object Instancia del modelo cargado
     */
        /**
         * Carga y devuelve una instancia del modelo solicitado.
         *
         * @param mixed $model Descripción del parámetro $model.
         * @return mixed
         */
/**
 * Método `model` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `model` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $model Descripción detallada de $model.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->model($model);
 * ```
 */
        protected function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }
    /**
     * El método toma el nombre del modelo como argumento, 
     * incluye el archivo correspondiente mediante `require_once` que se encuentra en 
     * `app/models`, y luego crea y devuelve una nueva instancia de ese modelo.
     * 
     * protected function es un método que carga un modelo específico desde la carpeta 
     * `app/models` y devuelve una instancia de ese modelo. Esto nos permite interactuar 
     * con la base de datos a través de los modelos desde cualquier controlador que 
     * extienda esta clase base. 
     * 
     * Por ejemplo, si llamamos a `$this->model('User')`, usando este método se incluirá 
     * el archivo `app/models/User.php` y se creará una instancia de la clase `User`,
     * que luego podremos usar para realizar operaciones relacionadas con los usuarios 
     * en la base de datos.
     */

    /**
     * Renderiza una vista pasando datos a su scope.
     * Extrae las claves de `$data` como variables en el scope de la vista.
     * @param string $view Ruta relativa a `app/views` (sin extensión)
     * @param array $data Datos a pasar a la vista (opcional)
     */
    /**
     * Renderiza una vista pasando datos a su scope.
     *
     * @param mixed $view Descripción del parámetro $view.
     * @param mixed $param Descripción del parámetro $param.
     * @return mixed
     */
/**
 * Método principal de visualización/entrada para esta ruta.
 *
 * Descripción detallada:
 * Este método `view` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $view Descripción detallada de $view.
 * @param mixed [] Descripción detallada de [].
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->view($view, []);
 * ```
 */
    protected function view($view, $data = []) {
        extract($data);
        require_once '../app/views/' . $view . '.php';
    }

    /**
     * Redirige a la URL proporcionada (relativa a la configuración de la app).
     * @param string $url Ruta relativa o absoluta a la que redirigir
     */
    /**
     * Redirige a la URL proporcionada.
     *
     * @param mixed $url Descripción del parámetro $url.
     * @return mixed
     */
/**
 * Realiza una redirección o genera una URL basada en la ruta indicada.
 *
 * Descripción detallada:
 * Este método `redirect` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $url Descripción detallada de $url.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->redirect($url);
 * ```
 */
    protected function redirect($url) {
        header('Location: ' . $this->url($url));
        exit();
    }

    /**
     * Construye la URL completa basada en `APP_URL` y la ruta dada.
     * @param string $path Ruta relativa dentro de la aplicación
     * @return string URL completa construida
     */
    /**
     * Construye la URL completa basada en la configuración de la aplicación.
     *
     * @param mixed $param Descripción del parámetro $param.
     * @return mixed
     */
/**
 * Realiza una redirección o genera una URL basada en la ruta indicada.
 *
 * Descripción detallada:
 * Este método `url` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed '' Descripción detallada de ''.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->url('');
 * ```
 */
    protected function url($path = '') {
        $baseUrl = rtrim(Config::get('APP_URL', 'http://localhost:8080'), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Envía una respuesta JSON y finaliza la ejecución.
     * @param mixed $data Datos serializables a JSON
     * @param int $status Código HTTP de respuesta (por defecto 200)
     */
    /**
     * Envía una respuesta JSON y finaliza la ejecución.
     *
     * @param mixed $data Descripción del parámetro $data.
     * @param mixed $param Descripción del parámetro $param.
     * @return mixed
     */
/**
 * Método `json` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `json` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $data Descripción detallada de $data.
 * @param mixed 200 Descripción detallada de 200.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->json($data, 200);
 * ```
 */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Comprueba si la petición actual es de tipo POST.
     * @return bool
     */
    /**
     * Comprueba si la petición actual es de tipo POST.
     * @return mixed
     */
/**
 * Comprueba si post y devuelve booleano.
 *
 * Descripción detallada:
 * Este método `isPost` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->isPost();
 * ```
 */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Comprueba si la petición actual es de tipo GET.
     * @return bool
     */
    /**
     * Comprueba si la petición actual es de tipo GET.
     * @return mixed
     */
/**
 * Comprueba si get y devuelve booleano.
 *
 * Descripción detallada:
 * Este método `isGet` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->isGet();
 * ```
 */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Valida el token CSRF enviado en el formulario contra el token en sesión.
     * @return bool True si el token es válido, false en caso contrario
     */
    /**
     * Valida el token CSRF enviado en el formulario contra el token en sesión.
     * @return mixed
     */
/**
 * Valida los datos proporcionados para csrf.
 *
 * Descripción detallada:
 * Este método `validateCSRF` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->validateCSRF();
 * ```
 */
    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    /**
     * Genera y devuelve un token CSRF almacenado en la sesión si no existe.
     * @return string Token CSRF (hex)
     */
    /**
     * Genera y devuelve un token CSRF almacenado en la sesión si no existe.
     * @return mixed
     */
/**
 * Método `generateCSRF` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `generateCSRF` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->generateCSRF();
 * ```
 */
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Sanea recursivamente una entrada (string o array) para evitar XSS y etiquetas.
     * - `trim` para recortar espacios
     * - `strip_tags` para eliminar etiquetas HTML
     * - `htmlspecialchars` para escapar caracteres especiales
     * @param mixed $data String o array a sanear
     * @return mixed Datos saneados con la misma estructura de entrada
     */
    /**
         * Método `sanitize`.
         * Descripción: Implementación generada automáticamente: sanitize.
         * @param mixed $data Descripción del parámetro $data
         * @return mixed
         */
        /**
         * Sanea recursivamente una entrada para evitar XSS y etiquetas.
         *
         * @param mixed $data Descripción del parámetro $data.
         * @return mixed
         */
/**
 * Método `sanitize` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `sanitize` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 * @param mixed $data Descripción detallada de $data.
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->sanitize($data);
 * ```
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
     * Fuerza que la ruta requiera autenticación.
     * Si no hay sesión de usuario, añade un mensaje flash y redirige al login.
     */
    /**
     * Fuerza que la ruta requiera autenticación.
     * @return mixed
     */
/**
 * Método `requireAuth` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `requireAuth` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->requireAuth();
 * ```
 */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'You must be logged in to access this page.';
            $this->redirect('login');
        }
    }

    /**
     * Fuerza que la ruta sea accesible solo por invitados (no autenticados).
     * Redirige a `home` si el usuario ya está autenticado.
     */
    /**
     * Fuerza que la ruta sea accesible solo por invitados (no autenticados).
     * @return mixed
     */
/**
 * Método `requireGuest` del controlador/clase.
 *
 * Descripción detallada:
 * Este método `requireGuest` realiza la operación descrita arriba. Se recomienda
 * revisar las validaciones previas y sanitización de los parámetros antes
 * de invocar este método. No modifica el estado global salvo se indique.
 *
 *
 * @return mixed Resultado de la operación (tipo dependiendo del contexto).
 *
 * Ejemplo de uso:
 * ```php
 * $this->requireGuest();
 * ```
 */
    protected function requireGuest() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('home');
        }
    }
}
