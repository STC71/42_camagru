# 📸 Camagru - Plataforma de Compartición de Fotos

**Proyecto de 42 School** - Una aplicación web similar a Instagram que permite a los usuarios capturar, editar y compartir fotos con stickers y filtros.

![Camagru Banner](https://img.shields.io/badge/42-Camagru-00babc?style=for-the-badge&logo=42)
![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker)

---

## 📋 Tabla de Contenidos

- [Resumen del Proyecto](#-resumen-del-proyecto)
- [Características](#-características)
  - [Parte Obligatoria](#parte-obligatoria)
  - [Parte Bonus](#parte-bonus)
- [Stack Tecnológico](#️-stack-tecnológico)
- [Arquitectura](#-arquitectura)
- [Instalación](#-instalación)
  - [Prerrequisitos](#prerrequisitos)
  - [Inicio Rápido](#inicio-rápido)
  - [Configuración Manual](#configuración-manual)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Características de Seguridad](#-características-de-seguridad)
- [Uso](#-uso)
- [Bonus Implementados](#-bonus-implementados)
- [Testing](#-testing)
- [Compliance del Subject](#-compliance-del-subject)
- [Documentación Adicional](#-documentación-adicional)
- [Licencia](#-licencia)

---

## 🎯 Resumen del Proyecto

**Camagru** es una pequeña aplicación web inspirada en Instagram que permite a los usuarios:

- 📸 **Capturar fotos** usando su webcam
- 🎨 **Editar imágenes** añadiendo stickers y efectos
- 📁 **Subir archivos** desde su dispositivo
- 👀 **Ver galería pública** con likes y comentarios
- 🔐 **Sistema de autenticación** seguro con verificación de email
- 📧 **Notificaciones** por email para likes y comentarios
- 🎬 **Crear GIFs animados** (BONUS)

Este proyecto es parte del currículo de 42 School y se centra en desarrollo web con **PHP puro** (sin frameworks), **MySQL**, y **JavaScript vanilla**.

---

## ✨ Características

### Parte Obligatoria

#### 🔐 Sistema de Autenticación
- ✅ Registro de usuario con validación de formulario
- ✅ Verificación de email mediante enlace único
- ✅ Sistema de login/logout seguro
- ✅ Recuperación de contraseña con enlace por email
- ✅ Perfil de usuario editable
- ✅ Preferencias de notificaciones

#### 📷 Editor de Fotos
- ✅ Captura desde webcam usando la API `getUserMedia`
- ✅ Carga de archivo de imagen (JPG, PNG)
- ✅ Selección de stickers predefinidos
- ✅ Superposición de stickers sobre la foto base
- ✅ Procesamiento de imagen del lado del servidor usando PHP GD
- ✅ Soporte para canal alfa (transparencia)
- ✅ Gestión de tus propias fotos (visualizar, eliminar)

#### 🖼️ Galería Pública
- ✅ Visualización de todas las fotos públicas
- ✅ Paginación (5 fotos por página)
- ✅ Sistema de likes (un like por usuario por imagen)
- ✅ Sistema de comentarios (ilimitado)
- ✅ Notificaciones por email al autor cuando recibe likes/comentarios
- ✅ Vista ordenada por fecha de creación (más recientes primero)

#### 🔒 Seguridad
- ✅ Protección contra inyección SQL (PDO con sentencias preparadas)
- ✅ Protección contra XSS (htmlspecialchars en todas las salidas)
- ✅ Protección CSRF con tokens
- ✅ Validación de archivos (tipo, tamaño, contenido real)
- ✅ Hashing de contraseñas con BCrypt
- ✅ Validación de input en servidor y cliente
- ✅ Sanitización de datos de usuario

### Parte Bonus

#### 🌟 Características Avanzadas Implementadas

1. **📡 AJAXificación** (Intercambios con el servidor sin recargar)
   - ✅ Sistema de likes con actualización en tiempo real
   - ✅ Comentarios dinámicos sin recarga de página
   - ✅ Manejo de errores con notificaciones
   - ✅ Actualización de contadores en tiempo real

2. **👁️ Vista Previa en Vivo**
   - ✅ Overlay de sticker en tiempo real sobre el stream de la webcam
   - ✅ Drag & drop para posicionar stickers
   - ✅ Control de tamaño del sticker con slider
   - ✅ Soporte táctil para dispositivos móviles
   - ✅ Visualización del resultado antes de capturar

3. **♾️ Scroll Infinito**
   - ✅ Carga automática de más imágenes al hacer scroll
   - ✅ Implementado con Intersection Observer API (moderno y eficiente)
   - ✅ Indicador de carga
   - ✅ Mensaje cuando no hay más imágenes
   - ✅ Fallback a paginación tradicional si JS está deshabilitado

4. **🔗 Compartir en Redes Sociales**
   - ✅ Botones de compartir para Facebook, Twitter, WhatsApp
   - ✅ Copiar enlace al portapapeles
   - ✅ Modal con opciones de compartir
   - ✅ URLs optimizadas con metadatos

5. **🎬 Generación de GIF Animado**
   - ✅ Grabación de 30 frames a 100ms de intervalo
   - ✅ Aplicación automática del sticker seleccionado en cada frame
   - ✅ Soporte para posición y tamaño personalizados del sticker
   - ✅ Procesamiento del lado del servidor con ImageMagick
   - ✅ Indicador visual durante la grabación
   - ✅ Guardado automático en la galería

**Puntuación Estimada:** 125/100 ⭐

---

## 🛠️ Stack Tecnológico

### Backend
- **PHP 8.2** (lenguaje del lado del servidor)
- **MySQL 8.0** (base de datos relacional)
- **Apache 2.4** (servidor web)
- **PHP GD Library** (procesamiento de imágenes)
- **ImageMagick** (generación de GIFs - BONUS)
- **PHPMailer / Mailtrap** (envío de emails)

### Frontend
- **HTML5** (estructura semántica)
- **CSS3** (estilos y diseño responsive)
- **JavaScript (Vanilla)** (sin frameworks)
- **Canvas API** (manipulación de imágenes)
- **getUserMedia API** (acceso a la webcam)
- **Fetch API** (peticiones AJAX)
- **Intersection Observer API** (scroll infinito)

### DevOps
- **Docker** (containerización)
- **Docker Compose** (orquestación multi-contenedor)
- **Git** (control de versiones)

### Arquitectura
- **MVC** (Model-View-Controller personalizado)
- **PDO** (PHP Data Objects para abstracción de BD)
- **RESTful** (endpoints API para AJAX)

---

## 🏗️ Arquitectura

### Patrón MVC Personalizado

```
camagru/
├── app/
│   ├── controllers/      # Lógica de negocio
│   │   ├── AuthController.php
│   │   ├── EditorController.php
│   │   ├── GalleryController.php
│   │   ├── HomeController.php
│   │   └── UserController.php
│   ├── models/           # Modelos de datos
│   │   ├── User.php
│   │   ├── Image.php
│   │   ├── Like.php
│   │   └── Comment.php
│   ├── views/            # Templates HTML
│   │   ├── auth/
│   │   ├── editor/
│   │   ├── gallery/
│   │   ├── user/
│   │   └── partials/
│   └── core/             # Framework core
│       ├── Router.php
│       ├── Controller.php
│       ├── Database.php
│       ├── Mailer.php
│       ├── Validator.php
│       └── Config.php
├── public/               # Archivos públicos
│   ├── index.php         # Entry point
│   ├── css/
│   ├── js/
│   └── uploads/
├── database/             # Scripts SQL
│   └── init.sql
└── docker/               # Configuración Docker
    ├── docker-compose.yml
    ├── Dockerfile
    └── apache.conf
```

### Flujo de Solicitud

```
1. Cliente → public/index.php
2. Router → Analiza la URL
3. Controller → Procesa la lógica
4. Model → Interactúa con la BD
5. View → Renderiza HTML
6. Cliente ← Respuesta HTML/JSON
```

---

## 🚀 Instalación

### Prerrequisitos

- **Docker** (versión 20.10+)
- **Docker Compose** (versión 2.0+)
- **Git**

O para instalación manual:
- **PHP 8.2+**
- **MySQL 8.0+**
- **Apache 2.4+**
- **Extensiones PHP:** pdo, pdo_mysql, gd, mbstring, openssl
- **ImageMagick** (para GIFs)

### Inicio Rápido

```bash
# 1. Clonar el repositorio
git clone https://github.com/tuusuario/camagru.git
cd camagru

# 2. Configurar variables de entorno
cp .env.example .env
# Editar .env con tus credenciales

# 3. Construir e iniciar con Docker
docker-compose up --build -d

# 4. Acceder a la aplicación
# Web: http://localhost:8080
# PHPMyAdmin: http://localhost:8081
```

### Configuración Manual

<details>
<summary>Haz clic para ver la instalación manual</summary>

```bash
# 1. Instalar dependencias
sudo apt update
sudo apt install php8.2 php8.2-mysql php8.2-gd php8.2-mbstring
sudo apt install mysql-server apache2 imagemagick

# 2. Configurar base de datos
mysql -u root -p < database/init.sql

# 3. Configurar Apache
sudo cp docker/apache.conf /etc/apache2/sites-available/camagru.conf
sudo a2ensite camagru
sudo a2enmod rewrite
sudo systemctl restart apache2

# 4. Configurar permisos
sudo chown -R www-data:www-data public/uploads
sudo chmod -R 755 public/uploads

# 5. Configurar .env
cp .env.example .env
# Editar credenciales de BD y SMTP
```
</details>

Ver [QUICKSTART_ES.md](QUICKSTART_ES.md) para instrucciones detalladas.

---

## 📁 Estructura del Proyecto

```
camagru/
├── 📄 README.md                    # Este archivo
├── 📄 README_ES.md                 # Versión en español
├── 📄 QUICKSTART.md                # Guía de inicio rápido
├── 📄 COMPLIANCE.md                # Checklist del subject
├── 📄 SUMMARY.md                   # Resumen técnico
├── 📄 PROJECT_OVERVIEW.md          # Vista general
├── 📄 .env.example                 # Template de configuración
├── 📄 .gitignore                   # Archivos ignorados por Git
│
├── 📁 app/                         # Aplicación principal
│   ├── 📁 controllers/             # (5 archivos)
│   ├── 📁 models/                  # (4 archivos)
│   ├── 📁 views/                   # (15+ templates)
│   └── 📁 core/                    # (6 clases base)
│
├── 📁 public/                      # Directorio web raíz
│   ├── 📄 index.php                # Entry point
│   ├── 📄 .htaccess                # Reglas de reescritura
│   ├── 📁 css/                     # Estilos
│   │   └── style.css
│   ├── 📁 js/                      # Scripts
│   │   ├── gallery.js
│   │   └── editor.js
│   └── 📁 uploads/                 # Archivos subidos por usuarios
│       ├── images/
│       ├── stickers/
│       └── temp/
│
├── 📁 database/                    # Scripts de base de datos
│   └── 📄 init.sql                 # Schema + datos iniciales
│
└── 📁 docker/                      # Configuración de Docker
    ├── docker-compose.yml
    ├── Dockerfile
    └── apache.conf
```

**Total:** ~40 archivos | ~8,000 líneas de código

---

## 🔒 Características de Seguridad

### 1. Inyección SQL
```php
// ❌ MAL (vulnerable)
$query = "SELECT * FROM users WHERE email = '$email'";

// ✅ BIEN (seguro)
$query = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($query);
$stmt->execute(['email' => $email]);
```

### 2. Cross-Site Scripting (XSS)
```php
// En todas las vistas
<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
```

### 3. Cross-Site Request Forgery (CSRF)
```php
// Generar token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validar en cada POST
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token inválido');
}
```

### 4. Validación de Archivos
```php
// Validar tipo MIME real (no solo extensión)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $tmpName);
$allowed = ['image/jpeg', 'image/png'];

if (!in_array($mimeType, $allowed)) {
    throw new Exception('Tipo de archivo no permitido');
}
```

### 5. Hashing de Contraseñas
```php
// Nunca almacenar contraseñas en texto plano
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Verificar
if (password_verify($inputPassword, $storedHash)) {
    // Contraseña correcta
}
```

---

## 🎮 Uso

### 1. Registro e Inicio de Sesión

```
1. Visita http://localhost:8080/register
2. Completa el formulario de registro
3. Verifica tu email (revisa Mailtrap inbox)
4. Haz clic en el enlace de verificación
5. Inicia sesión en /login
```

### 2. Editar Fotos

```
1. Ve a http://localhost:8080/editor
2. Elige una opción:
   a) Webcam: Iniciar cámara → Seleccionar sticker → Capturar
   b) Upload: Arrastra archivo → Seleccionar sticker → Confirmar
3. Para GIF (BONUS): Iniciar cámara → Seleccionar sticker → Grabar GIF
4. Ver tus fotos en la sección "Mis Fotos Recientes"
```

### 3. Galería e Interacción

```
1. Visita http://localhost:8080/gallery
2. Navega por las fotos (scroll infinito BONUS)
3. Haz clic en ❤️ para dar like
4. Añade comentarios en las fotos
5. Comparte en redes sociales (BONUS)
```

### 4. Gestión de Perfil

```
1. Ve a http://localhost:8080/profile
2. Actualiza información personal
3. Cambia contraseña
4. Configura notificaciones por email
```

---

## 🌟 Bonus Implementados

### 1. AJAXificación (20 pts)

**Implementación:**
- **Archivo:** `public/js/gallery.js`
- **Endpoints:** `/gallery/like`, `/gallery/comment`
- **Características:**
  - Likes sin recarga de página
  - Actualización de contadores en tiempo real
  - Añadir comentarios dinámicamente
  - Sistema de notificaciones toast
  - Manejo de errores con feedback visual

**Código Clave:**
```javascript
// Ejemplo: Like con AJAX
async function toggleLike(imageId) {
    const response = await fetch(`/gallery/like`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `image_id=${imageId}&csrf_token=${token}`
    });
    const data = await response.json();
    // Actualizar UI sin recargar
    updateLikeButton(imageId, data.has_liked, data.like_count);
}
```

---

### 2. Vista Previa en Vivo (25 pts)

**Implementación:**
- **Archivo:** `public/js/editor.js`
- **APIs usadas:** Canvas API, getUserMedia
- **Características:**
  - Stream de webcam con overlay de sticker en tiempo real
  - Posicionamiento drag & drop (mouse + touch)
  - Control de tamaño con slider
  - 60 FPS usando `requestAnimationFrame`

**Código Clave:**
```javascript
function drawLivePreview() {
    const ctx = canvas.getContext('2d');
    // Dibujar video frame
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    // Superponer sticker
    if (selectedSticker) {
        ctx.drawImage(stickerImg, stickerX, stickerY, stickerWidth, stickerHeight);
    }
    requestAnimationFrame(drawLivePreview);
}
```

---

### 3. Scroll Infinito (15 pts)

**Implementación:**
- **Archivo:** `public/js/gallery.js`
- **API usada:** Intersection Observer
- **Endpoint:** `/gallery/loadMore?page=N`
- **Características:**
  - Detección automática del final de la página
  - Carga de 5 imágenes adicionales
  - Indicador de carga
  - Fallback a paginación tradicional

**Código Clave:**
```javascript
const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !loading && hasMore) {
        loadMoreImages();
    }
}, { rootMargin: '100px' });

observer.observe(document.querySelector('.gallery-sentinel'));
```

---

### 4. Compartir en Redes Sociales (10 pts)

**Implementación:**
- **Archivo:** `public/js/gallery.js`
- **Redes:** Facebook, Twitter, WhatsApp, Clipboard
- **Características:**
  - Modal con opciones de compartir
  - URLs parametrizadas para cada red social
  - Copy to clipboard con API nativa
  - Feedback visual

**URLs de Compartir:**
```javascript
const shareUrls = {
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
    twitter: `https://twitter.com/intent/tweet?url=${url}&text=${text}`,
    whatsapp: `https://wa.me/?text=${text}%20${url}`
};
```

---

### 5. Generación de GIF Animado (30 pts)

**Implementación:**
- **Archivos:** `public/js/editor.js`, `app/controllers/EditorController.php`
- **Librería:** ImageMagick (PHP Imagick extension)
- **Características:**
  - Captura de 30 frames a 100ms de intervalo
  - Aplicación de sticker en cada frame
  - Procesamiento del lado del servidor
  - Loop infinito a 10 FPS
  - Guardado automático en galería

**Flujo:**
```
1. JS: Capturar 30 frames del canvas → Base64 array
2. JS: Enviar frames + sticker info vía AJAX
3. PHP: Decodificar frames y crear imágenes temporales
4. PHP: Aplicar sticker a cada frame
5. PHP: Usar ImageMagick para ensamblar GIF
6. PHP: Guardar GIF y limpiar archivos temporales
7. JS: Mostrar notificación de éxito
```

**Código Backend:**
```php
// EditorController::createGif()
$animation = new Imagick();
$animation->setFormat('gif');

foreach ($processedFrames as $frame) {
    $image = new Imagick($frame);
    $image->setImageDelay(10); // 100ms
    $animation->addImage($image);
}

$animation->setImageIterations(0); // Loop infinito
$animation->writeImages($gifPath, true);
```

---

## 🧪 Testing

### Suite Automatizada de Pruebas

El proyecto incluye un script completo de testing automatizado que verifica:

- ✅ Entorno (Docker, Docker Compose, configuración)
- ✅ Contenedores y servicios
- ✅ Base de datos y conectividad
- ✅ Endpoints HTTP y recursos estáticos
- ✅ Seguridad básica (SQL injection, XSS, protección .env)
- ✅ Rendimiento y uso de recursos
- ✅ Funcionalidades bonus

```bash
# Ejecutar suite completa de tests
./test_auto.sh

# O usando make
make test-full

# Tests básicos rápidos
make test
```

**Salida del Script:**
- 🎨 Interfaz colorida con descripción de cada test
- 📊 Contadores de éxitos/fallos/omitidos
- 📈 Tasa de éxito porcentual
- 🚦 Exit codes: 0 (perfecto), 1 (advertencias), 2 (crítico)

### Pruebas Manuales

#### Seguridad
```bash
# 1. Probar inyección SQL
Username: admin' OR '1'='1
Password: cualquiera
# Resultado esperado: Login fallido

# 2. Probar XSS
Comentario: <script>alert('XSS')</script>
# Resultado esperado: El script se muestra como texto

# 3. Probar CSRF
curl -X POST http://localhost:8080/gallery/like \
  -d "image_id=1" \
  --cookie "session=..."
# Resultado esperado: Error 403 (sin CSRF token)
```

#### Validación de Archivos
```bash
# 1. Subir archivo PHP disfrazado de imagen
mv malicious.php malicious.jpg
# Resultado esperado: Rechazo por tipo MIME inválido

# 2. Subir imagen > 5MB
# Resultado esperado: Error de tamaño excedido
```

#### Funcionalidad AJAX
```bash
# 1. Deshabilitar JavaScript en el navegador
# 2. Intentar dar like
# Resultado esperado: Fallback a formulario POST tradicional

# 3. Habilitar JavaScript
# 4. Dar like
# Resultado esperado: Actualización sin recarga
```

---

## ✅ Compliance del Subject

Ver [COMPLIANCE.md](COMPLIANCE.md) para el checklist completo.

### Resumen

| Categoría | Ítems | Estado |
|-----------|-------|--------|
| Autenticación | 6/6 | ✅ 100% |
| Editor de Fotos | 7/7 | ✅ 100% |
| Galería | 5/5 | ✅ 100% |
| Seguridad | 7/7 | ✅ 100% |
| Bonus | 5/5 | ✅ 100% |
| **TOTAL** | **30/30** | **✅ 100%** |

---

## 📚 Documentación Adicional

- [QUICKSTART.md](QUICKSTART.md) - Guía de instalación paso a paso
- [COMPLIANCE.md](COMPLIANCE.md) - Checklist detallado del subject
- [SUMMARY.md](SUMMARY.md) - Resumen técnico ejecutivo
- [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) - Vista general de arquitectura

### Diagramas

#### Flujo de Autenticación
```
Usuario → Registro → Email Verificación → Login → Sesión
                         ↓
                  Token en BD (48h)
                         ↓
                  Click en Email
                         ↓
                  Cuenta Activada
```

#### Flujo de Creación de Foto
```
Usuario → Editor → [Webcam | Upload]
                        ↓
                  Seleccionar Sticker
                        ↓
                  Posicionar (Drag & Drop)
                        ↓
                  [Capturar | Grabar GIF]
                        ↓
                  Procesar en Servidor
                        ↓
                  Guardar en BD + Filesystem
                        ↓
                  Mostrar en Galería
```

---

## 🐛 Problemas Conocidos

### Limitaciones

1. **GIF Generation**
   - Requiere ImageMagick instalado en el servidor
   - No funciona solo con PHP GD
   - Tamaño de archivo puede ser grande (optimización futura)

2. **Browser Compatibility**
   - `getUserMedia` no funciona en HTTP (solo HTTPS o localhost)
   - Intersection Observer no soportado en IE11
   - Drag & Drop requiere eventos táctiles para móviles

3. **Performance**
   - Galería no optimizada para >10,000 imágenes
   - GIF processing puede ser lento para 30 frames

### Soluciones

```bash
# Instalar ImageMagick en Docker
RUN apt-get update && apt-get install -y imagemagick php8.2-imagick

# Habilitar HTTPS para producción
# Ver docker/apache.conf para configuración SSL
```

---

## 🤝 Contribución

Este es un proyecto educativo de 42 School. No se aceptan contribuciones externas, pero puedes:

1. **Fork** el repositorio
2. **Crear** tu propia versión
3. **Compartir** tus mejoras

### Directrices

- Seguir el subject de 42 (no frameworks)
- Mantener código limpio y comentado
- Probar todas las características de seguridad
- Documentar cambios importantes

---

### Issues

Si encuentras un bug:
1. Verifica que Docker esté funcionando
2. Revisa logs: `docker-compose logs`
3. Comprueba `.env` configuración
4. Consulta [QUICKSTART.md](QUICKSTART.md)

---

## 👨‍💻 Autor

**sternero**
- Email: sternero@student.42malaga.com
- GitHub: [@STC71](https://github.com/STC71)
- 42 Intra: `sternero`

---

## 📜 Licencia

Este proyecto es código abierto bajo la licencia MIT.

```
MIT License

Copyright (c) 2026 sternero

Se concede permiso, de forma gratuita, a cualquier persona que obtenga una copia
de este software y archivos de documentación asociados (el "Software"), para
utilizar el Software sin restricciones...
```

Ver [LICENSE](LICENSE) para el texto completo.

---

## 🎓 Agradecimientos

- **42 School** por el excelente proyecto
- **Peer evaluators** por el feedback constructivo
- **Stack Overflow** por resolver dudas técnicas
- **MDN Web Docs** por la documentación de APIs

---

## 📈 Estadísticas del Proyecto

- 📅 **Fecha de Inicio:** [07/01/2026]
- ⏱️ **Tiempo de Desarrollo:** ~80 horas
- 📝 **Líneas de Código:** ~8,000
- 📁 **Archivos Creados:** ~40
- ☕ **Cafés Consumidos:** ∞

---

## � Videos Educativos en Español

### Desarrollo Web con PHP
- [PHP desde CERO - Curso Completo 2024](https://www.youtube.com/watch?v=ubD6CpC0WDs) - Por HolaMundo (8h)
- [Curso de PHP y MySQL - Conceptos Fundamentales](https://www.youtube.com/watch?v=MPYJGeJ0kJI) - Por FaztCode
- [Arquitectura MVC en PHP - Explicación Completa](https://www.youtube.com/watch?v=5s7Nn5dxC0E) - Por Código Facilito

### Seguridad Web
- [Seguridad en Aplicaciones Web - Top 10 OWASP](https://www.youtube.com/watch?v=aEngebgM8vI) - Por Nate Gentile
- [SQL Injection y Cómo Prevenirla](https://www.youtube.com/watch?v=ciNHn38EyRc) - Por Pelado Nerd
- [XSS y CSRF - Explicación con Ejemplos](https://www.youtube.com/watch?v=_Z0ID3lCVZQ) - Por MoureDev

### JavaScript y APIs Web
- [JavaScript Moderno - ES6+](https://www.youtube.com/watch?v=Z4TuS0HEJP8) - Por MiduDev
- [API Fetch en JavaScript - Tutorial Completo](https://www.youtube.com/watch?v=cuEtnrL9-H0) - Por FalconMasters
- [WebCam API - getUserMedia Tutorial](https://www.youtube.com/watch?v=wXtlrMjRx3k) - Por Coding Shiksha

### Base de Datos MySQL
- [MySQL desde CERO - Tutorial Completo](https://www.youtube.com/watch?v=uUdKAYl-F7g) - Por Hola Mundo
- [Diseño de Base de Datos - Normalización](https://www.youtube.com/watch?v=oUdKH0zUdJI) - Por Freddy Vega

### Docker y DevOps
- [Docker de Principiante a Experto](https://www.youtube.com/watch?v=CV_Uf3Dq-EU) - Por Pelado Nerd
- [Docker Compose - Tutorial Completo](https://www.youtube.com/watch?v=7kja9tCd9bU) - Por Fazt Code

### Git y GitHub
- [Git y GitHub desde CERO](https://www.youtube.com/watch?v=mBYSUUnMt9M) - Por MoureDev (4h)
- [Git Flow - Flujo de Trabajo Profesional](https://www.youtube.com/watch?v=1SXpE08hvGs) - Por EDteam

---

## �🚀 Próximas Mejoras

Ideas para extender el proyecto (no requerido por el subject):

- [ ] Sistema de followers/following
- [ ] Chat privado entre usuarios
- [ ] Filtros de imagen avanzados (blur, sepia, etc.)
- [ ] Biblioteca de stickers con categorías
- [ ] Moderación de contenido
- [ ] API REST completa
- [ ] Aplicación móvil (React Native)
- [ ] Almacenamiento en la nube (AWS S3)
- [ ] CDN para imágenes
- [ ] Machine Learning para detección de objetos

---

<div align="center">

## ⭐ Si este proyecto te ayudó, dale una estrella!

**Hecho con ❤️ por sternero estudiante de 42 Málaga**

[⬆ Volver arriba](#-camagru---plataforma-de-compartición-de-fotos)

</div>
