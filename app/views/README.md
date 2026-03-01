# 🎨 Directorio `views/` - Vistas y Templates

## 🎯 ¿Qué son y para qué sirven?

Las vistas son las **páginas que ve el usuario**. Son archivos PHP que contienen HTML con pequeños trozos de código PHP para mostrar datos dinámicos.

**¿Por qué existen?** Para separar cómo se VEN las cosas de cómo FUNCIONAN. Los diseñadores pueden trabajar en las vistas sin tocar la lógica.

**¿Para qué sirven?**
- Mostrar formularios (login, registro)
- Presentar información (galería de fotos, perfil)
- Renderizar el HTML que verá el usuario
- Aplicar estilos CSS
- Añadir interactividad con JavaScript

**Regla de oro:** Las vistas SOLO muestran información, NUNCA hacen cálculos complejos ni hablan con la base de datos.

## 📋 Estructura

```
views/
├── auth/          # Vistas de autenticación
│   ├── login.php
│   ├── register.php
│   ├── forgot-password.php
│   └── reset-password.php
├── editor/        # Editor de fotos
│   └── index.php
├── gallery/       # Galería pública
│   └── index.php
├── home/          # Página de inicio
│   └── index.php
├── user/          # Perfil y configuración
│   ├── profile.php
│   ├── settings.php
│   └── my-photos.php
└── partials/      # Componentes reutilizables
    ├── header.php
    └── footer.php
```

## 🎯 ¿Qué es una Vista?

Una **vista** es:
- ✅ Un archivo PHP que contiene HTML con datos dinámicos
- ✅ La capa de presentación (lo que ve el usuario)
- ✅ Responsable SOLO de mostrar datos, no de procesarlos
- ✅ Puede incluir CSS y JavaScript
- ✅ Usa datos pasados desde el controlador

### Separación de Responsabilidades

```
┌──────────────────────────────────────┐
│         Controller                   │
│  • Procesa lógica de negocio         │
│  • Consulta modelos                  │
│  • Prepara datos para la vista       │
└────────────┬─────────────────────────┘
             │
             v
┌──────────────────────────────────────┐
│            View                      │
│  • Recibe datos del controlador      │
│  • Renderiza HTML                    │
│  • Aplica estilos CSS                │
│  • Añade interactividad (JS)         │
│  • NO consulta la base de datos      │
└──────────────────────────────────────┘
```

## 🔧 Estructura de una Vista

### Ejemplo: gallery/index.php

```php
<?php require_once 'app/views/partials/header.php'; ?>

<div class="gallery-container">
    <h1>Galería Pública</h1>
    
    <?php if (empty($images)): ?>
        <p>No hay imágenes todavía.</p>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($images as $image): ?>
                <div class="gallery-item">
                    <img src="/uploads/images/<?= htmlspecialchars($image['filename']) ?>" 
                         alt="Foto de <?= htmlspecialchars($image['username']) ?>">
                    
                    <div class="image-info">
                        <p class="author">Por: <?= htmlspecialchars($image['username']) ?></p>
                        <p class="date"><?= date('d/m/Y', strtotime($image['created_at'])) ?></p>
                        
                        <div class="social-actions">
                            <button class="like-btn" data-image-id="<?= $image['id'] ?>">
                                ❤️ <span class="likes-count"><?= $image['likes_count'] ?></span>
                            </button>
                            
                            <button class="comment-btn">
                                💬 <span class="comments-count"><?= $image['comments_count'] ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="/gallery?page=<?= $i ?>" 
                       class="<?= $i === $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'app/views/partials/footer.php'; ?>
```

## 🛡️ Seguridad en Vistas

### 1. Escapar TODAS las Salidas (XSS)
```php
<!-- ❌ MAL - Vulnerable a XSS -->
<p>Bienvenido <?= $username ?></p>

<!-- ✅ BIEN - Escapado -->
<p>Bienvenido <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></p>
```

### 2. Tokens CSRF en Formularios
```php
<form method="POST" action="/editor/upload">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="file" name="photo" accept="image/*">
    <button type="submit">Subir</button>
</form>
```

### 3. Validación de Datos del Usuario
```php
<!-- Mostrar errores de validación -->
<?php if (!empty($errors)): ?>
    <div class="error-message">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
```

## 📂 Contenido de Subcarpetas

### [auth/](auth/) - Autenticación
Vistas relacionadas con el sistema de autenticación:
- `login.php` - Formulario de inicio de sesión
- `register.php` - Formulario de registro
- `forgot-password.php` - Solicitud de recuperación
- `reset-password.php` - Cambio de contraseña

### [editor/](editor/) - Editor de Fotos
Vista del editor de fotos con webcam:
- `index.php` - Interfaz del editor con webcam, stickers y controles

### [gallery/](gallery/) - Galería
Vista de la galería pública:
- `index.php` - Grid de imágenes con likes, comentarios y paginación

### [home/](home/) - Inicio
Página de bienvenida:
- `index.php` - Landing page del proyecto

### [user/](user/) - Usuario
Vistas del perfil de usuario:
- `profile.php` - Perfil público (en desarrollo)
- `settings.php` - Configuración de cuenta (en desarrollo)
- `my-photos.php` - Gestión de mis fotos (en desarrollo)

### [partials/](partials/) - Componentes
Componentes reutilizables:
- `header.php` - Cabecera con navegación
- `footer.php` - Pie de página

## 🎨 Buenas Prácticas en Vistas

### 1. Mantener la Lógica Mínima
```php
<!-- ✅ BIEN - Lógica simple de presentación -->
<?php if ($isAdmin): ?>
    <button>Panel Admin</button>
<?php endif; ?>

<!-- ❌ MAL - Lógica de negocio en vista -->
<?php 
$user = $db->query("SELECT * FROM users WHERE id = $id");
if ($user['role'] === 'admin' && $user['verified']) {
    // ...
}
?>
```

### 2. Usar Partials para Reutilización
```php
<!-- header.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Camagru' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav>
        <!-- Navegación -->
    </nav>

<!-- footer.php -->
    <footer>
        <p>&copy; 2026 Camagru - Proyecto de 42 School</p>
    </footer>
    <script src="/js/main.js"></script>
</body>
</html>
```

### 3. Separar CSS y JavaScript
```php
<!-- ✅ BIEN - Archivos separados -->
<link rel="stylesheet" href="/css/gallery.css">
<script src="/js/gallery.js"></script>

<!-- ❌ MAL - CSS/JS inline (difícil de mantener) -->
<style>
    .gallery { display: grid; }
</style>
<script>
    function likePhoto() { /* ... */ }
</script>
```

### 4. Nombres Descriptivos para Variables
```php
<!-- ✅ BIEN - Nombres claros -->
<?php foreach ($recentImages as $image): ?>
    <img src="<?= $image['thumbnail'] ?>" alt="<?= $image['description'] ?>">
<?php endforeach; ?>

<!-- ❌ MAL - Nombres confusos -->
<?php foreach ($arr as $x): ?>
    <img src="<?= $x['t'] ?>" alt="<?= $x['d'] ?>">
<?php endforeach; ?>
```

## 📺 Videos Educativos en Español

### HTML y Estructura
- [HTML5 desde Cero - Curso Completo](https://www.youtube.com/watch?v=MJkdaVFHrto) - Por MoureDev
- [Estructura Semántica en HTML5](https://www.youtube.com/watch?v=6LMEu-XDFqE) - Por FalconMasters
- [Formularios HTML - Guía Completa](https://www.youtube.com/watch?v=ua0Z3nHoJDY) - Por Píldoras Informáticas

### CSS y Diseño
- [CSS Grid Layout - Tutorial Completo](https://www.youtube.com/watch?v=QBOUSrMqlSQ) - Por FalconMasters
- [Flexbox en 15 Minutos](https://www.youtube.com/watch?v=JX2FTvE4yCI) - Por MoureDev
- [CSS Responsive Design](https://www.youtube.com/watch?v=2KL-z9A56SQ) - Por FaztCode

### PHP en Vistas
- [Templates en PHP](https://www.youtube.com/watch?v=v0cpNQ0F7Nw) - Por Código Facilito
- [Include y Require en PHP](https://www.youtube.com/watch?v=NUvj4Fzg5j4) - Por HolaMundo
- [Alternativa Syntax en PHP](https://www.youtube.com/watch?v=LXKfW4VLSWg) - Por CodigoMasters

### Seguridad Frontend
- [XSS - Cross Site Scripting Explicado](https://www.youtube.com/watch?v=_Z0ID3lCVZQ) - Por MoureDev
- [Validación de Formularios](https://www.youtube.com/watch?v=3rGpPUl_Ztg) - Por Píldoras Informáticas
- [CSRF Protection](https://www.youtube.com/watch?v=m0EHlfTgGUU) - Por Codigo Facilito

### Accesibilidad
- [Accesibilidad Web - ARIA y Semántica](https://www.youtube.com/watch?v=VwkP0uJEoA4) - Por EDteam
- [Buenas Prácticas de Accesibilidad](https://www.youtube.com/watch?v=iww-RvBDMIw) - Por Google Developers

## 🔗 Enlaces Relacionados

- [Volver a app/](../README.md)
- [Controladores](../controllers/README.md)
- [CSS](../../public/css/README.md)
- [JavaScript](../../public/js/README.md)

---

[⬆ Volver al README principal](../../README.md)
