# ✅ COMPLIANCE – Camagru (Objetivo 125 %)

Documento de cumplimiento respecto al subject oficial y a la hoja de evaluación de [42](https://www.42network.org/) – Campus [42 Málaga](https://www.42malaga.com/).

[![42](https://img.shields.io/badge/42-Málaga-00babc?style=for-the-badge&logo=42)](https://www.42malaga.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker)](https://docs.docker.com/)

---

## 1. Preflight Checks (Obligatorios)

| Requisito | Estado | Evidencia |
|-----------|--------|-----------|
| PHP puro (sin frameworks / micro-frameworks) | ✅ | Sin Composer, sin Laravel, etc. |
| `index.php` en la raíz pública | ✅ | `public/index.php` |
| `config/database.php` | ✅ | Presente |
| `config/setup.php` | ✅ | Presente |
| PDO con `PDO::ERRMODE_EXCEPTION` | ✅ | `app/core/Database.php` |
| Containerización actualizada | ✅ | Dockerfile + docker-compose.yml |
| Funciona “as is” tras `docker-compose up --build` | ✅ | Incluye ImageMagick + imagick |

---

## 2. Parte Obligatoria

### 2.1 Usuario (creación y autenticación)

| Requisito | Estado |
|-----------|--------|
| Registro con email + username + password | ✅ |
| Validación de contraseña (mín. 8 + letras + números) | ✅ |
| Email de verificación con token único | ✅ |
| No se puede loguear sin verificar | ✅ |
| Login / Logout | ✅ |
| Recuperación de contraseña por email | ✅ |
| Preferencias de notificación de comentarios | ✅ |

### 2.2 Editor (ft_snapchat)

| Requisito | Estado |
|-----------|--------|
| Webcam con `getUserMedia` | ✅ |
| Selección de stickers | ✅ |
| Superposición **server-side** (GD) | ✅ |
| Soporte canal alfa | ✅ |
| Subida de archivo como alternativa | ✅ |
| Botón de captura deshabilitado sin sticker | ✅ |
| Gestión de fotos propias (borrar) | ✅ |

### 2.3 Galería (ft_instagram)

| Requisito | Estado |
|-----------|--------|
| Galería pública de todas las fotos | ✅ |
| Paginación (mínimo 5 por página) | ✅ |
| Likes (un like por usuario) | ✅ |
| Comentarios | ✅ |
| Notificación por email al autor (respetando preferencia) | ✅ |

### 2.4 Seguridad

| Requisito | Estado | Cómo se cumple |
|-----------|--------|----------------|
| Contraseñas cifradas | ✅ | `password_hash` / `password_verify` |
| Protección XSS | ✅ | `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` |
| Protección SQL Injection | ✅ | PDO prepared statements |
| CSRF | ✅ | Token en sesión + `hash_equals` |
| Validación de archivos | ✅ | MIME real + tamaño máximo |

### 2.5 UI / UX / Compatibilidad

| Requisito | Estado |
|-----------|--------|
| Diseño usable y limpio | ✅ |
| Responsive / móvil | ✅ |
| Compatible Firefox ≥ 41 y Chrome ≥ 46 | ✅ |
| Sin errores en consola / logs del servidor | ✅ |

---

## 3. Parte Bonus

| Bonus | Estado | Notas |
|-------|--------|-------|
| AJAX (likes + comentarios sin recarga) | ✅ | `GalleryController::like` / `comment` |
| Live preview + drag del sticker | ✅ | Canvas + eventos de ratón/táctiles |
| Infinite scroll | ✅ | `GalleryController::loadMore` + Intersection Observer |
| GIF animado | ✅ | Imagick instalado en el Dockerfile + fallback `convert` |

---

## 4. Mejoras de calidad realizadas

- Código limpio y comentarios didácticos en controladores, modelos y core
- Rutas absolutas basadas en `__DIR__` (robustas en Docker)
- ImageMagick + extensión `imagick` instaladas en el contenedor
- Documentación actualizada y ampliada (`docs/`)
- Scripts de configuración y test revisados

---

## 5. Cómo demostrar el 125 % en la defensa

1. `docker-compose up --build -d` → todo funciona out-of-the-box
2. Registro → verificación de email → login
3. Editor: live preview + drag del sticker + captura (botón deshabilitado sin sticker)
4. Subida de archivo con y sin sticker
5. Galería: likes, comentarios, notificaciones (activar/desactivar)
6. Scroll infinito
7. Creación de GIF animado
8. Pruebas de seguridad (XSS, SQLi, contraseñas hasheadas)

---


---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
