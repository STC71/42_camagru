# 🗄️ SQL / MySQL en Camagru

[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/doc/)
[![PDO](https://img.shields.io/badge/PDO-Prepared_Statements-777BB4?style=flat-square)](https://www.php.net/manual/en/book.pdo.php)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

---

## 1. ¿Qué es SQL y qué papel juega?

**SQL** (Structured Query Language) es el lenguaje que usamos para hablar con la base de datos.

En Camagru, MySQL guarda:

- Los usuarios (nombre, email, contraseña hasheada, tokens…)
- Las fotos y GIFs
- Los likes
- Los comentarios

> **Analogía cotidiana:**  
> La base de datos es el **archivo de la empresa**. SQL es el lenguaje en el que le pedimos: “dame todos los usuarios verificados” o “guarda este nuevo comentario”.

Documentación oficial de MySQL: [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)

---

## 2. Motor y versión

| Concepto | Valor |
|----------|--------|
| Motor | **MySQL 8.0** |
| Imagen Docker | `mysql:8.0` |
| Acceso desde PHP | **PDO** (PHP Data Objects) |

---

## 3. Principios de seguridad que aplicamos

| Principio | Cómo lo cumplimos |
|-----------|-------------------|
| Nunca concatenar input del usuario | Usamos **sentencias preparadas** (`?`) |
| Emulación de prepares desactivada | `PDO::ATTR_EMULATE_PREPARES => false` |
| Errores visibles en desarrollo | `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` |
| Contraseñas | Solo se guarda el **hash** (`password_hash`) |
| Unicidad | Constraints `UNIQUE` en email, username y likes |

---

## 4. Tablas principales

```text
users
├── id
├── username          (UNIQUE)
├── email             (UNIQUE)
├── password          (hash bcrypt)
├── verified          (0/1)
├── verification_token
├── reset_token
├── reset_token_expiry
├── notify_comments   (0/1)
└── created_at

images
├── id
├── user_id           → users.id
├── filename
└── created_at

likes
├── id
├── image_id          → images.id
├── user_id           → users.id
└── UNIQUE(image_id, user_id)

comments
├── id
├── image_id          → images.id
├── user_id           → users.id
├── content
└── created_at
```

---

## 5. Ejemplos reales del proyecto

### 5.1 Crear un usuario (sentencia preparada)

```php
$sql = "INSERT INTO users (username, email, password, verification_token)
        VALUES (?, ?, ?, ?)";
$this->db->query($sql, [$username, $email, $hashedPassword, $token]);
```

### 5.2 Buscar usuario por email

```php
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $this->db->query($sql, [$email]);
$user = $stmt->fetch();
```

### 5.3 Obtener la galería con contadores

```sql
SELECT i.*, u.username,
       (SELECT COUNT(*) FROM likes WHERE image_id = i.id) AS like_count,
       (SELECT COUNT(*) FROM comments WHERE image_id = i.id) AS comment_count
FROM images i
JOIN users u ON i.user_id = u.id
ORDER BY i.created_at DESC
LIMIT 5 OFFSET 0;
```

### 5.4 Evitar likes duplicados

En la base de datos:

```sql
UNIQUE KEY unique_like (image_id, user_id)
```

En el código PHP el método `toggle()` comprueba si ya existe y añade o borra en consecuencia.

### 5.5 Token de reset con caducidad

```sql
UPDATE users
SET reset_token = ?, reset_token_expiry = ?
WHERE email = ?;

-- Al usarlo:
SELECT * FROM users
WHERE reset_token = ?
  AND reset_token_expiry > NOW();
```

---

## 6. Cómo se inicializa la base de datos

El archivo `config/setup.php` crea las tablas cuando es necesario.  
También se puede ejecutar manualmente:

```bash
docker-compose exec db mysql -u root -p camagru < schema.sql
```

---

## 7. Recursos oficiales recomendados

| Recurso | Enlace |
|---------|--------|
| MySQL 8.0 Reference Manual | https://dev.mysql.com/doc/refman/8.0/en/ |
| Prepared Statements | https://dev.mysql.com/doc/refman/8.0/en/sql-prepared-statements.html |
| PDO (PHP) | https://www.php.net/manual/en/book.pdo.php |
| PDO::prepare | https://www.php.net/manual/en/pdo.prepare.php |
| password_hash | https://www.php.net/manual/en/function.password-hash.php |

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
