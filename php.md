# 🐘 PHP en Camagru

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [app/core](app/core/README.md) · [docker.md](docker.md) · [Manual PHP](https://www.php.net/manual/es/)

---

## 1. Rol en el proyecto

PHP ejecuta el **backend** sin framework: enrutado propio, PDO, sesiones, GD/Imagick y envío SMTP.

| Tema | Dónde |
|------|--------|
| Front controller | `public/index.php` |
| MVC | `app/controllers`, `models`, `views` |
| PDO | `app/core/Database.php` |
| Password | `password_hash` / `password_verify` ([doc](https://www.php.net/manual/en/function.password-hash.php)) |
| Imágenes | extensión **gd** + **imagick** |

---

## 2. Extensiones necesarias en el contenedor

```bash
docker-compose exec web php -m | grep -E 'pdo_mysql|gd|imagick'
docker-compose exec web php -r 'print_r(gd_info());'
```

| Extensión | Para qué |
|-----------|----------|
| `pdo_mysql` | MySQL |
| `gd` (+ JPEG) | capture, stickers, upload |
| `imagick` | bonus GIF |

El [Dockerfile](Dockerfile) instala dependencias de sistema (`libjpeg`, ImageMagick, etc.) y habilita extensiones. Guía: [docker.md](docker.md).

---

## 3. Buenas prácticas usadas

- Prepared statements vía `Database::query($sql, $params)`.  
- `PDO::ERRMODE_EXCEPTION`.  
- Salida escapada en vistas.  
- CSRF en POST.  
- Sin `eval` / `shell_exec` con input de usuario.

---

## 4. Enlaces oficiales

- [PHP 8.2](https://www.php.net/releases/8.2/en.php)  
- [PDO](https://www.php.net/manual/en/book.pdo.php)  
- [GD](https://www.php.net/manual/en/book.image.php)  
- [Imagick](https://www.php.net/manual/en/book.imagick.php)  
- [Sessions](https://www.php.net/manual/en/book.session.php)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
