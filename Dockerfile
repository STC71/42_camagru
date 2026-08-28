FROM php:8.2-apache-bookworm

# Install system dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        libzip-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
    
# Enable Apache modules and set a global server name
RUN a2enmod rewrite headers \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

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
