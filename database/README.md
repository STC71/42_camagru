# 🗄️ Directorio `database/` - Base de Datos

## 🎯 ¿Qué es y para qué sirve?

Este directorio contiene el **esquema de la base de datos**: la estructura de tablas y sus relaciones.

**¿Por qué existe?** Para poder crear la base de datos desde cero con un solo comando.

**¿Para qué sirve?**
- Crear las 4 tablas principales (users, images, likes, comments)
- Definir las relaciones entre tablas (claves foráneas)
- Establecer restricciones (emails únicos, un like por usuario/imagen)
- Inicializar la base de datos en desarrollo o producción

## 📋 Contenido

**`init.sql`** - Script SQL que crea toda la estructura

## 🗂️ Las 4 Tablas del Proyecto

### 1. **users** - Información de Usuarios
**¿Qué guarda?** Toda la información de cada usuario registrado

**Datos importantes:**
- Username y email (únicos, no pueden repetirse)
- Contraseña (guardada como hash seguro, nunca en texto plano)
- Tokens para verificar email y recuperar contraseña
- Si las notificaciones están activadas o no

#### 2. images - Imágenes
```sql
CREATE TABLE images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    type ENUM('photo', 'gif') DEFAULT 'photo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
 2. **images** - Fotos Subidas
**¿Qué guarda?** Cada foto o GIF que un usuario sube al proyecto

**Datos importantes:**
- Qué usuario la subió (enlace a tabla users)
- Nombre del archivo guardado en el servidor
- Si es una foto normal o un GIF animado (BONUS)
- Cuándo se subió

**Regla importante:** Si eliminas un usuario, sus fotos también se eliminan automáticamente (CASCADE)

### 3. **likes** - Me Gusta
**¿Qué guarda?** Cada like que un usuario da a una foto

**Datos importantes:**
- Quién dio el like (enlace a users)
- A qué foto (enlace a images)

**Regla especial:** Un usuario solo puede dar UN like por foto. Si vuelve a hacer clic, se quita el like (toggle).

### 4. **comments** - Comentarios
**¿Qué guarda?** Todos los comentarios en las fotos

**Datos importantes:**
- Quién comentó (enlace a users)
- En qué foto (enlace a images)
- Texto del comentario (máximo 500 caracteres)
- Cuándo se escribió

**Sin límite:** Un usuario puede comentar todas las veces que quiera en la misma foto
       v
┌─────────────┐          ┌──────────────┐
│   images    │◄─────────│    likes     │
│ (many imgs) │   1:N    │ (many likes) │
└──────┬──────┘          └──────┬───────┘
       │                        │
       │ 1:N                    │ N:1
       │                        │
       v                        v
┌─────────────┐          ┌──────────────┐
│  comments   │          │    users     │
│(many comms) │──────────►│  (1 user)   │
└─────────────┘   N:1    └──────────────┘
```

## 📊 Índices y Optimización

### Índices Recomendados
```sql
-- Búsqueda por email (login)
CREATE INDEX idx_users_email ON users(email);

-- Búsqueda de imágenes por usuario
CREATE INDEX idx_images_user ON images(user_id);

-- Ordenamiento por fecha
CREATE INDEX idx_images_created ON images(created_at);

-- Conteo de likes por imagen
CREATE INDEX idx_likes_image ON likes(image_id);

-- Comentarios por imagen
CREATE INDEX idx_comments_image ON comments(image_id);
```

## 🛠️ Inicialización

### Método 1: Docker (Automático)
```bash
docker-compose up -d
# El script init.sql se ejecuta automáticamente
```

### Método 2: Manual
```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS camagru"

# Ejecutar script
mysql -u root -p camagru < database/init.sql

# O desde MySQL
mysql -u root -p
source /path/to/init.sql
```

### Método 3: PHP Script
```php
<?php
$pdo = new PDO("mysql:host=localhost", "root", "password");
$sql = file_get_contents(__DIR__ . '/database/init.sql');
$pdo->exec($sql);
echo "Base de datos inicializada\n";
```

## 🔒 Seguridad en Base de Datos

### 1. Usuario con Permisos Limitados
```sql
-- Crear usuario específico para la app
CREATE USER 'camagru_user'@'localhost' IDENTIFIED BY 'strong_password';

-- Dar solo permisos necesarios
GRANT SELECT, INSERT, UPDATE, DELETE ON camagru.* TO 'camagru_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Backup Regular
```bash
# Backup completo
mysqldump -u root -p camagru > backup_$(date +%Y%m%d).sql

# Backup solo estructura
mysqldump -u root -p --no-data camagru > schema.sql

# Backup solo datos
mysqldump -u root -p --no-create-info camagru > data.sql
```

### 3. Validación de Constraints
```sql
-- Verificar integridad referencial
SELECT * FROM images WHERE user_id NOT IN (SELECT id FROM users);

-- Verificar likes duplicados
SELECT user_id, image_id, COUNT(*) 
FROM likes 
GROUP BY user_id, image_id 
HAVING COUNT(*) > 1;
```

## 📺 Videos Educativos en Español

### MySQL Fundamentals
- [MySQL desde CERO - Tutorial Completo](https://www.youtube.com/watch?v=uUdKAYl-F7g) - Por Hola Mundo
- [Bases de Datos Relacionales](https://www.youtube.com/watch?v=GdeZbJBgV0o) - Por Freddy Vega

### Diseño de Bases de Datos
- [Diseño de Base de Datos - Normalización](https://www.youtube.com/watch?v=oUdKH0zUdJI) - Por Freddy Vega
- [Relaciones entre Tablas](https://www.youtube.com/watch?v=R-K0m0K5B4o) - Por Píldoras Informáticas
- [Foreign Keys y Constraints](https://www.youtube.com/watch?v=6Jf0OFHQFY8) - Por HolaMundo

### Optimización
- [Índices en MySQL - Optimización](https://www.youtube.com/watch?v=HubezKbFL7E) - Por Hola Mundo
- [Query Optimization](https://www.youtube.com/watch?v=BHwzDmr6d7s) - Por Píldoras Informáticas
- [EXPLAIN en MySQL](https://www.youtube.com/watch?v=sC8a31-Wz4A) - Por EDteam

### Seguridad
- [Seguridad en MySQL](https://www.youtube.com/watch?v=aEngebgM8vI) - Por Nate Gentile
- [Backup y Restore en MySQL](https://www.youtube.com/watch?v=vDKdvdlmgbE) - Por HolaMundo
- [User Permissions en MySQL](https://www.youtube.com/watch?v=j7DEtEpvZmg) - Por Píldoras Informáticas

### Herramientas
- [phpMyAdmin - Tutorial Completo](https://www.youtube.com/watch?v=FsY6kJRGj_Q) - Por FaztCode
- [MySQL Workbench](https://www.youtube.com/watch?v=5TQ_iODt2i8) - Por EDteam

## 🔗 Enlaces Relacionados

- [Modelos](../app/models/README.md)
- [Core Database Class](../app/core/README.md)
- [Configuración](../config/README.md)

---

[⬆ Volver al README principal](../README.md)
