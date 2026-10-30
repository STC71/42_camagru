# 🐳 Docker en Camagru

[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docs.docker.com/)
[![Compose](https://img.shields.io/badge/Compose-V2-2496ED?style=flat-square&logo=docker)](https://docs.docker.com/compose/)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [QUICKSTART](QUICKSTART.md) · [Docs Docker](https://docs.docker.com/)

El subject exige **containerización actual**: la app debe poder evaluarse con el entorno del repo (“works as is”).

---

## 1. Servicios (`docker-compose.yml`)

| Servicio | Rol | Puerto host |
|----------|-----|-------------|
| `web` | PHP 8.2 + Apache + GD + Imagick | 8080 → 80 |
| `db` | MySQL 8 + `init.sql` | 3306 |
| `phpmyadmin` | UI opcional | 8081 |

Recomendado: `restart: unless-stopped` en los tres.

Variables `MAIL_*`, `DB_*`, `APP_URL` desde `.env`.

---

## 2. Imagen web (`Dockerfile`)

- Base `php:8.2-apache-bookworm`  
- Extensiones: `pdo_mysql`, `gd` (JPEG), `imagick`  
- `mod_rewrite`, `apache.conf`, `php-camagru.ini`  
- Directorios `uploads` con permisos de escritura  

```bash
docker-compose build --no-cache web
docker-compose up -d --force-recreate web
docker-compose exec web php -m | grep -i imagick
```

---

## 3. Volúmenes

- `db_data`: datos MySQL persistentes  
- A menudo `./public/uploads` montado para no perder fotos al rebuild  

---

## 4. Comandos útiles

```bash
docker-compose up --build -d
docker-compose logs web --tail 80
docker-compose exec web bash
docker-compose down       # para contenedores
docker-compose down -v    # + borra volumen BD
```

---

## 5. Enlaces oficiales

- [Docker Compose](https://docs.docker.com/compose/)  
- [PHP Docker images](https://hub.docker.com/_/php)  
- [MySQL Docker](https://hub.docker.com/_/mysql)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
