# 🌐 `public/` — Document root

> **Navegación:** [README](../README.md) · [app/](../app/README.md) · [docker.md](../docker.md) · [js.md](../js.md) · [css.md](../css.md)

**Único** árbol expuesto por Apache. Todo lo demás (`app/`, `config/`, …) queda fuera del vhost.

---

## Estructura

```
public/
├── index.php      # Front controller (sesión, router, I18n)
├── .htaccess      # rewrite hacia index.php
├── css/           → [css/README.md](css/README.md)
├── js/            → [js/README.md](js/README.md)
├── stickers/      → [stickers/README.md](stickers/README.md)
└── uploads/       → [uploads/README.md](uploads/README.md)
```

---

## `index.php`

- `session_start()`  
- Carga config / autoload sencillo de clases  
- `I18n::init()`  
- Enruta con `Router` hacia controladores en `app/`

URLs “bonitas”: `/gallery`, `/editor`, `/login` → rewrite (ver `apache.conf` / `.htaccess`).

---

## Seguridad

- No colocar secretos ni `.env` aquí.  
- Uploads solo bajo `uploads/` con validación en servidor.  
- Headers (XSS, frame, etc.) pueden configurarse en Apache del contenedor.

---

## Documentación hija

| Subcarpeta | Tema |
|------------|------|
| [css/](css/README.md) | Hoja de estilos principal |
| [js/](js/README.md) | editor.js, gallery/main |
| [stickers/](stickers/README.md) | PNG superponibles |
| [uploads/](uploads/README.md) | Fotos y GIFs de usuarios |

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
