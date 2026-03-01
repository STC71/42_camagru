# 📤 Directorio `uploads/` - Archivos de Usuario

## 🎯 ¿Qué es y para qué sirve?

Aquí se guardan **todas las fotos y GIFs** que crean los usuarios con el editor.

**¿Por qué existe?** Para almacenar el contenido generado por los usuarios de forma organizada y segura.

**¿Para qué sirve?**
- Guardar permanentemente las fotos con stickers
- Almacenar los GIFs animados (BONUS)
- Tener un lugar temporal durante el procesamiento

## 📋 Estructura

```
uploads/
├── images/      # PERMANENTE - Fotos y GIFs finales
└── temp/        # TEMPORAL - Procesamiento intermedio
```

### images/ - Contenido Final
**¿Qué hay aquí?** Las fotos y GIFs guardados que aparecen en la galería

**Contenido:**
- Fotos capturadas desde la webcam (con sticker superpuesto)
- Fotos subidas desde archivo (con sticker superpuesto)
- GIFs animados generados (BONUS)

**Nombres de archivo:** `photo_{user_id}_{timestamp}_{random}.png` o `.gif`

### temp/ - Archivos Temporales
**¿Qué hay aquí?** Archivos que se usan durante el procesamiento y luego se borran

**Contenido:**
- Frames individuales cuando se crea un GIF
- Versiones intermedias durante la fusión de stickers
- Archivos que se eliminan automáticamente después de 24 horas

## 🔒 Seguridad Crítica

### 1. .htaccess - DENEGAR EJECUCIÓN DE PHP

```apache
# En public/uploads/.htaccess

# CRÍTICO: Denegar ejecución de PHP
<FilesMatch "\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Solo permitir imágenes
<FilesMatch "\.(jpg|jpeg|png|gif)$">
    Order allow,deny
    Allow from all
</FilesMatch>

# Prevenir listado de directorio
Options -Indexes

# Cabeceras de seguridad
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set Content-Security-Policy "default-src 'none'; img-src 'self'"
</IfModule>
```

**¿Por qué es importante?**
Sin esto, un atacante podría:
1. Subir un archivo PHP disfrazado de imagen
2. Ejecutarlo visitando `/uploads/shell.php`
3. Comprometer todo el servidor

### 2. Validación de Archivos

```php
// En EditorController.php
function validateUpload($file) {
    // 1. Verificar error de upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir archivo');
    }
    
    // 2. Verificar tamaño
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        throw new Exception('Archivo demasiado grande (máx 5MB)');
    }
    
    // 3. Verificar MIME TYPE REAL (no solo extensión)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Solo se permiten imágenes JPG/PNG');
    }
    
    // 4. Verificar que es una imagen válida
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('El archivo no es una imagen válida');
    }
    
    // 5. Verificar dimensiones razonables
    list($width, $height) = $imageInfo;
    $maxDimension = 4096; // px
    if ($width > $maxDimension || $height > $maxDimension) {
        throw new Exception('Dimensiones de imagen demasiado grandes');
    }
    
    return true;
}
```

### 3. Nombres de Archivo Seguros

```php
// ❌ MAL - Usar nombre original (peligroso)
$filename = $_FILES['photo']['name'];
move_uploaded_file($tmp, "uploads/images/$filename");

// ✅ BIEN - Generar nombre único y seguro
$extension = 'png'; // o 'jpg', 'gif'
$userId = $_SESSION['user_id'];
$timestamp = time();
$random = bin2hex(random_bytes(8));

$filename = "photo_{$userId}_{$timestamp}_{$random}.{$extension}";
move_uploaded_file($tmp, "public/uploads/images/$filename");
```

### 4. Permisos de Archivos

```bash
# Permisos del directorio (escritura para servidor)
chmod 755 public/uploads/
chmod 755 public/uploads/images/
chmod 755 public/uploads/temp/

# Permisos de archivos (solo lectura)
chmod 644 public/uploads/images/*

# Owner correcto (usuario del servidor web)
chown -R www-data:www-data public/uploads/
```

## 🧹 Limpieza de Archivos

### Script de Limpieza (Cron Job)

```php
<?php
// scripts/cleanup_temp.php

// Eliminar archivos temp antiguos (>24h)
$tempDir = __DIR__ . '/../public/uploads/temp/';
$files = glob($tempDir . '*');
$now = time();
$maxAge = 24 * 60 * 60; // 24 horas

foreach ($files as $file) {
    if (is_file($file)) {
        $age = $now - filemtime($file);
        if ($age > $maxAge) {
            unlink($file);
            echo "Eliminado: $file\n";
        }
    }
}
```

### Configurar Cron

```bash
# Ejecutar cada hora
0 * * * * php /path/to/camagru/scripts/cleanup_temp.php

# O cada día a las 3 AM
0 3 * * * php /path/to/camagru/scripts/cleanup_temp.php
```

## 📊 Gestión de Almacenamiento

### Verificar Espacio Disponible

```php
function getStorageInfo() {
    $uploadDir = __DIR__ . '/public/uploads/images/';
    
    // Espacio usado
    $files = glob($uploadDir . '*');
    $totalSize = 0;
    foreach ($files as $file) {
        $totalSize += filesize($file);
    }
    
    // Espacio disponible
    $diskFree = disk_free_space($uploadDir);
    $diskTotal = disk_total_space($uploadDir);
    
    return [
        'used' => $totalSize,
        'used_readable' => formatBytes($totalSize),
        'free' => $diskFree,
        'free_readable' => formatBytes($diskFree),
        'total' => $diskTotal,
        'total_readable' => formatBytes($diskTotal),
        'file_count' => count($files)
    ];
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}
```

### Límite de Uploads por Usuario

```php
class Image {
    public function canUpload($userId) {
        // Límite: 100 imágenes por usuario
        $sql = "SELECT COUNT(*) as count FROM images WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        
        return $result['count'] < 100;
    }
}
```

## 📺 Videos Educativos en Español

### Upload de Archivos
- [Upload de Archivos en PHP](https://www.youtube.com/watch?v=ZJUvMHolT5o) - Por FalconMasters
- [Validación de Uploads](https://www.youtube.com/watch?v=3JvEEML5E8c) - Por HolaMundo
- [Seguridad en File Uploads](https://www.youtube.com/watch?v=aEngebgM8vI) - Por Nate Gentile

### Procesamiento de Imágenes
- [PHP GD Library](https://www.youtube.com/watch?v=O-5d2KIXvX8) - Por Código Facilito
- [ImageMagick en PHP](https://www.youtube.com/watch?v=rX6tC5K_XaI) - Por Tech With Tim
- [Optimizar Imágenes](https://www.youtube.com/watch?v=F1kYBnY6mwg) - Por Google Developers

### Seguridad
- [File Upload Vulnerabilities](https://www.youtube.com/watch?v=CWeWD_1J9ew) - Por LiveOverflow
- [Prevenir Malicious Uploads](https://www.youtube.com/watch?v=CmF9sEyKZNo) - Por Hak5

### Cronjobs
- [Cron Jobs en Linux](https://www.youtube.com/watch?v=QZJ1drMQz1A) - Por Pelado Nerd
- [Automatizar Tareas con Cron](https://www.youtube.com/watch?v=t_d_aOVHH3k) - Por EDteam

## 🔗 Enlaces Relacionados

- [Volver a public/](../README.md)
- [Editor Controller](../../app/controllers/README.md)
- [Image Model](../../app/models/README.md)
- [Stickers](../stickers/README.md)

---

[⬆ Volver al README principal](../../README.md)
