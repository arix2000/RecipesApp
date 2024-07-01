# Use the official PHP image as a parent image
FROM php:8.2-fpm

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    wget \
    && docker-php-ext-install pdo pdo_pgsql zip mbstring intl xml gd

# Install Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Copy existing application directory contents
COPY . .

COPY php.ini /usr/local/etc/php/conf.d/php.ini

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Install application dependencies
RUN composer install

# Expose port 9000
EXPOSE 9000

# Set the entrypoint
ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]
