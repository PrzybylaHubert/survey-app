FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libonig-dev libxml2-dev libcurl4-openssl-dev libssl-dev mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Symfony CLI (optional)
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set working dir
WORKDIR /var/www/html

# Copy custom Apache config (see below)
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
