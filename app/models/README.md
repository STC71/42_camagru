# 📊 Directorio `models/` - Modelos de Datos

## 🎯 ¿Qué son y para qué sirven?

Los modelos son los **gestores de información**. Son las únicas clases que hablan directamente con la base de datos MySQL.

**¿Por qué existen?** Para centralizar TODO el acceso a datos en un solo lugar y hacerlo de forma segura.

**¿Para qué sirven?**
- Guardar nuevos datos (crear usuario, guardar foto)
- Leer datos existentes (buscar usuario por email, listar fotos)
- Actualizar datos (cambiar contraseña, editar perfil)
- Eliminar datos (borrar foto, eliminar cuenta)
- Proteger contra inyecciones SQL (siempre usan sentencias preparadas)

## 📋 Contenido

| Archivo | Descripción | Tabla BD |
|---------|-------------|----------|
| `User.php` | Modelo de usuario | `users` |
| `Image.php` | Modelo de imagen/foto | `images` |
| `Like.php` | Modelo de likes | `likes` |
| `Comment.php` | Modelo de comentarios | `comments` |

## 🔄 ¿Cómo funciona un modelo?

**Ejemplo simple:** Cuando un usuario se registra

1. **Controller** le dice al modelo: "Crea un usuario con estos datos"
2. **Modelo** valida: "¿El email ya existe? No → OK, procedo"
3. **Modelo** hace la query SQL de forma segura (sentencia preparada)
4. **Base de datos** guarda el nuevo usuario
5. **Modelo** devuelve al Controller: "Usuario creado con ID #123"

**¿Por qué usar modelos?**
- **Seguridad:** Todas las queries SQL están en un sitio controlado
- **Reutilización:** Si 3 controladores necesitan buscar un usuario, todos usan el mismo método
- **Mantenimiento:** Si cambias cómo se guardan los datos, solo tocas el modelo

## 📁 Detalles de cada Modelo

### User.php - Modelo de Usuario

**Tabla:** `users`

**Campos principales:**
- `id` - INT AUTO_INCREMENT PRIMARY KEY
- `username` - VARCHAR(50) UNIQUE
- `email` - VARCHAR(100) UNIQUE
- `password` - VARCHAR(255) (hash bcrypt)
- `verified` - BOOLEAN DEFAULT 0
- `verification_token` - VARCHAR(64) UNIQUE
- `reset_token` - VARCHAR(64) UNIQUE
- `reset_expires` - DATETIME
- `notifications_enabled` - BOOLEAN DEFAULT 1
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Métodos principales:**
- `create($data)` - Registrar nuevo usuario
- `findByEmail($email)` - Buscar por email
- `findById($id)` - Buscar por ID
- `update($id, $data)` - Actualizar perfil
- `verify($token)` - Verificar email
- `verifyPassword($email, $password)` - Login
- `createResetToken($email)` - Token de recuperación
- `resetPassword($token, $newPassword)` - Cambiar contraseña
- `toggleNotifications($userId)` - Activar/desactivar emails

**Características:**
- ✅ Hashing de contraseñas con bcrypt
- ✅ Tokens seguros para verificación
- ✅ Validación de unicidad (email, username)
- ✅ Gestión de preferencias

### Image.php - Modelo de Imagen

**Tabla:** `images`

**Campos principales:**
- `id` - INT AUTO_INCREMENT PRIMARY KEY
- `user_id` - INT (FK a users)
- `filename` - VARCHAR(255)
- `type` - ENUM('photo', 'gif')
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Métodos principales:**
- `create($userId, $filename, $type)` - Guardar nueva imagen
- `findById($id)` - Buscar imagen por ID
- `findByUser($userId)` - Imágenes de un usuario
- `getAll($limit, $offset)` - Galería paginada
- `delete($id, $userId)` - Eliminar imagen (con autorización)
- `getWithLikesAndComments($id)` - Imagen con datos sociales
- `getTotalCount()` - Contador para paginación

**Características:**
- ✅ Almacenamiento de metadatos
- ✅ Relación con usuario (FK)
- ✅ Soporte para fotos y GIFs
- ✅ Joins con likes y comments
- ✅ Paginación eficiente

### Like.php - Modelo de Like

**Tabla:** `likes`

**Campos principales:**
- `id` - INT AUTO_INCREMENT PRIMARY KEY
- `user_id` - INT (FK a users)
- `image_id` - INT (FK a images)
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- UNIQUE KEY (`user_id`, `image_id`) - Un usuario, un like por imagen

**Métodos principales:**
- `toggle($userId, $imageId)` - Añadir/quitar like
- `hasLiked($userId, $imageId)` - Verificar si ya dio like
- `getCount($imageId)` - Contador de likes
- `getByImage($imageId)` - Todos los likes de una imagen

**Características:**
- ✅ Toggle automático (like/unlike)
- ✅ Constraint de unicidad (un like por usuario/imagen)
- ✅ Contador eficiente
- ✅ Notificación al autor

### Comment.php - Modelo de Comentario

**Tabla:** `comments`

**Campos principales:**
- `id` - INT AUTO_INCREMENT PRIMARY KEY
- `user_id` - INT (FK a users)
- `image_id` - INT (FK a images)
- `text` - TEXT
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Métodos principales:**
- `create($userId, $imageId, $text)` - Añadir comentario
- `getByImage($imageId)` - Comentarios de una imagen
- `delete($id, $userId)` - Eliminar comentario (autorizado)
- `getCount($imageId)` - Contador de comentarios

**Características:**
- ✅ Validación de longitud de texto
- ✅ Sanitización contra XSS
- ✅ Ordenamiento por fecha
- ✅ JOIN con users para mostrar autor
- ✅ Notificación al autor de la imagen

## 🔐 Seguridad en Modelos

### 1. Sentencias Preparadas (SIEMPRE)
```php
// ❌ MAL - Vulnerable a SQL Injection
$sql = "SELECT * FROM users WHERE email = '$email'";

// ✅ BIEN - Sentencia preparada
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $db->prepare($sql);
$stmt->execute(['email' => $email]);
```

### 2. Hashing de Contraseñas
```php
// Crear hash
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Verificar
if (password_verify($inputPassword, $storedHash)) {
    // Contraseña correcta
}
```

### 3. Validación en Modelo
```php
public function create($data) {
    // Validar antes de insertar
    if (strlen($data['username']) < 3) {
        throw new Exception('Username muy corto');
    }
    
    if ($this->emailExists($data['email'])) {
        throw new Exception('Email ya registrado');
    }
    
    // Proceder con INSERT
}
```

### 4. Autorización en Operaciones
```php
public function delete($imageId, $userId) {
    // Verificar que la imagen pertenece al usuario
    $image = $this->findById($imageId);
    
    if ($image['user_id'] !== $userId) {
        throw new Exception('No autorizado');
    }
    
    // Proceder con DELETE
}
```

## 📊 Relaciones entre Modelos

```
┌─────────────┐
│    USER     │
│  (1 user)   │
└──────┬──────┘
       │
       │ has many
       │
       v
┌─────────────┐          ┌──────────────┐
│   IMAGE     │◄─────────│    LIKE      │
│ (many imgs) │ has many │ (many likes) │
└──────┬──────┘          └──────────────┘
       │                         │
       │ has many                │ belongs to
       │                         │
       v                         v
┌─────────────┐          ┌──────────────┐
│  COMMENT    │          │     USER     │
│(many comms) │──────────►│   (1 user)  │
└─────────────┘ belongs to└──────────────┘
```

### Ejemplo de Query con JOINs
```php
// Obtener imagen con likes, comentarios y autor
public function getImageWithDetails($imageId) {
    $sql = "SELECT 
                i.*,
                u.username as author_username,
                COUNT(DISTINCT l.id) as likes_count,
                COUNT(DISTINCT c.id) as comments_count
            FROM images i
            JOIN users u ON i.user_id = u.id
            LEFT JOIN likes l ON i.id = l.image_id
            LEFT JOIN comments c ON i.id = c.image_id
            WHERE i.id = :id
            GROUP BY i.id";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $imageId]);
    return $stmt->fetch();
}
```

## 📺 Videos Educativos en Español

### Modelos y Acceso a Datos
- [Modelos en MVC - ¿Qué son?](https://www.youtube.com/watch?v=5s7Nn5dxC0E) - Por Código Facilito
- [Active Record Pattern en PHP](https://www.youtube.com/watch?v=k4Yj7SgFHFs) - Por CodigoMasters
- [ORM vs Active Record](https://www.youtube.com/watch?v=QE7R7cSzc9k) - Por EDteam

### PDO y MySQL
- [PDO en PHP - Tutorial Completo](https://www.youtube.com/watch?v=3_htg_TUmas) - Por Píldoras Informáticas
- [Consultas Preparadas en PHP](https://www.youtube.com/watch?v=5s8DWLx_wS8) - Por HolaMundo
- [Transacciones en PDO](https://www.youtube.com/watch?v=XlQJAX7QI0Y) - Por FaztCode

### Diseño de Base de Datos
- [Normalización de Base de Datos](https://www.youtube.com/watch?v=oUdKH0zUdJI) - Por Freddy Vega
- [Relaciones entre Tablas - MySQL](https://www.youtube.com/watch?v=R-K0m0K5B4o) - Por Píldoras Informáticas
- [Foreign Keys y JOINs](https://www.youtube.com/watch?v=6Jf0OFHQFY8) - Por HolaMundo

### Seguridad en Modelos
- [Prevenir SQL Injection](https://www.youtube.com/watch?v=ciNHn38EyRc) - Por Pelado Nerd
- [Password Hashing en PHP](https://www.youtube.com/watch?v=caCJSWJWM1I) - Por FaztCode
- [Seguridad en Bases de Datos](https://www.youtube.com/watch?v=aEngebgM8vI) - Por Nate Gentile

### CRUD Operations
- [CRUD Completo en PHP y MySQL](https://www.youtube.com/watch?v=s5hGWj1uwA8) - Por FaztCode
- [Operaciones CRUD - Buenas Prácticas](https://www.youtube.com/watch?v=QQd-wCQHYAo) - Por Código Facilito

## 🔗 Enlaces Relacionados

- [Volver a app/](../README.md)
- [Controladores](../controllers/README.md)
- [Core](../core/README.md)
- [Base de Datos](../../database/README.md)

---

[⬆ Volver al README principal](../../README.md)
