# 🚀 QUICKSTART – Camagru

Guía rápida para poner en marcha el proyecto en menos de 5 minutos.

[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)
[![Docker](https://img.shields.io/badge/Docker-Required-2496ED?style=flat-square&logo=docker)](https://docs.docker.com/get-docker/)

---

## 1. Prerrequisitos

- [Docker](https://docs.docker.com/get-docker/) instalado
- [Docker Compose](https://docs.docker.com/compose/install/) (v2 recomendado)
- Puerto **8080** libre

---

## 2. Pasos

```bash
# Clonar (o entrar en el directorio del proyecto)
cd 42_camagru

# Generar el archivo .env de forma interactiva
# (sugiere automáticamente el usuario de tu sistema con whoami)
chmod +x env_setup.sh
./env_setup.sh

# Construir y levantar los contenedores
docker-compose up --build -d

# (Opcional) Ver los logs
docker-compose logs -f web
```

Abre el navegador en:

```
http://localhost:8080
```

---

## 3. Verificar que todo está bien

```bash
# Comprobar que la extensión imagick (GIF) está cargada
docker-compose exec web php -m | grep imagick

# Debe mostrar:
# imagick
```

Si aparece `imagick`, el bonus de GIF animado funciona **out-of-the-box**.

---

## 4. Configuración de correo (opcional pero recomendada)

Para que funcionen los emails de verificación y notificaciones:

1. Crea una cuenta gratuita en [Mailtrap](https://mailtrap.io)
2. Copia las credenciales SMTP
3. Vuelve a ejecutar `./env_setup.sh` o edita `.env` manualmente

---

## 5. Comandos útiles

| Comando | Descripción |
|---------|-------------|
| `docker-compose up --build -d` | Construir y arrancar |
| `docker-compose up -d` | Arrancar sin reconstruir |
| `docker-compose down` | Parar y eliminar contenedores (conserva volúmenes) |
| `docker-compose logs -f web` | Ver logs del servidor web |
| `docker-compose exec web bash` | Entrar en el contenedor |
| `./test_eval.sh` | Guía interactiva de evaluación |
| `./test_auto.sh` | Suite de pruebas automatizadas |

---

## 6. Contenedores detenidos y reinicio automático

Los servicios de Camagru declaran la política **`restart: unless-stopped`** en `docker-compose.yml`.

Eso significa:

- Si Docker o la máquina se reinician, los contenedores **vuelven a arrancar solos**.
- Si tú los detienes a propósito (`docker-compose stop` / `down`), **no** se reactivan hasta que los vuelvas a levantar.
- No hace falta reconstruir imágenes ni borrar volúmenes solo porque estén parados.

### Arranque habitual (lo primero que debes probar)

```bash
cd ~/sgoinfre/42_outer_core/web_database/camagru   # o la ruta de tu proyecto
docker-compose up -d
docker ps
curl -I http://localhost:8080
```

La respuesta esperada del último comando incluye `HTTP/1.1 200 OK`.

### Si `docker-compose` no está disponible o quieres arrancar contenedores ya existentes

```bash
docker ps -a
docker start camagru_db
docker exec camagru_db mysqladmin ping
docker start camagru_web camagru_phpmyadmin
docker ps
curl -I http://localhost:8080
```

Adapta los nombres si `docker ps -a` muestra otros.

### Comprobar la política de reinicio

```bash
docker inspect -f '{{.Name}} | restart={{.HostConfig.RestartPolicy.Name}}' \
  camagru_db camagru_web camagru_phpmyadmin
```

Debería mostrar `restart=unless-stopped` en los tres.

### Si un servicio no responde

Consulta los logs **antes** de destruir nada:

```bash
docker logs --tail 100 camagru_db
docker logs --tail 100 camagru_web
docker logs --tail 100 camagru_phpmyadmin
```

**No uses** `docker-compose down -v` como primer intento: la opción `-v` puede eliminar volúmenes y, con ellos, la base de datos.

---

## 7. Problemas frecuentes

**El contenedor no arranca**
```bash
docker-compose down
docker-compose up --build -d
```

**Permisos en uploads** (si fallan las subidas; en macOS/Windows a veces `chown` no funciona)
```bash
docker-compose exec web chmod -R 777 /var/www/html/public/uploads
```

**Cuenta creada pero no puedes entrar (“verify your email”)**  
Si el correo no llega, puedes marcar el usuario como verificado en la BD:

```bash
source .env
docker-compose exec db mysql -u root -p"${DB_ROOT_PASS}" -D "${DB_NAME}" -e \
  "UPDATE users SET verified = 1, verification_token = NULL WHERE username = 'TU_USERNAME';"
```

**Cambiar el puerto**  
Edita `docker-compose.yml` (sección `ports`) y la variable `APP_URL` en `.env`.

---

## 8. Documentación relacionada

- [README.md](README.md) – Visión general
- [COMPLIANCE.md](COMPLIANCE.md) – Checklist de evaluación
- [docs/docker.md](docs/docker.md) – Detalles de containerización
- [docs/php.md](docs/php.md) – PHP y extensiones

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
