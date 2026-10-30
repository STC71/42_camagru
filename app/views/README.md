# 🖼️ `app/views/` — Plantillas

> **Navegación:** [app/](../README.md) · [partials/](partials/README.md) · [css.md](../../css.md) · [public/](../../public/README.md)

HTML generado por PHP. **Sin lógica de negocio pesada**: reciben datos del controlador y muestran UI. Textos traducibles con `__()` cuando existe I18n.

---

## Subcarpetas

| Carpeta | README | Contenido |
|---------|--------|-----------|
| [home/](home/README.md) | Landing / features |
| [auth/](auth/README.md) | Login, registro, forgot/reset |
| [editor/](editor/README.md) | Webcam, upload, stickers |
| [gallery/](gallery/README.md) | Galería pública |
| [user/](user/README.md) | Perfil y ajustes |
| [partials/](partials/README.md) | Header, footer, sesión, idiomas |

---

## Convención

```php
<?php
$title = function_exists('__') ? __('nav.login') : 'Login';
include __DIR__ . '/../partials/header.php';
?>
<!-- contenido -->
<?php include __DIR__ . '/../partials/footer.php'; ?>
```

- Escapar salidas de usuario: `htmlspecialchars(...)`.  
- Flash: `$_SESSION['flash_success']` / `flash_error` (mensajes i18n desde controladores).

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
