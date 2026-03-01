# 🎮 Directorio `controllers/` - Controladores

## 🎯 ¿Qué son y para qué sirven?

Los controladores son los **coordinadores** de la aplicación. Cuando un usuario hace algo (hace clic en un botón, envía un formulario, visita una página), el controlador decide qué hacer.

**¿Por qué existen?** Para mantener la lógica de negocio organizada y separada de la presentación y los datos.

**¿Para qué sirven?**
- Reciben las peticiones del usuario
- Validan los datos que llegan
- Deciden qué información necesitan de la base de datos
- Preparan esa información para mostrarla
- Responden al usuario (HTML, JSON, redirección, etc.)

## 📋 Contenido

| Archivo | Descripción | Rutas |
|---------|-------------|-------|
| `AuthController.php` | Autenticación y registro | `/register`, `/login`, `/logout`, `/verify`, `/forgot-password`, `/reset-password` |
| `EditorController.php` | Editor de fotos y webcam | `/editor`, `/editor/capture`, `/editor/upload`, `/editor/gif` |
| `GalleryController.php` | Galería pública y social | `/gallery`, `/gallery/like`, `/gallery/comment` |
| `HomeController.php` | Página de inicio | `/`, `/home` |
| `UserController.php` | Perfil y configuración | `/profile`, `/settings`, `/my-photos` |

## 🔄 ¿Cómo funciona un controlador?

**Flujo típico:**
1. Usuario hace una acción (ej: sube una foto)
2. El Router dice: "Esta petición es para EditorController"
3. El Controller valida: "¿La foto es válida? ¿El usuario tiene sesión?"
4. Si todo está bien, le pide al Model: "Guarda esta foto en la BD"
5. El Model responde: "Listo, guardada con ID #42"
6. El Controller prepara la respuesta y muestra una Vista con el mensaje de éxito

**Analogía:** El controlador es como el camarero de un restaurante:
- Toma tu pedido (petición HTTP)
- Lo valida ("¿quieres patatas o ensalada?")
- Se lo pasa a la cocina (Model/Base de datos)
- Te trae el plato (Vista/HTML)

## 📁 Detalles de cada Controlador

### AuthController.php
**Responsabilidad:** Gestión de autenticación y seguridad

**Métodos principales:**
- `register()` - Registro de nuevos usuarios
- `login()` - Inicio de sesión
- `logout()` - Cerrar sesión
- `verify()` - Verificación de email
- `forgotPassword()` - Solicitud de recuperación
- `resetPassword()` - Cambio de contraseña

**Características:**
- ✅ Validación de formularios
- ✅ Hashing de contraseñas (bcrypt)
- ✅ Tokens de verificación
- ✅ Protección CSRF
- ✅ Rate limiting

### EditorController.php
**Responsabilidad:** Captura y edición de fotos

**Métodos principales:**
- `index()` - Muestra el editor
- `capture()` - Captura desde webcam
- `upload()` - Sube archivo desde disco
- `createGif()` - Genera GIF animado (BONUS)
- `deletePhoto()` - Elimina foto del usuario

**Características:**
- ✅ Procesamiento de imágenes (GD Library)
- ✅ Superposición de stickers
- ✅ Validación de archivos
- ✅ Generación de thumbnails
- ✅ Gestión de almacenamiento

### GalleryController.php
**Responsabilidad:** Galería pública y funciones sociales

**Métodos principales:**
- `index()` - Lista de imágenes paginada
- `show()` - Detalle de una imagen
- `like()` - Toggle de like (AJAX)
- `comment()` - Añadir comentario (AJAX)
- `loadMore()` - Scroll infinito (BONUS)

**Características:**
- ✅ Paginación
- ✅ Respuestas JSON para AJAX
- ✅ Notificaciones por email
- ✅ Scroll infinito
- ✅ Ordenamiento

### HomeController.php
**Responsabilidad:** Página de inicio y landing

**Métodos principales:**
- `index()` - Muestra el home

**Características:**
- ✅ Contenido estático
- ✅ Links a registro/login
- ✅ Información del proyecto

### UserController.php
**Responsabilidad:** Perfil y configuración de usuario

**Métodos principales:**
- `profile()` - Ver perfil público
- `settings()` - Configuración de cuenta
- `updateProfile()` - Actualizar datos
- `updatePassword()` - Cambiar contraseña
- `myPhotos()` - Mis fotos subidas
- `toggleNotifications()` - Activar/desactivar emails

**Características:**
- ✅ Edición de perfil
- ✅ Cambio de contraseña
- ✅ Gestión de privacidad
- ✅ Preferencias de notificación

## 🛡️ Seguridad en Controladores

### 1. Validación de Input
```php
// Validar TODOS los datos de entrada
$validator = new Validator($_POST);
$validator->required(['field1', 'field2']);
$validator->email('email');
$validator->minLength('password', 8);
```

### 2. Protección CSRF
```php
// Verificar token en cada POST
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token inválido');
}
```

### 3. Autenticación
```php
// Verificar login antes de acciones sensibles
if (!$this->isAuthenticated()) {
    redirect('/login', 'error', 'Debes iniciar sesión');
    return;
}
```

### 4. Autorización
```php
// Verificar permisos del usuario
if ($photo->user_id !== $_SESSION['user_id']) {
    die('No tienes permiso para eliminar esta foto');
}
```

### 5. Rate Limiting
```php
// Limitar intentos de login
if ($this->tooManyAttempts($_POST['email'])) {
    die('Demasiados intentos. Espera 15 minutos');
}
```

## 🎨 Respuestas del Controlador

### Renderizar Vista HTML
```php
$this->view('gallery/index', [
    'images' => $images,
    'page' => $page
]);
```

### Respuesta JSON (AJAX)
```php
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Like añadido',
    'likes_count' => $newCount
]);
```

### Redirección
```php
redirect('/gallery', 'success', 'Foto subida correctamente');
```

## 📺 Videos Educativos en Español

### Controladores y MVC
- [Controladores en MVC - ¿Qué hacen?](https://www.youtube.com/watch?v=5s7Nn5dxC0E) - Por Código Facilito
- [Cómo crear Controladores en PHP](https://www.youtube.com/watch?v=jGJIZKXRego) - Por CodigoMasters
- [Routing y Controladores](https://www.youtube.com/watch?v=Zf2FYpio47M) - Por FaztCode

### Validación de Datos
- [Validación de Formularios en PHP](https://www.youtube.com/watch?v=3rGpPUl_Ztg) - Por Píldoras Informáticas
- [Sanitización y Validación de Input](https://www.youtube.com/watch?v=Y7oZ5j3oXwA) - Por HolaMundo
- [Filter Functions en PHP](https://www.youtube.com/watch?v=UWKAqvvMlxg) - Por Código Facilito

### Manejo de Peticiones HTTP
- [GET vs POST en PHP](https://www.youtube.com/watch?v=P5cHMXlGTFg) - Por FaztCode
- [Headers HTTP en PHP](https://www.youtube.com/watch?v=A4CWJoKGQVM) - Por EDteam
- [AJAX con PHP y JSON](https://www.youtube.com/watch?v=rX6tC5K_XaI) - Por MoureDev

### Seguridad
- [Protección CSRF en PHP](https://www.youtube.com/watch?v=m0EHlfTgGUU) - Por Codigo Facilito
- [Validación y Seguridad en PHP](https://www.youtube.com/watch?v=jRIJFp0mGWo) - Por HolaMundo
- [Autenticación Segura en PHP](https://www.youtube.com/watch?v=1bbpKF33b9s) - Por Fazt

## 🔗 Enlaces Relacionados

- [Volver a app/](../README.md)
- [Modelos](../models/README.md)
- [Vistas](../views/README.md)
- [Core](../core/README.md)

---

[⬆ Volver al README principal](../../README.md)
