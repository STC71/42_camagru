# 📸 Camagru – Plataforma de Compartición de Fotos

**Proyecto Outer Core implementado en [42 Málaga](https://www.42malaga.com/)** — aplicación web tipo Instagram: captura con webcam, stickers, subida de archivos, galería pública, likes, comentarios, notificaciones por email y GIFs animados.

[![42](https://img.shields.io/badge/42-Málaga-00babc?style=for-the-badge&logo=42)](https://www.42malaga.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/doc/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docs.docker.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![Subject](https://img.shields.io/badge/Subject-v4.1-informational?style=for-the-badge)](https://www.42malaga.com/)

---

## 📋 Tabla de contenidos

- [Resumen](#-resumen-del-proyecto)
- [Características](#-características)
- [Stack](#️-stack-tecnológico)
- [Arquitectura](#-arquitectura)
- [Instalación](#-instalación)
- [Estructura y mapa de documentación](#-estructura-y-mapa-de-documentación)
- [Seguridad](#-características-de-seguridad)
- [Bonus](#-bonus-implementados)
- [Testing / defensa](#-testing--defensa)
- [Documentación por capa](#-documentación-por-capa)
- [Licencia](#-licencia)

---

## 🎯 Resumen del proyecto

**Camagru** permite a usuarios registrados:

| Área | Qué hace |
|------|----------|
| 📸 Captura | Webcam en el navegador + composición **en el servidor** (GD) |
| 🎨 Stickers | Pegatinas con transparencia; preview en vivo y drag (bonus) |
| 📁 Subida | JPG/PNG como imagen base + sticker |
| 🎬 GIF | Secuencia corta animada (Imagick) |
| 🖼️ Galería | Pública, likes, comentarios, paginación / scroll |
| 🔐 Auth | Registro, verificación por email, login, reset de contraseña |
| ⚙️ Ajustes | Perfil, password, notificaciones; baja de cuenta opcional |
| 🌐 i18n | ES (por defecto), EN, PT, DE, FR (banderas en cabecera) |

> Proyecto educativo **sin frameworks PHP** ni Composer/npm (requisito de la hoja de evaluación).

---

## ✨ Características

### Obligatorias (subject / eval)

- Registro (usuario, email, password con complejidad) + **email de confirmación**
- Login / logout; reset de password por email
- Editor autenticado: webcam o upload, stickers, miniaturas propias
- Composición de imagen **server-side**
- Galería pública ordenada por fecha; likes y comentarios si hay sesión
- Notificación por email al autor del comentario (preferencia activable)
- Paginación (≥ 5 elementos por página)
- PDO + `PDO::ERRMODE_EXCEPTION`
- Contraseñas con `password_hash` / `password_verify`
- Defensa XSS / SQLi; CSRF en POST sensibles
- Containerización Docker (“works as is”)

### Extra / bonus

- AJAX en likes y comentarios
- Preview en vivo del sticker + arrastre
- Infinite scroll / carga progresiva
- GIF animado (extensión **imagick** en el contenedor)
- i18n multi-idioma
- Eliminación de cuenta en Ajustes (con opción de borrar fotos)

---

## 🛠️ Stack tecnológico

| Capa | Tecnología | Doc. oficial |
|------|------------|--------------|
| Backend | PHP 8.2 | [php.net](https://www.php.net/) |
| BD | MySQL 8 + PDO | [MySQL docs](https://dev.mysql.com/doc/) |
| Frontend | HTML5, CSS3, JS ES6+ | [MDN](https://developer.mozilla.org/) |
| Imágenes | GD + Imagick | [Imagick](https://www.php.net/manual/en/book.imagick.php) |
| Contenedores | Docker / Compose | [docs.docker.com](https://docs.docker.com/) |
| Correo (dev) | SMTP (p. ej. Mailtrap) | [mailtrap.io](https://mailtrap.io/) |

Guías internas del repo: [php.md](php.md) · [js.md](js.md) · [css.md](css.md) · [sql.md](sql.md) · [docker.md](docker.md)

---

## 🏗️ Arquitectura

Front controller en `public/index.php` → Router → Controllers → Models (PDO) → Views.

```
Navegador  ──HTTP──►  Apache/PHP (web)
                          │
                          ├── app/controllers  (Auth, Editor, Gallery, User…)
                          ├── app/models       (User, Image, Like, Comment…)
                          ├── app/core         (Router, Database, Mailer, I18n…)
                          └── app/views        (HTML + partials)
                          │
                     PDO ──► MySQL (db)
```

Más detalle: [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) · [app/README.md](app/README.md)

---

## 🚀 Instalación

Guía corta: **[QUICKSTART.md](QUICKSTART.md)**

```bash
git clone <tu-repo> && cd camagru
cp .env.example .env   # o ./env_setup.sh
docker-compose up --build -d
# App:  http://localhost:8080
# Admin BD (opcional): http://localhost:8081
```

Comprobaciones útiles:

```bash
docker-compose exec web php -m | grep -E 'pdo_mysql|gd|imagick'
docker-compose exec web php -r 'print_r(gd_info());' | grep JPEG
./test_auto.sh
```

---

## 📁 Estructura y mapa de documentación

Cada carpeta relevante tiene su propio `README.md` (de lo general a lo concreto). **No** usamos una carpeta `docs/` monolítica: la documentación vive junto al código.

```
camagru/
├── README.md                 ← estás aquí
├── QUICKSTART.md
├── PROJECT_OVERVIEW.md
├── SUMMARY.md
├── COMPLIANCE.md
├── DATABASE_INITIALIZATION.md
├── php.md · js.md · css.md · sql.md · docker.md
├── test_auto.sh · test_eval.sh
├── Dockerfile · docker-compose.yml · apache.conf · php-camagru.ini
├── app/                      → [app/README.md](app/README.md)
│   ├── core/                 → [app/core/README.md](app/core/README.md)
│   ├── controllers/          → [app/controllers/README.md](app/controllers/README.md)
│   ├── models/               → [app/models/README.md](app/models/README.md)
│   ├── views/                → [app/views/README.md](app/views/README.md)
│   │   ├── auth/ editor/ gallery/ home/ partials/ user/
│   └── lang/                 (traducciones ES/EN/PT/DE/FR)
├── config/                   → [config/README.md](config/README.md)
├── database/                 → [database/README.md](database/README.md)
└── public/                   → [public/README.md](public/README.md)
    ├── css/ js/ stickers/ uploads/
```

---

## 🔒 Características de seguridad

| Medida | Dónde |
|--------|--------|
| `password_hash` / `password_verify` | Modelo User / Auth |
| PDO prepared statements | `Database::query` + models |
| `PDO::ERRMODE_EXCEPTION` | `app/core/Database.php` |
| Escape HTML (`htmlspecialchars`) | Vistas / helpers |
| Tokens CSRF | Formularios y POST AJAX |
| Editor y acciones sensibles solo autenticados | `requireAuth()` |
| Uploads validados (tipo/tamaño) | `EditorController` |

Checklist de evaluación: [COMPLIANCE.md](COMPLIANCE.md)

---

## 🏆 Bonus implementados

1. Interfaz AJAX (likes / comentarios)  
2. Preview en vivo + drag del sticker  
3. Infinite scroll / load-more en galería  
4. GIF animado con **Imagick** (debe aparecer en `php -m`)  
5. Reset de contraseña completo por email  

---

## 🧪 Testing / defensa

| Script | Uso |
|--------|-----|
| [test_auto.sh](test_auto.sh) | Batería automática (preflight, Docker, HTTP, seguridad, features, bonus) |
| [test_eval.sh](test_eval.sh) | Guía interactiva alineada con la hoja de evaluación |

```bash
chmod +x test_auto.sh test_eval.sh
./test_auto.sh
./test_eval.sh
```

---

## 📚 Documentación por capa

### Raíz (visión y arranque)

| Documento | Contenido |
|-----------|-----------|
| [QUICKSTART.md](QUICKSTART.md) | Arranque en minutos, Mailtrap, permisos, troubleshooting |
| [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) | Arquitectura y flujos |
| [SUMMARY.md](SUMMARY.md) | Resumen ejecutivo del deliverable |
| [COMPLIANCE.md](COMPLIANCE.md) | Subject + eval punto a punto |
| [DATABASE_INITIALIZATION.md](DATABASE_INITIALIZATION.md) | Esquema e init de MySQL |

### Tecnologías (raíz)

| Documento | Contenido |
|-----------|-----------|
| [php.md](php.md) | PHP 8.2, GD, Imagick, sesiones |
| [js.md](js.md) | Editor, galería, fetch, webcam |
| [css.md](css.md) | Layout, responsive, i18n UI |
| [sql.md](sql.md) | Tablas, FK, CASCADE |
| [docker.md](docker.md) | Imágenes, compose, volúmenes |

### Código (por directorio)

| Ruta | README |
|------|--------|
| [app/](app/README.md) | Capa de aplicación MVC |
| [app/core/](app/core/README.md) | Kernel (Router, DB, Mailer, I18n…) |
| [app/controllers/](app/controllers/README.md) | Controladores HTTP |
| [app/models/](app/models/README.md) | Acceso a datos |
| [app/views/](app/views/README.md) | Plantillas |
| [config/](config/README.md) | Entorno y setup |
| [database/](database/README.md) | SQL de arranque |
| [public/](public/README.md) | Document root y estáticos |

---

## 📄 Licencia

Proyecto educativo en el marco de [42 School](https://www.42network.org/) — campus [42 Málaga](https://www.42malaga.com/).

Stickers tipo emoji: [Twemoji](https://github.com/twitter/twemoji) (Twitter/X), licencia CC-BY 4.0.

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
