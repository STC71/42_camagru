# 🐘 PHP en Camagru

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![PHP Docs](https://img.shields.io/badge/Documentación-Oficial-777BB4?style=flat-square&logo=php)](https://www.php.net/docs.php)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

---

## 1. ¿Qué es PHP y por qué lo usamos?

**PHP** (PHP: Hypertext Preprocessor) es un lenguaje de programación del lado del servidor, especialmente pensado para crear páginas web dinámicas.

En Camagru lo usamos para:

- Recibir los datos de los formularios (login, registro, comentarios…)
- Hablar con la base de datos de forma segura
- Procesar las imágenes (pegar stickers, crear GIFs)
- Generar las páginas HTML que ve el usuario
- Enviar correos de verificación y notificaciones

> **Analogía cotidiana:**  
> Si el navegador es el comedor de un restaurante, **PHP es la cocina**. El camarero (JavaScript) recoge el pedido, la cocina lo prepara y devuelve el plato listo.

Documentación oficial: [https://www.php.net/](https://www.php.net/)

---

## 2. Versión utilizada

| Concepto | Valor |
|----------|--------|
| Versión | **PHP 8.2** |
| Imagen Docker | `php:8.2-apache-bookworm` |
| Servidor web | Apache (módulo `mod_php`) |

Elegimos PHP 8.2 porque es estable, moderna y es la que trae la imagen oficial de Docker que usamos.

---

## 3. Extensiones que usamos (y para qué)

| Extensión | ¿Para qué la necesitamos? | Dónde se ve en el código |
|-----------|---------------------------|--------------------------|
| **PDO + pdo_mysql** | Conectar con MySQL de forma segura (sentencias preparadas) | `app/core/Database.php` |
| **GD** | Superponer stickers sobre fotos, redimensionar, guardar PNG | `EditorController::mergeImages()`, `loadImageWithAlpha()` |
| **Imagick** | Crear GIFs animados a partir de varios fotogramas | `EditorController::createGif()` |
| **mbstring** | Manejar correctamente textos con caracteres especiales | Validaciones y comentarios |
| **fileinfo** | Saber el tipo real de un archivo subido (no solo la extensión) | `Validator::isImage()` |
| **openssl** | Generar tokens aleatorios seguros | Tokens de verificación y CSRF |

### Cómo se instalan en Docker

```dockerfile
RUN apt-get update && apt-get install -y \
        libpng-dev libmagickwand-dev \
    && docker-php-ext-install pdo_mysql gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick
```

---

## 4. Decisiones de diseño importantes

### 4.1 Sin frameworks ni Composer

El subject de 42 prohíbe frameworks y gestores de dependencias. Por eso todo está escrito en **PHP puro**.

### 4.2 Arquitectura MVC ligera

```
app/
├── controllers/   → Reciben la petición y deciden qué hacer
├── models/        → Hablan con la base de datos
├── views/         → Generan el HTML
└── core/          → Piezas reutilizables (Router, Database, Mailer…)
```

**Flujo real de ejemplo:**

1. El usuario entra en `/editor`
2. `Router` decide llamar a `EditorController::index()`
3. El controlador pide al modelo `Image` las últimas fotos del usuario
4. Carga la vista `editor/index.php` y le pasa los datos

### 4.3 Seguridad desde el diseño

| Amenaza | Cómo la evitamos en PHP |
|---------|-------------------------|
| SQL Injection | PDO + sentencias preparadas (`?`) |
| XSS | `htmlspecialchars($texto, ENT_QUOTES, 'UTF-8')` |
| CSRF | Token aleatorio en sesión + `hash_equals()` |
| Contraseñas en texto plano | `password_hash()` / `password_verify()` |
| Archivos maliciosos | `finfo` + lista blanca de tipos MIME |

---

## 5. Ejemplos reales del proyecto

### 5.1 Hash de contraseña (modelo User)

```php
// Al registrar
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Al hacer login
if (password_verify($password, $user['password'])) {
    // Contraseña correcta
}
```

### 5.2 Consulta segura a la base de datos

```php
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $this->db->query($sql, [$email]);
$user = $stmt->fetch();
```

Nunca hacemos esto (peligroso):

```php
// ❌ PELIGROSO
$sql = "SELECT * FROM users WHERE email = '$email'";
```

### 5.3 Superposición de sticker (GD)

```php
$baseImage = imagecreatefromstring($decodedImage);
$sticker   = imagecreatefrompng($stickerPath);

imagecopy(
    $finalImage, $sticker,
    $x, $y, 0, 0,
    imagesx($sticker), imagesy($sticker)
);

imagepng($finalImage, $filepath);
```

### 5.4 Creación de GIF (Imagick)

```php
$animation = new Imagick();
$animation->setFormat('gif');

foreach ($processedFrames as $frame) {
    $image = new Imagick($frame);
    $image->setImageDelay(10);   // 100 ms
    $animation->addImage($image);
}

$animation->setImageIterations(0); // bucle infinito
$animation->writeImages($gifPath, true);
```

---

## 6. Recursos oficiales recomendados

| Recurso | Enlace |
|---------|--------|
| Manual de PHP | https://www.php.net/manual/en/ |
| Seguridad en PHP | https://www.php.net/manual/en/security.php |
| PDO | https://www.php.net/manual/en/book.pdo.php |
| GD | https://www.php.net/manual/en/book.image.php |
| Imagick | https://www.php.net/manual/en/book.imagick.php |
| password_hash | https://www.php.net/manual/en/function.password-hash.php |

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
