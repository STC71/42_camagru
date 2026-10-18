# ⚡ JavaScript en Camagru

[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![MDN](https://img.shields.io/badge/MDN-Web_Docs-000000?style=flat-square&logo=mdnwebdocs)](https://developer.mozilla.org/)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

---

## 1. ¿Qué es JavaScript y qué papel juega aquí?

**JavaScript** es el lenguaje que se ejecuta **dentro del navegador** del usuario.

En Camagru se encarga de todo lo interactivo:

- Encender la webcam
- Mostrar el sticker en tiempo real encima del vídeo
- Permitir arrastrar el sticker
- Enviar likes y comentarios sin recargar la página (AJAX)
- Cargar más fotos al hacer scroll (infinite scroll)
- Grabar fotogramas para crear un GIF

> **Analogía cotidiana:**  
> Si PHP es la cocina del restaurante, **JavaScript es el camarero**. Recoge lo que el cliente quiere, se lo comunica a la cocina y actualiza la mesa sin que el cliente tenga que levantarse.

Documentación de referencia: [MDN Web Docs – JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

---

## 2. Enfoque del proyecto

| Decisión | Motivo |
|----------|--------|
| **JavaScript vanilla** (sin React, Vue, jQuery…) | El subject de 42 prohíbe frameworks |
| Código en `/public/js/` | Separación clara cliente / servidor |
| ES6+ (`let`, `const`, arrow functions, `fetch`…) | Código más legible y moderno |

---

## 3. APIs del navegador que utilizamos

| API | ¿Para qué? | Documentación oficial |
|-----|------------|------------------------|
| **MediaDevices.getUserMedia** | Acceder a la webcam | [MDN getUserMedia](https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia) |
| **Canvas API** | Dibujar el vídeo + sticker y capturar fotogramas | [MDN Canvas](https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API) |
| **Fetch API** | Enviar likes, comentarios y pedir más fotos | [MDN Fetch](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API) |
| **Intersection Observer** | Detectar cuándo el usuario llega al final de la galería | [MDN Intersection Observer](https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API) |
| **FileReader** | Previsualizar una imagen antes de subirla | [MDN FileReader](https://developer.mozilla.org/en-US/docs/Web/API/FileReader) |
| **Drag / Pointer events** | Mover el sticker con el ratón o el dedo | [MDN Pointer events](https://developer.mozilla.org/en-US/docs/Web/API/Pointer_events) |

---

## 4. Compatibilidad exigida por el subject

El subject pide que funcione en:

- **Firefox ≥ 41**
- **Chrome ≥ 46**

Todas las APIs que usamos están disponibles en esas versiones (o tienen alternativas sencillas).

---

## 5. Ejemplos reales del proyecto

### 5.1 Encender la webcam

```javascript
async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user' },
        audio: false
    });
    videoElement.srcObject = stream;
}
```

### 5.2 Dibujar vídeo + sticker en un canvas (live preview)

```javascript
function drawFrame() {
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    ctx.drawImage(stickerImg, stickerX, stickerY, stickerW, stickerH);
    requestAnimationFrame(drawFrame); // siguiente fotograma
}
```

### 5.3 Enviar un like con Fetch (AJAX)

```javascript
async function toggleLike(imageId) {
    const formData = new FormData();
    formData.append('image_id', imageId);
    formData.append('csrf_token', csrfToken);

    const response = await fetch('/gallery/like', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();

    // Actualizamos el contador y el corazón sin recargar la página
    updateLikeUI(imageId, data.liked, data.like_count);
}
```

### 5.4 Scroll infinito con Intersection Observer

```javascript
const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && hasMore) {
        loadMoreImages(nextPage);
    }
});

observer.observe(document.querySelector('#scroll-sentinel'));
```

### 5.5 Capturar fotogramas para el GIF

```javascript
function captureFrame() {
    const dataUrl = canvas.toDataURL('image/png');
    frames.push(dataUrl);
}
// Luego se envían todos los frames a EditorController::createGif
```

---

## 6. Buenas prácticas que seguimos

- Usamos `const` y `let` (nunca `var`)
- Preferimos `async/await` frente a `.then()` encadenados
- Siempre enviamos el token CSRF en las peticiones POST
- Validamos en el cliente **y** en el servidor (nunca confiamos solo en el cliente)
- Evitamos contaminar el ámbito global siempre que es posible

---

## 7. Recursos oficiales recomendados

| Recurso | Enlace |
|---------|--------|
| JavaScript Guide (MDN) | https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide |
| Web APIs | https://developer.mozilla.org/en-US/docs/Web/API |
| getUserMedia | https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia |
| Fetch API | https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API |
| Canvas tutorial | https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API/Tutorial |
| Intersection Observer | https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API |

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
