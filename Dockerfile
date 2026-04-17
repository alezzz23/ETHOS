# syntax=docker/dockerfile:1.6

############################
# Stage 1 — Frontend build #
############################
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./

RUN npm run build


##########################
# Stage 2 — PHP runtime  #
##########################
FROM php:8.2-apache AS runtime

ENV DEBIAN_FRONTEND=noninteractive \
    APP_ENV=production \
    APP_DEBUG=false \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

# System dependencies + PHP extensions required by Laravel / project.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        curl \
        ca-certificates \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        zip \
        gd \
        intl \
        exif \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Production-tuned php.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

# Apache configuration: listen on $PORT, DocumentRoot on /public
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite headers \
    && rm -f /etc/apache2/sites-enabled/000-default.conf
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first for layer caching.
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-scripts \
        --no-progress \
        --optimize-autoloader

# Application source
COPY . .

# Built assets from frontend stage
COPY --from=frontend /app/public/build ./public/build

# Finalize composer (runs package:discover now that /app code is present)
RUN composer dump-autoload --no-dev --optimize \
    && php artisan package:discover --ansi || true

# Permissions for Laravel writable paths
RUN chown -R www-data:www-data storage bootstrap/cache \
    && find storage -type d -exec chmod 775 {} \; \
    && find storage -type f -exec chmod 664 {} \; \
    && chmod -R 775 bootstrap/cache

# Entrypoint handles runtime config + migrations + cache warming.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080
ENV PORT=8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
