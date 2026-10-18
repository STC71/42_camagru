# JavaScript en Camagru

[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![MDN](https://img.shields.io/badge/MDN-Web_Docs-000000?style=flat-square&logo=mdnwebdocs)](https://developer.mozilla.org/)

## Enfoque

JavaScript **vanilla** (sin frameworks ni librerías externas).  
Todo el código cliente está en `/public/js/`.

Documentación oficial de referencia: [MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

## APIs del navegador utilizadas

| API | Uso | Documentación oficial |
|-----|-----|-----------------------|
| **getUserMedia** | Acceso a la webcam | [MediaDevices.getUserMedia](https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia) |
| **Canvas** | Vista previa en vivo + drag del sticker | [Canvas API](https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API) |
| **Fetch / XMLHttpRequest** | Likes, comentarios y scroll infinito (AJAX) | [Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API) |
| **Intersection Observer** | Detección de scroll infinito | [Intersection Observer](https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API) |
| **FileReader** | Previsualización de imágenes subidas | [FileReader](https://developer.mozilla.org/en-US/docs/Web/API/FileReader) |

## Compatibilidad exigida por el subject

- Firefox ≥ 41
- Chrome ≥ 46

## Características implementadas en el cliente

- Vista previa en vivo de la webcam con sticker arrastrable
- Captura de fotogramas para GIF animado
- Actualización de likes y comentarios sin recargar la página
- Scroll infinito en la galería
- Validación básica de formularios antes de enviar

## Recursos oficiales

- JavaScript Guide: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide
- Web APIs: https://developer.mozilla.org/en-US/docs/Web/API
- getUserMedia: https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
