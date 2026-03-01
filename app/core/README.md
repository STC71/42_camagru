# ⚙️ Directorio `core/` - Framework Personalizado

## 🎯 ¿Qué es el Core y para qué sirve?

El **core** es la caja de herramientas compartida por toda la aplicación. Son las piezas fundamentales que todos necesitan usar.

**¿Por qué existe?** Para no repetir código. Si 5 controladores necesitan enviar emails, mejor tener una clase `Mailer` que usen todos.

**¿Para qué sirve?**
- **Router:** Convierte URLs bonitas (`/gallery/42`) en código ejecutable
- **Database:** Proporciona una conexión única y segura a MySQL
- **Controller:** Da funcionalidad común a todos los controladores (mostrar vistas, redirecciones, etc.)
- **Mailer:** Envía emails (verificación, recuperación de contraseña, notificaciones)
- **Validator:** Valida y limpia datos de formularios

## 📋 Contenido

| Archivo | Descripción | Función Principal |
|---------|-------------|-------------------|
| `Router.php` | Enrutador | Mapea URLs a controladores |
| `Controller.php` | Controlador base | Clase padre de todos los controladores |
| `Database.php` | Conexión a BD | Gestiona la conexión PDO |
| `Mailer.php` | Sistema de correos | Envío de emails (verificación, notificaciones) |
| `Validator.php` | Validador | Validación y sanitización de datos |

## 🎯 Arquitectura del Framework

```
┌──────────────────────────────────────┐
│         public/index.php             │
│      (Entry Point de la App)         │
└────────────────┬─────────────────────┘
                 │
                 v
┌──────────────────────────────────────┐
│          Router.php                  │
│   (Analiza URL y determina ruta)    │
└────────────────┬─────────────────────┘
                 │
                 v
┌──────────────────────────────────────┐
│        Controller.php                │
│    (Clase base, métodos comunes)    │
└────────────────┬─────────────────────┘
                 │
         ┌───────┴───────┐
         v               v
    ┌─────────┐    ┌──────────┐
    │  Model  │    │   View   │
    │(Database)    │(Template)│
    └─────────┘    └──────────┘
```

## 🔧 Componentes Detallados

### Router.php - Sistema de Enrutamiento

**Responsabilidad:** Convertir URLs amigables en llamadas a controladores.

**Funciones principales:**
- Analizar la URL solicitada
- Extraer controlador, método y parámetros
- Instanciar el controlador correcto
- Llamar al método apropiado
- Manejar rutas no encontradas (404)

**Ejemplo de uso:**
```php
// URL: /gallery/show/42
// Se convierte en: GalleryController->show(42)

class Router {
    public function route($url) {
        $parts = explode('/', trim($url, '/'));
        
        $controller = $parts[0] ?? 'home';
        $method = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        $controllerClass = ucfirst($controller) . 'Controller';
        
        if (class_exists($controllerClass)) {
            $instance = new $controllerClass();
            if (method_exists($instance, $method)) {
                call_user_func_array([$instance, $method], $params);
            }
        }
    }
}
```

**Características:**
- ✅ URLs limpias sin parámetros GET
- ✅ Soporte para parámetros dinámicos
- ✅ Middleware para autenticación
- ✅ Manejo de errores 404

### Controller.php - Controlador Base

**Responsabilidad:** Proporcionar funcionalidad común a todos los controladores.

**Métodos compartidos:**
```php
abstract class Controller {
    // Renderizar una vista
    protected function view($view, $data = []) {
        extract($data);
        require_once "app/views/$view.php";
    }
    
    // Verificar autenticación
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    // Redirigir con mensaje flash
    protected function redirect($url, $type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        header("Location: $url");
        exit;
    }
    
    // Verificar token CSRF
    protected function verifyCsrfToken() {
        if (!isset($_POST['csrf_token']) || 
            $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF token inválido');
        }
    }
}
```

**Ventajas:**
- ✅ Evita duplicación de código
- ✅ Métodos reutilizables
- ✅ Interfaz consistente
- ✅ Facilita testing

### Database.php - Gestión de Base de Datos

**Responsabilidad:** Proporcionar conexión PDO segura y singleton.

**Características:**
```php
class Database {
    private static $instance = null;
    private $pdo;
    
    // Patrón Singleton
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // Constructor privado
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
    
    // Obtener conexión PDO
    public function getConnection() {
        return $this->pdo;
    }
}
```

**Ventajas:**
- ✅ Una sola instancia de conexión (Singleton)
- ✅ Sentencias preparadas por defecto
- ✅ Manejo de errores con excepciones
- ✅ Previene inyección SQL

### Mailer.php - Sistema de Correos

**Responsabilidad:** Enviar emails transaccionales y notificaciones.

**Tipos de emails:**
- 📧 Verificación de cuenta
- 🔑 Recuperación de contraseña
- 💬 Notificación de nuevo comentario
- ❤️ Notificación de nuevo like

**Ejemplo:**
```php
class Mailer {
    private $transport;
    
    public function __construct() {
        // Configurar SMTP (ej: Mailtrap para desarrollo)
        $this->transport = new SMTPTransport(
            SMTP_HOST,
            SMTP_PORT,
            SMTP_USER,
            SMTP_PASS
        );
    }
    
    public function sendVerificationEmail($user) {
        $token = bin2hex(random_bytes(32));
        $link = BASE_URL . "/verify?token=$token";
        
        $html = "
            <h1>Verifica tu cuenta</h1>
            <p>Hola {$user->username},</p>
            <p>Haz clic en el enlace para verificar tu cuenta:</p>
            <a href='$link'>Verificar Email</a>
        ";
        
        return $this->send($user->email, 'Verifica tu cuenta', $html);
    }
    
    public function sendCommentNotification($image, $comment) {
        $link = BASE_URL . "/gallery/show/{$image->id}";
        
        $html = "
            <h1>Nuevo comentario</h1>
            <p>{$comment->username} comentó en tu foto:</p>
            <blockquote>{$comment->text}</blockquote>
            <a href='$link'>Ver foto</a>
        ";
        
        return $this->send($image->author_email, 'Nuevo comentario', $html);
    }
}
```

**Características:**
- ✅ Templates HTML
- ✅ Configuración SMTP
- ✅ Respeta preferencias del usuario
- ✅ Mailtrap para desarrollo

### Validator.php - Validación de Datos

**Responsabilidad:** Validar y sanitizar todos los datos de entrada.

**Reglas disponibles:**
```php
class Validator {
    private $data;
    private $errors = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    // Campo requerido
    public function required($fields) {
        foreach ($fields as $field) {
            if (empty($this->data[$field])) {
                $this->errors[$field] = "$field es requerido";
            }
        }
    }
    
    // Validar email
    public function email($field) {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Email inválido";
        }
    }
    
    // Longitud mínima
    public function minLength($field, $min) {
        if (strlen($this->data[$field]) < $min) {
            $this->errors[$field] = "$field debe tener al menos $min caracteres";
        }
    }
    
    // Validar formato (regex)
    public function pattern($field, $pattern, $message) {
        if (!preg_match($pattern, $this->data[$field])) {
            $this->errors[$field] = $message;
        }
    }
    
    // Archivo válido
    public function file($field, $allowedTypes, $maxSize) {
        $file = $_FILES[$field];
        
        // Verificar error de upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$field] = "Error al subir archivo";
            return;
        }
        
        // Verificar tamaño
        if ($file['size'] > $maxSize) {
            $this->errors[$field] = "Archivo demasiado grande";
            return;
        }
        
        // Verificar tipo MIME real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        
        if (!in_array($mime, $allowedTypes)) {
            $this->errors[$field] = "Tipo de archivo no permitido";
        }
    }
    
    // Verificar si hay errores
    public function isValid() {
        return empty($this->errors);
    }
    
    // Obtener errores
    public function getErrors() {
        return $this->errors;
    }
}
```

**Uso en controladores:**
```php
$validator = new Validator($_POST);
$validator->required(['username', 'email', 'password']);
$validator->email('email');
$validator->minLength('password', 8);
$validator->pattern('username', '/^[a-zA-Z0-9_]+$/', 'Usuario inválido');

if ($validator->isValid()) {
    // Procesar datos
} else {
    // Mostrar errores
    $errors = $validator->getErrors();
}
```

## 🛡️ Seguridad en el Core

### 1. Prevención de Inyección SQL
```php
// SIEMPRE usar sentencias preparadas
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

### 2. Prevención XSS
```php
// Escapar TODAS las salidas
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

### 3. Tokens CSRF
```php
// Generar en formularios
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validar en POST
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Token inválido');
}
```

### 4. Configuración PDO Segura
```php
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false, // Sentencias preparadas reales
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
```

## 📺 Videos Educativos en Español

### Frameworks PHP y MVC
- [Crea tu propio Framework PHP - Tutorial](https://www.youtube.com/watch?v=YEjpKa-K1ps) - Por Código Facilito
- [Router PHP desde Cero](https://www.youtube.com/watch?v=4tcou1iqiEg) - Por FaztCode
- [Sistema de Routing en PHP](https://www.youtube.com/watch?v=kGTGOyEv5NM) - Por CodigoMasters

### PDO y Base de Datos
- [PDO en PHP - Curso Completo](https://www.youtube.com/watch?v=3_htg_TUmas) - Por Píldoras Informáticas
- [Sentencias Preparadas en PDO](https://www.youtube.com/watch?v=5s8DWLx_wS8) - Por HolaMundo
- [Patrón Singleton en PHP](https://www.youtube.com/watch?v=YbIBCE7nmVg) - Por Código Facilito

### Validación y Seguridad
- [Validación de Datos en PHP](https://www.youtube.com/watch?v=3rGpPUl_Ztg) - Por Píldoras Informáticas
- [Sanitización de Input](https://www.youtube.com/watch?v=Y7oZ5j3oXwA) - Por HolaMundo
- [Prevenir SQL Injection](https://www.youtube.com/watch?v=ciNHn38EyRc) - Por Pelado Nerd

### Email y SMTP
- [Enviar Emails con PHP](https://www.youtube.com/watch?v=CS7MNR6nXlQ) - Por FaztCode
- [PHPMailer Tutorial Completo](https://www.youtube.com/watch?v=mBvCoZxdF8o) - Por CodigoMasters
- [SMTP en PHP - Configuración](https://www.youtube.com/watch?v=BLgJQYLgVPA) - Por HolaMundo

### Patrones de Diseño
- [Singleton Pattern en PHP](https://www.youtube.com/watch?v=YbIBCE7nmVg) - Por Código Facilito
- [Factory Pattern en PHP](https://www.youtube.com/watch?v=s_4ZrtQs8Do) - Por MoureDev
- [Patrones de Diseño - Guía Completa](https://www.youtube.com/watch?v=cwfuydUHZ7o) - Por Píldoras Informáticas

## 🔗 Enlaces Relacionados

- [Volver a app/](../README.md)
- [Controladores](../controllers/README.md)
- [Modelos](../models/README.md)
- [Configuración](../../config/README.md)

---

[⬆ Volver al README principal](../../README.md)
