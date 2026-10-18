# PHP en Camagru

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![PHP Docs](https://img.shields.io/badge/Documentación-Oficial-777BB4?style=flat-square&logo=php)](https://www.php.net/docs.php)

## Versión utilizada

El proyecto está diseñado para **PHP 8.2** (imagen oficial `php:8.2-apache-bookworm`).

Documentación oficial: [https://www.php.net/](https://www.php.net/)

## Extensiones utilizadas

| Extensión | Uso en Camagru | Documentación |
|-----------|----------------|---------------|
| **PDO + pdo_mysql** | Acceso a base de datos con sentencias preparadas | [PDO](https://www.php.net/manual/en/book.pdo.php) |
| **GD** | Procesamiento de imágenes (superposición de stickers) | [GD](https://www.php.net/manual/en/book.image.php) |
| **Imagick** | Generación de GIFs animados (bonus) | [Imagick](https://www.php.net/manual/en/book.imagick.php) |
| **mbstring** | Manejo seguro de cadenas multibyte | [mbstring](https://www.php.net/manual/en/book.mbstring.php) |
| **fileinfo** | Validación real del tipo MIME de archivos | [fileinfo](https://www.php.net/manual/en/book.fileinfo.php) |

## Decisiones de diseño

- **Sin frameworks ni Composer**: cumple estrictamente el subject de 42.
- **Arquitectura MVC ligera** hecha a mano (`Router`, `Controller`, modelos).
- **PDO con `ERRMODE_EXCEPTION`** y `ATTR_EMULATE_PREPARES = false`.
- **Hashing de contraseñas** con `password_hash()` / `password_verify()` (algoritmo por defecto = bcrypt).
- **Tokens CSRF** generados con `random_bytes()` y comprobados con `hash_equals()`.

## Recursos oficiales recomendados

- Manual de PHP: https://www.php.net/manual/en/
- Seguridad: https://www.php.net/manual/en/security.php
- PDO: https://www.php.net/manual/en/book.pdo.php
- GD: https://www.php.net/manual/en/book.image.php
- Imagick: https://www.php.net/manual/en/book.imagick.php
