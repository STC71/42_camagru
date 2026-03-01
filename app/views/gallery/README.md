# 🖼️ Vista de Galería

## 🎯 ¿Qué es y para qué sirve?

La **galería pública** donde se ven todas las fotos que han subido los usuarios.

**¿Por qué existe?** Para compartir las creaciones y permitir interacción social (likes y comentarios).

## 📋 Contenido

**`index.php`** - Grid de fotos con sistema de likes y comentarios

## ✨ ¿Qué puede hacer el usuario aquí?

### Ver Fotos
- Grid responsive (se adapta a móvil/tablet/desktop)
- Información de quién subió cada foto
- Fecha de publicación
- Carga progresiva de imágenes

### Interacción Social
- ✅ Sistema de likes con toggle
- ✅ Contador de likes en tiempo real
- ✅ Sistema de comentarios
- ✅ Formulario de comentario
- ✅ Lista de comentarios

### Paginación y Navegación
- ✅ Paginación tradicional (5 imágenes/página)
- ✅ Scroll infinito con Intersection Observer (BONUS)
- ✅ Indicador de carga
- ✅ Mensaje cuando no hay más contenido

### Compartir (BONUS)
- ✅ Botones para redes sociales
- ✅ Copiar enlace al portapapeles
- ✅ Modal de compartir

## 🎨 JavaScript - Funcionalidad AJAX

### Sistema de Likes
```javascript
async function toggleLike(imageId) {
    const response = await fetch('/gallery/like', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ image_id: imageId })
    });
    
    const data = await response.json();
    
    // Actualizar contador en UI
    document.querySelector(`#likes-${imageId}`)
        .textContent = data.likes_count;
}
```

### Comentarios Dinámicos
```javascript
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
    
    // Añadir comentario al DOM sin recargar
    appendCommentToList(comment);
}
```

### Scroll Infinito (BONUS)
```javascript
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !loading && hasMore) {
            loadMoreImages();
        }
    });
});

observer.observe(document.querySelector('#sentinel'));
```

## 📺 Videos Educativos en Español

### AJAX y Fetch API
- [Fetch API - Tutorial Completo](https://www.youtube.com/watch?v=cuEtnrL9-H0) - Por FalconMasters
- [AJAX con JavaScript Moderno](https://www.youtube.com/watch?v=rX6tC5K_XaI) - Por MoureDev
- [Async/Await en JavaScript](https://www.youtube.com/watch?v=Q3HtXuDEy5s) - Por MiduDev

### Intersection Observer
- [Intersection Observer API](https://www.youtube.com/watch?v=T8EYosX4NOo) - Por FalconMasters
- [Scroll Infinito con JavaScript](https://www.youtube.com/watch?v=NZKUirTtxcg) - Por MiduDev
- [Lazy Loading de Imágenes](https://www.youtube.com/watch?v=aUjBvuUdkhg) - Por Google Chrome Developers

### CSS Grid
- [CSS Grid Layout - Tutorial](https://www.youtube.com/watch?v=QBOUSrMqlSQ) - Por FalconMasters
- [Gallery con CSS Grid](https://www.youtube.com/watch?v=68O6eOGAGqA) - Por DesignCourse

---

[⬆ Volver a views/](../README.md) | [⬆ README principal](../../../README.md)
