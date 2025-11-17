# docker/php/Dockerfile
FROM php:8.2-fpm-alpine

# Instala dependências do sistema e extensões PHP comuns para Laravel
RUN apk add --no-cache \
    build-base \
    linux-headers \
    libzip-dev \
    zip \
    curl \
    git \
    nginx \
    supervisor \
    mariadb-client \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml \
    && rm -rf /var/cache/apk/*

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos da aplicação
COPY . .

# Instala as dependências do Composer
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# Muda o proprietário dos arquivos para o usuário do www-data
RUN chown -R www-data:www-data /var/www/html

# Expõe a porta 9000 para o PHP-FPM
EXPOSE 9000
