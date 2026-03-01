# 🌐 Directorio `public/` - Archivos Públicos

## 🎯 ¿Qué es y para qué sirve?

Este es el **único directorio accesible desde el navegador**. Es la "puerta de entrada" de la aplicación web.

**¿Por qué existe?** Por seguridad. Solo lo que está en public/ puede verse desde internet. El resto (app/, config/, database/) está protegido.

**¿Para qué sirve?**
- **index.php** - Punto de entrada único de toda la aplicación
- **css/** - Estilos que hacen bonita la aplicación
- **js/** - JavaScript que añade interactividad (likes sin recargar, scroll infinito, webcam)
- **stickers/** - Imágenes PNG predefinidas para superponer en fotos
- **uploads/** - Fotos y GIFs creados por los usuarios
- **.htaccess** - Convierte URLs feas en URLs bonitas (`/gallery/42` en lugar de `/index.php?url=gallery&id=42`)

## 📋 Estructura

```
public/
├── index.php        # ENTRADA ÚNICA - Todo pasa por aquí
├── .htaccess        # Configuración de Apache (URLs bonitas)
├── css/             # Estilos visuales
├── js/              # Interactividad del navegador
├── stickers/        # Stickers predefinidos para fotos
└── uploads/         # Contenido generado por usuarios
```

## 🚪 index.php - El Portero

**¿Qué hace?** Es el ÚNICO archivo PHP al que accede el usuario directamente. Recibe TODAS las peticiones.

**¿Cómo funciona?**

```php
<?php
// 1. Iniciar sesión
session_start();

// 2. Cargar configuración
require_once __DIR__ . '/../config/Config.php';

// 3. Autoload de clases
spl_autoload_register(function($class) {
    $directories = ['controllers', 'models', 'core'];
    foreach ($directories as $dir) {
        $file = __DIR__ . "/../app/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// 4. Enrutamiento
$router = new Router();
$url = $_GET['url'] ?? '';
$router->route($url);
```

### Ventajas del Single Entry Point
- ✅ Control centralizado
- ✅ Seguridad mejorada
- ✅ URLs limpias
- ✅ Fácil mantenimiento
- ✅ Middleware aplicable a todas las rutas

## ⚙️ .htaccess - Reescritura de URLs

### ¿Para qué sirve?

Convierte URLs feas en URLs limpias:

```
# Antes (sin .htaccess)
/index.php?url=gallery/show&id=42

# Después (con .htaccess)
/gallery/show/42
```

### Contenido típico
```apache
RewriteEngine On

# No reescribir archivos existentes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Redirigir todo a index.php
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Prevenir listado de directorios
Options -Indexes

# Proteger archivos sensibles
<FilesMatch "\.(env|sql|md)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

## 🔒 Seguridad en public/

### 1. Estructura de Directorios
```
✅ BIEN - Código fuera de public/
project/
├── public/           ← Document Root
│   └── index.php
├── app/              ← NO accesible desde web
├── config/           ← NO accesible desde web
└── database/         ← NO accesible desde web

❌ MAL - Todo en public/
public/
├── index.php
├── controllers/      ← Accesible (peligroso)
├── models/           ← Accesible (peligroso)
└── config/           ← Accesible (GRAVE)
```

### 2. Permisos de Archivos
```bash
# Archivos PHP (lectura/ejecución)
chmod 644 public/index.php

# Directorios
chmod 755 public/

# Uploads (escritura)
chmod 755 public/uploads/
chmod 755 public/uploads/images/
chmod 755 public/uploads/temp/

# Prevenir ejecución en uploads
chmod 644 public/uploads/images/*
```

### 3. Protección contra Uploads Maliciosos
```apache
# En public/uploads/.htaccess
# Denegar ejecución de PHP
<FilesMatch "\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Solo permitir imágenes
<FilesMatch "\.(jpg|jpeg|png|gif)$">
    Order allow,deny
    Allow from all
</FilesMatch>
```

### 4. Headers de Seguridad
```php
// En index.php o bootstrap
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

## 📂 Subcarpetas

### [css/](css/) - Hojas de Estilo
Contiene todos los archivos CSS de la aplicación:
- `style.css` - Estilos principales

### [js/](js/) - JavaScript
Scripts del lado del cliente:
- `main.js` - Funcionalidad general
- `gallery.js` - Lógica de la galería (likes, comentarios, scroll infinito)
- `editor.js` - Lógica del editor (webcam, stickers, GIF)

### [stickers/](stickers/) - Stickers
Imágenes PNG con transparencia para superponer sobre fotos:
- Predefinidos por la aplicación
- Solo lectura (no modificables por usuarios)

### [uploads/](uploads/) - Archivos de Usuario
Contenido generado por usuarios:
- `images/` - Fotos y GIFs guardados
- `temp/` - Archivos temporales durante procesamiento

## 🚀 Configuración del Servidor

### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName camagru.local
    DocumentRoot /path/to/camagru/public
    
    <Directory /path/to/camagru/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/camagru_error.log
    CustomLog ${APACHE_LOG_DIR}/camagru_access.log combined
</VirtualHost>
```

### Nginx Equivalent
```nginx
server {
    listen 80;
    server_name camagru.local;
    root /path/to/camagru/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    location ~ /uploads/.*\.php$ {
        deny all;
    }
}
```

## 📺 Videos Educativos en Español

### Apache y Configuración
- [Apache desde Cero](https://www.youtube.com/watch?v=a7wOW9Rq3sk) - Por EDteam
- [.htaccess - Tutorial Completo](https://www.youtube.com/watch?v=aOMdJ3V1oDw) - Por Píldoras Informáticas
- [URL Rewriting con Apache](https://www.youtube.com/watch?v=m4PaE_10shE) - Por HolaMundo

### Estructura de Proyectos
- [Estructura de Carpetas en PHP](https://www.youtube.com/watch?v=6Z7M1qMq3Fo) - Por HolaMundo
- [Document Root y Public Folder](https://www.youtube.com/watch?v=YEjpKa-K1ps) - Por Código Facilito

### Seguridad
- [Seguridad en Uploads de Archivos](https://www.youtube.com/watch?v=aEngebgM8vI) - Por Nate Gentile
- [Permisos de Archivos en Linux](https://www.youtube.com/watch?v=ngJG6Ix5FR4) - Por Pelado Nerd
- [HTTP Security Headers](https://www.youtube.com/watch?v=MYm6T_-xEXc) - Por Web Dev Simplified

### Virtual Hosts
- [Virtual Hosts en Apache](https://www.youtube.com/watch?v=JEXiYKWvuKw) - Por CodigoFacilito
- [Nginx Configuration](https://www.youtube.com/watch?v=7VAI73roXaY) - Por Pelado Nerd

## 🔗 Enlaces Relacionados

- [CSS](css/README.md)
- [JavaScript](js/README.md)
- [Stickers](stickers/README.md)
- [Uploads](uploads/README.md)

---

[⬆ Volver al README principal](../README.md)
