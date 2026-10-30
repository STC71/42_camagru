# 📦 `app/` — Aplicación principal (MVC)

> **Navegación:** [README del proyecto](../README.md) · [QUICKSTART](../QUICKSTART.md) · [PROJECT_OVERVIEW](../PROJECT_OVERVIEW.md) · [public/](../public/README.md)

Este directorio es el **corazón de Camagru**: lógica de negocio y presentación, **fuera** del document root para que no sea accesible directamente por URL (Apache solo sirve `public/`).

---

## ¿Qué es y para qué sirve?

Organiza el código en capas **MVC** más un **core** compartido:

| Capa | Carpeta | Rol |
|------|---------|-----|
| Coordinación HTTP | [controllers/](controllers/README.md) | Recibe la petición, valida, llama modelos, elige vista o JSON |
| Datos | [models/](models/README.md) | SQL vía PDO (`Database::query`) |
| HTML | [views/](views/README.md) | Plantillas PHP + partials |
| Kernel | [core/](core/README.md) | Router, PDO, Mailer, Validator, I18n, Controller base |
| Traducciones | `lang/` | Arrays ES / EN / PT / DE / FR para `__()` |

**Analogía:** el restaurante. Los *controllers* son los camareros, los *models* la cocina/almacén, las *views* el plato servido, el *core* la infraestructura (caja, luces, reservas).

---

## Flujo de una petición

```
Cliente → public/index.php → Router → Controller → Model (MySQL)
                                         ↓
                                       View (HTML) o json()
```

1. `public/index.php` arranca sesión, carga config, `I18n::init()`, despacha la ruta.  
2. El controlador usa `$this->model('User')`, `$this->view(...)`, `$this->redirect()`, `$this->json()`.  
3. Las vistas incluyen [partials/header](views/partials/README.md) y footer.

Detalle de rutas y clases: [app/core/README.md](core/README.md).

---

## Estructura

```
app/
├── README.md              ← este archivo
├── core/                  → Router, Database, Controller, Mailer, Validator, I18n
├── controllers/           → Auth, Editor, Gallery, Home, User
├── models/                → User, Image, Like, Comment
├── views/                 → auth, editor, gallery, home, user, partials
└── lang/                  → es.php, en.php, pt.php, de.php, fr.php
```

---

## Seguridad en esta capa

- Sin frameworks ni Composer (eval).  
- Entrada validada (`Validator`) y consultas preparadas.  
- Sesión + `requireAuth()` / `requireGuest()` en el controlador base.  
- CSRF en formularios y muchos POST AJAX.  
- Salida escapada en vistas (`htmlspecialchars`).

Más: [COMPLIANCE.md](../COMPLIANCE.md) · [php.md](../php.md)

---

## Documentación hija

| Ruta | Contenido |
|------|-----------|
| [core/](core/README.md) | Piezas reutilizables |
| [controllers/](controllers/README.md) | Endpoints y responsabilidades |
| [models/](models/README.md) | Tablas y métodos |
| [views/](views/README.md) | Plantillas y partials |

Tecnologías: [php.md](../php.md) · [sql.md](../sql.md) · [js.md](../js.md)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
