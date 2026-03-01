# 🚀 Guía de Inicio Rápido

## 1. Instalación

```bash
# Clonar el repositorio
git clone <tu-repo-url> camagru
cd camagru
```

### Configuración del Entorno

**Opción 1: Script Automático (Recomendado)**

```bash
# Usar el asistente interactivo
./env_setup.sh
```

El script te guiará paso a paso para configurar todas las variables necesarias.

**Opción 2: Manual**

```bash
# Copiar archivo de configuración
cp env.example .env

# Editar .env con tus credenciales
nano .env
```

**Opción 3: Usando Make**

```bash
# Ejecutar el asistente de configuración
make setup
```

## 2. Configurar Email (¡Importante!)

Para desarrollo, usa [Mailtrap.io](https://mailtrap.io/) (gratis):

1. Regístrate en https://mailtrap.io/
2. Crea un inbox
3. Copia las credenciales SMTP a `.env`:

```env
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=<tu_usuario_mailtrap>
MAIL_PASS=<tu_contraseña_mailtrap>
```

## 3. Iniciar Aplicación con Docker

```bash
# Construir e iniciar contenedores
docker-compose up --build -d

# Verificar estado
docker-compose ps

# Ver logs
docker-compose logs -f
```

## 4. Acceder a la Aplicación

- **Aplicación Web**: http://localhost:8080
- **PHPMyAdmin**: http://localhost:8081
  - Servidor: `db`
  - Usuario: `root`
  - Contraseña: (de tu .env DB_ROOT_PASS)

## 5. Probar la Aplicación

### Tests Automatizados

```bash
# Suite completa de tests (recomendado)
make test-full

# O ejecutar directamente
./test_auto.sh

# Tests básicos rápidos
make test
```

La suite automatizada verifica:
- Entorno y configuración
- Contenedores Docker
- Base de datos y tablas
- Endpoints y recursos
- Seguridad básica
- Rendimiento

### Pruebas Manuales

1. **Registrarse**: http://localhost:8080/register
2. **Verificar Email**: Revisa Mailtrap inbox
3. **Iniciar Sesión**: http://localhost:8080/login
4. **Editor**: Crea una foto con stickers
5. **Galería**: Visualiza e interactúa con fotos

## 6. Comandos Make Disponibles

```bash
make help        # Mostrar todos los comandos disponibles
make setup       # Ejecutar asistente de configuración
make install     # Setup inicial completo
make build       # Construir contenedores
make up          # Iniciar aplicación
make down        # Detener aplicación
make restart     # Reiniciar aplicación
make logs        # Ver logs en tiempo real
make clean       # Limpiar contenedores y uploads
make fclean      # Limpieza completa (incluye .env)
make test        # Tests básicos
make test-full   # Suite completa de tests
make db          # Acceder a MySQL CLI
make shell       # Acceder a shell del contenedor
make status      # Ver estado de contenedores
```

## 7. Comandos Docker Útiles

```bash
# Iniciar
docker-compose up -d

# Detener
docker-compose down

# Reiniciar
docker-compose restart

# Ver logs
docker-compose logs -f web

# Limpiar todo (incluyendo BD)
docker-compose down -v

# Acceder a MySQL
docker-compose exec db mysql -u root -p camagru

# Acceder al contenedor web
docker-compose exec web bash
```

## Solución de Problemas

### Puerto Ya en Uso

Edita `docker-compose.yml`:
```yaml
services:
  web:
    ports:
      - "8090:80"  # Cambia 8080 a 8090
```

### Error de Permisos en Uploads

```bash
chmod -R 777 public/uploads
```

### Webcam No Funciona

- Usa **localhost** o **HTTPS** (getUserMedia requiere contexto seguro)
- Permite acceso a la cámara en el navegador
- Chrome/Firefox recomendados (Safari puede fallar)

### Base de Datos No Conecta

```bash
# Ver logs del contenedor de BD
docker-compose logs db

# Verificar credenciales
cat .env | grep DB_

# Reiniciar BD
docker-compose restart db
```

### Emails No Se Envían

- Verifica credenciales de Mailtrap en `.env`
- Revisa logs: `docker-compose logs web`
- Asegúrate que `MAIL_HOST` sea `smtp.mailtrap.io`

## Variables de Entorno Clave

```env
# Base de Datos
DB_HOST=db
DB_NAME=camagru
DB_USER=camagru_user
DB_PASS=tu_contraseña_segura
DB_ROOT_PASS=root_password

# Email (Mailtrap para desarrollo)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=tu_usuario
MAIL_PASS=tu_contraseña
MAIL_FROM=noreply@camagru.local

# Aplicación
APP_URL=http://localhost:8080
APP_ENV=development
```

## Stickers Predefinidos

Los stickers están en `public/uploads/stickers/`. Puedes añadir más:

```bash
# Descargar stickers PNG con transparencia
wget https://example.com/sticker.png -P public/uploads/stickers/
```

Recursos de stickers gratis:
- https://www.flaticon.com/
- https://www.freepik.com/
- https://www.iconfinder.com/

## Características Bonus Implementadas

- ✅ **AJAX**: Likes y comentarios sin recargar
- ✅ **Live Preview**: Vista previa en tiempo real con drag & drop
- ✅ **Infinite Scroll**: Carga automática de más imágenes
- ✅ **Social Share**: Compartir en Facebook, Twitter, WhatsApp
- ✅ **GIF Animation**: Crear GIFs animados (requiere ImageMagick)

## Instalación de ImageMagick (para GIFs)

El Dockerfile ya incluye ImageMagick, pero si instalas manualmente:

```bash
# Ubuntu/Debian
apt-get install imagemagick php-imagick

# MacOS
brew install imagemagick
pecl install imagick
```

## Próximos Pasos

1. Lee [README.md](README.md) para documentación completa
2. Revisa [COMPLIANCE.md](COMPLIANCE.md) para checklist del subject
3. Consulta [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) para arquitectura

---

**¡Listo!** Tu aplicación Camagru está corriendo en http://localhost:8080 🎉
