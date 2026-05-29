FROM php:8.4-fpm-alpine

# Устанавливаем системные зависимости и расширения PHP для PostgreSQL
RUN apk add --no-cache libpq-dev git unzip bash \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Копируем Composer из официального Docker-образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настраиваем рабочую директорию
WORKDIR /var/www/html
