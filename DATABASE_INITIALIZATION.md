> **Navegación:** [README](README.md) · [database/README.md](database/README.md) · [sql.md](sql.md) · [config/README.md](config/README.md)

# 📋 Inicialización de la Base de Datos - Camagru

## 🎯 ¿Qué sucede cuando faltan las tablas?

Durante un arranque fresco (`make re`), el test_auto.sh verifica si existen 4 tablas críticas:

```sql
usuarios, imágenes, likes (bonus), comentarios (bonus)
```

Si la base de datos acababa de crearse (primer arranque), estas tablas **NO EXISTEN** aún.

## 📂 ¿Cuál es el script de inicialización?

Archivo: **`database/init.sql`**

### Contenido:

```sql
-- Crea la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `camagru` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `camagru`;

-- Tabla users (almacena cuentas de usuario)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  -- ... más campos
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
);

-- Tabla images (almacena metadatos de imágenes)
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_images_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- Tabla likes (BONUS: sistema de likes)
CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`image_id`, `user_id`),
  CONSTRAINT `fk_likes_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- Tabla comments (BONUS: sistema de comentarios)
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_comments_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

## ❓ ¿Por qué faltan las tablas inicialmente?

### En un arranque fresco:

1. **Docker Compose** crea un volumen vacío (`camagru_db_data`)
2. **MySQL** inicializa el motor y crea las bases de datos del sistema
3. **Custom script** (database/Dockerfile) ejecuta `init.sql` en el temporary server
4. Pero en ese momento, el contenedor no está completamente listo
5. **Primera vez**: Las tablas pueden no estar disponibles inmediatamente

### Solución:

El test_auto.sh detecta que:
- ✅ La BD `camagru` existe (se creó)
- ❌ Pero las 4 tablas no están presentes
- 🤔 Ofrece ejecutar `database/init.sql` manualmente

## 🔧 ¿Por qué se ofrece inicializar en ese punto?

Es el **punto ideal** porque:

1. **La BD existe**: Confirmamos que MySQL está corriendo y accesible
2. **Está vacía**: No hay datos que perder
3. **Es crítico**: Sin tablas, la aplicación NO puede funcionar
4. **Es interactivo**: El usuario decide si proceder

## 🚀 ¿Cómo se ejecuta?

```bash
# El test_auto.sh corre esto automáticamente si el usuario responde "s":
docker-compose exec -T db mysql -u root -p[password] camagru < database/init.sql
```

Esto:
- Conecta al contenedor de BD
- Usa credenciales root
- Carga todas las sentencias SQL del archivo
- Crea las 4 tablas en orden correcto
- Establece relaciones (foreign keys)

## ✅ Verificación posterior

El test comprueba nuevamente que todas las tablas existen:

```bash
SHOW TABLES LIKE 'users';     ✓
SHOW TABLES LIKE 'images';    ✓
SHOW TABLES LIKE 'likes';     ✓
SHOW TABLES LIKE 'comments';  ✓
```

Si todas están presentes → **Test aprobado**

## 📊 Estructura del esquema

```
users (tabla base)
  ├── images (referencia: user_id → users.id)
  │   ├── likes (referencia: image_id → images.id, user_id → users.id)
  │   └── comments (referencia: image_id → images.id, user_id → users.id)
```

## 🎯 Resumen

| Aspecto | Detalles |
|---------|----------|
| **Script** | `database/init.sql` |
| **Ubicación** | Directorio `database/` |
| **Contenido** | 4 tabla CREATE TABLE con indices y foreign keys |
| **Cuándo se ofrece** | Cuando la BD existe pero está vacía |
| **Requisito** | BD debe estar corriendo en Docker |
| **Alternativa** | Ejecutar manualmente: `make db < database/init.sql` |

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
