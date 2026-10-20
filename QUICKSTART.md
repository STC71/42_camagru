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
| `docker-compose down` | Parar y eliminar contenedores |
| `docker-compose logs -f web` | Ver logs del servidor web |
| `docker-compose exec web bash` | Entrar en el contenedor |
| `./test_eval.sh` | Guía interactiva de evaluación |
| `./test_auto.sh` | Suite de pruebas automatizadas |

---

### Recuperar contenedores existentes que están detenidos

Si Camagru ya funcionaba y los contenedores aparecen como `Exited`, no es necesario reconstruirlos ni crear otros nuevos. En ese caso, arranca los contenedores existentes y comprueba los servicios.

```bash
# 1. Entra en el directorio del proyecto
cd 42_camagru

# 2. Comprueba el estado de los contenedores, incluidos los detenidos
docker ps -a

# 3. Arranca primero la base de datos existente
docker start camagru_db

# 4. Comprueba que MySQL responde
docker exec camagru_db mysqladmin ping

# La respuesta esperada contiene:
# mysqld is alive

# 5. Arranca los contenedores web y phpMyAdmin existentes
docker start camagru_web camagru_phpmyadmin

# 6. Comprueba que los contenedores están en ejecución
docker ps

# 7. Comprueba que la aplicación web responde
curl -I http://localhost:8080
```

La respuesta del último comando debería incluir `HTTP/1.1 200 OK` si la página principal está disponible.

**Si los nombres de los contenedores son distintos**, consulta los nombres reales con `docker ps -a` y adapta los comandos `docker start` y `docker exec`.

**Importante:** esta recuperación arranca los contenedores que ya existen; no elimina sus datos ni reconstruye las imágenes. Si el contenedor no arranca o algún servicio no responde, consulta sus registros antes de hacer cambios:

```bash
docker logs --tail 100 camagru_db
docker logs --tail 100 camagru_web
docker logs --tail 100 camagru_phpmyadmin
```

No uses `docker-compose down -v` como primer intento de solución: la opción `-v` puede eliminar volúmenes y, con ellos, datos persistentes. Tampoco hace falta ejecutar `docker-compose up --build -d` únicamente porque los contenedores estén detenidos.

---

## 6. Problemas frecuentes

**El contenedor no arranca**
```bash
docker-compose down -v
docker-compose up --build -d
```

**Permisos en uploads**
```bash
docker-compose exec web chown -R www-data:www-data /var/www/html/public/uploads
```

**Cambiar el puerto**
Edita `docker-compose.yml` (sección `ports`) y la variable `APP_URL` en `.env`.

---

## 7. Documentación relacionada

- [README.md](README.md) – Visión general
- [COMPLIANCE.md](COMPLIANCE.md) – Checklist de evaluación
- [docs/docker.md](docs/docker.md) – Detalles de containerización
- [docs/php.md](docs/php.md) – PHP y extensiones

---


---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
