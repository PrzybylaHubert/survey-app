FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libonig-dev libxml2-dev libcurl4-openssl-dev libssl-dev \
    mariadb-client librabbitmq-dev \
    && docker-php-ext-install pdo pdo_mysql zip sockets pcntl \
    && pecl install amqp redis \
    && docker-php-ext-enable amqp redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Symfony CLI (optional)
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set working dir
WORKDIR /var/www/html

# Copy custom Apache config (see below)
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Run Apache in the foreground
CMD ["apache2-foreground"]
