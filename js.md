# 🟨 JavaScript en Camagru

[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/es/docs/Web/JavaScript)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [public/js](public/js/README.md) · [editor view](app/views/editor/README.md) · [MDN JS](https://developer.mozilla.org/es/docs/Web/JavaScript)

---

## 1. Sin bundler

El eval desaconseja npm/webpack: los `.js` se sirven tal cual desde `public/js/`.

---

## 2. Editor (`editor.js`)

| API / idea | Uso |
|------------|-----|
| `getUserMedia` | Webcam |
| `canvas` | Preview del sticker y frames |
| `fetch` + `FormData` | capture, upload, create-gif, delete |
| Drag events | Posición del sticker en % |

**Upload:** impedir el default del navegador en `dragover`/`drop`; guardar el `File` en memoria; enviar campo `image`.

**GIF:** varios frames en corto intervalo → POST al servidor (Imagick).

---

## 3. Galería

- `fetch` para like/comment sin recargar.  
- Scroll o botón “cargar más” → más páginas JSON/HTML.

---

## 4. Enlaces MDN

- [Fetch](https://developer.mozilla.org/es/docs/Web/API/Fetch_API)  
- [MediaDevices.getUserMedia](https://developer.mozilla.org/es/docs/Web/API/MediaDevices/getUserMedia)  
- [Canvas](https://developer.mozilla.org/es/docs/Web/API/Canvas_API)  
- [FormData](https://developer.mozilla.org/es/docs/Web/API/FormData)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
