# =============================================================================
# Dockerfile - Camagru
# PHP 8.2 + Apache + GD (PNG/JPEG) + Imagick (GIF animados)
# =============================================================================

FROM php:8.2-apache-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        zip \
        unzip \
        imagemagick \
        libmagickwand-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

WORKDIR /var/www/html

COPY . /var/www/html

COPY apache.conf /etc/apache2/sites-available/000-default.conf

COPY php-camagru.ini /usr/local/etc/php/conf.d/camagru.ini

RUN mkdir -p /var/www/html/public/uploads/images \
    && mkdir -p /var/www/html/public/uploads/temp \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 777 /var/www/html/public/uploads

RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions \
    && chmod 1733 /var/lib/php/sessions

RUN echo "umask 0000" >> /etc/apache2/envvars

EXPOSE 80

CMD ["apache2-foreground"]
