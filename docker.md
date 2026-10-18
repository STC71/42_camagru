# Docker en Camagru

[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docs.docker.com/)
[![Docker Compose](https://img.shields.io/badge/Compose-V2-2496ED?style=flat-square&logo=docker)](https://docs.docker.com/compose/)

## Por qué Docker

El subject exige **containerización actualizada** (“Up to date containerization is a must”).  
Todo el entorno (Apache + PHP 8.2 + extensiones + MySQL) se levanta con un solo comando.

Documentación oficial: [https://docs.docker.com/](https://docs.docker.com/)

## Servicios

| Servicio | Imagen base | Puerto |
|----------|-------------|--------|
| **web** | `php:8.2-apache-bookworm` + GD + Imagick | 8080 → 80 |
| **db** | `mysql:8.0` | 3306 (interno) |

## Extensiones instaladas en el contenedor web

- `pdo_mysql`, `gd`, `zip`, `mbstring`, `exif`, `pcntl`, `bcmath`
- **ImageMagick** + extensión PHP **imagick** (necesaria para el bonus de GIF)

## Comandos habituales

```bash
# Construir y levantar
docker-compose up --build -d

# Ver logs
docker-compose logs -f web

# Entrar en el contenedor web
docker-compose exec web bash

# Comprobar que imagick está instalado
docker-compose exec web php -m | grep imagick
```

## Recursos oficiales

- Docker Docs: https://docs.docker.com/
- Docker Compose: https://docs.docker.com/compose/
- PHP Docker Official Image: https://hub.docker.com/_/php
- MySQL Docker Official Image: https://hub.docker.com/_/mysql
