# 🗄️ SQL / MySQL en Camagru

[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/doc/)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [database/](database/README.md) · [models](app/models/README.md)

---

## 1. Creación (extracto de `database/init.sql`)

```sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `notify_comments` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

```sql
CONSTRAINT `fk_images_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
```

---

## 2. Consultas desde PHP (modelos)

```php
// Login / email
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $this->db->query($sql, [$email]);

// Like único (la UNIQUE en BD evita duplicados)
$sql = "INSERT INTO likes (image_id, user_id) VALUES (?, ?)";
$this->db->query($sql, [$imageId, $userId]);
```

---

## 3. Qué evita el proyecto

```sql
-- ❌ Concatenar input
SELECT * FROM users WHERE email = '$email'
```

Siempre placeholders `?` + array de parámetros.

---

## 4. Docs oficiales

- [CREATE TABLE](https://dev.mysql.com/doc/refman/8.0/en/create-table.html)  
- [Foreign keys](https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html)  
- [PDO](https://www.php.net/manual/en/book.pdo.php)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
