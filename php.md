# 🐘 PHP en Camagru

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [app/core](app/core/README.md) · [docker.md](docker.md) · [Manual PHP](https://www.php.net/manual/es/)

---

## 1. Rol

Backend **sin framework**: front controller, MVC propio, PDO, sesiones, GD/Imagick, SMTP.

| Pieza | Ruta |
|-------|------|
| Entrada | `public/index.php` |
| Kernel | `app/core/*` |
| HTTP | `app/controllers/*` |
| Datos | `app/models/*` |

---

## 2. PDO y consultas preparadas

En `app/core/Database.php` la conexión fija excepciones y desactiva emulación de prepares:

```php
$this->connection = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
```

Wrapper usado por los modelos:

```php
// Database::query — idea del proyecto
public function query($sql, $params = []) {
    $stmt = $this->connection->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// app/models/User.php (ejemplo de uso)
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $this->db->query($sql, [$email]);
$user = $stmt ? $stmt->fetch() : false;
```

Documentación: [PDO](https://www.php.net/manual/en/book.pdo.php).

---

## 3. Contraseñas

```php
// Alta / cambio
$hash = password_hash($plain, PASSWORD_DEFAULT);

// Login
if (password_verify($plain, $user['password'])) {
    // sesión OK
}
```

[password_hash](https://www.php.net/manual/en/function.password-hash.php)

---

## 4. Controlador: JSON y CSRF

```php
// Patrón EditorController / GalleryController
if (!$this->validateCSRF()) {
    $this->json(['success' => false, 'message' => 'Invalid CSRF'], 403);
}
$this->json([
    'success'  => true,
    'filename' => $filename,
    'url'      => $this->url('uploads/images/' . $filename),
]);
```

---

## 5. Extensiones de imagen

```bash
docker-compose exec web php -m | grep -E 'gd|imagick'
docker-compose exec web php -r 'var_export(gd_info()["JPEG Support"]);'
```

| Extensión | Uso en Camagru |
|-----------|----------------|
| **gd** | capture, upload, merge de stickers |
| **imagick** | bonus GIF (`readImageBlob`, ensamblado de frames) |

Manual: [GD](https://www.php.net/manual/en/book.image.php) · [Imagick](https://www.php.net/manual/en/book.imagick.php)

---

## 6. i18n en PHP

```php
echo __('nav.home');  // → "Inicio" si lang=es
```

Ver [app/lang/README.md](app/lang/README.md).

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
