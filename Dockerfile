# Keep the FrankenPHP base image
FROM dunglas/frankenphp:1.3.0-php8.3-bookworm

# Install PHP dependencies
RUN apt-get update && apt-get install -y \
    gnupg2 \
    curl \
    apt-transport-https \
    ca-certificates \
    unixodbc-dev \
    libgssapi-krb5-2 \
    libssl-dev \
    libxml2-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    libonig-dev \
    software-properties-common && \
    curl -sSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /usr/share/keyrings/microsoft-prod.gpg && \
    echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && \
    ACCEPT_EULA=Y apt-get install -y msodbcsql18 && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install ONLY PHP SQLSRV extensions — NOT the tools
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Other Laravel extensions
RUN docker-php-ext-install pdo mbstring exif pcntl bcmath gd zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# App
WORKDIR /app
COPY . .

COPY php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Permissions
RUN chmod -R 775 storage bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

ENV LD_LIBRARY_PATH="/opt/microsoft/msodbcsql18/lib64"

CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=127.0.0.1", "--port=8080", "--admin-port=2019"]





# FROM php:8.1-fpm-alpine

# # Set working directory
# WORKDIR /var/www

# # Install system dependencies
# RUN apk update && apk add --no-cache \
#     git \
#     curl \
#     libpng-dev \
#     oniguruma-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     libzip-dev \
#     postgresql-dev \
#     mysql-client \
#     supervisor \
#     autoconf \
#     gcc \
#     g++ \
#     make \
#     bash

# # Install PHP extensions
# RUN docker-php-ext-install \
#     pdo_mysql \
#     mbstring \
#     exif \
#     pcntl \
#     bcmath \
#     gd \
#     zip

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copy existing application directory
# COPY . /var/www

# # Set permissions
# RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# # Expose port
# EXPOSE 9000

# # Default command
# CMD ["php-fpm"]
