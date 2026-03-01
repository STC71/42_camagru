FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Cambiar UID de www-data para que coincida con el usuario del host
# Esto permite a Apache leer archivos montados desde /sgoinfre
RUN usermod -u 104765 www-data && groupmod -g 104765 www-data

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory
COPY . /var/www/html

# Configure Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Configurar Apache para leer archivos independientemente del owner
RUN echo "umask 0000" >> /etc/apache2/envvars

EXPOSE 80

CMD ["apache2-foreground"]
