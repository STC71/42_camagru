# ⚙️ Directorio `config/` - Configuración

## 🎯 ¿Qué es y para qué sirve?

Este directorio guarda la **configuración de la aplicación**: conexiones, URLs, límites, credenciales, etc.

**¿Por qué existe?** Para tener todos los ajustes en un solo lugar y poder cambiarlos fácilmente sin tocar el código.

**¿Para qué sirve?**
- Configurar la conexión a MySQL (host, base de datos, usuario, contraseña)
- Configurar el servidor de emails (SMTP para Mailtrap)
- Definir URLs base del proyecto
- Establecer límites (tamaño máximo de fotos, imágenes por página)
- Ajustes de seguridad (tiempo de sesión, longitud de contraseñas)

## 📋 Contenido

**`Config.php`** - Archivo principal que define todas las constantes de configuración

**¿Qué define?**
- **Base de datos:** Dónde conectarse (localhost, puerto, nombre BD, credenciales)
- **Email:** Servidor SMTP para enviar correos de verificación y notificaciones
- **Rutas:** Dónde están los uploads, stickers, cuál es la URL base
- **Límites:** Tamaño máximo de archivos (5MB), fotos por página (5), longitud de comentarios (500 caracteres)
- **Seguridad:** Duración de sesiones, longitud mínima de contraseñas (8 caracteres)

## 🔒 Variables de Entorno

### Archivo .env
```bash
# Base de Datos
DB_HOST=localhost
DB_NAME=camagru
DB_USER=root
DB_PASS=secret

# SMTP
SMTP_HOST=smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USER=your_username
SMTP_PASS=your_password

# App
BASE_URL=http://localhost:8080
DEBUG_MODE=true
```

### Carga de Variables
```php
// En Config.php o bootstrap
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}
```

## 🛡️ Seguridad en Configuración

### 1. No Commitear Credenciales
```bash
# .gitignore
.env
config/local.php
```

### 2. Usar Variables de Entorno
```php
// ✅ BIEN
$dbHost = getenv('DB_HOST');

// ❌ MAL - Credenciales hardcoded
$dbHost = 'localhost';
$dbPass = 'password123';
```

### 3. Validar Configuración
```php
if (empty(DB_HOST) || empty(DB_NAME)) {
    die('Error: Configuración de base de datos incompleta');
}
```

### 4. Diferentes Configs por Entorno
```php
// Desarrollo
if (getenv('ENVIRONMENT') === 'development') {
    define('DEBUG_MODE', true);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Producción
if (getenv('ENVIRONMENT') === 'production') {
    define('DEBUG_MODE', false);
    error_reporting(0);
    ini_set('display_errors', 0);
}
```

## 🎯 Buenas Prácticas

### 1. Constantes vs Variables
```php
// ✅ Usar constantes para valores que no cambian
define('APP_NAME', 'Camagru');
define('VERSION', '1.0.0');

// ✅ Usar variables para valores que pueden cambiar
$uploadPath = getenv('UPLOAD_PATH') ?: '/default/path';
```

### 2. Organización
```php
// Agrupar por categoría
// === DATABASE CONFIG ===
define('DB_HOST', ...);
define('DB_NAME', ...);

// === SMTP CONFIG ===
define('SMTP_HOST', ...);
define('SMTP_PORT', ...);

// === FILE UPLOAD CONFIG ===
define('MAX_FILE_SIZE', ...);
define('ALLOWED_TYPES', ...);
```

### 3. Documentación
```php
/**
 * Maximum file upload size in bytes (5MB)
 */
define('MAX_FILE_SIZE', 5242880);

/**
 * Number of images to display per page in gallery
 */
define('IMAGES_PER_PAGE', 5);
```

## 📺 Videos Educativos en Español

### Configuración de Aplicaciones
- [Variables de Entorno en PHP](https://www.youtube.com/watch?v=0xPJdHXjG5k) - Por HolaMundo
- [Archivos .env en PHP](https://www.youtube.com/watch?v=pPpvZPb_LW4) - Por Código Facilito
- [Configuración de Apps PHP](https://www.youtube.com/watch?v=6Z7M1qMq3Fo) - Por FaztCode

### Seguridad
- [Gestión Segura de Credenciales](https://www.youtube.com/watch?v=aEngebgM8vI) - Por Nate Gentile
- [Buenas Prácticas de Configuración](https://www.youtube.com/watch?v=dO3deLNHxCw) - Por MoureDev

### 12-Factor App
- [The Twelve-Factor App Explicado](https://www.youtube.com/watch?v=wjZ5bqv-Fhs) - Por Pelado Nerd
- [Configuración en Apps Modernas](https://www.youtube.com/watch?v=Iw-A3T3W5l0) - Por Freddy Vega

## 🔗 Enlaces Relacionados

- [Core Framework](../app/core/README.md)
- [Database](../database/README.md)
- [Docker Configuration](../docker-compose.yml)

---

[⬆ Volver al README principal](../README.md)
