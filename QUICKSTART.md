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

**Campus:** [42 Málaga](https://www.42malaga.com/)
