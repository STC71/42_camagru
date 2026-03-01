# ⚡ Directorio `js/` - JavaScript

## 🎯 ¿Qué es y para qué sirve?

Aquí está el JavaScript que añade **interactividad** sin recargar la página: likes en tiempo real, scroll infinito, webcam, etc.

**¿Por qué existe?** Para hacer la aplicación más fluida y moderna, sin recargas constantes.

**¿Para qué sirve?**
- Hacer peticiones AJAX (likes y comentarios sin recargar)
- Acceder a la webcam del usuario (getUserMedia API)
- Procesar imágenes con Canvas API
- Scroll infinito en la galería (Intersection Observer)
- Validaciones en tiempo real

## 📋 Contenido

| Archivo | ¿Qué hace? | ¿Dónde se usa? |
|---------|------------|----------------|
| `main.js` | Funcionalidad general (mensajes flash, confirmaciones) | Todas las páginas |
| `gallery.js` | Likes y comentarios con AJAX, scroll infinito | Página `/gallery` |
| `editor.js` | Webcam, stickers, captura de fotos, GIFs | Página `/editor` |

## 🎬 ¿Qué hace cada archivo?

### main.js - Funcionalidad Común
**¿Qué hace?** Cosas que se necesitan en todas las páginas

**Funciones:**
- Mensajes flash que desaparecen automáticamente después de 3 segundos
- Confirmaciones antes de borrar algo importante
- Helpers para hacer peticiones AJAX con token CSRF
- Validaciones básicas de formularios

```javascript
// Mensajes flash auto-dismiss
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 300);
        }, 3000);
    }
});

// Confirmación de eliminación
function confirmDelete(message) {
    return confirm(message || '¿Estás seguro de eliminar esto?');
}

// Helper para AJAX con CSRF
async function fetchWithCSRF(url, options = {}) {
    const csrfToken = document.querySelector('[name="csrf_token"]').value;
    
    options.headers = {
        ...options.headers,
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
    };
    
    return fetch(url, options);
}
```

### gallery.js - Galería Pública

**Características:**
- ✅ Sistema de likes (AJAX)
- ✅ Sistema de comentarios (AJAX)
- ✅ Scroll infinito (BONUS)
- ✅ Compartir en redes sociales (BONUS)

```javascript
// Toggle de like
async function toggleLike(imageId) {
    const response = await fetch('/gallery/like', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ image_id: imageId })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Actualizar UI
        const counter = document.querySelector(`#likes-${imageId}`);
        counter.textContent = data.likes_count;
        
        const btn = document.querySelector(`[data-image-id="${imageId}"]`);
        btn.classList.toggle('liked', data.liked);
    }
}

// Añadir comentario
async function addComment(imageId, text) {
    const response = await fetch('/gallery/comment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            image_id: imageId, 
            text: text 
        })
    });
    
    const comment = await response.json();
    
    if (comment.success) {
        // Añadir al DOM
        appendCommentToList(imageId, comment);
        
        // Limpiar textarea
        document.querySelector(`#comment-form-${imageId} textarea`).value = '';
    }
}

// Scroll infinito con Intersection Observer
let page = 1;
let loading = false;
let hasMore = true;

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !loading && hasMore) {
            loadMoreImages();
        }
    });
}, { threshold: 0.5 });

async function loadMoreImages() {
    loading = true;
    page++;
    
    const response = await fetch(`/gallery/load-more?page=${page}`);
    const data = await response.json();
    
    if (data.images.length > 0) {
        data.images.forEach(image => {
            appendImageToGallery(image);
        });
    } else {
        hasMore = false;
        observer.disconnect();
    }
    
    loading = false;
}

// Observar elemento sentinel
observer.observe(document.querySelector('#sentinel'));
```

### editor.js - Editor de Fotos

**Características:**
- ✅ Acceso a webcam (getUserMedia)
- ✅ Captura de foto
- ✅ Superposición de stickers
- ✅ Vista previa en vivo (BONUS)
- ✅ Drag & drop de stickers (BONUS)
- ✅ Generación de GIF (BONUS)

```javascript
// Variables globales
let stream = null;
let selectedSticker = null;
let stickerX = 0;
let stickerY = 0;
let stickerSize = 100;

// Iniciar webcam
async function startWebcam() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: 640, 
                height: 480 
            } 
        });
        
        const video = document.getElementById('video');
        video.srcObject = stream;
        video.play();
        
        // Renderizar preview en loop
        requestAnimationFrame(renderPreview);
        
    } catch (error) {
        console.error('Error al acceder a la webcam:', error);
        alert('No se pudo acceder a la cámara. Verifica los permisos.');
    }
}

// Renderizar preview con sticker en tiempo real (BONUS)
function renderPreview() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('preview-canvas');
    const ctx = canvas.getContext('2d');
    
    // Dibujar video
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Dibujar sticker si hay uno seleccionado
    if (selectedSticker) {
        ctx.drawImage(
            selectedSticker, 
            stickerX, 
            stickerY, 
            stickerSize, 
            stickerSize
        );
    }
    
    requestAnimationFrame(renderPreview);
}

// Seleccionar sticker
function selectSticker(stickerPath) {
    const img = new Image();
    img.onload = () => {
        selectedSticker = img;
    };
    img.src = stickerPath;
}

// Drag & drop de sticker (BONUS)
let isDragging = false;

document.getElementById('preview-canvas').addEventListener('mousedown', (e) => {
    if (selectedSticker) {
        isDragging = true;
    }
});

document.addEventListener('mousemove', (e) => {
    if (isDragging) {
        const rect = e.target.getBoundingClientRect();
        stickerX = e.clientX - rect.left - stickerSize / 2;
        stickerY = e.clientY - rect.top - stickerSize / 2;
    }
});

document.addEventListener('mouseup', () => {
    isDragging = false;
});

// Capturar foto
async function capturePhoto() {
    const canvas = document.getElementById('capture-canvas');
    const ctx = canvas.getContext('2d');
    const video = document.getElementById('video');
    
    // Dibujar video
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Superponer sticker
    if (selectedSticker) {
        ctx.drawImage(
            selectedSticker, 
            stickerX, 
            stickerY, 
            stickerSize, 
            stickerSize
        );
    }
    
    // Convertir a blob y enviar
    canvas.toBlob(async (blob) => {
        const formData = new FormData();
        formData.append('photo', blob, 'capture.png');
        
        const response = await fetch('/editor/capture', {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            alert('Foto guardada correctamente');
            location.reload();
        }
    }, 'image/png');
}

// Generar GIF animado (BONUS)
async function recordGif() {
    const frames = [];
    const totalFrames = 30;
    const delay = 100; // ms
    
    // Capturar 30 frames
    for (let i = 0; i < totalFrames; i++) {
        const canvas = document.getElementById('capture-canvas');
        const ctx = canvas.getContext('2d');
        const video = document.getElementById('video');
        
        // Dibujar frame
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Añadir sticker
        if (selectedSticker) {
            ctx.drawImage(
                selectedSticker, 
                stickerX, 
                stickerY, 
                stickerSize, 
                stickerSize
            );
        }
        
        // Guardar frame
        const frameData = canvas.toDataURL('image/png');
        frames.push(frameData);
        
        // Esperar
        await new Promise(resolve => setTimeout(resolve, delay));
        
        // Actualizar progreso
        updateProgress((i + 1) / totalFrames * 100);
    }
    
    // Enviar al servidor para procesar
    const response = await fetch('/editor/create-gif', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ frames })
    });
    
    if (response.ok) {
        alert('GIF generado correctamente');
        location.reload();
    }
}
```

## 🛡️ Buenas Prácticas

### 1. Evitar Global Scope Pollution
```javascript
// ❌ MAL - Variables globales (contaminan window, colisiones de nombres)
let count = 0;
function increment() { count++; }
function getCount() { return count; }

// ✅ BIEN - Módulo con IIFE (patrón revelador)
const Counter = (function() {
    let count = 0;  // Variable privada
    
    return {
        increment: function() { count++; },
        decrement: function() { count--; },
        getCount: function() { return count; }
    };
})();

// Uso: Counter.increment(); console.log(Counter.getCount());

// ✅ TAMBIÉN BIEN - ES6 Modules (moderno)
// En counter.js:
let count = 0;
export function increment() { count++; }
export function getCount() { return count; }

// En main.js:
// import { increment, getCount } from './counter.js';
```

### 2. Manejo de Errores
```javascript
// ✅ Siempre manejar errores en async/await
async function fetchData() {
    try {
        const response = await fetch('/api/data');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        showErrorMessage('Error al cargar datos');
    }
}
```

### 3. Event Delegation
```javascript
// ❌ MAL - Listener en cada elemento (ineficiente, problemas con elementos dinámicos)
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const imageId = this.dataset.imageId;
        toggleLike(imageId);
    });
});

// ✅ BIEN - Delegation en contenedor (eficiente, funciona con elementos dinámicos)
document.querySelector('.gallery').addEventListener('click', (e) => {
    if (e.target.classList.contains('like-btn')) {
        const imageId = e.target.dataset.imageId;
        toggleLike(imageId);
    }
});

// Ventajas de Event Delegation:
// - Solo un listener (mejor rendimiento)
// - Funciona con elementos añadidos dinámicamente (AJAX, scroll infinito)
// - Menos uso de memoria
```

## 📺 Videos Educativos en Español

### JavaScript Moderno
- [JavaScript Moderno - ES6+](https://www.youtube.com/watch?v=Z4TuS0HEJP8) - Por MiduDev
- [Async/Await Explicado](https://www.youtube.com/watch?v=Q3HtXuDEy5s) - Por MiduDev
- [Fetch API Tutorial](https://www.youtube.com/watch?v=cuEtnrL9-H0) - Por FalconMasters

### APIs Web
- [getUserMedia API - Webcam](https://www.youtube.com/watch?v=wXtlrMjRx3k) - Por Coding Shiksha
- [Canvas API Completo](https://www.youtube.com/watch?v=EO6OkltgudE) - Por FalconMasters
- [Intersection Observer](https://www.youtube.com/watch?v=T8EYosX4NOo) - Por FalconMasters

### Event Handling
- [Event Delegation JavaScript](https://www.youtube.com/watch?v=pKzf80F3O0U) - Por MiduDev
- [addEventListener Avanzado](https://www.youtube.com/watch?v=XF1_MlZ5l6M) - Por FalconMasters

### Best Practices
- [JavaScript Clean Code](https://www.youtube.com/watch?v=UXO8_pXnR1g) - Por MoureDev
- [Manejo de Errores en JS](https://www.youtube.com/watch?v=L0Uin6gYKf0) - Por MiduDev

## 🔗 Enlaces Relacionados

- [Volver a public/](../README.md)
- [CSS](../css/README.md)
- [Vistas](../../app/views/README.md)
- [Controladores](../../app/controllers/README.md)

---

[⬆ Volver al README principal](../../README.md)
