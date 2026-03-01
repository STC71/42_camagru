# 🎭 Directorio `stickers/` - Stickers Predefinidos

## 🎯 ¿Qué es y para qué sirve?

Aquí están los **stickers** que los usuarios pueden poner sobre sus fotos: gafas de sol, sombreros, efectos, etc.

**¿Por qué existe?** Para ofrecer opciones divertidas que personalicen las fotos sin que los usuarios suban sus propios stickers.

**¿Para qué sirve?**
- Proporcionar elementos decorativos listos para usar
- Hacer las fotos más divertidas e interesantes
- Cumplir con el requisito del subject (superposición de imágenes)

## 📋 Contenido

Archivos PNG con **fondo transparente** que se superponen sobre las fotos:

```
stickers/
├── sticker1.png  (ej: gafas de sol)
├── sticker2.png  (ej: sombrero)
├── sticker3.png  (ej: máscara)
└── ...
```

## 🎨 Características Técnicas

**Requisitos importantes:**
- **Formato:** PNG con transparencia (canal alfa)
- **Fondo:** Completamente transparente
- **Tamaño:** Entre 200x200px y 500x500px
- **Peso:** Menos de 200KB por sticker

**Ejemplos de qué stickers tener:**
- 😎 Gafas y accesorios
- 🎩 Sombreros y coronas
- 🎭 Máscaras
- 💬 Bocadillos de texto
- ⭐ Efectos visuales
- 🐱 Orejas de animales

## 🔧 Cómo Añadir Nuevos Stickers

### 1. Preparar la Imagen
```bash
# Asegurarse de que tiene transparencia
# Usar GIMP, Photoshop, o ImageMagick

# Con ImageMagick - convertir a PNG con alfa
convert sticker.jpg -background none -alpha on sticker.png

# Optimizar tamaño
pngquant sticker.png --output sticker-optimized.png
```

### 2. Añadir al Directorio
```bash
# Copiar al directorio de stickers
cp nuevo-sticker.png public/stickers/

# Verificar permisos
chmod 644 public/stickers/nuevo-sticker.png
```

### 3. Actualizar la Vista del Editor
```php
<!-- En app/views/editor/index.php -->
<div class="sticker-selector">
    <?php 
    $stickers = glob('stickers/*.png');
    foreach ($stickers as $sticker): 
    ?>
        <img src="/<?= $sticker ?>" 
             alt="Sticker" 
             class="sticker-option"
             onclick="selectSticker('/<?= $sticker ?>')">
    <?php endforeach; ?>
</div>
```

## 💡 Superposición de Stickers

### Procesamiento del Lado del Cliente
```javascript
// En editor.js
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');

// Dibujar foto base
ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

// Superponer sticker
const sticker = new Image();
sticker.src = '/stickers/sticker1.png';
sticker.onload = () => {
    ctx.drawImage(sticker, x, y, width, height);
};
```

### Procesamiento del Lado del Servidor (PHP GD)
```php
// En EditorController.php
$photo = imagecreatefromstring($photoData);
$sticker = imagecreatefrompng('stickers/sticker1.png');

// Obtener dimensiones
$photoWidth = imagesx($photo);
$photoHeight = imagesy($photo);
$stickerWidth = imagesx($sticker);
$stickerHeight = imagesy($sticker);

// Posición del sticker
$x = ($photoWidth - $stickerWidth) / 2;
$y = ($photoHeight - $stickerHeight) / 2;

// Fusionar (mantener transparencia)
imagealphablending($photo, true);
imagesavealpha($photo, true);

imagecopy(
    $photo,           // imagen destino
    $sticker,         // imagen origen
    $x, $y,           // posición en destino
    0, 0,             // posición en origen
    $stickerWidth,    // ancho
    $stickerHeight    // alto
);

// Guardar resultado
imagepng($photo, 'uploads/images/result.png');
```

## 🎯 Recursos para Stickers

### Sitios con Stickers Gratuitos
- [Flaticon](https://www.flaticon.com/) - Miles de iconos PNG
- [FreePik](https://www.freepik.com/) - Recursos gráficos
- [Noun Project](https://thenounproject.com/) - Iconos minimalistas
- [Stickermule](https://www.stickermule.com/) - Stickers varios
- [OpenMoji](https://openmoji.org/) - Emojis open source

### Crear Stickers Propios
```bash
# Herramientas recomendadas
- GIMP (gratis)
- Photoshop (pago)
- Inkscape (gratis, vectorial)
- Figma (gratis, online)
```

## 🔒 Seguridad

### Proteger contra Uploads No Autorizados
```apache
# En public/stickers/.htaccess
Order allow,deny
Allow from all

# Denegar subida de archivos
<Limit POST PUT DELETE>
    Order deny,allow
    Deny from all
</Limit>
```

### Validar Stickers
```php
// Antes de usar un sticker
function validateSticker($path) {
    // Verificar que existe
    if (!file_exists($path)) {
        throw new Exception('Sticker no encontrado');
    }
    
    // Verificar que es PNG
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $path);
    
    if ($mime !== 'image/png') {
        throw new Exception('Solo se permiten stickers PNG');
    }
    
    // Verificar que está en el directorio correcto
    $realPath = realpath($path);
    $stickerDir = realpath('public/stickers');
    
    if (strpos($realPath, $stickerDir) !== 0) {
        throw new Exception('Path de sticker inválido');
    }
    
    return true;
}
```

## 📺 Videos Educativos en Español

### Procesamiento de Imágenes
- [PHP GD Library - Tutorial](https://www.youtube.com/watch?v=3JvEEML5E8c) - Por HolaMundo
- [Manipulación de Imágenes en PHP](https://www.youtube.com/watch?v=O-5d2KIXvX8) - Por Código Facilito
- [Canvas API - Superposición de Imágenes](https://www.youtube.com/watch?v=EO6OkltgudE) - Por FalconMasters

### Diseño Gráfico
- [GIMP Tutorial Completo](https://www.youtube.com/watch?v=Yz3-fNVXVt0) - Por Dragovian
- [Crear Transparencias en GIMP](https://www.youtube.com/watch?v=dF5sXDy_J8Y) - Por Tutoriales y Más
- [Figma para Principiantes](https://www.youtube.com/watch?v=FTFaQWZBqQ8) - Por MoureDev

### Optimización de Imágenes
- [Optimizar Imágenes Web](https://www.youtube.com/watch?v=F1kYBnY6mwg) - Por Google Developers
- [ImageMagick Tutorial](https://www.youtube.com/watch?v=rX6tC5K_XaI) - Por Tech With Tim

## 🔗 Enlaces Relacionados

- [Volver a public/](../README.md)
- [Editor](../../app/views/editor/README.md)
- [JavaScript del Editor](../js/README.md)

---

[⬆ Volver al README principal](../../README.md)
