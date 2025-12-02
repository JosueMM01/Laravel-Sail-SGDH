# Dockerfile multi-stage para Laravel en producción
# Genera imagen sgdh:latest optimizada para despliegue

# ===== STAGE 1: Builder (Composer & NPM) =====
FROM php:8.3-fpm-alpine AS builder

ENV TZ=America/Mexico_City

WORKDIR /var/www/html

# Instalar dependencias del sistema necesarias para compilar extensiones PHP
RUN set -eux; \
    for mirror in dl-cdn.alpinelinux.org mirrors.aliyun.com uk.alpinelinux.org; do \
        sed -E -i "s|https://[^/]+/alpine/|https://${mirror}/alpine/|g" /etc/apk/repositories; \
        if apk update; then break; fi; \
    done; \
    apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    libzip-dev \
    zlib-dev \
    libwebp-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    autoconf \
    dpkg-dev \
    dpkg \
    file \
    build-base \
    re2c \
    zip \
    unzip \
    nodejs \
    npm \
    tzdata \
    && ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo "${TZ}" > /etc/timezone \
    && export PHPIZE_DEPS="" \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos de dependencias primero (caching)
COPY composer.json composer.lock ./
COPY package.json package-lock.json* ./

# Instalar dependencias PHP (sin dev)
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias Node y compilar assets
COPY . .
RUN npm ci && npm run build

# Optimizaciones Laravel
RUN composer dump-autoload --optimize && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# ===== STAGE 2: Runtime (PHP-FPM + Nginx) =====
FROM php:8.3-fpm-alpine

ENV TZ=America/Mexico_City

LABEL maintainer="JosueMM01"
LABEL description="SGDH - Sistema de Gestión de Dotaciones Hospitalarias"

WORKDIR /var/www/html

# Instalar solo librerías runtime (sin compilar)
RUN set -eux; \
    for mirror in dl-cdn.alpinelinux.org mirrors.aliyun.com uk.alpinelinux.org; do \
        sed -E -i "s|https://[^/]+/alpine/|https://${mirror}/alpine/|g" /etc/apk/repositories; \
        if apk update; then break; fi; \
    done; \
    apk add --no-cache \
    nginx \
    supervisor \
    libpng \
    oniguruma \
    libxml2 \
    icu-libs \
    libzip \
    zlib \
    libwebp \
    libjpeg-turbo \
    freetype \
    tzdata \
    && ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo "${TZ}" > /etc/timezone

# Copiar extensiones PHP compiladas desde builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copiar archivos compilados desde builder
COPY --from=builder --chown=www-data:www-data /var/www/html /var/www/html

# Configuración Nginx
COPY docker/prod/nginx.conf /etc/nginx/nginx.conf
COPY docker/prod/default.conf /etc/nginx/http.d/default.conf

# Configuración PHP-FPM
RUN echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.start_servers = 10" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_spare_servers = 20" >> /usr/local/etc/php-fpm.d/www.conf

# Configuración Supervisor para ejecutar PHP-FPM + Nginx
COPY docker/prod/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crear directorios y permisos
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto 8000 (Nginx escucha internamente)
EXPOSE 8000

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php artisan about || exit 1

# Entrypoint y comando
COPY docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
