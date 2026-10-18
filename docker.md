# 🐳 Docker en Camagru

[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docs.docker.com/)
[![Docker Compose](https://img.shields.io/badge/Compose-V2-2496ED?style=flat-square&logo=docker)](https://docs.docker.com/compose/)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

---

## 1. ¿Qué es Docker y por qué lo exigimos?

**Docker** permite empaquetar una aplicación junto con todo lo que necesita (sistema operativo, PHP, extensiones, Apache, MySQL…) en un **contenedor**.

El subject de 42 dice explícitamente:

> *“Up to date containerization is a must.”*

Ventajas concretas en Camagru:

- Cualquier persona puede levantar el proyecto con un solo comando
- Todos usamos exactamente las mismas versiones de PHP, MySQL, etc.
- No “en mi máquina funciona”
- El bonus de GIF funciona out-of-the-box (ImageMagick va dentro de la imagen)

> **Analogía cotidiana:**  
> Un contenedor Docker es como una **caja de mudanza perfectamente etiquetada**. Da igual en qué casa la abras: dentro está todo lo necesario y en el mismo sitio.

Documentación oficial: [https://docs.docker.com/](https://docs.docker.com/)

---

## 2. Arquitectura de contenedores

```text
┌─────────────────────────────────────────────────────┐
│                   docker-compose                    │
│                                                     │
│  ┌────────────────────┐    ┌────────────────────┐  │
│  │   servicio web     │    │   servicio db      │  │
│  │  php:8.2-apache    │    │   mysql:8.0        │  │
│  │  + GD + Imagick    │    │                    │  │
│  │  puerto 8080→80    │    │  puerto 3306       │  │
│  └────────────────────┘    └────────────────────┘  │
└─────────────────────────────────────────────────────┘
```

| Servicio | Imagen base | Puerto | Rol |
|----------|-------------|--------|-----|
| **web** | `php:8.2-apache-bookworm` | 8080 → 80 | Apache + PHP + nuestra aplicación |
| **db** | `mysql:8.0` | 3306 (interno) | Base de datos |

---

## 3. Qué instalamos dentro del contenedor web

| Paquete / extensión | Motivo |
|---------------------|--------|
| `libpng-dev`, `gd` | Procesar imágenes (stickers) |
| `imagemagick`, `libmagickwand-dev` | Motor para crear GIFs |
| `imagick` (pecl) | Extensión PHP que habla con ImageMagick |
| `pdo_mysql` | Conexión con MySQL |
| `mbstring`, `zip`, `exif`… | Utilidades varias |

Fragmento relevante del Dockerfile:

```dockerfile
RUN apt-get update && apt-get install -y \
        imagemagick libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick
```

---

## 4. Comandos que más se usan

```bash
# Construir las imágenes y levantar los contenedores
docker-compose up --build -d

# Ver los logs en tiempo real
docker-compose logs -f web

# Entrar dentro del contenedor web
docker-compose exec web bash

# Comprobar que imagick está cargado
docker-compose exec web php -m | grep imagick

# Parar y eliminar contenedores
docker-compose down

# Parar y borrar también los volúmenes (¡cuidado, borra la BD!)
docker-compose down -v
```

---

## 5. Variables de entorno

Las credenciales y la URL de la aplicación se configuran en el archivo `.env` (generado por `./env_setup.sh`).

Docker Compose lee ese archivo y se lo pasa a los contenedores.

```env
DB_HOST=db
DB_NAME=camagru
DB_USER=...
DB_PASS=...
APP_URL=http://localhost:8080
```

---

## 6. Volúmenes y persistencia

- El código de la aplicación se copia (o se monta) dentro del contenedor web
- Los datos de MySQL se guardan en un **volumen** de Docker para que no se pierdan al reiniciar
- Las fotos subidas viven en `public/uploads/`

---

## 7. Buenas prácticas aplicadas

- Una sola responsabilidad por contenedor (web / db)
- No guardar secretos dentro de la imagen (van en `.env`)
- Permisos correctos en `public/uploads` (`www-data`)
- Limpieza de caché de `apt` para que la imagen ocupe menos

---

## 8. Recursos oficiales recomendados

| Recurso | Enlace |
|---------|--------|
| Docker Docs | https://docs.docker.com/ |
| Docker Compose | https://docs.docker.com/compose/ |
| Best practices | https://docs.docker.com/develop/dev-best-practices/ |
| Imagen oficial PHP | https://hub.docker.com/_/php |
| Imagen oficial MySQL | https://hub.docker.com/_/mysql |
| Dockerfile reference | https://docs.docker.com/engine/reference/builder/ |

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
