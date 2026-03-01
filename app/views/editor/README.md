# 📸 Vista del Editor de Fotos

## 🎯 ¿Qué es y para qué sirve?

La página donde los usuarios **capturan fotos con su webcam** y les ponen stickers.

**¿Por qué existe?** Es la funcionalidad central del proyecto: crear fotos personalizadas.

## 📋 Contenido

**`index.php`** - La interfaz completa del editor (webcam + stickers + controles)

## ✨ ¿Qué puede hacer el usuario aquí?

### 1. Captura desde Webcam
- Activar la cámara (pide permiso al usuario)
- Ver vista previa en tiempo real
- Botón para tomar la foto
- Mensaje de error si deniega permisos

### Superposición de Stickers
- ✅ Selección de stickers predefinidos
- ✅ Vista previa en vivo (BONUS)
- ✅ Posicionamiento drag & drop (BONUS)
- ✅ Control de tamaño con slider (BONUS)

### Subida de Archivo
- ✅ Input type="file"
- ✅ Validación de tipo
- ✅ Vista previa antes de subir
- ✅ Drag & drop de archivos

### Generación de GIF (BONUS)
- ✅ Botón de grabación
- ✅ Captura de 30 frames
- ✅ Indicador visual de progreso
- ✅ Procesamiento en servidor

### Galería Personal
- ✅ Mis fotos subidas
- ✅ Botón de eliminar
- ✅ Confirmación antes de borrar

## 🎨 Tecnologías Frontend

### HTML5 APIs
```javascript
// getUserMedia API - Acceso a webcam
navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    });

// Canvas API - Procesamiento de imagen
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
```

### JavaScript Vanilla
```javascript
// Capturar foto con sticker
function capturePhoto() {
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Superponer sticker
    if (selectedSticker) {
        ctx.drawImage(selectedSticker, stickerX, stickerY, stickerSize, stickerSize);
    }
    
    // Convertir a blob y enviar
    canvas.toBlob(blob => {
        const formData = new FormData();
        formData.append('photo', blob);
        fetch('/editor/capture', { method: 'POST', body: formData });
    });
}
```

## 📺 Videos Educativos en Español

### WebCam y Canvas
- [getUserMedia API - Tutorial](https://www.youtube.com/watch?v=wXtlrMjRx3k) - Por Coding Shiksha
- [Canvas API en JavaScript](https://www.youtube.com/watch?v=EO6OkltgudE) - Por FalconMasters
- [Manipulación de Imágenes con Canvas](https://www.youtube.com/watch?v=0eWrpsCLMJQ) - Por MoureDev

### Drag & Drop
- [HTML5 Drag and Drop API](https://www.youtube.com/watch?v=wv7pvH1O5Ho) - Por MiduDev
- [Drag & Drop desde Cero](https://www.youtube.com/watch?v=C22hQKE_32c) - Por FaztCode

### File Upload
- [File API y FileReader](https://www.youtube.com/watch?v=ScZZoHj3gK4) - Por Código Facilito
- [Upload de Archivos con AJAX](https://www.youtube.com/watch?v=ZJUvMHolT5o) - Por FalconMasters

---

[⬆ Volver a views/](../README.md) | [⬆ README principal](../../../README.md)
