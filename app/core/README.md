# ⚙️ `app/core/` — Kernel de la aplicación

> **Navegación:** [app/](../README.md) · [controllers/](../controllers/README.md) · [config/](../../config/README.md) · [php.md](../../php.md)

Caja de herramientas compartida: **una sola implementación** de router, BD, correo, validación e i18n para todos los controladores.

---

## Archivos

| Archivo | Responsabilidad |
|---------|-----------------|
| `Router.php` | Mapea método + path → `Controller@action` |
| `Controller.php` | Base: `view`, `redirect`, `json`, CSRF, `requireAuth` / `requireGuest`, `model()` |
| `Database.php` | Singleton PDO + `query($sql, $params)` con `prepare` |
| `Mailer.php` | SMTP (`MAIL_*` del `.env`) para verify, reset, comentarios |
| `Validator.php` | Username, email, password, sanitización |
| `I18n.php` | Idioma por sesión/cookie; helper `__('clave')` |

---

## Database (PDO)

```php
$db = Database::getInstance();
$stmt = $db->query('SELECT * FROM users WHERE id = ?', [$id]);
$row = $stmt->fetch();
```

- `PDO::ERRMODE_EXCEPTION`  
- `PDO::ATTR_EMULATE_PREPARES => false`  
- DSN desde [config/database.php](../../config/README.md)

---

## Controller base (ideas clave)

| Método | Uso |
|--------|-----|
| `view($name, $data)` | Renderiza `app/views/...` |
| `json($data, $code)` | Respuestas AJAX (capture, like, GIF…) |
| `generateCSRF()` / `validateCSRF()` | Tokens de formulario |
| `requireAuth()` | Redirige a login si no hay sesión |
| `isPost()` | Comprueba verbo HTTP |

---

## I18n

- Ficheros en `app/lang/{es,en,pt,de,fr}.php`  
- Por defecto **español**  
- Cabecera: banderas → cambian textos de UI y muchos `flash_*`

---

## Mailer

Requiere `MAIL_HOST`, `MAIL_PORT`, `MAIL_USER`, `MAIL_PASS`, `MAIL_FROM` (ver [QUICKSTART.md](../../QUICKSTART.md)). En desarrollo suele usarse [Mailtrap](https://mailtrap.io/).

---

## Enlaces

- [controllers/](../controllers/README.md) — quién usa el core  
- [models/](../models/README.md) — consumidores de `Database`  
- [docker.md](../../docker.md) — PHP dentro del contenedor  

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
