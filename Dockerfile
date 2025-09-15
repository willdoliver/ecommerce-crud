FROM php:8.1-fpm-alpine

# Instala dependências e extensões do PHP para PostgreSQL
RUN apk add --no-cache \
        $PHPIZE_DEPS \
        postgresql-dev \
        && docker-php-ext-install pdo pdo_pgsql

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

EXPOSE 9000
CMD ["php-fpm"]