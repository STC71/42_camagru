# =============================================================================
# Dockerfile - Camagru
# =============================================================================
# Este archivo le dice a Docker cómo construir la "caja" (imagen) donde va a
# vivir nuestra aplicación.
#
# Analogía cotidiana:
# Imagina que quieres montar un restaurante. El Dockerfile es la lista de
# ingredientes y pasos que le das al constructor para que te monte la cocina
# completa: fogones, nevera, platos, etc.
# =============================================================================

FROM php:8.2-apache-bookworm

# -----------------------------------------------------------------------------
# 1. Instalamos todo lo necesario en el sistema
# -----------------------------------------------------------------------------
# - librerías de desarrollo para imágenes (libpng, etc.)
# - ImageMagick (el programa que sabe crear GIFs animados)
# - libmagickwand-dev (necesario para que PHP pueda hablar con ImageMagick)
# -----------------------------------------------------------------------------
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        zip \
        unzip \
        imagemagick \
        libmagickwand-dev \
    # Configuramos e instalamos las extensiones de PHP que necesitamos
    && docker-php-ext-configure gd \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    # Instalamos la extensión PHP "imagick" (permite crear GIFs desde PHP)
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    # Limpiamos la basura para que la imagen ocupe menos espacio
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# 2. Activamos módulos de Apache que necesitamos
# -----------------------------------------------------------------------------
# rewrite  → permite URLs bonitas (/gallery en vez de /index.php?page=gallery)
# headers  → permite añadir cabeceras de seguridad
# -----------------------------------------------------------------------------
RUN a2enmod rewrite headers \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

# -----------------------------------------------------------------------------
# 3. Definimos la carpeta de trabajo dentro del contenedor
# -----------------------------------------------------------------------------
WORKDIR /var/www/html

# -----------------------------------------------------------------------------
# 4. Copiamos TODO el código del proyecto dentro del contenedor
# -----------------------------------------------------------------------------
COPY . /var/www/html

# -----------------------------------------------------------------------------
# 5. Configuramos Apache para que use nuestra configuración personalizada
# -----------------------------------------------------------------------------
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# -----------------------------------------------------------------------------
# 6. Creamos las carpetas de subida y les damos permisos correctos
# -----------------------------------------------------------------------------
# www-data es el usuario con el que Apache ejecuta PHP.
# Si no le damos permisos, la aplicación no podrá guardar fotos ni GIFs.
# -----------------------------------------------------------------------------
RUN mkdir -p /var/www/html/public/uploads/images \
    && mkdir -p /var/www/html/public/uploads/temp \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# -----------------------------------------------------------------------------
# 7. Pequeño ajuste para que los permisos de archivos creados sean correctos
# -----------------------------------------------------------------------------
RUN echo "umask 0000" >> /etc/apache2/envvars

# -----------------------------------------------------------------------------
# 8. Exponemos el puerto 80 (el puerto normal de las páginas web)
# -----------------------------------------------------------------------------
EXPOSE 80

# -----------------------------------------------------------------------------
# 9. Comando que se ejecuta cuando arranca el contenedor
# -----------------------------------------------------------------------------
CMD ["apache2-foreground"]
