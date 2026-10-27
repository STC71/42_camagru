# 📸 Camagru – Plataforma de Compartición de Fotos

**Proyecto implementado en [42 Málaga](https://www.42malaga.com/)** — Aplicación web tipo Instagram que permite capturar, editar y compartir fotos con stickers, likes, comentarios y GIFs animados.

[![42](https://img.shields.io/badge/42-Málaga-00babc?style=for-the-badge&logo=42)](https://www.42malaga.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/doc/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docs.docker.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)

---

## 📋 Tabla de Contenidos

- [Resumen del Proyecto](#-resumen-del-proyecto)
- [Características](#-características)
- [Stack Tecnológico](#️-stack-tecnológico)
- [Arquitectura](#-arquitectura)
- [Instalación](#-instalación)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Características de Seguridad](#-características-de-seguridad)
- [Bonus Implementados](#-bonus-implementados)
- [Testing](#-testing)
- [Documentación Adicional](#-documentación-adicional)
- [Licencia](#-licencia)

---

## 🎯 Resumen del Proyecto

**Camagru** es una pequeña aplicación web inspirada en Instagram que permite a los usuarios:

- 📸 **Capturar fotos** usando su webcam
- 🎨 **Editar imágenes** añadiendo stickers con transparencia
- 📁 **Subir archivos** desde su dispositivo
- 👀 **Ver galería pública** con likes y comentarios
- 🔐 **Sistema de autenticación** seguro con verificación de email
- 📧 **Notificaciones** por email cuando alguien comenta
- 🎬 **Crear GIFs animados** (bonus, funcional out-of-the-box)

Este proyecto forma parte del currículo de [42 School](https://www.42network.org/) y se centra en desarrollo web con **PHP puro** (sin frameworks), **MySQL** y **JavaScript vanilla**.

Campus: **[42 Málaga](https://www.42malaga.com/)**.

---

## ✨ Características

### Parte Obligatoria

#### 🔐 Sistema de Autenticación
- Registro con validación estricta de contraseñas
- Verificación de email mediante enlace único
- Login / Logout seguros
- Recuperación de contraseña por email
- Perfil editable y preferencias de notificación

#### 📷 Editor de Fotos
- Captura desde webcam (`getUserMedia`)
- Subida de archivos (JPG / PNG)
- Stickers con canal alfa (transparencia)
- Superposición **server-side** con PHP GD
- Gestión de fotos propias (ver / eliminar)

#### 🖼️ Galería Pública
- Paginación (5 fotos por página)
- Sistema de likes (un like por usuario por imagen)
- Sistema de comentarios
- Notificaciones por email al autor (respetando preferencias)
- Ordenación por fecha (más recientes primero)

#### 🔒 Seguridad
- PDO + sentencias preparadas (protección SQLi)
- `htmlspecialchars` en todas las salidas (protección XSS)
- Tokens CSRF
- Hashing de contraseñas con `password_hash` (bcrypt)
- Validación real de tipo MIME y tamaño de archivos

### Parte Bonus

| Bonus | Estado |
|-------|--------|
| AJAXificación (likes & comentarios sin recarga) | ✅ Completo |
| Vista previa en vivo + drag del sticker | ✅ Completo |
| Scroll infinito | ✅ Completo |
| GIFs animados (ImageMagick / Imagick) | ✅ Completo (funciona out-of-the-box) |

---

## 🛠️ Stack Tecnológico

| Tecnología | Uso | Documentación oficial |
|------------|-----|-----------------------|
| **PHP 8.2** | Backend (sin frameworks) | [php.net](https://www.php.net/) |
| **MySQL 8.0** | Base de datos | [dev.mysql.com](https://dev.mysql.com/doc/) |
| **Apache** | Servidor web | [httpd.apache.org](https://httpd.apache.org/docs/) |
| **Docker + Compose** | Containerización | [docs.docker.com](https://docs.docker.com/) |
| **JavaScript (ES6+)** | Frontend interactivo | [MDN JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript) |
| **CSS3** | Estilos responsive | [MDN CSS](https://developer.mozilla.org/en-US/docs/Web/CSS) |
| **GD + Imagick** | Procesamiento de imágenes y GIFs | [GD](https://www.php.net/manual/en/book.image.php) · [Imagick](https://www.php.net/manual/en/book.imagick.php) |

Documentación detallada por tecnología en la carpeta [`docs/`](docs/).

---

## 🏗️ Arquitectura

Arquitectura **MVC ligera** hecha a mano:

```
public/          → Punto de entrada (index.php) + assets
app/
  ├── controllers/   → Lógica de negocio (Auth, Editor, Gallery…)
  ├── models/        → Acceso a datos (User, Image, Like, Comment)
  ├── views/         → Plantillas PHP
  └── core/          → Router, Controller base, Database, Mailer, Validator
config/          → database.php + setup.php
```

- **Router** personalizado (mapeo de URLs amigables)
- **Database** Singleton con PDO (`ERRMODE_EXCEPTION`)
- Rutas absolutas basadas en `__DIR__` (robustas en Docker)

---

## 🚀 Instalación

### Prerrequisitos

- [Docker](https://docs.docker.com/get-docker/) + Docker Compose
- (Opcional) `make`

### Inicio Rápido

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd 42_camagru

# 2. Generar el archivo .env (interactivo)
chmod +x env_setup.sh
./env_setup.sh

# 3. Levantar los contenedores
docker-compose up --build -d
# o
make up

# 4. Abrir en el navegador
# http://localhost:8080
```

### Verificar que el GIF funciona

```bash
docker-compose exec web php -m | grep imagick
# Debe aparecer: imagick
```

Más detalles en [QUICKSTART.md](QUICKSTART.md).

---

## 📁 Estructura del Proyecto

```
├── app/
│   ├── controllers/     # Auth, Editor, Gallery, User, Home
│   ├── models/          # User, Image, Like, Comment
│   ├── views/           # Plantillas
│   └── core/            # Router, Controller, Database, Mailer, Validator
├── config/
│   ├── database.php
│   └── setup.php
├── public/
│   ├── index.php        # Front controller
│   ├── css/
│   ├── js/
│   ├── stickers/
│   └── uploads/
├── docs/                # Documentación por tecnología
├── Dockerfile
├── docker-compose.yml
├── env_setup.sh
├── test_auto.sh
├── test_eval.sh
└── README.md
```

---

## 🔒 Características de Seguridad

- Contraseñas hasheadas con `password_hash(PASSWORD_DEFAULT)`
- Protección CSRF en todos los formularios POST
- Protección XSS (`htmlspecialchars` + `ENT_QUOTES`)
- Protección SQLi (PDO prepared statements)
- Validación de archivos por contenido real (`finfo`)
- Tokens de verificación y reset de un solo uso con expiración

---

## 🌟 Bonus Implementados

1. **AJAX** – Likes y comentarios sin recargar la página
2. **Live Preview** – Sticker superpuesto en tiempo real + drag & drop
3. **Infinite Scroll** – Carga progresiva de la galería
4. **GIF Animado** – Generación server-side con Imagick (instalado en el Dockerfile)

---

## 🧪 Testing

```bash
# Suite automatizada
./test_auto.sh

# Guía interactiva de evaluación (125 %)
./test_eval.sh
```

---

## 📚 Documentación Adicional

| Documento | Descripción |
|-----------|-------------|
| [QUICKSTART.md](QUICKSTART.md) | Puesta en marcha rápida |
| [COMPLIANCE.md](COMPLIANCE.md) | Checklist de cumplimiento del subject |
| [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) | Visión general del proyecto |
| [DATABASE_INITIALIZATION.md](DATABASE_INITIALIZATION.md) | Inicialización de la BD |
| [docs/php.md](docs/php.md) | PHP y extensiones |
| [docs/js.md](docs/js.md) | JavaScript y APIs del navegador |
| [docs/css.md](docs/css.md) | CSS y diseño responsive |
| [docs/sql.md](docs/sql.md) | MySQL y PDO |
| [docs/docker.md](docs/docker.md) | Docker y containerización |

---

## 📄 Licencia

Proyecto educativo realizado en el marco de [42 School](https://www.42network.org/) – Campus [42 Málaga](https://www.42malaga.com/).

Stickers emoji: [Twemoji](https://github.com/twitter/twemoji) by Twitter/X, CC-BY 4.0.

---

<br>
<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
