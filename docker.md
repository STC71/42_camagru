# 🐳 Docker en Camagru

[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docs.docker.com/)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [QUICKSTART](QUICKSTART.md) · [docs.docker.com](https://docs.docker.com/)

---

## 1. Compose (idea del proyecto)

```yaml
# Extracto conceptual de docker-compose.yml
services:
  web:
    build: .
    ports: ["8080:80"]
    environment:
      - DB_HOST=db
      - MAIL_HOST=${MAIL_HOST}
    volumes:
      - ./public/uploads:/var/www/html/public/uploads
    restart: unless-stopped
  db:
    build: ./database
    volumes:
      - db_data:/var/lib/mysql
    restart: unless-stopped
```

---

## 2. Comprobar extensiones dentro del contenedor

```bash
docker-compose exec web php -m | grep -E 'pdo_mysql|gd|imagick'
docker-compose exec web php -r 'print_r(gd_info());' | grep JPEG
```

Rebuild limpio si falta JPEG/Imagick:

```bash
docker-compose build --no-cache web
docker-compose up -d --force-recreate web
```

---

## 3. Logs y shell

```bash
docker-compose logs web --tail 50
docker-compose exec web bash
```

---

## 4. Dockerfile (responsabilidades)

- `php:8.2-apache-bookworm`  
- Paquetes `libjpeg`, ImageMagick, `libmagickwand-dev`  
- `docker-php-ext-install pdo_mysql gd` + `pecl install imagick`  
- `a2enmod rewrite`  
- COPY del código + `apache.conf` + `php-camagru.ini`  

---

## 5. Enlaces

- [Compose file reference](https://docs.docker.com/compose/compose-file/)  
- [Official PHP image](https://hub.docker.com/_/php)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
