# 🟨 JavaScript en Camagru

[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/es/docs/Web/JavaScript)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [public/js](public/js/README.md) · [MDN](https://developer.mozilla.org/es/docs/Web/JavaScript)

Sin npm: scripts estáticos en `public/js/`.

---

## 1. CSRF desde el DOM

```javascript
// public/js/editor.js — getCsrfToken()
function getCsrfToken() {
    const el = document.getElementById('csrf_token')
        || document.querySelector('input[name="csrf_token"]');
    return el ? el.value : '';
}
```

---

## 2. Subida de imagen (`FormData`)

```javascript
// Idea de doUpload() en editor.js
const formData = new FormData();
formData.append('image', file, file.name || 'photo.jpg');
formData.append('csrf_token', getCsrfToken());
formData.append('sticker', EditorState.selectedSticker);
formData.append('sticker_x', String(EditorState.stickerPosition.x));
formData.append('sticker_y', String(EditorState.stickerPosition.y));
formData.append('sticker_size', String(EditorState.stickerSize));

const response = await fetch('/editor/upload', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    },
    body: formData
});
const result = await response.json();
```

El backend espera **`$_FILES['image']`**, no `photo`.

---

## 3. Evitar que el drop abra el fichero en otra pestaña

```javascript
window.addEventListener('dragover', function (e) {
    if (e.dataTransfer && [...e.dataTransfer.types].includes('Files')) {
        e.preventDefault();
    }
}, true);

dropZone.addEventListener('drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
    acceptFile(e.dataTransfer.files[0]);
});
```

---

## 4. Webcam

```javascript
EditorState.stream = await navigator.mediaDevices.getUserMedia({
    video: { width: 640, height: 480 },
    audio: false
});
EditorState.videoElement.srcObject = EditorState.stream;
await EditorState.videoElement.play();
```

[getUserMedia — MDN](https://developer.mozilla.org/es/docs/Web/API/MediaDevices/getUserMedia)

---

## 5. Fetch de likes (galería)

```javascript
// Patrón habitual en gallery.js / main.js
const body = new URLSearchParams();
body.set('image_id', id);
body.set('csrf_token', getCsrfToken());
await fetch('/gallery/like', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString()
});
```

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
