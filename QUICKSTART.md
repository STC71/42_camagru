# 🚀 QUICKSTART – Camagru

Puesta en marcha en pocos minutos. Documentación general: [README.md](README.md).

[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)
[![Docker](https://img.shields.io/badge/Docker-Required-2496ED?style=flat-square&logo=docker)](https://docs.docker.com/get-docker/)

---

## 1. Requisitos

- [Docker](https://docs.docker.com/get-docker/) + Docker Compose
- Puertos libres: **8080** (app), **3306** (MySQL), **8081** (phpMyAdmin, opcional)
- (Opcional) cuenta [Mailtrap](https://mailtrap.io/) u otro SMTP para correos reales de verificación

---

## 2. Clonar y configurar entorno

```bash
cd /ruta/a/camagru
cp .env.example .env
# o: ./env_setup.sh
```

Variables habituales en `.env`:

| Variable | Ejemplo | Uso |
|----------|---------|-----|
| `APP_URL` | `http://localhost:8080` | Enlaces de email y assets |
| `DB_*` | host `db`, usuario/clave del compose | PDO |
| `MAIL_HOST` | `sandbox.smtp.mailtrap.io` | SMTP |
| `MAIL_PORT` | `2525` | SMTP |
| `MAIL_USER` / `MAIL_PASS` | credenciales Mailtrap | Auth SMTP |
| `MAIL_FROM` | `noreply@camagru.local` | Remitente |

Permisos del `.env` (el contenedor debe poder leerlo):

```bash
chmod 644 .env
```

---

## 3. Levantar servicios

```bash
docker-compose up --build -d
docker-compose ps
curl -sI http://localhost:8080 | head -3
```

| URL | Servicio |
|-----|----------|
| http://localhost:8080 | Aplicación |
| http://localhost:8081 | phpMyAdmin |

Si los contenedores se detienen al cerrar la sesión de Docker, vuelve a:

```bash
docker-compose up -d
```

Política recomendada en compose: `restart: unless-stopped` (ver [docker.md](docker.md)).

---

## 4. Correo (verificación y notificaciones)

1. Crea un inbox en [Mailtrap](https://mailtrap.io/) (Email Testing → Sandboxes).  
2. Copia **Host, Port, Username, Password** al `.env`.  
3. `docker-compose restart web`  
4. Registra un usuario → el mail de verificación aparece en Mailtrap → abre el enlace.

Sin SMTP válido el registro puede crearse pero **no** podrás verificar (salvo marcar `verified` a mano en BD; solo para depuración local).

---

## 5. Extensiones PHP críticas

```bash
docker-compose exec web php -m | grep -E 'pdo_mysql|gd|imagick'
docker-compose exec web php -r 'print_r(gd_info());' | grep -i jpeg
```

| Extensión | Para qué |
|-----------|----------|
| `pdo_mysql` | Base de datos |
| `gd` (+ JPEG) | Captura, stickers, upload |
| `imagick` | Bonus GIF |

Si falta JPEG o imagick, revisa el [Dockerfile](Dockerfile) y reconstruye:

```bash
docker-compose build --no-cache web
docker-compose up -d --force-recreate web
```

Detalle: [php.md](php.md) · [docker.md](docker.md)

---

## 6. Uploads escribibles

```bash
chmod -R 777 public/uploads
# o dentro del contenedor:
docker-compose exec web bash -c 'mkdir -p /var/www/html/public/uploads/images /var/www/html/public/uploads/temp && chmod -R 777 /var/www/html/public/uploads'
```

Más: [public/uploads/README.md](public/uploads/README.md)

---

## 7. Primer uso funcional

1. **Registro** → verificar email (Mailtrap).  
2. **Login**.  
3. **Editor**: Iniciar cámara → sticker → Capturar (o pestaña Subir archivo).  
4. **Galería**: likes / comentarios.  
5. **Ajustes**: perfil, notificaciones, (opcional) borrar cuenta.

GIF: sticker → Grabar GIF → Network `create-gif` debe devolver `"success": true`.

---

## 8. Tests

```bash
./test_auto.sh          # automático
./test_auto.sh --quick  # solo críticos
./test_eval.sh          # guía de defensa interactiva
```

---

## 9. Problemas frecuentes

| Síntoma | Qué mirar |
|---------|-----------|
| Permission denied en `.env` | `chmod 644 .env` + recreate web |
| “No JPEG support” | Dockerfile GD `--with-jpeg` + rebuild `--no-cache` |
| GIF falla | `php -m \| grep imagick` |
| No llega el mail | `MAIL_*` en `.env` y reinicio `web` |
| Upload no escribe | permisos `public/uploads` |
| Contenedores parados | `docker-compose up -d` |

Logs:

```bash
docker-compose logs web --tail 50
```

---

## 10. Documentación relacionada

- [README.md](README.md) — visión general y mapa de docs  
- [COMPLIANCE.md](COMPLIANCE.md) — checklist subject/eval  
- [DATABASE_INITIALIZATION.md](DATABASE_INITIALIZATION.md) — esquema SQL  
- [docker.md](docker.md) · [php.md](php.md) · [config/README.md](config/README.md)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
