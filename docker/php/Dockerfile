FROM php:8.4-fpm

###############################################################
# Variables
###############################################################

ARG user=www-data
ARG uid=1000

###############################################################
# Directorio de trabajo
###############################################################

WORKDIR /var/www

###############################################################
# Dependencias del sistema
###############################################################

RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    unzip \
    zip \
    nano \
    vim \
    supervisor \
    cron \
    procps \
    iputils-ping \
    net-tools \
    default-mysql-client \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libxslt1-dev \
    libmagickwand-dev \
    ffmpeg \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    webp \
    gnupg \
    ca-certificates

###############################################################
# NodeJS 22
###############################################################

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -

RUN apt-get install -y nodejs

###############################################################
# PHP Extensions
###############################################################

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    bcmath \
    exif \
    intl \
    gd \
    zip \
    pcntl \
    soap \
    xsl \
    opcache

###############################################################
# Redis
###############################################################

# El catalogo de PECL que trae la imagen base esta desactualizado y hace que
# 'pecl install redis' falle con "No releases available". Refrescarlo primero
# lo resuelve; van en el mismo RUN para que compartan capa y cache.
RUN pecl channel-update pecl.php.net \
    && pecl install redis

RUN docker-php-ext-enable redis

###############################################################
# Composer
###############################################################

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

###############################################################
# PHP Configuration
###############################################################

COPY docker/php/php.ini /usr/local/etc/php/php.ini

###############################################################
# Supervisor
###############################################################

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/queue.conf /etc/supervisor/conf.d/queue.conf
COPY docker/supervisor/scheduler.conf /etc/supervisor/conf.d/scheduler.conf

###############################################################
# Entrypoint
###############################################################

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

###############################################################
# Permisos
###############################################################

RUN usermod -u ${uid} www-data

RUN chown -R www-data:www-data /var/www

###############################################################
# Exponer PHP-FPM
###############################################################

EXPOSE 9000

###############################################################
# ENTRYPOINT
###############################################################

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

###############################################################
# COMANDO PRINCIPAL
###############################################################

CMD ["/usr/bin/supervisord","-c","/etc/supervisor/supervisord.conf","-n"]