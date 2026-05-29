FROM php:8.4-fpm-alpine

# Устанавливаем системные зависимости для работы (PostgreSQL, git, библиотеки для архивов)
RUN apk add --no-cache libpq-dev git unzip bash

# Скачиваем инструмент для удобной установки расширений PHP
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Устанавливаем расширения: родные для Postgres и Imagick (автоматически соберет совместимую версию)
RUN install-php-extensions pdo pdo_pgsql pgsql imagick

# Копируем Composer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настраиваем рабочую директорию
WORKDIR /var/www/html
