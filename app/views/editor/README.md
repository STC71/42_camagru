# 🎨 `app/views/editor/` — Editor de fotos

> **Navegación:** [views/](../README.md) · [EditorController](../../controllers/README.md) · [js.md](../../../js.md) · [public/js](../../../public/js/README.md)

## Contenido

- `index.php`: pestañas **Webcam** / **Subir archivo**, grid de stickers, miniaturas propias, CSRF, controles de captura/GIF.
- Script: `/js/editor.js` (getUserMedia, preview, drag sticker, upload, GIF).
- Estilos embebidos + [public/css/style.css](../../../public/css/README.md).

## Reglas de UX alineadas al subject

- Editor solo si hay login (si no, redirect).  
- Capturar / GIF / subir: requieren **sticker** seleccionado (botones deshabilitados si falta).  
- La superposición definitiva se calcula en el **servidor** (no confiar solo en el canvas del cliente).

## Upload

- Zona compacta drag-and-drop o clic (`#file-input` a pantalla de la zona).  
- Campo `image` + CSRF; preview local antes de enviar.

## Bonus

- Preview en vivo + drag del sticker.  
- GIF vía `create-gif` + Imagick en el contenedor ([docker.md](../../../docker.md)).

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
