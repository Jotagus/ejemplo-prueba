FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    nginx \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install \
        zip \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        xml \
        curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Script de inicio que usa el PORT dinámico de Railway
RUN echo '#!/bin/sh\n\
PORT=${PORT:-80}\n\
cat > /etc/nginx/sites-available/default <<EOF\n\
server {\n\
    listen ${PORT};\n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
    location / {\n\
        try_files \$uri \$uri/ /index.php?\$query_string;\n\
    }\n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
}\n\
EOF\n\
php-fpm -D\n\
nginx -g "daemon off;"' > /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]