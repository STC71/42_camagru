# 🎮 `app/controllers/` — Controladores HTTP

> **Navegación:** [app/](../README.md) · [core/](../core/README.md) · [models/](../models/README.md) · [views/](../views/README.md)

Coordinan cada URL: autenticación, editor, galería, home y usuario.

---

## Inventario

| Clase | Rutas típicas | Rol |
|-------|---------------|-----|
| `HomeController` | `/`, `/home` | Landing |
| `AuthController` | `/login`, `/register`, `/logout`, `/verify`, `/forgot-password`, `/reset-password` | Alta, sesión, mails |
| `EditorController` | `/editor`, `/editor/capture`, `/upload`, `/create-gif`, `/delete` | Webcam, stickers, GIF, borrado propio |
| `GalleryController` | `/gallery`, like, comment, load-more | Galería pública y social |
| `UserController` | `/profile`, `/settings` | Perfil, preferencias, **baja de cuenta** |

Todos extienden `Controller` ([core/](../core/README.md)).

---

## Editor (subject)

- Solo usuarios autenticados (`requireAuth`).  
- **Captura / GIF:** composición en servidor (GD / Imagick).  
- Stickers desde `public/stickers/`.  
- Upload: campo archivo `image` + CSRF + sticker.  
- Delete: comprueba `user_id` dueño.

Ver [views/editor/](../views/editor/README.md) · [js.md](../../js.md).

---

## Gallery

- Listado público ordenado por fecha.  
- Likes/comentarios si hay sesión (a menudo por AJAX).  
- Email al autor si `notify_comments` (Mailer).  
- Paginación o infinite scroll.

---

## Auth & User

- Password hasheado; verificación de email antes de login.  
- Settings: perfil, password, notificaciones, eliminar cuenta (+ fotos opcionales).

---

## Cómo probar

```bash
./test_auto.sh --section features
./test_eval.sh   # pasos 2–7
```

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
