# 🌍 `app/lang/` — Internacionalización (i18n)

> **Navegación:** [app/](../README.md) · [I18n core](../core/README.md) · [partials](../views/partials/README.md) · [README](../../README.md)

Textos de la interfaz en **cinco idiomas**. El idioma por defecto al entrar es **español (`es`)**.

---

## 1. Archivos

| Archivo | Idioma | Bandera (UI) |
|---------|--------|--------------|
| `es.php` | Español | 🇪🇸 |
| `en.php` | English | 🇬🇧 |
| `pt.php` | Português | 🇵🇹 |
| `de.php` | Deutsch | 🇩🇪 |
| `fr.php` | Français | 🇫🇷 |

Cada fichero **devuelve un array asociativo** clave → cadena:

```php
<?php
// app/lang/es.php (extracto)
return [
    'nav.home'     => 'Inicio',
    'nav.gallery'  => 'Galería',
    'nav.editor'   => 'Editor',
    'login.title'  => 'Iniciar sesión',
    'flash.invalid_credentials' => 'Usuario o contraseña incorrectos.',
    // ...
];
```

**Convención de claves:** `ámbito.concepto` (`nav.*`, `home.*`, `editor.*`, `flash.*`, `settings.*`, `footer.*`…).

Los nombres propios del proyecto **no se traducen** en la leyenda del pie: *Camagru*, *Outer Core*, *sternero*, *42 Málaga*.

---

## 2. Cómo se elige el idioma

Lógica en `app/core/I18n.php`, método `init()` (se llama al arrancar desde el front controller):

**Prioridad:**

1. Query `?lang=en` (y equivalentes) → se guarda en sesión y cookie `camagru_lang` (30 días)  
2. `$_SESSION['lang']`  
3. Cookie `camagru_lang`  
4. Por defecto: **`es`**

```php
// Idea equivalente a I18n::init()
if (isset($_GET['lang']) && idioma_soportado($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('camagru_lang', $_GET['lang'], time() + 60 * 60 * 24 * 30, '/');
} elseif (!empty($_SESSION['lang'])) {
    // usar sesión
} elseif (!empty($_COOKIE['camagru_lang'])) {
    // usar cookie
} else {
    $_SESSION['lang'] = 'es';
}
```

Las banderas del **header** enlazan a la misma página con `?lang=xx` (sin mostrar el nombre del idioma en texto: solo iconos).

---

## 3. Uso en vistas

Helper global `__()`:

```php
<?php
// app/views/home/index.php (ejemplo)
$title = function_exists('__') ? __('home.welcome') : 'Welcome';
?>
<h1><?php echo htmlspecialchars(__('home.welcome')); ?></h1>
<p><?php echo htmlspecialchars(__('home.subtitle')); ?></p>
<a class="btn" href="/register"><?php echo htmlspecialchars(__('home.get_started')); ?></a>
```

Comprobación `function_exists('__')` evita errores si alguien renderiza una vista fuera del bootstrap completo.

---

## 4. Uso en controladores (mensajes flash)

```php
// Ejemplo alineado con AuthController / UserController
$_SESSION['flash_error'] = function_exists('__')
    ? __('flash.invalid_credentials')
    : 'Invalid username or password.';

$_SESSION['flash_success'] = function_exists('__')
    ? str_replace('{name}', $user['username'], __('flash.welcome_back'))
    : ('Welcome back, ' . $user['username'] . '!');
```

Si la cadena del idioma usa placeholders tipo `:name` o `{name}`, el proyecto puede normalizarlos con `str_replace` o con `I18n::t($key, ['name' => $valor])` según la implementación exacta de `t()`.

---

## 5. Añadir una clave nueva (checklist)

1. Añadir la misma clave en **los cinco** ficheros (`es`, `en`, `pt`, `de`, `fr`).  
2. Usar `__('mi.clave')` en la vista o controlador.  
3. No hardcodear solo en español en el HTML.  
4. Probar cambiando bandera y recargando (sesión/cookie).

```php
// es.php
'settings.delete_account' => 'Eliminar cuenta',

// en.php
'settings.delete_account' => 'Delete account',
```

---

## 6. Qué no va en `lang/`

- Mensajes de error **técnicos** de depuración (logs del servidor).  
- Contenido generado por el usuario (comentarios, usernames).  
- Rutas de archivos o SQL.

---

## 7. Enlaces

- Código: [`app/core/I18n.php`](../core/I18n.php)  
- UI: [`app/views/partials/header.php`](../views/partials/README.md)  
- Guía PHP general: [php.md](../../php.md)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
