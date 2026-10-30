# 📜 `public/js/` — JavaScript del cliente

> **Navegación:** [public/](../README.md) · [js.md](../../js.md) · [views/editor](../../app/views/editor/README.md)

## Archivos habituales

| Archivo | Responsabilidad |
|---------|-----------------|
| `editor.js` | Webcam, preview sticker, drag, capture, upload, GIF, delete |
| `main.js` / `gallery.js` | Navegación auxiliar, likes/comentarios AJAX, infinite scroll |

## Contratos con el backend

- CSRF: input `#csrf_token` o `name="csrf_token"`.  
- Capture / upload / GIF → JSON `{ success, url, message… }`.  
- Upload: `FormData` con campo **`image`**.  
- Stickers: paths relativos bajo `/stickers/...`.

Sin npm/bundler (requisito eval): JS plano servido estático.

Más profundidad: **[js.md](../../js.md)**.

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
