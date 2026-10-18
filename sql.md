# SQL / MySQL en Camagru

[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/doc/)
[![PDO](https://img.shields.io/badge/PDO-Prepared_Statements-777BB4?style=flat-square)](https://www.php.net/manual/en/book.pdo.php)

## Motor utilizado

**MySQL 8.0** (contenedor oficial `mysql:8.0`).

Documentación oficial: [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)

## Principios de seguridad aplicados

- **Todas** las consultas usan **sentencias preparadas** (PDO).
- Nunca se concatena input del usuario directamente en el SQL.
- `PDO::ATTR_EMULATE_PREPARES = false` para máxima seguridad.
- Claves foráneas y restricciones `UNIQUE` donde corresponde (likes, usernames, emails…).

## Tablas principales

| Tabla | Propósito |
|-------|-----------|
| `users` | Cuentas de usuario, tokens de verificación y reset |
| `images` | Fotos y GIFs subidos |
| `likes` | Relación usuario ↔ imagen (UNIQUE) |
| `comments` | Comentarios de usuarios en las fotos |

## Recursos oficiales

- MySQL Reference Manual: https://dev.mysql.com/doc/refman/8.0/en/
- Prepared Statements (concepto): https://dev.mysql.com/doc/refman/8.0/en/sql-prepared-statements.html
- PDO PHP: https://www.php.net/manual/en/book.pdo.php
